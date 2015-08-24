<?php

/**
 * Application configuration for the development environment.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

$app['db.options'] = include __DIR__ . '/../db/dev.php';

$app['debug'] = true;
$app['monolog.level'] = 'INFO';