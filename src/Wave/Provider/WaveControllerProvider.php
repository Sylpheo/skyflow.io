<?php

/**
 * Controller provider for the Wave addon.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Wave\Provider;

use Silex\Application;
use Silex\ControllerProviderInterface;

class WaveControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->match('/auth', 'wave.controller.auth:credentialsAction')->bind('wave-credentials');
        
        $controllers->get('/auth/authenticate', 'wave.controller.auth:authenticateAction')->bind('wave-authenticate');
        $controllers->get('/auth/callback', 'wave.controller.auth:callbackAction')->bind('wave-callback');

        $controllers->match('/helper', 'wave.controller.helper:requestAction')->bind('wave-helper');
        $controllers->get('/helper/resend/{id}', 'wave.controller.helper:resendAction')->bind('/resend/{id}');

        return $controllers;
    }
}
