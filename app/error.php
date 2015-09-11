<?php

/**
 * Skyflow Error and Exception handlers registration.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;

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
