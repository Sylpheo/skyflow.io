<?php

/**
 * Controller for the Skyflow API.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller for the Skyflow API.
 */
class ApiController
{
    /**
     * Handle execution of a flow from a provided event name.
     *
     * The event name is provided as a URL parameter. The request
     * must be a HTTP POST request that may contain parameters provided
     * in JSON format in the HTTP request content.
     *
     * @param string      $event   The Event name.
     * @param Request     $request The JSON request.
     * @param Application $app     The Silex application.
     * @return mixed
     */
    public function flowAction($event, Request $request, Application $app)
    {
        if (isset($app['flow'])) {
            $result = $app['flow']->event($request);

            return $app->json($result);
        }
    }
}
