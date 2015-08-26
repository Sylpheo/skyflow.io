<?php

/**
 * Database configuration.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

$database_url = $_ENV['DATABASE_URL'];

return function() use ($database_url) {
    extract(parse_url($database_url));

    return array(
        'driver'   => 'pdo_pgsql',
        'charset'  => 'utf8',
        'host'     => $host,
        'port'     => $port,
        'user'     => $user,
        'password' => $pass,
        'dbname'   => substr($path, 1)
    );
};