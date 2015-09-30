<?php

/**
 * Register the Silex providers used by the Skyflow application.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;

$app->register(new DoctrineServiceProvider());

$app->register(new MonologServiceProvider(), array(
    'monolog.logfile' => (
        $app['dev'] ? __DIR__ . '/../var/logs/silex.log' : 'php://stderr'
    ),
    'monolog.name' => 'silex',
    'monolog.level' => \Monolog\Logger::WARNING
));

$app->register(new ServiceControllerServiceProvider());

$app->register(new SessionServiceProvider());

$app->register(new TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));

$app['twig'] = $app->share($app->extend('twig', function (Twig_Environment $twig, $app) {
    $twig->addExtension(new Twig_Extensions_Extension_Text());
    return $twig;
}));

$app->register(new TranslationServiceProvider());

$app->register(new UrlGeneratorServiceProvider());

$app->register(new FormServiceProvider());

$app->register(new SecurityServiceProvider(), array(
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
