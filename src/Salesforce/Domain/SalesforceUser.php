<?php

/**
 * Class for a Salesforce OAuth2 user.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Salesforce\Domain;

use Skyflow\Domain\OAuthUser;

class SalesforceUser extends OAuthUser
{
    /**
     * The Salesforce application instance url used by user.
     *
     * @var string
     */
    protected $instanceUrl;

    /**
     * Whether if the salesforce application of the user
     * is in a sandbox or a production organization.
     *
     * @var boolean
     */
    protected $isSandbox;

    /**
     * Get the Salesforce application instance_url.
     *
     * @return string The application instance_url.
     */
    public function getInstanceUrl()
    {
        return $this->instanceUrl;
    }

    /**
     * Set the Salesforce application instance_url.
     *
     * @param string $instanceUrl The application instance_url.
     */
    public function setInstanceUrl($instanceUrl)
    {
        $this->instanceUrl = $instanceUrl;
    }

    /**
     * Get if the Salesforce application is on a
     * sandbox or a production organization.
     *
     * @return boolean True if sandbox, false if production.
     */
    public function getIsSandbox()
    {
        return $this->isSandbox;
    }

    /**
     * Set if the Salesforce application is on a
     * sandbox or a production organization.
     *
     * @param boolean $isSandbox True if sandbox, false if production.
     */
    public function setIsSandbox($isSandbox)
    {
        $this->isSandbox = $isSandbox;
    }
}
