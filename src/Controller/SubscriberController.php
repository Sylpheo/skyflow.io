<?php
namespace skyflow\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use ET_Subscriber;

class SubscriberController{

	/**
	 * Retrieve all subscribers from ExactTarget
	 * @param Application $app
	 * @return subscribers or redirect to login
	 */
	public function subscribersAction(Application $app){
		if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {

			$exacttarget = $app['exacttarget'];
			$myclient = $exacttarget->client;

	 		$subscriber = new ET_Subscriber();
			$subscriber->authStub = $myclient;
			$response = $subscriber->get();

       		return $app['twig']->render('subscribers.html.twig',
            		array('subscribers' => $response->results));
       	}else{
       		return $app->redirect('/login');
       	}
    }


	/**
	 * Add subscriber to ExactTarget
	 * @param Request $request
	 * @param Application $app
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
    public function addSubscriberAction(Request $request, Application $app){
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
	    	return $app['twig']->render('subscriber-form.html.twig',
	    		array('subForm' => $form->createView()));
	    }else{
	    	return $app->redirect('/login');
	    }
    	
    }

	/**
	 * Delete subscriber from ExactTarget
	 * @param $id
	 * @param Application $app
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
	    }else{
	    	return $app->redirect('/login');
	    }
     }
}
