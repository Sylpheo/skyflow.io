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
 * The application is in development environment if at least one of the following
 * statements, in order, is true :
 *
 * * The run command has been prefixed with "DEV=" (whatever the value of DEV).
 * * The application is run on localhost.
 */
$app['dev'] = function () {
    if (getenv('DEV') !== false) {
        return true;
    } else if ($_SERVER['SERVER_NAME'] === 'localhost') {
        return true;
    }

    return false;
};

/**
 * The application name is retrieved from the heroku application url :
 * https://application-name.herokuapp.com/
 *
 * If the application has been run from the command-line, the application name
 * is "cli".
 */
$app['application_name'] = function () {
    if (PHP_SAPI === 'cli') {
        return 'cli';
    } else {
        return explode('.', $_SERVER['SERVER_NAME'], 2)[0];
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
