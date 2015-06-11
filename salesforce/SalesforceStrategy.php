<?php

/**
 * Salesforce strategy for Opauth
 *
 * @copyright    Copyright © 2014, Better Brief LLP
 * @license      BSD License
 */
class SalesforceStrategy extends OpauthStrategy {

	/**
	 * Compulsory config keys, listed as unassociative arrays
	 */
	public $expects = array(
		'client_id',
		'client_secret',
	);

	/**
	 * Optional config keys, without predefining any default values.
	 */
	public $optionals = array(
		'redirect_uri',
		'scope',
		'state',
		'display',
		'immediate',
		'response_type',
	);

	/**
	 * Optional config keys with respective default values, listed as associative arrays
	 * eg. array('scope' => 'email');
	 */
	public $defaults = array(
		'redirect_uri' => '{complete_url_to_strategy}oauth2callback',
	);

	/**
	 * Auth request
	 */
	public function request() {
		$url = 'https://login.salesforce.com/services/oauth2/authorize';
		$params = array(
			'response_type' => 'code',
			'client_id' => $this->strategy['client_id'],
			'redirect_uri' => $this->strategy['redirect_uri'],
		);

		foreach ($this->optionals as $key) {
			if (!empty($this->strategy[$key])) $params[$key] = $this->strategy[$key];
		}

		$this->clientGet($url, $params);
	}

	/**
	 * Internal callback, after OAuth
	 */
	public function oauth2callback() {
		if (array_key_exists('code', $_GET) && !empty($_GET['code'])) {
			$code = $_GET['code'];
			$url = 'https://login.salesforce.com/services/oauth2/token';

			$params = array(
				'grant_type' => 'authorization_code',
				'code' => $code,
				'client_id' => $this->strategy['client_id'],
				'client_secret' => $this->strategy['client_secret'],
				'redirect_uri' => $this->strategy['redirect_uri'],
				'format' => 'json',
			);
			if (!empty($this->strategy['state'])) $params['state'] = $this->strategy['state'];

			$response = $this->serverPost($url, $params, null, $headers);
			$results = json_decode($response, true);

			if (!empty($results) && !empty($results['access_token'])) {

				$this->auth = array(
					'uid' => $results['id'],
					'info' => array(),
					'credentials' => array(
						'token' => $results['access_token']
					),
					'raw' => $results,
				);

				$this->callback();
			}
			else {
				$error = array(
					'code' => 'access_token_error',
					'message' => 'Failed when attempting to obtain access token',
					'raw' => array(
						'response' => $response,
						'headers' => $headers
					)
				);

				$this->errorCallback($error);
			}
		}
		else {
			$error = array(
				'code' => 'oauth2callback_error',
				'raw' => $_GET
			);

			$this->errorCallback($error);
		}
	}

}
