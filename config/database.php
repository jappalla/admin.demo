<?php
declare(strict_types=1);

use App\Support\Env;

$driver = (string) Env::get('DB_CONNECTION', 'mysql');
$host = (string) Env::get('DB_HOST', '127.0.0.1');
$port = (string) Env::get('DB_PORT', '3306');
$database = (string) Env::get('DB_DATABASE', 'cv_portal_local');
$charset = (string) Env::get('DB_CHARSET', 'utf8mb4');
$collation = (string) Env::get('DB_COLLATION', 'utf8mb4_unicode_ci');

return [
    'default' => $driver,
    'connections' => [
        'mysql' => [
            'driver' => $driver,
            'host' => $host,
            'port' => $port,
            'database' => $database,
            'charset' => $charset,
            'collation' => $collation,
            'username' => (string) Env::get('DB_USERNAME', 'root'),
            'password' => (string) Env::get('DB_PASSWORD', ''),
            'dsn' => sprintf(
                '%s:host=%s;port=%s;dbname=%s;charset=%s',
                $driver,
                $host,
                $port,
                $database,
                $charset
            ),
        ],
    ],
];
