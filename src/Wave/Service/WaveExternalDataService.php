<?php

/**
 * External Data Service for Wave.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Wave\Service;

use GuzzleHttp\ClientInterface as HttpClientInterface;

use skyflow\Domain\OAuthUser;
use skyflow\Service\OAuthServiceInterface;
use skyflow\Service\RestOAuthAuthenticatedService;

use Salesforce\Domain\SalesforceUser;

/**
 * External Data Service for Wave.
 */
class WaveExternalDataService extends RestOAuthAuthenticatedService
{
}
