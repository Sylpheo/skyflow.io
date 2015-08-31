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
     * Send a Wave request.
     *
     * @return mixed
     */
    public function requestAction()
    {
        $userId = $this->getUser->getId();
        $history = $this->waveRequestDAO->findAllByUser($userId);

        $form = $this->formFactory->createBuilder('form')
            ->add('Request', 'textarea', array(
                'attr' => array('cols' => '100', 'rows' => '3'),
            ))
            ->getForm();

        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $array = $form->getData();
            $r = $array['Request'];

            $result = $this->waveRequestDAO->findByRequest($r, $userId);
            if ($result == null) {
                $waveRequest = new WaveRequest();
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
