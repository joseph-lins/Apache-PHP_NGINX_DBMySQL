<?php

require __DIR__ . '/db_config.php';

if (!defined('DB_HOST') || !defined('DB_PASS')) {
    http_response_code(500);
    die('Configuração de banco inválida.');
}

$dsn = sprintf(
    "mysql:host=%s;port=%s;charset=utf8mb4",
    DB_HOST,
    DB_PORT
);

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT => 3,
    ]);

    $pdo->exec("
        CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "`
        CHARACTER SET utf8mb4
        COLLATE utf8mb4_unicode_ci
    ");

    $pdo->exec("USE `" . DB_NAME . "`");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS access_log (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            accessed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            remote_addr VARCHAR(45) NULL,
            forwarded_for VARCHAR(255) NULL,
            request_method VARCHAR(10) NULL,
            request_uri VARCHAR(2048) NULL,
            user_agent VARCHAR(512) NULL,
            PRIMARY KEY (id),
            KEY idx_accessed_at (accessed_at)
        ) ENGINE=InnoDB
        DEFAULT CHARSET=utf8mb4
        COLLATE=utf8mb4_unicode_ci
    ");

} catch (Throwable $e) {
    http_response_code(500);
    die("ERRO AO CONECTAR/GRAVAR NO MYSQL<br><pre>" . htmlspecialchars($e->getMessage(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</pre>");
}
