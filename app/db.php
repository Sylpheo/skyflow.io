<?php

/**
 * Database configuration.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

$db = parse_url($_ENV['DATABASE_URL']);

return array(
    'driver'   => 'pdo_pgsql',
    'charset'  => 'utf8',
    'host'     => $db['host'],
    'port'     => $db['port'],
    'user'     => $db['user'],
    'password' => $db['pass'],
    'dbname'   => substr($db['path'], 1)
);