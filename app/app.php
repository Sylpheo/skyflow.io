<?php

/**
 * Application configuration.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

use Silex\Application;

use Skyflow\Provider\SkyflowControllerProvider;
use Skyflow\Provider\SkyflowServiceProvider;

use Salesforce\Provider\SalesforceControllerProvider;
use Salesforce\Provider\SalesforceServiceProvider;

use Wave\Provider\WaveControllerProvider;
use Wave\Provider\WaveServiceProvider;

$app['debug'] = true;

/**
 * The application name when the application is run via the command-line and
 * we can't guess the name of the heroku application.
 */
$app['cli_default_application_name'] = 'cli';

/**
 * The application is in development environment if at least one of the following
 * statements, in order, is true :
 *
 * * The run command has been prefixed with "DEV=" (whatever the value of DEV).
 * * The application is run on localhost.
 *
 * To run the application in development environment from the command-line you
 * MUST prefix the run command with "DEV=".
 */
$app['dev'] = function () {
    if (getenv('DEV') !== false) {
        return true;
    } else if (!array_key_exists('SERVER_NAME', $_SERVER)) {
        return false;
    } else {
        if ($_SERVER['SERVER_NAME'] === 'localhost') {
            return true;
        } else {
            return false;
        }
    }
};

/**
 * When executed in a web environment, the application name is retrieved from the
 * the application url :
 * * "https://application-name.herokuapp.com/" => "application-name" (heroku)
 * * "http://localhost:8080/" => "localhost:8080" (local development)
 *
 * When executed in a cli environment, the application name is :
 * * "localhost:8080" if in development environment.
 * * $app['cli_default_application_name'] if we can't guess the application name.
 */
$app['application_name'] = function () use ($app) {
    if (PHP_SAPI === 'cli') {
        if ($app['dev'] === true) {
            return 'localhost:8080';
        } else {
            return $app['cli_default_application_name'];
        }
    } else {
        return explode('.', $_SERVER['SERVER_NAME'], 2)[0];
    }
};

/**
 * The server name.
 *
 * Use $app['server_name'] instead of $_SERVER['SERVER_NAME'] because using
 * $app['server_name'] allows execution of the Skyflow.io application from the
 * command-line.
 */
$app['server_name'] = function () use ($app) {
    if ($app['application_name'] === 'localhost'
        || $app['application_name'] === $app['cli_default_application_name']
    ) {
        // development machine
        return 'localhost';
    } else {
        return $app['application_name'] . '.herokuapp.com';
    }
};

/**
 * The http host name.
 *
 * Use $app['http_host'] instead of $_SERVER['HTTP_HOST'] because using
 * $app['http_host'] allows execution of the Skyflow.io application from the
 * command-line.
 */
$app['http_host'] = function () use ($app) {
    if ($app['application_name'] === 'localhost:8080'
        || $app['application_name'] === $app['cli_default_application_name']
    ) {
        // development machine
        return 'localhost:8080';
    } else {
        // heroku dyno
        return $app['application_name'] . '.herokuapp.com';
    }
};

$app['db.options'] = include __DIR__ . '/db.php';

include_once __DIR__ . '/error.php';
include_once __DIR__ . '/providers.php';

/**
 * Skyflow and addons ServiceProviders and ControllerProviders.
 */

$app->mount('/', new SkyflowControllerProvider());
$app->register(new SkyflowServiceProvider());

$app->mount('/salesforce', new SalesforceControllerProvider());
$app->register(new SalesforceServiceProvider());

$app->mount('/wave', new WaveControllerProvider());
$app->register(new WaveServiceProvider());

$app['exacttarget'] = $app->share(function ($app) {
    if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
        return Skyflow\Service\ExactTarget::login($app);
    } else {
        return Skyflow\Service\ExactTarget::loginByApi($app);
    }
});
