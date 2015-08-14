<?php

// Doctrine (db)
$app['db.options'] = array(
    'driver'   => 'pdo_pgsql',
    'charset'  => 'utf8',
    'host'     => 'ec2-54-83-57-86.compute-1.amazonaws.com',
    'port'     => '5432',
    'dbname'   => 'd70b123ck17v74',
    'user'     => 'yasrxhlztiwpnq',
    'password' => 'YT5kh8cYoPq4e9Xa2i2cWWz7rs',
);
// define log level
$app['monolog.level'] = 'WARNING';

//A ENLEVER 
// enable the debug mode
//$app['debug'] = true;
// define log level
//$app['monolog.level'] = 'INFO';