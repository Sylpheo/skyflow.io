<?php

/**
 * Application configuration for the development environment.
 */

$app['db.options'] = include __DIR__ . '/../../db/dev.php';

$app['debug'] = true;
$app['monolog.level'] = 'INFO';