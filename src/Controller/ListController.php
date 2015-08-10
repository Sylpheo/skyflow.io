<?php
namespace skyflow\Controller;

 use Silex\Application;
 use Symfony\Component\HttpFoundation\Request;
 use ET_List;
 use ET_Folder;
 use ET_Subscriber;
 use ET_List_Subscriber;

 class ListController {

	 /**
	  * Retrieve all lists from exactTarget
	  * @param Application $app
	  * @return lists or redirect to login
	  */
 	public function listsAction(Application $app){
 		if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {

			$exacttarget = $app['exacttarget'];
			$myclient = $exacttarget->client;

			$list = new ET_List();
			$list->authStub = $myclient;
			$response = $list->get();
			    	
				return $app['twig']->render('lists.html.twig',
					array('lists'=>$response->results));
		}else{
				return $app->redirect('/login');
		}
    }

	 /**
	  * Retrieve all susbcribers lists rom exactTarget
	  * @param Application $app
	  * @return Lists subscribers or redirect to login
	  */
    public function listSubscriberAction(Application $app){
    	if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {

			$exacttarget = $app['exacttarget'];
			$myclient = $exacttarget->client;
	    	
			$listsubscriber = new ET_List_Subscriber();
			$listsubscriber->authStub = $myclient;
			$response = $listsubscriber->get();
			
			return $app['twig']->render('list-sub.html.twig',
				array('lists' => $response->results));
		}else{
				return $app->redirect('/login');
		}
		
    }

	 /**
	  * Add list to exactTarget
	  * @param Request $request
	  * @param Application $app
	  * @return \Symfony\Component\HttpFoundation\RedirectResponse
	  */
     public function addListAction(Request $request, Application $app){
     	if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {

			$exacttarget = $app['exacttarget'];
			$myclient = $exacttarget->client;

	    	// Get folder lists
	    	$folder = new ET_Folder();
			$folder->authStub = $myclient;
			$folder->props = array('Name', 'ID');
			$folder->filter = array('Property' => 'ContentType','SimpleOperator' => 'equals','Value' => 'List');
			$response = $folder->get();
			
			$folders = [];

			foreach ($response->results as $f) {
				$folders[$f->ID]=$f->Name;
			}
		

	    	$form = $app['form.factory']->createBuilder('form')
	    		->add('ListName','text')
				->add('Description','textarea')
				->add('Type','choice',array(
						'choices' => array('Private' => 'Private', 'Public' => 'Public')
						))
				->add('CustomerKey','text')
				->add('Category','choice',array(
					'choices' => $folders
					))
				->getForm();

			$form->handleRequest($request);

			if($form->isSubmitted() && $form->isValid()){
				$data = $form->getData();
				//var_dump($data);

				$list = new ET_List();
				$list->authStub = $myclient;
				$list->props = array(
					"ListName" => $data['ListName'],
					"Description" => $data['Description'], 
					"CustomerKey" => $data['CustomerKey'], 
					"Category" => $data['Category']
					);
				$results = $list->post();

			        if ($results->results[0]->StatusCode == 'OK') {
						return $app->redirect('/et-helper');
			    	}
			}
				return $app['twig']->render('list-form.html.twig',
					array('listForm' => $form->createView()));
		}else{
				return $app->redirect('/login');
		}
	}

	 /**
	  * Delete list from ExactTarget
	  * @param $id
	  * @param Application $app
	  * @return \Symfony\Component\HttpFoundation\RedirectResponse
	  */
	public function deleteListAction ($id, Application $app){
		
		if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {

			$exacttarget = $app['exacttarget'];
			$myclient = $exacttarget->client;

	 		$list = new ET_List();
			$list->authStub = $myclient;
			$list->props = array("ID" => $id);
			$results = $list->delete();

				return $app->redirect('/et-helper');
		}else{
				return $app->redirect('/login');
		}
	}

	 /**
	  * Add subscriber to list ExactTarget
	  * @param Request $request
	  * @param Application $app
	  * @return \Symfony\Component\HttpFoundation\RedirectResponse
	  */
	public function addSubToListAction(Request $request, Application $app){
		if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
			$exacttarget = $app['exacttarget'];
			$myclient = $exacttarget->client;

	    	//All Subscribers
			$subscriber = new ET_Subscriber();
			$subscriber->authStub = $myclient;
			$subscriber->props = array('EmailAddress', 'SubscriberKey');
			$responseSub = $subscriber->get();

			//All Lists
			$list = new ET_List();
			$list->authStub = $myclient;
			$list->props = array('ID','ListName');
			$response = $list->get();

			$subs = [];
				foreach ($responseSub->results as $s) {
					$subs[$s->SubscriberKey]=$s->EmailAddress;
				}

			$lists = [];
				foreach ($response->results as $l) {
					$lists[$l->ID]=$l->ListName;
				}

					$form = $app['form.factory']->createBuilder('form')
						->add('Subscriber','choice',array(
								'choices' =>$subs
								))
						 ->add('Lists', 'choice', array(
				            'choices' => $lists,
				            'expanded' => true,
				            'multiple' => true,
				        	))
						->getForm();

						$form->handleRequest($request);

							if($form->isSubmitted()){
								$data = $form->getData();

								$subscriber = new ET_Subscriber();
								$subscriber->authStub = $myclient;
								$subscriber->props = array('EmailAddress', 'SubscriberKey');
								//Filtrer les résultats
								$subscriber->filter = array('Property' => 'SubscriberKey','SimpleOperator' => 'equals','Value' => $data['Subscriber']);
								$response = $subscriber->get();

								$tab=$response->results;
								$email = $tab[0]->EmailAddress;

								$add = $myclient->AddSubscriberToList($email,$data['Lists'],$data['Subscriber']);

								  if ($add->results[0]->StatusCode == 'OK') {
									return $app->redirect('/lists');
								  }
							}
						return $app['twig']->render('addSubToList.html.twig',
							array('form' => $form->createView()));
		}else{
			return $app->redirect('/login');
		}
	}
}
