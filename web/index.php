<?php

/**
 * Application entry-point.
 */

use Symfony\Component\HttpFoundation\Request;

date_default_timezone_set('UTC');

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();
require __DIR__ . '/../app/app.php';

if (PHP_SAPI === 'cli'
    && defined('CLI_FLOW_NAME')
    && defined('CLI_SKYFLOW_TOKEN')
) {
    // CLI_FLOW_NAME and CLI_SKYFLOW_TOKEN defined in bin/flow.php
    // for flow execution from command-line
    $request = Request::create('/api/flow', 'GET');

    $request->headers->set('Flow-Name', CLI_FLOW_NAME);
    $request->headers->set('Skyflow-Token', CLI_SKYFLOW_TOKEN);

    $app->run($request);
} else {
    $app->run();
}
