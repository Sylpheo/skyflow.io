<?php

namespace skyflow\Service;

use Silex\Application;
use ET_Client;
use ET_Subscriber;
use ET_TriggeredSend;
use Symfony\Component\Config\Definition\Exception\Exception;


/**
 * Class ExactTarget
 *
 * ExactTarget service. Provide login/management methods over the ET_Client.
 *
 * @package skyflow\Service
 */
class ExactTarget  {

	/**
	 * @var Instance of Application
	 */
	public $app;

	/**
	 * @var Instance of ET_Client
	 */
	public $client;

	/**
	 * ExactTarget constructor.
	 *
	 * Throws an Exception if $client is invalid, to be sure the ExactTarget object is logged in to ExactTarget.
	 *
	 * @param $app    Instance of Application
	 * @param $client Instance of ET_Client
	 */
	public function __construct($app, $client){

		if (!($client instanceof ET_Client)) {
			throw new Exception('$client must be an instance of ET_Client. Please use login static method to instanciate an ExactTarget object');
		}

		$this->app = $app;
		$this->client = $client;
	}

	/**
	 * Login to ExactTarget using authenticated user's clientId and clientSecret.
	 *
	 * This login method is called after successful login on skyflow web login page.
	 *
	 * @param $app Instance of Application
	 * @return Instance of ExactTarget
	 */
	public static function login(Application $app){

		if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
			$clientid = $app['security']->getToken()->getUser()->getClientid();
			$clientsecret = $app['security']->getToken()->getUser()->getClientsecret();

			if($clientid == null || $clientsecret == null){
				throw new Exception('clientid or clientsecret is null: must be provided in database before using application');
			}
			$params =array(
				'appsignature' => 'none',
				'clientid' => $clientid,
				'clientsecret' => $clientsecret,
				'defaultwsdl' => 'https://webservice.exacttarget.com/etframework.wsdl',
				'xmlloc' => __DIR__ . '/../../app/wsdl/ExactTargetWSDL.xml',
				//'xmlloc' => 'https://skyflow.herokuapp.com/exacttarget/exact.xml',
			);

			return new ExactTarget($app, new ET_Client(false,false,$params));
		}else{
			throw new Exception('User is not fully authenticated');
		}
	}

	/**
	 *
	 * Request json
	 * @return Instance of ExactTarget
	 */
	public static function loginByApi(Application $app){
		if ($app['request']->headers->has('Skyflow-Token')) {
			$token = $app['request']->headers->get('Skyflow-Token');

			$user = $app['dao.user']->findByToken($token);

			if (empty($user)) {
				return $app->json('No user matching');
			}

			$clientid = $user->getClientid();
			$clientsecret = $user->getClientsecret();

			if($clientid == null || $clientsecret == null){
				throw new Exception('clientid or clientsecret is null: must be provided in database before using application');
			}

			$params = array(
				'appsignature' => 'none',
				'clientid' => $clientid,
				'clientsecret' => $clientsecret,
				'defaultwsdl' => 'https://webservice.exacttarget.com/etframework.wsdl',
				'xmlloc' => __DIR__ . '/../../app/wsdl/ExactTargetWSDL.xml',
				//'xmlloc' => 'https://skyflow.herokuapp.com/exacttarget/exact.xml',
			);

			return new ExactTarget($app, new ET_Client(false,false,$params));
		}else{
			throw new Exception('Missing Skyflow-Token');
		}
	}

	public function retrieveCreateSubscriber(){

		if($this->app['request']->request->has('email')){
			$email = $this->app['request']->request->get('email');
		}

		$subscriber = new ET_Subscriber();
		$subscriber->authStub = $this->client;
		$subscriber->filter = array('Property' => 'EmailAddress', 'SimpleOperator' => 'equals', 'Value' => $email);
		$responseSub = $subscriber->get();

		/**
		 * If subscriber does not exist
		 * -> add
		 */
		if (empty($responseSub->results)) {
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
			$subscriber->props = array("SubscriberKey" => $subKey);

			$results = $subscriber->patch();
		}
	}

	public function sendTriggeredSend($trigger){
		if($this->app['request']->request->has('email')){
			$email = $this->app['request']->request->get('email');
		}
		$subscriber = new ET_Subscriber();
		$subscriber->authStub = $this->client;
		$subscriber->filter = array('Property' => 'EmailAddress', 'SimpleOperator' => 'equals', 'Value' => $email);
		$responseSub = $subscriber->get();
		$subKey = $responseSub->results[0]->SubscriberKey;
		/**
		 * Retrieve TriggeredSend
		 */
		$triggeredsend = new ET_TriggeredSend();
		$triggeredsend->authStub = $this->client;
		$triggeredsend->props = array('TriggeredSendStatus', 'Email.ID');
		$triggeredsend->filter = array('Property' => 'CustomerKey', 'SimpleOperator' => 'equals', 'Value' => $trigger);
		$responseTrig = $triggeredsend->get();

		/**
		 * Check if triggeredSendStatus is active
		 */
		if ($responseTrig->results[0]->TriggeredSendStatus != 'Active') {
			$triggeredsend->props = array("CustomerKey" => $trigger, "TriggeredSendStatus" => "Active");
			$resultsTrig = $triggeredsend->patch();
		}
		/**
		 * Send triggeredSend
		 */
		$triggeredsend->props = array("CustomerKey" => $trigger);
		$triggeredsend->subscribers = array(array("EmailAddress" => $email, "SubscriberKey" => $subKey));
		$results = $triggeredsend->send();

		return $results;
	}


	public function retrieveSubscriber(){

		if ($this->app['request']->request->has('email')) {
			$email = $this->app['request']->request->get('email');
		}
		$subscriber = new ET_Subscriber();
		$subscriber->authStub = $this->client;
		$subscriber->filter = array('Property' => 'EmailAddress', 'SimpleOperator' => 'equals', 'Value' => $email);
		$response = $subscriber->get();

		if(empty($response->results)){
			return null;
		}else{
			return $response->results;
		}

	}

	/**
	 * Upsert current subscriber.
	 *
	 * Current subscriber is retrieved using method retrieveSubscriber.
	 *
	 * @param $props      Subscriber props as associative array
	 * @param $attributes Subscriber attributes as associative array
	 */
	public function upsertSubscriber($props, $attributes) {

		$subscriber = $this->retrieveSubscriber();

		if ($subscriber === null) {
			/*if ($props['SubscriberKey'] === null) {
				throw new Exception('Missing SubscriberKey')
			}*/
			$subscriber = new ET_Subscriber();
			$insert = true;
		} else {
			$insert = false;
		}
		$subscriber = new ET_Subscriber();
		$subscriber->props = $props;
		$subscriber->authStub = $this->client;

		$attr = array();
		foreach ($attributes as $key => $value) {
			array_push($attr, array('Name' => $key, 'Value' => $value));
		}

		$subscriber->props['Attributes'] = $attr;

		if ($insert === true) {
			$result =$subscriber->post();
		}else{
			$result =$subscriber->patch();
		}

		return $result;
	}

 }