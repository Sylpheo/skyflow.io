<?php

/**
 * Controller for Salesforce helper actions.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Salesforce\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Form\FormInterface;

use skyflow\Controller\AbstractHelperController;
use skyflow\Facade;

/**
 * Controller for Salesforce helper actions.
 */
class SalesforceHelperController extends AbstractHelperController
{
    /**
     * The query form.
     *
     * @var FormInterface
     */
    protected $queryForm;

    /**
     * AbstractHelperController constructor.
     *
     * @param Request               $request      The HTTP request.
     * @param Facade                $addon        The addon facade.
     * @param FormInterface         $queryForm    The query form.
     */
    public function __construct(
        Request $request,
        Facade $addon,
        FormInterface $queryForm
    ) {
        parent::__construct($request, $addon);
        $this->queryForm = $queryForm;
    }

    /**
     * Send a SOQL Query to Salesforce.
     *
     * @return mixed
     */
    public function queryAction()
    {
        $form = $this->formFactory
            ->createBuilder('form')
            ->add('Request', 'textarea', array(
                'attr' => array('cols' => '100', 'rows' => '3'),
            ))
            ->getForm();

        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $array = $form->getData();
            $query = $array['Request'];

            $accessToken = $this->addonUser->getAccessToken();
            $instanceUrl = $this->addonUser->getInstanceUrl();

            try {
                $salesforceRequest = $this->httpClient->createRequest(
                    'GET',
                    $instanceUrl . "/services/data/v20.0/query?q=" . urlencode($query)
                );

                $salesforceRequest->setHeader(
                    'Authorization',
                    'OAuth ' . $accessToken
                );

                $response = $this->httpClient->send($salesforceRequest);
                $statuscode = $response->getStatusCode();
            } catch (\Exception $e) {
                $statuscode= $e->getCode();
            }

            if ($statuscode == '401') {
                $this->auth->refresh();
            }

            // Resend request
            $salesforceRequest->setHeader(
                'Authorization',
                'OAuth ' . $this->user->getRefreshToken()
            );

            $response = $httpClient->send($salesforceRequest);
            $data = $response->json();
            $data = json_encode($data);

            return $this->twig->render(
                'results.html.twig',
                array(
                    'results' => $data,
                )
            );
        }

        return $this->twig->render(
            'salesforce-apihelper.html.twig',
            array(
                'requestForm' => $form->createView(),
            )
        );
    }
}
