<?php

/**
 * Service for Salesforce OAuth authentication.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Salesforce\Service;

use skyflow\Authenticator\OAuthAuthenticatorInterface;
use skyflow\Service\OAuthService;

use Salesforce\DAO\SalesforceUserDAO;
use Salesforce\Domain\SalesforceUser;

/**
 * Service for Salesforce authentication.
 */
class SalesforceOAuthService extends OAuthService
{
    /**
     * {@inherit}
     */
    public function authenticate()
    {
        $this->getOAuth()->clientId = $this->getUser()->getClientId();
        $this->getOAuth()->clientSecret = $this->getUser()->getClientSecret();
        $this->getOAuth()->loginUrl = $this->getUser()->getIsSandbox() ? 'https://test.salesforce.com' : 'https://login.salesforce.com';
        $this->getOAuth()->authenticate();
    }

    /**
     * {@inheritdoc}
     */
    public function callback($code)
    {
        $this->getOAuth()->code = $code;
        $this->getOAuth()->callback();

        $this->getUser()->setInstanceUrl($this->getOAuth()->instance_url);
        $this->getUser()->setAccessToken($this->getOAuth()->access_token);
        $this->getUser()->setRefreshToken($this->getOAuth()->refresh_token);
        $this->getUserDAO()->save($this->getUser());
    }
}
