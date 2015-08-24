<?php

/**
 * Development database configuration.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

return array(
    'driver'   => 'pdo_mysql',
    'charset'  => 'utf8',
    'host'     => '127.0.0.1',  // Mandatory for PHPUnit testing
    'port'     => '3306',
    'dbname'   => 'skyflow',
    'user'     => 'skyflow',
    'password' => 'skyflow',
);