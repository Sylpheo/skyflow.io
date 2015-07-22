<?php

namespace skyflow\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use skyflow\Domain\Event;
use ET_TriggeredSend;



class EventController {

	/**
	 * Retrieve all events
	 * @param Application $app
	 * @return mixed
	 */
    public function indexAction(Application $app){
    	if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
    		$id= $app['security']->getToken()->getUser()->getId();
	        $events = $app['dao.event']->findAllByUser($id);
	       	 return $app['twig']->render("events.html.twig",
	       	 	array('events'=> $events));
    	}else{
			return $app->redirect('/login');
		}
    }

	/**
	 * Create an event associated to triggeredSend
	 * @param Request $request
	 * @param Application $app
	 * @return form or redirect to login
	 */
    public function createEventAction(Request $request,Application $app){
    	if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
    		$iduser = $app['security']->getToken()->getUser()->getId();
	       /*	$myclient = $app['exacttarget']->login($app);

	    	$triggeredsend = new ET_TriggeredSend();
			$triggeredsend->authStub = $myclient;
			$responseTrig = $triggeredsend->get();
		  
		  	$trig =[];
		  	foreach($responseTrig->results as $t){
		  		if($t->TriggeredSendStatus != 'Deleted'){
		  			$trig[$t->CustomerKey]=$t->Name;
		  		}		
		  	}

	    	$form = $app['form.factory']->createBuilder('form')
			    		->add('event','text')
			    		->add('trigger','choice',array(
							'choices' => $trig
							))
						->getForm();

						$form->handleRequest($request);*/

			$form = $app['form.factory']->createBuilder('form')
				->add('name','text')
				->add('description','textarea')
				->getForm();
			$form->handleRequest($request);

			if($form->isSubmitted() && $form->isValid()){
				$data = $form->getData();

				$event = new Event();
				$event->setName($data['name']);
				$event->setDescription($data['name']);
				$event->setIdUsers($iduser);

				$app['dao.event']->save($event);

				return $app->redirect('/events');
			}

			return $app['twig']->render('event-form.html.twig',
				array('eventForm'=>$form->createView()));   
		}else{
				return $app->redirect('/login');
		}
    }

	/**
	 * Delete event
	 * @param $id
	 * @param Application $app
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
    public function deleteEventAction($id, Application $app){
    	
    	$app['dao.event']->delete($id);

    	return $app->redirect('/events');

    }

 }