<?php

/**
 * Database configuration.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

// development datbase in localhost
if ($app['dev']) {
    return array(
        'driver' => 'pdo_pgsql',
        'host' => 'localhost',
        'port' => '5432',
        'user' => 'skyflow',
        'password' => 'skyflow',
        'dbname' => 'skyflow'
    );
}

$db = parse_url(getenv('DATABASE_URL'));

return array(
    'driver'   => 'pdo_pgsql',
    'host'     => $db['host'],
    'port'     => $db['port'],
    'user'     => $db['user'],
    'password' => $db['pass'],
    'dbname'   => substr($db['path'], 1)
);
