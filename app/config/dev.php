<?php

// ---------- VM Linux ----------

$app['db.options'] = array(
    'driver'   => 'pdo_mysql',
    'charset'  => 'utf8',
    'host'     => '127.0.0.1',  // Mandatory for PHPUnit testing
    'port'     => '3306',
    'dbname'   => 'exacttarget',
    'user'     => 'root',
    'password' => 'swiffer',
);

// ---------- using create.sql ----------

/*$app['db.options'] = array(
    'driver'   => 'pdo_mysql',
    'charset'  => 'utf8',
    'host'     => '127.0.0.1',  // Mandatory for PHPUnit testing
    'port'     => '3306',
    'dbname'   => 'exacttarget',
    'user'     => 'skyflow',
    'password' => 'skyflow',
);*/

// enable the debug mode
$app['debug'] = true;
// define log level
$app['monolog.level'] = 'INFO';