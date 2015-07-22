<?php

namespace skyflow\Controller;

 use Silex\Application;
 use ET_Email;
 use ET_List;
 use ET_Subscriber;
 use ET_TriggeredSend;




class ExactTargetController {

    /**
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
  public function exactTargetHelperAction(Application $app){
     if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {

      $myclient = $app['exacttarget']->login($app);

         /**
          * Retrieve all subscribers from ExactTarget
          */
      $subscriber = new ET_Subscriber();
      $subscriber->authStub = $myclient;
      $responseSub = $subscriber->get();

         $subscribers = array();
         $tout = array();

        foreach($responseSub->results as $r){
                $subscribers['ID']=$r->ID;
                $subscribers['SubscriberKey']=$r->SubscriberKey;
                $subscribers['EmailAddress']=$r->EmailAddress;
                foreach($r->Attributes as $a){
                    if($a->Name == 'FirstName'){
                        $subscribers['FirstName']= $a->Value;
                    }
                    if($a->Name == 'LastName'){
                        $subscribers['LastName']= $a->Value;
                    }
                }
                array_push($tout,$subscribers);
        }
         //var_dump($responseSub->results);
        // var_dump($tout);

      /**
       * Retrieve all lists from exactTarget
      */
      $list = new ET_List();
      $list->authStub = $myclient;
      $responseList = $list->get();

      /**
      * Retrieve all triggeredSend from ExactTarget
      */
      $triggeredsend = new ET_TriggeredSend();
      $triggeredsend->authStub = $myclient;
      $triggeredsend->props = array('Name', 'Description','CustomerKey','TriggeredSendStatus','Email.ID');
      $responseTrig = $triggeredsend->get();
      $triggers =[];
         foreach($responseTrig->results as $t){
             if($t->CustomerKey == 'testinfo_wave' || $t->CustomerKey == 'show_wave' || $t->CustomerKey == '1450' || $t->CustomerKey == 'merci_wave'){
                 array_push($triggers, $t);
             }
         }

      /**
      * Retrieve all emails from ExactTarget
      */
      $email = new ET_Email();
      $email->authStub = $myclient;
      $responseEmail = $email->get();

          return $app['twig']->render('et-apihelper.html.twig',
                array(
                  'subscribers' => $tout,
                  'lists'=>$responseList->results,
                  'triggers'=> $triggers,
                  'emails' => $responseEmail->results));
      }else{
          return $app->redirect('/login');
      }
  }

}