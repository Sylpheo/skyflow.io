<?php

/**
 * Controller for ExactTarget TriggeredSend actions.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

use ET_TriggeredSend;
use ET_Email;
use ET_Subscriber;

/**
 * Controller for ExactTarget TrigerredSend actions.
 */
class TriggerController {

    /**
     * Retrieve all TrigerredSend from ExactTarget.
     *
     * @param Application $app The Silex Application.
     * @return mixed
     */
    public function triggersAction(Application $app) {
        if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $exacttarget = $app['exacttarget'];
            $myclient = $exacttarget->client;

            $triggeredsend = new ET_TriggeredSend();
            $triggeredsend->authStub = $myclient;
            $triggeredsend->props = array('Name', 'Description','CustomerKey','TriggeredSendStatus','Email.ID');

            $response = $triggeredsend->get();

            return $app['twig']->render(
                'triggers.html.twig',
                array('triggers'=> $response->results)
            );
        } else {
            return $app->redirect('/login');
        }
    }

    /**
     * Create TriggeredSend (associated with email).
     *
     * @param Request     $request The HTTP Request.
     * @param Application $app     The Silex Application.
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function createTriggerAction(Request $request, Application $app) {
        if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $exacttarget = $app['exacttarget'];
            $myclient = $exacttarget->client;

            $email = new ET_Email();
            $email->authStub = $myclient;
            $response = $email->get();

            $emails =[];
            foreach ($response->results as $e) {
                $emails[$e->ID]=$e->Name;
            }

            $form = $app['form.factory']->createBuilder('form')
                ->add('Name','text')
                ->add('Description','text')
                ->add('CustomerKey','text')
                ->add('Email','choice',array(
                        'choices' => $emails
                        ))
                ->add('SendClassification','choice',array(
                    'choices' => array('Default Transactional' => 'Default Transactional','Default Commercial' => 'Default Commercial')
                    ))

                ->getForm();

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $data = $form->getData();
                //var_dump($data);

                $triggeredsend = new ET_TriggeredSend();
                $triggeredsend->authStub = $myclient;
                $triggeredsend->props = array();
                $triggeredsend->props["Name"] = $data['Name'];
                $triggeredsend->props["Description"] = $data['Description'];
                $triggeredsend->props["Email"] = array("ID" => $data['Email']);
                $triggeredsend->props["CustomerKey"] = $data['CustomerKey'];
                $triggeredsend->props["SendClassification"] = array("CustomerKey" => $data['SendClassification']);
                $results = $triggeredsend->post();
                //print_r($results);

                if ($results->results[0]->StatusCode == 'OK') {
                        return $app->redirect('/et-helper');
                }
            }

            return $app['twig']->render(
                'trigger-form.html.twig',
                array('triggerForm' => $form->createView())
            );
        } else {
            return $app->redirect('/login');
        }
    }

    /**
     * Send a TriggeredSend to a subscriber.
     *
     * @param Request     $request The HTTP Request.
     * @param Application $app     The Silex Application.
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function sendTriggeredSendAction(Request $request, Application $app){
        if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $exacttarget = $app['exacttarget'];
            $myclient = $exacttarget->client;

            $subscriber = new ET_Subscriber();
            $subscriber->authStub = $myclient;
            $response = $subscriber->get();

            $sub = [];
            foreach($response->results as $s){
                $sub[$s->EmailAddress]=$s->EmailAddress;
            }

            $triggeredsend = new ET_TriggeredSend();
            $triggeredsend->authStub = $myclient;
            $responseTrig = $triggeredsend->get();

            $trig =[];
            foreach($responseTrig->results as $t){
                if ($t->TriggeredSendStatus != 'Deleted') {
                    $trig[$t->CustomerKey]=$t->Name;
                }
            }

            $form = $app['form.factory']->createBuilder('form')
                ->add('Subscriber','choice',array(
                    'choices' => $sub
                ))
                ->add('TriggeredSend','choice',array(
                    'choices' => $trig
                ))
                ->getForm();

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {

                $data = $form->getData();

                $triggeredsend = new ET_TriggeredSend();
                $triggeredsend->authStub = $myclient;
                $triggeredsend->props = array('TriggeredSendStatus');
                $triggeredsend->filter = array('Property' => 'CustomerKey','SimpleOperator' => 'equals','Value' => $data['TriggeredSend']);
                $response = $triggeredsend->get();

                if($response->results[0]->TriggeredSendStatus != 'Active'){
                    //Set triggeredSendStatus -> Active
                    $triggeredsend = new ET_TriggeredSend();
                    $triggeredsend->authStub = $myclient;
                    $triggeredsend->props = array("CustomerKey" => $data['TriggeredSend'], "TriggeredSendStatus"=> "Active");
                    $results = $triggeredsend->patch();
                }

                //Retrieve Subscriber
                $subscriber = new ET_Subscriber();
                $subscriber->authStub = $myclient;
                $subscriber->props=array('SubscriberKey');
                $subscriber->filter=array('Property'=>'EmailAddress','SimpleOperator'=>'equals','Value'=>$data['Subscriber']);
                $responseSub = $subscriber->get();

                $subKey=$responseSub->results[0]->SubscriberKey;

                //Send !
                $triggeredsend = new ET_TriggeredSend();
                $triggeredsend->authStub = $myclient;
                $triggeredsend->props = array("CustomerKey" => $data['TriggeredSend']);
                $triggeredsend->subscribers = array(array("EmailAddress"=>$data['Subscriber'],"SubscriberKey" => $subKey));
                $results = $triggeredsend->send();

                if ($results->results[0]->StatusCode == 'OK') {
                        return $app->redirect('/et-helper');
                }
            }

            return $app['twig']->render(
                'send-trigger.html.twig',
                array('sendForm'=>$form->createView())
            );
         } else {
            return $app->redirect('/login');
        }
    }

    /**
     * Retrieve TriggeredSend's info.
     *
     * @param string      $customerKey TODO: put a description
     * @param Application $app         The Silex Application
     * @return mixed
     */
    public function infoTriggeredSendAction($customerKey, Application $app) {
        if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $exacttarget = $app['exacttarget'];
            $myclient = $exacttarget->client;

            $triggeredsend = new ET_TriggeredSend();
            $triggeredsend->authStub = $myclient;
            $triggeredsend->filter = array('Property' => 'CustomerKey','SimpleOperator' => 'equals','Value' => $customerKey);
            $response = $triggeredsend->get();

            return $app['twig']->render(
                'trigger.html.twig',
                array('trigger' => $response->results[0])
            );
        } else {
            return $app->redirect('/login');
        }
    }
}