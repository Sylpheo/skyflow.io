<?php

/**
 * Application configuration.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

use Silex\Application;
use Silex\Provider\FormServiceProvider;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Skyflow\DAO\SkyflowUserDAO;
use Skyflow\Security\AesEncryption;
use Skyflow\Service\ExactTarget;
use Skyflow\Service\GenerateToken;
use Skyflow\SilexOpauth\OpauthExtension;

use Salesforce\Provider\SalesforceServiceProvider;
use Salesforce\Provider\SalesforceControllerProvider;

use Wave\Provider\WaveServiceProvider;
use Wave\Provider\WaveControllerProvider;

$app['debug'] = true;
$app['dev'] = $_SERVER['SERVER_NAME'] === 'localhost' ? true : false;
$app['application_name'] = explode('.', $_SERVER['SERVER_NAME'], 2)[0];

require_once __DIR__ . '/routes.php';

$app['db.options'] = include __DIR__ . '/db.php';

// ========== Skyflow Security ==========

$app['skyflow.security.encryption_key'] = 'bcb04b7e103a0cd8b54763051cef08bc55abe029fdebae5e1d417e2ffb2a00a3';

$app['skyflow.security.encryption'] = $app->share(function () use ($app) {
    return new AesEncryption($app['skyflow.security.encryption_key']);
});

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

    return $app['twig']->render('error.html.twig', array('exception' => $e));
});

// ========== Silex Service Providers ==========

$app->register(new Silex\Provider\DoctrineServiceProvider());

$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => (
        $app['dev'] ? __DIR__ . '/../var/logs/silex.log' : 'php://stderr'
    ),
    'monolog.name' => 'silex',
    'monolog.level' => \Monolog\Logger::WARNING
));

$app->register(new Silex\Provider\ServiceControllerServiceProvider());

$app->register(new Silex\Provider\SessionServiceProvider());

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));

$app['twig'] = $app->share($app->extend('twig', function (Twig_Environment $twig, $app) {
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
            'form' => array(
                'login_path' => '/login',
                'check_path' => '/login_check'
            ),
            'users' => $app->share(function () use ($app) {
                return $app['dao.user'];
            }),
        ),
    ),
));

// ========== Addons ==========

$app->before(function (Request $request, Application $app) {
    if ($request->get('_route') === 'flow') {
        $eventName = $request
            ->attributes
            ->get('_route_params')['event'];

        if ($eventName !== '') {
            $userId = $app['user']->getId();
            $event = $app['dao.event']->findOne($eventName, $userId);

            if (isset($event)) {
                $mapping = $app['dao.mapping']->findByEventUser($event->getId(), $userId);

                if (isset($mapping)) {
                    $flow = $mapping->getFlow();
                    $class = $flow->getClass();

                    $app['flow'] = new $class();
                }
            }
        }
    }
});

$app->register(new SalesforceServiceProvider());
$app->mount('/salesforce', new SalesforceControllerProvider());

$app->register(new WaveServiceProvider());
$app->mount('/wave', new WaveControllerProvider());

$app['http.client'] = $app->share(function ($app) {
    return new \GuzzleHttp\Client();
});

// ========== DAO ==========

$app['dao.user'] = $app->share(function ($app) {
    $dao = new Skyflow\DAO\SkyflowUserDAO($app['db']);
    $dao->setEncryption($app['skyflow.security.encryption']);

    return $dao;
});

$app['dao.event'] = $app->share(function ($app) {
    return new Skyflow\DAO\EventDAO($app['db']);
});

$app['dao.flow'] = $app->share(function ($app) {
    return new Skyflow\DAO\FlowDAO($app['db']);
});

$app['dao.mapping'] = $app->share(function ($app) {
    $mappingDAO = new Skyflow\DAO\MappingDAO($app['db']);
    $mappingDAO->setEventDAO($app['dao.event']);
    $mappingDAO->setFlowDAO($app['dao.flow']);
    return $mappingDAO;
});

// ========== Domain ==========

$app['user'] = $app->share(function () use ($app) {
    $user = null;

    if (0 === strpos($app['request']->headers->get('Content-Type'), 'application/json')) {
        // Skyflow JSON API request
        if ($app['request']->headers->has('Skyflow-Token')) {
            $token = $app['request']->headers->get('Skyflow-Token');
            $user = $app['dao.user']->findByToken($token);
        } else {
            throw new \Exception('Missing skyflow-token');
        }
    } else {
        // Skyflow interface request
        if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user = $app['security']->getToken()->getUser();
        } else {
            $user = null;
        }
    }

    return $user;
});

// ========== Services ==========

$app['generatetoken'] = $app->share(function ($app) {
    $generate = new Skyflow\Service\GenerateToken();
    return $generate;
});

$app['exacttarget'] = $app->share(function ($app) {
    if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
        return Skyflow\Service\ExactTarget::login($app);
    } else {
        return Skyflow\Service\ExactTarget::loginByApi($app);
    }
});

/**
 * Automatically redirect non authenticated users to /login.
 */
$app->before(function (Request $request, Application $app) {
    if (!($app['request']->headers->has('Skyflow-Token'))) {
        if ($app['user'] === null && $request->get('_route') !== 'login') {
            return new RedirectResponse('/login');
        }
    }
});
