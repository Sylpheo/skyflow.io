<?php

$app['db.options'] = array(
    'driver'   => 'pdo_mysql',
    'charset'  => 'utf8',
    'host'     => '127.0.0.1',  // Mandatory for PHPUnit testing
    'port'     => '3306',
    'dbname'   => 'skyflow',
    'user'     => 'skyflow',
    'password' => 'skyflow',
);

$app['debug'] = true;
$app['monolog.level'] = 'INFO';