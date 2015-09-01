<?php

/**
 * Controller provider for the Salesforce addon.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Salesforce\Provider;

use Silex\Application;
use Silex\ControllerProviderInterface;

class SalesforceControllerProvider implements ControllerProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->match('/auth', 'salesforce.controller.user:credentialsAction')->bind('salesforce-credentials');
        
        $controllers->get('/auth/authenticate', 'salesforce.controller.oauth:authenticateAction')->bind('salesforce-authenticate');
        $controllers->get('/auth/callback', 'salesforce.controller.oauth:callbackAction')->bind('salesforce-callback');

        $controllers->match('/helper', 'salesforce.controller.helper:queryAction')->bind('salesforce-helper');

        return $controllers;
    }
}
