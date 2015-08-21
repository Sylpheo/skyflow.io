<?php

/**
 * Application configuration for the production environment.
 */

$app['db.options'] = include __DIR__ . '/../../db/prod.php';

//$app['debug'] = true;
$app['monolog.level'] = 'WARNING';
