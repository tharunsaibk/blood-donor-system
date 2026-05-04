<?php
declare(strict_types=1);

function db_connect(array $cfg): PDO
{
    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=%s',
        $cfg['host'],
        $cfg['name'],
        $cfg['charset'] ?? 'utf8mb4'
    );

    try {
        return new PDO($dsn, $cfg['user'], $cfg['pass'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        $detail = cfg('app', 'debug')
            ? ' Reason: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')
            : '';
        exit('Database connection failed. Please check config.php.' . $detail);
    }
}

function db(): PDO
{
    return $GLOBALS['db'];
}
