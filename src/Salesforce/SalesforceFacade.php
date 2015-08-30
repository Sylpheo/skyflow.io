<?php

/**
 * Facade object for Salesforce.
 *
 * Provide a unified interface to Salesforce.
 */

namespace Salesforce\SalesforceFacade;

use Salesforce\Service\SalesforceAuthService;
use Salesforce\Service\SalesforceDataService;

class SalesforceFacade
{
    /**
     * The Salesforce Auth service.
     *
     * @var SalesforceAuthService
     */
    protected $auth;

    /**
     * The Salesforce Data service.
     *
     * @var SalesforceDataService
     */
    protected $data;

    /**
     * The SalesforceFacade constructor.
     *
     * @param SalesforceAuthService $auth The Salesforce Auth service.
     * @param SalesforceDataService $data The Salesforce Data service.
     */
    public function __construct(
        SalesforceAuthService $auth,
        SalesforceDataService $data
    ) {
        $this->auth = $auth;
        $this->data = $data;
    }
}
