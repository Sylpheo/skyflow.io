<?php
/*$app['db.options'] = array(
    'driver'   => 'pdo_mysql',
    'charset'  => 'utf8',
    'host'     => '127.0.0.1',  // Mandatory for PHPUnit testing
    'port'     => '3306',
    'dbname'   => 'exacttarget',
    'user'     => 'root',
    'password' => 'swiffer',
);*/

$app['db.options'] = array(
    'driver'   => 'pdo_pgsql',
    'charset'  => 'utf8',
    'host'     => 'ec2-54-83-57-86.compute-1.amazonaws.com',
    'port'     => '5432',
    'dbname'   => 'd70b123ck17v74',
    'user'     => 'yasrxhlztiwpnq',
    'password' => 'YT5kh8cYoPq4e9Xa2i2cWWz7rs',
);

// enable the debug mode
$app['debug'] = true;
// define log level
$app['monolog.level'] = 'INFO';