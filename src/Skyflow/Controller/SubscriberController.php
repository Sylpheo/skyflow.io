<?php

/**
 * Controller for ExactTarget Subscriber actions.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

use ET_Subscriber;

/**
 * Controller for ExactTarget Subscriber actions.
 */
class SubscriberController{

    /**
     * Retrieve all subscribers from ExactTarget.
     *
     * @param Application $app The Silex Application.
     * @return mixed
     */
    public function subscribersAction(Application $app) {
        if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $exacttarget = $app['exacttarget'];
            $myclient = $exacttarget->client;

            $subscriber = new ET_Subscriber();
            $subscriber->authStub = $myclient;
            $response = $subscriber->get();

            return $app['twig']->render(
                'subscribers.html.twig',
                array('subscribers' => $response->results)
            );
        } else {
           return $app->redirect('/login');
       }
    }

    /**
     * Add Subscriber to ExactTarget.
     *
     * @param Request     $request The HTTP Request.
     * @param Application $app     The Silex Application.
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addSubscriberAction(Request $request, Application $app) {
        if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $exacttarget = $app['exacttarget'];
            $myclient = $exacttarget->client;

            $form = $app['form.factory']->createBuilder('form')
                ->add('EmailAddress','email')
                ->add('SubscriberKey','text')
                ->add('Status','choice',array(
                        'choices' => array('Active' => 'Active', 'Held' => 'Held','Unsubscribe' =>'Unsubscribe')
                        ))
                ->getForm();

            $form->handleRequest($request);

            if($form->isSubmitted()){
                $data = $form->getData();

                $subscriber = new ET_Subscriber();
                $subscriber->authStub = $myclient;
                $subscriber->props = array(
                    "EmailAddress" => $data['EmailAddress'],
                    "SubscriberKey" => $data['SubscriberKey'],
                    "Status" => $data['Status']
                    );
                $results = $subscriber->post();

                if ($results->results[0]->StatusCode == 'OK') {
                    return $app->redirect('/et-helper');
                }
            }

            return $app['twig']->render(
                'subscriber-form.html.twig',
                array('subForm' => $form->createView())
            );
        } else {
            return $app->redirect('/login');
        }
    }

    /**
     * Delete Subscriber from ExactTarget.
     *
     * @param string      $id  The Subscriber id.
     * @param Application $app The Silex Application.
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteSubscriberAction($id, Application $app) {
        if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $exacttarget = $app['exacttarget'];
            $myclient = $exacttarget->client;

            $subscriber1 = new ET_Subscriber();
            $subscriber1->authStub = $myclient;
             $subscriber1->props = array("ID" => $id);
            $results = $subscriber1->delete();

            return $app->redirect('/et-helper');
        } else {
            return $app->redirect('/login');
        }
     }
}