<?php

namespace skyflow\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use skyflow\Domain\Event;



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

			$form = $app['form.factory']->createBuilder('form')
				->add('name','text')
				->add('description','textarea')
				->getForm();
			$form->handleRequest($request);

			if($form->isSubmitted() && $form->isValid()){
				$data = $form->getData();

				$event = new Event();
				$event->setName($data['name']);
				$event->setDescription($data['description']);
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

	public function editEventAction($id,Request $request,Application $app){
		if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
			$event = $app['dao.event']->findOneById($id);


			$form = $app['form.factory']->createBuilder('form',$event)
				->add('name','text')
				->add('description','textarea')
				->getForm();
			$form->handleRequest($request);

			if($form->isSubmitted() && $form->isValid()){
				$app['dao.event']->save($event);
				return $app->redirect('/mapping');
			}

			return $app['twig']->render('event-edit.html.twig',
				array('eventForm'=>$form->createView(),
					)
		 		);
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