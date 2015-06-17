<?php

namespace exactSilex\Controller;

 use Silex\Application;
 use ET_Email;
 use ET_List;
 use ET_Subscriber;
 use ET_TriggeredSend;




class ExactTargetController {

  public function exactTargetHelperAction(Application $app){
     if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {

      $myclient = $app['exacttarget']->login($app);
      $subscriber = new ET_Subscriber();
      $subscriber->authStub = $myclient;
      $responseSub = $subscriber->get();


      $list = new ET_List();
      $list->authStub = $myclient;
      $responseList = $list->get();
            
      $triggeredsend = new ET_TriggeredSend();
      $triggeredsend->authStub = $myclient;
      $triggeredsend->props = array('Name', 'Description','CustomerKey','TriggeredSendStatus','Email.ID');
      $responseTrig = $triggeredsend->get();

      $email = new ET_Email();
      $email->authStub = $myclient;
      $responseEmail = $email->get();

          return $app['twig']->render('et-apihelper.html.twig',
                array(
                  'subscribers' => $responseSub->results,
                  'lists'=>$responseList->results,
                  'triggers'=> $responseTrig->results,
                  'emails' => $responseEmail->results));
      }else{
          return $app->redirect('/login');
      }

  }

 
  
}