<?php

/**
 * Database configuration.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

$db = parse_url(getenv('DATABASE_URL'));

var_dump($db);

$db_config = array(
    'driver'   => 'pdo_pgsql',
    'charset'  => 'utf8',
    'host'     => $db['host'],
    'port'     => $db['port'],
    'user'     => $db['user'],
    'password' => $db['pass'],
    'dbname'   => substr($db['path'], 1)
);

var_dump($db_config);

return $db_config;