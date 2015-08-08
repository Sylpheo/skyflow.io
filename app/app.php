<?php
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;
use skyflow\Service\ExactTarget;
use skyflow\Service\GenerateToken;
use Silex\Provider\FormServiceProvider;
use Symfony\Component\HttpFoundation\Request;

use skyflow\SilexOpauth\OpauthExtension;



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
                return new skyflow\DAO\UsersDAO($app['db']);
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


// Register services
$app['dao.event'] = $app->share(function ($app) {
    return new skyflow\DAO\EventDAO($app['db']);
});

$app['exacttarget'] = $app->share(function($app) {
    $exact = new skyflow\Service\ExactTarget();
    return $exact;
});

$app['generatetoken'] = $app->share(function($app) {
    $generate = new skyflow\Service\GenerateToken();
    return $generate;
});

$app['wave'] = $app->share(function($app) {
    $wave = new skyflow\Service\Wave();
    return $wave;
});
//DAO
$app['dao.user'] = $app->share(function ($app) {
    return new skyflow\DAO\UsersDAO($app['db']);
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

//Register Flows
$app['flow_mail_remerciements'] = $app->share(function ($app){
    return new skyflow\Flows\Flow_mail_remerciements();
});




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
});


/*
$app['opauth'] = array(
    'login' => '/auth',
    'callback' => '/auth/callback',
    'config' => array(
        'security_salt' => '_SECURE_RANDOM_SALT_',
        'strategy_dir' => '../strategy/',
        'Strategy' => array(
            'salesforce' => array(
                'client_id' => '3MVG9SemV5D80oBcbOkdI2WCxIIA5fZMPI3ZDTZBBU_6E6zc8Z5wKZ4DCh.bPDxBEV4PocUnC3ELl70tjOSof',
                'client_secret' => '8180025755972035170'
            ),
        )

    )
);


$app->register(new OpauthExtension());
// Listen for events
$app->on(OpauthExtension::EVENT_ERROR, function($e) use ($app){
    $app->log->error('Auth error: ' . $e['message'], ['response' => $e->getSubject()]);
    $e->setArgument('result', $app->redirect('/'));
});
$app->on(OpauthExtension::EVENT_SUCCESS, function($e) use ($app){
    $response = $e->getSubject();

    $access_token = $response['auth']['raw']['access_token'];
    $instance_url= $response['auth']['raw']['instance_url'];

    var_dump($response);
    $app['session']->set('access_token',$access_token);
    $app['session']->set('instance_url',$instance_url);



    //$e->setArgument('result', $app->redirect('/wave'));
});
*/