<?php
namespace exactSilex\Controller;

 use Silex\Application;
 use Symfony\Component\HttpFoundation\Request;
 use ET_Email;
 use ET_Folder;

 class EmailController {

 	public function emailsAction(Application $app){
 		if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
 		
	 		$myclient = $app['exacttarget']->login($app);
			$email = new ET_Email();
			$email->authStub = $myclient;
			$response = $email->get();

        	return $app['twig']->render('emails.html.twig',
        		array('emails' => $response->results));
        }else{
        	return $app->redirect('/login');
        }
    }

    public function createEmailAction(Request $request, Application $app){

    	if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {

	 		$myclient = $app['exacttarget']->login($app);
	    	// Get folder lists
	    	$folder = new ET_Folder();
			$folder->authStub = $myclient;
			$folder->props = array('Name', 'ID');
			$folder->filter = array('Property' => 'ContentType','SimpleOperator' => 'equals','Value' => 'Email');
			$response = $folder->get();
			
			$folders =[];

				foreach ($response->results as $f) {
					$folders[$f->ID]=$f->Name;
				}

					$form = $app['form.factory']->createBuilder('form')
			    		->add('Name','text')
			    		->add('Category','choice',array(
							'choices' => $folders
							))
			    		->add('Description','textarea')
			    		->add('HTMLBody','textarea',array('attr' => array('class' => 'ckeditor')))
			    		->add('Subject','text')
			    		->add('CustomerKey','text')
						->getForm();

						$form->handleRequest($request);

						if($form->isSubmitted() && $form->isValid()){
							$data = $form->getData();

							$email = new ET_Email();
							$email->authStub = $myclient;
							$email->props = array(
								 "CustomerKey" => $data['CustomerKey'],
								 "Name"=> $data['Name'],
								 "Subject"=> $data['Subject'],
								 "HTMLBody"=> $data['HTMLBody'], 
								 "EmailType" => "HTML", "IsHTMLPaste" => "true"
								 );
							$results = $email->post();
					
			                	if ($results->results[0]->StatusCode == 'OK') {
									return $app->redirect('/emails');
								}
						}
						return $app['twig']->render('email-form.html.twig',
							array('emailForm' => $form->createView() ));
		}else{
			return $app->redirect('/login');
		}
    }

    public function deleteEmailAction($id,Request $request,Application $app){
    	
    	if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
	 		$myclient = $app['exacttarget']->login($app);
			
			$email = new ET_Email();
			$email->authStub = $myclient;
			$email->props = array("ID" => $id);
			$results = $email->delete();

	        	return $app->redirect('/emails');
	    }else{
	    		return $app->redirect('/login');
	    }
    }

    public function infoEmailAction($id, Application $app){
    	if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
	 		$myclient = $app['exacttarget']->login($app);

			$email = new ET_Email();
			$email->authStub = $myclient;
			$email->filter = array('Property' => 'ID','SimpleOperator' => 'equals','Value' => $id);
			$response = $email->get();

			//var_dump($response->results);

				return $app['twig']->render('email.html.twig',
					array('email'=> $response->results[0]));
		}else{
				return $app->redirect('/login');
		}

    }

 }
 
