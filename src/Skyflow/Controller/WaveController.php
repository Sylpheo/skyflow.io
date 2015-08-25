<?php

/**
 * Controller for Wave actions.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow\Controller;

use GuzzleHttp\Client;
use GuzzleHttp\EntityBody;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Skyflow\Domain\Wave_request;

/**
 * Controller for Wave actions.
 */
class WaveController {

    /**
     * Send a Wave request.
     *
     * @param Request $request The HTTP Request.
     * @param Application $app The Silex Application.
     * @return mixed
     */
    public function requestWaveAction(Request $request, Application $app) {
        if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user=$app['security']->getToken()->getUser();

            $id_user = $user->getId();
            $history = $app['dao.wave_request']->findAllByUser($id_user);

            $form = $app['form.factory']->createBuilder('form')
                ->add('Request','textarea',array(
                    'attr' => array('cols' => '100', 'rows' => '3'),
                ))
                ->getForm();

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $array = $form->getData();
                $r = $array['Request'];

                $result = $app['dao.wave_request']->findByRequest($r,$id_user);
                if($result == null){
                    $r_wave = new Wave_request();
                    $r_wave->setIdUser($id_user);
                    $r_wave->setRequest($r);
                    $app['dao.wave_request']->save($r_wave);
                }

                $wave = $app['wave'];
                $data = $wave->request($r);

                return $app['twig']->render(
                    'results.html.twig',
                    array('results'=>$data)
                );
            }

            return $app['twig']->render('wave-apihelper.html.twig',
                array(
                    'requestForm' => $form->createView(),
                    'history' => $history,
                )
            );
        } else {
            return $app->redirect('/login');
        }
    }

    /**
     * Resend a Wave request.
     *
     * @param string      $id The Wave request id to resend.
     * @param Application $app The Silex Application.
     * @return string The rendered results template.
     */
    public function resendAction($id, Application $app) {
        if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $wave_request = $app['dao.wave_request']->findById($id);

            $wave = $app['wave'];
            $data = $wave->request($wave_request->getRequest());

            return $app['twig']->render(
                'results.html.twig',
                array('results'=>$data)
            );
        }
    }

    /**
     * Set Wave credentials.
     *
     * @param Request     $request The HTTP Request.
     * @param Application $app     The Silex Application.
     * @return mixed
     */
    public function setCredentialsWaveAction(Request $request, Application $app) {
        if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user = $app['security']->getToken()->getUser();

            $form = $app['form.factory']->createBuilder('form')
                ->add('waveid','text')
                ->add('wavesecret','text')
                ->add('wavelogin','text')
                ->add('wavepassword','password')
                ->getForm();

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $data = $form->getData();
                $user->setWaveid($data['waveid']);
                $user->setWavesecret($data['wavesecret']);
                $user->setWavelogin($data['wavelogin']);
                $user->setWavepassword($data['wavepassword']);
                $app['dao.user']->save($user);
                $app['session']->getFlashBag()->add('success', 'The user was succesfully updated.');
            }

            return $app['twig']->render(
                'wave-credentials-form.html.twig',
                array('waveForm' => $form->createView())
            );
        } else {
            return $app->redirect('/login');
        }
    }
}