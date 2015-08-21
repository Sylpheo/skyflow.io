<?php

date_default_timezone_set('UTC');

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

require __DIR__.'/../app/config/prod.php';
require __DIR__.'/../app/app.php';
require __DIR__.'/../app/routes.php';

$app->run();