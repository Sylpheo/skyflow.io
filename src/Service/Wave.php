<?php
namespace skyflow\Service;


use Silex\Application;


class Wave {


    /**
     * @param $client_id
     * @param $client_secret
     * @param $username
     * @param $password
     * @return string
    */
	public static function login($client_id,$client_secret,$username,$password){

		//$loginurl = "https://gs0.salesforce.com/services/oauth2/token";
        $params = "grant_type=password"
        . "&client_id=".$client_id
        . "&client_secret=".$client_secret
        . "&username=".$username
        . "&password=".$password;

		return $params;
	}


 }

