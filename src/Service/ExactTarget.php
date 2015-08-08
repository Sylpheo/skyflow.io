<?php
namespace skyflow\Service;

use ET_Client;
use Silex\Application;
use ET_Subscriber;
use ET_TriggeredSend;
use ET_Email;


class ExactTarget {


	/**
	 * @param Application $app
	 * @return ET_Client
	 */
	public static function login(Application $app){

		if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
			$clientid = $app['security']->getToken()->getUser()->getClientid();
			$clientsecret = $app['security']->getToken()->getUser()->getClientsecret();
        
		        $params =array(
			        'appsignature' => 'none', 
			    	'clientid' => $clientid,
			   		'clientsecret' => $clientsecret,
					'defaultwsdl' => 'https://webservice.exacttarget.com/etframework.wsdl',
			    	'xmlloc' => '../exacttarget/exact.xml',
			    	//'xmlloc' => 'https://skyflow.herokuapp.com/exacttarget/exact.xml',
				);

        	return new ET_Client(false,false,$params);
		}
	}

	/**
	 * @param $clientid
	 * @param $clientsecret
	 * @return ET_Client
	 */
	public static function loginByApi($clientid,$clientsecret){
		$params =array(
			        'appsignature' => 'none', 
			    	'clientid' => $clientid,
			   		'clientsecret' => $clientsecret,
					'defaultwsdl' => 'https://webservice.exacttarget.com/etframework.wsdl',
			    	'xmlloc' => '../exacttarget/exact.xml',
			    	//'xmlloc' => 'https://skyflow.herokuapp.com/exacttarget/exact.xml',
				);

        	return new ET_Client(false,false,$params);
	}

	public function retrieveCreateSubscriber($clientid, $clientsecret, $email){
		$myclient = $this->loginByApi($clientid, $clientsecret);
		$subscriber = new ET_Subscriber();
		$subscriber->authStub = $myclient;
		$subscriber->filter = array('Property' => 'EmailAddress', 'SimpleOperator' => 'equals', 'Value' => $email);
		$responseSub = $subscriber->get();

		/**
		 * If subscriber does not exist
		 * -> add
		 */
		if (empty($responseSub->results)) {
			$subscriber = new ET_Subscriber();
			$subscriber->authStub = $myclient;
			$subscriber->props = array(
				"EmailAddress" => $email,
				"SubscriberKey" => $email
			);

			$resultsSub = $subscriber->post();

			$subKey = $email;
		} else {
			/**
			 * If subscriber already exist
			 *  -> update
			 */
			$subKey = $responseSub->results[0]->SubscriberKey;
			$subscriber = new ET_Subscriber();
			$subscriber->authStub = $myclient;
			$subscriber->props = array("SubscriberKey" => $subKey);

			$results = $subscriber->patch();
		}

	}

	public function sendTriggeredSend($clientid, $clientsecret,$triggered,$email){
		$myclient = $this->loginByApi($clientid,$clientsecret);

		$subscriber = new ET_Subscriber();
		$subscriber->authStub = $myclient;
		$subscriber->filter = array('Property' => 'EmailAddress', 'SimpleOperator' => 'equals', 'Value' => $email);
		$responseSub = $subscriber->get();
		$subKey = $responseSub->results[0]->SubscriberKey;
			/**
		 * Retrieve TriggeredSend
		 *
		 */
		$triggeredsend = new ET_TriggeredSend();
		$triggeredsend->authStub = $myclient;
		$triggeredsend->props = array('TriggeredSendStatus','Email.ID');
		$triggeredsend->filter = array('Property' => 'CustomerKey','SimpleOperator' => 'equals','Value' => $triggered);
		$responseTrig = $triggeredsend->get();

		/**
		 * Check if triggeredSendStatus is active
		 *
		 */
		if($responseTrig->results[0]->TriggeredSendStatus != 'Active'){
			$triggeredsend = new ET_TriggeredSend();
			$triggeredsend->authStub = $myclient;
			$triggeredsend->props = array("CustomerKey" => $triggered, "TriggeredSendStatus"=> "Active");
			$resultsTrig = $triggeredsend->patch();
		}
		/**
		 * Send triggeredSend
		 *
		 */
		$triggeredsend = new ET_TriggeredSend();
		$triggeredsend->authStub = $myclient;
		$triggeredsend->props = array("CustomerKey" => $triggered);
		$triggeredsend->subscribers = array(array("EmailAddress"=>$email,"SubscriberKey" => $subKey));
		$results = $triggeredsend->send();

		return $results;

	}

	public function retrieveSubscriber($clientid,$clientsecret,$email){
		$myclient = $this->loginByApi($clientid, $clientsecret);
		$subscriber = new ET_Subscriber();
		$subscriber->authStub = $myclient;
		$subscriber->filter = array('Property' => 'EmailAddress', 'SimpleOperator' => 'equals', 'Value' => $email);
		$response = $subscriber->get();

		return $response;
	}


 }

