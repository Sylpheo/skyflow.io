<?php
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;
use exactSilex\Service\ExactTarget;
use Silex\Provider\FormServiceProvider;
use Symfony\Component\HttpFoundation\Request;

use exactSilex\SilexOpauth\OpauthExtension;


// Register global error and exception handlers
ErrorHandler::register();
ExceptionHandler::register();
// Register service providers
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));

$app['twig'] = $app->share($app->extend('twig', function(Twig_Environment $twig, $app) {
    $twig->addExtension(new Twig_Extensions_Extension_Text());
    return $twig;
}));
$app->register(new Silex\Provider\TranslationServiceProvider());
$app->register(new Silex\Provider\DoctrineServiceProvider());



$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'secured' => array(
            'pattern' => '^/',
            'anonymous' => true,
            'logout' => true,
            'form' => array('login_path' => '/login', 'check_path' => '/login_check'),
            'users' => $app->share(function () use ($app) {
                return new exactSilex\DAO\UsersDAO($app['db']);
            }),
        ),
    ),
));


$app->register(new FormServiceProvider());
$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/../var/logs/silex.log',
    'monolog.name' => 'silex',
    'monolog.level' => $app['monolog.level']
));


$app['opauth'] = array(
      'login' => '/auth',
      'callback' => '/auth/callback',
      'config' => array(
        'security_salt' => '_SECURE_RANDOM_SALT_',
        'Strategy' => array(
            'Salesforce' => array( // Is available at /auth/login/facebook
           'client_id' => '3MVG9SemV5D80oBcbOkdI2WCxIIA5fZMPI3ZDTZBBU_6E6zc8Z5wKZ4DCh.bPDxBEV4PocUnC3ELl70tjOSof',
           'client_secret' => '8180025755972035170'
         ),
        )
        
      )
    );


$app->register(new OpauthExtension());


    // Listen for events
    $app->on(OpauthExtension::EVENT_ERROR, function($e) {
        $this->log->error('Auth error: ' . $e['message'], ['response' => $e->getSubject()]);
        $e->setArgument('result', $this->redirect('/'));
    });

    $app->on(OpauthExtension::EVENT_SUCCESS, function($e) {
        $response = $e->getSubject();

       //var_dump($response);
        var_dump($response['auth']['raw']['access_token']);
        var_dump($response['auth']['raw']['instance_url']);



        /*
           find/create a user, oauth response is in $response and it's already validated!
           store the user in the session
        */

        $e->setArgument('result', $app->redirect('/'));
    });

// Register services
$app['dao.event'] = $app->share(function ($app) {
    return new exactSilex\DAO\EventDAO($app['db']);
});

$app['exacttarget'] = $app->share(function($app) {
    $exact = new exactSilex\Service\ExactTarget();
    return $exact;
});

$app['wave'] = $app->share(function($app) {
    $wave = new exactSilex\Service\Wave();
    return $wave;
});

$app['dao.user'] = $app->share(function ($app) {
    return new exactSilex\DAO\UsersDAO($app['db']);
});

// Register JSON data decoder for JSON requests
$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

/*
// Register error handler
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
});*/

