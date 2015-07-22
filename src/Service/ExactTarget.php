<?php
namespace skyflow\Service;

use ET_Client;
use Silex\Application;


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


 }

