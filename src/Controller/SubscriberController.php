<?php
namespace exactSilex\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use ET_Subscriber;

class SubscriberController{

	public function subscribersAction(Application $app){
		if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {

	 		$myclient = $app['exacttarget']->login($app);
	 		$subscriber = new ET_Subscriber();
			$subscriber->authStub = $myclient;
			$response = $subscriber->get();

       		return $app['twig']->render('subscribers.html.twig',
            		array('subscribers' => $response->results));
       	}else{
       		return $app->redirect('/login');
       	}
    }


    public function addSubscriberAction(Request $request, Application $app){
    	if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {

	 		$myclient = $app['exacttarget']->login($app);

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
	    	return $app['twig']->render('subscriber-form.html.twig',
	    		array('subForm' => $form->createView()));
	    }else{
	    	return $app->redirect('/login');
	    }
    	
    }

     public function deleteSubscriberAction($id, Application $app) {

    	if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
	 		$myclient = $app['exacttarget']->login($app);
			
			$subscriber1 = new ET_Subscriber();
			$subscriber1->authStub = $myclient;
	 		$subscriber1->props = array("ID" => $id);
			$results = $subscriber1->delete();
		
	     		return $app->redirect('/et-helper');
	    }else{
	    	return $app->redirect('/login');
	    }
     }
}
