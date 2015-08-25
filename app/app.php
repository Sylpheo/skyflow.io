<?php

/**
 * Application configuration.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

use Silex\Provider\FormServiceProvider;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\HttpFoundation\Request;

use skyflow\Service\ExactTarget;
use skyflow\Service\GenerateToken;
use skyflow\SilexOpauth\OpauthExtension;

require_once __DIR__ . '/config/dev.php';
//require_once __DIR__ . '/config.prod.php';
require_once __DIR__ . '/../app/routes.php';

// ========== Error Handlers ==========

ErrorHandler::register();
ExceptionHandler::register();

$app->error(function (\Exception $e, $code) use ($app) {
    switch ($code) {
        case 403:
            $message = 'Access denied.';
            break;
        case 404:
            $message = 'The requested resource could not be found.';
            break;
        default:
            $message = "Something went wrong.";
    }

    return $app['twig']->render('error.html.twig', array('message' => $message));
});

// ========== Silex Service Providers ==========

$app->register(new Silex\Provider\DoctrineServiceProvider());

$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/../var/logs/silex.log',
    'monolog.name' => 'silex',
    'monolog.level' => $app['monolog.level']
));

$app->register(new Silex\Provider\SessionServiceProvider());

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));

$app['twig'] = $app->share($app->extend('twig', function(Twig_Environment $twig, $app) {
    $twig->addExtension(new Twig_Extensions_Extension_Text());
    return $twig;
}));

$app->register(new Silex\Provider\TranslationServiceProvider());

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->register(new FormServiceProvider());

$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'secured' => array(
            'pattern' => '^/',
            'anonymous' => true,
            'logout' => true,
            'form' => array('login_path' => '/login', 'check_path' => '/login_check'),
            'users' => $app->share(function () use ($app) {
                return new skyflow\DAO\UsersDAO($app['db']);
            }),
        ),
    ),
));

// ========== Custom Services ==========

$app['generatetoken'] = $app->share(function($app) {
    $generate = new skyflow\Service\GenerateToken();
    return $generate;
});

// ========== DAO ==========

$app['dao.user'] = $app->share(function ($app) {
    return new skyflow\DAO\UsersDAO($app['db']);
});

$app['dao.event'] = $app->share(function ($app) {
    return new skyflow\DAO\EventDAO($app['db']);
});

$app['dao.flow'] = $app->share(function ($app) {
    return new skyflow\DAO\FlowDAO($app['db']);
});

$app['dao.mapping'] = $app->share(function ($app) {
    $mappingDAO = new skyflow\DAO\MappingDAO($app['db']);
    $mappingDAO->setEventDAO($app['dao.event']);
    $mappingDAO->setFlowDAO($app['dao.flow']);
    return $mappingDAO;
});

$app['dao.wave_request'] = $app->share(function ($app){
    return new skyflow\DAO\Wave_requestDAO($app['db']);
});

// ========== Flows ==========

$app['flow_mail_remerciements'] = $app->share(function ($app){
    return new skyflow\Flows\Flow_mail_remerciements($app);
});

// ========== Addons ==========

$app['exacttarget'] = $app->share(function($app) {
    if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
        return skyflow\Service\ExactTarget::login($app);
    }else{
        return skyflow\Service\ExactTarget::loginByApi($app);
    }
});

$app['salesforce'] = $app->share(function(){
    return new skyflow\Service\Salesforce();
});

$app['wave'] = $app->share(function($app) {
    if($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
        return skyflow\Service\Wave::login($app);
    }else{
        return skyflow\Service\Wave::loginByApi($app);
    }
});