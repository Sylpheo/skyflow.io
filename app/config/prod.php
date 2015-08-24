<?php

/**
 * Application configuration for the production environment.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

$app['db.options'] = include __DIR__ . '/../db/prod.php';

//$app['debug'] = true;
$app['monolog.level'] = 'WARNING';