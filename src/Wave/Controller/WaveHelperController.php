<?php

/**
 * Controller for Wave helper actions.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Wave\Controller;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

use skyflow\Domain\Users;

use Salesforce\Controller\SalesforceHelperController;

use Wave\Domain\WaveRequest;
use Wave\DAO\WaveRequestDAO;
use Wave\Service\WaveService;

/**
 * Controller for Wave helper actions.
 */
class WaveHelperController extends SalesforceHelperController
{
    /**
     * The Wave request history.
     *
     * @var WaveRequest[]
     * @todo Manage a Wave query history.
     */
    //private $waveQueries;

    /**
     * WaveHelperController constructor.
     *
     * @param Request               $request      The HTTP request.
     * @param Facade                $addon        The addon facade.
     * @param FormInterface         $queryForm    The query form.
     * @param WaveRequest[]         $waveRequests The wave request history.
     * @todo  Manage a Wave query history.
     */
    /*public function __construct(
        Request $request,
        Facade $addon,
        FormInterface $queryForm,
        array $waveRequests
    ) {
        parent::__construct($request, $addon, $queryForm);
        $this->waveQueries = $waveQueries;
    }*/

    /**
     * Get the Wave requests.
     *
     * @return WaveRequest[] An array of WaveRequests.
     * @todo   Manage a Wave query history.
     */
    /*protected function getWaveRequests()
    {
        return $this->waveRequests;
    }*/

    /**
     * Send a SAQL Query to Wave.
     *
     * @return mixed
     * @todo   Manage a Wave query history.
     */
    /*public function queryAction()
    {
        $this->getQueryForm()->handleRequest($this->getRequest());

        if ($this->getQueryForm()->isSubmitted()
            && $this->getQueryForm()->isValid()
        ) {
            $array = $form->getData();
            $r = $array['Request'];

            $currentWaveRequest = null;
            foreach ($this->waveRequests as $waveRequest) {
                if ($waveRequest->getRequest() === $r) {
                    $currentWaveRequest = $waveRequest;
                    exit;
                }
            }

            if (is_null($currentWaveRequest)) {
                $currentWaveRequest = new WaveRequest();
                $waveRequest->setUserId($userId);
                $waveRequest->setRequest($r);
                $this->waveRequestDAO->save($waveRequest);
            }

            $data = $this->wave->request($r);

            return $this->twig->render(
                'results.html.twig',
                array('results'=>$data)
            );
        }

        return $this->twig->render(
            'wave-apihelper.html.twig',
            array(
                'requestForm' => $form->createView(),
                'history' => $history,
            )
        );

        
        
        $this->getQueryForm()->handleRequest($this->getRequest());

        if ($this->getQueryForm()->isSubmitted() && $this->getQueryForm()->isValid()) {
            $array = $this->getQueryForm()->getData();
            $query = $array['Request'];

            $data = $this->getAddon()->getService('data')->query($query);

            return $this->getTwig()->render(
                'salesforce-soql-query-results.html.twig',
                array(
                    'results' => $data,
                )
            );
        }

        return $this->getTwig()->render(
            'salesforce-apihelper.html.twig',
            array(
                'requestForm' => $this->getQueryForm()->createView(),
            )
        );
    }*/

    /**
     * Send a SAQL Query to Wave.
     *
     * @return mixed
     */
    public function queryAction()
    {
        // Test query:
        // q = load "0FbB00000005KPEKA2/0FcB00000005W4tKAE";
        // q = filter q by Email in ["e.lodie62@hotmail.fr"];
        // q = foreach q generate FirstName as FirstName, LastName as LastName
        $this->getQueryForm()->handleRequest($this->getRequest());

        if ($this->getQueryForm()->isSubmitted()
            && $this->getQueryForm()->isValid()
        ) {
            $array = $this->getQueryForm()->getData();
            $query = $array['Request'];

            $data = $this->getAddon()->getService('data')->query($query);

            return $this->getTwig()->render(
                'wave/helper/query-results.html.twig',
                array(
                    'results' => json_encode($data, JSON_PRETTY_PRINT),
                )
            );
        }

        return $this->getTwig()->render(
            'wave/helper/query-form.html.twig',
            array(
                'queryForm' => $this->getQueryForm()->createView(),
            )
        );
    }

    /**
     * Resend a Wave request.
     *
     * @param string  $id The Wave request id to resend.
     * @return string The rendered results template.
     */
    public function resendAction($id)
    {
        //if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
        $waveRequest = $this->waveRequestDAO->findById($id);

        $data = $this->wave->request($waveRequest->getRequest());

        return $this->twig->render(
            'results.html.twig',
            array('results'=>$data)
        );
        //}
    }
}
