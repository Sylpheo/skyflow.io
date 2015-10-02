<?php

/**
 * Controller for the Skyflow API.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller for the Skyflow API.
 */
class ApiController
{
    /**
     * Handle execution of a flow from a provided flow name.
     *
     * The flow name is provided as a HTTP Header. The request must be a HTTP
     * GET request that has no parameters because the associated flow method
     * run does not handle parameters.
     *
     * @param Request     $request The request.
     * @param Application $app     The Silex application.
     * @return mixed The result from the run method.
     */
    public function flowAction(Request $request, Application $app)
    {
        if (isset($app['flow'])) {
            $result = $app['flow']->run();

            return $app->json($result);
        }
    }

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
    public function eventAction($event, Request $request, Application $app)
    {
        if (isset($app['flow'])) {
            $result = $app['flow']->event($request);

            return $app->json($result);
        }
    }
}
