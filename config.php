<?php
declare(strict_types=1);

$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbName = getenv('DB_NAME') ?: 'your_database_name';
$dbUser = getenv('DB_USER') ?: 'your_database_username';
$dbPass = getenv('DB_PASS') ?: 'your_database_password';
$dbPort = getenv('DB_PORT') ?: '3306';

if (!defined('DB_HOST')) {
    define('DB_HOST', $dbHost);
    define('DB_NAME', $dbName);
    define('DB_USER', $dbUser);
    define('DB_PASS', $dbPass);
    define('DB_PORT', (int) $dbPort);
}

define('APP_NAME', '33客服台');

function getDbConnection(): mysqli
{
    $connection = mysqli_init();
    if (!$connection) {
        throw new RuntimeException('MySQLi initialization failed.');
    }

    if (!mysqli_real_connect($connection, DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT)) {
        throw new RuntimeException('Database connection failed: ' . mysqli_connect_error());
    }

    mysqli_set_charset($connection, 'utf8mb4');
    return $connection;
}
?>
