<?php
require_once __DIR__ . '/config.php';

header('Content-Type: text/html; charset=utf-8');

function html($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Validação básica
if (!DB_PASS) {
    http_response_code(500);
    echo "<h1>CONFIGURAÇÃO INCOMPLETA</h1>";
    echo "<p>DB_PASS não foi definido nas variáveis de ambiente.</p>";
    exit;
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

    // Cria banco se não existir
    $pdo->exec("
        CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "`
        CHARACTER SET utf8mb4
        COLLATE utf8mb4_unicode_ci
    ");

    $pdo->exec("USE `" . DB_NAME . "`");

    // Cria tabela se não existir
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

    // Captura IP real (atrás do NGINX)
    $xff = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? null;
    $forwarded_for = $xff ? trim(explode(',', $xff)[0]) : null;

    // Insere registro
    $stmt = $pdo->prepare("
        INSERT INTO access_log (
            remote_addr,
            forwarded_for,
            request_method,
            request_uri,
            user_agent
        ) VALUES (
            :remote_addr,
            :forwarded_for,
            :method,
            :uri,
            :ua
        )
    ");

    $stmt->execute([
        ':remote_addr'   => $_SERVER['REMOTE_ADDR'] ?? null,
        ':forwarded_for' => $forwarded_for,
        ':method'        => $_SERVER['REQUEST_METHOD'] ?? null,
        ':uri'           => $_SERVER['REQUEST_URI'] ?? null,
        ':ua'            => $_SERVER['HTTP_USER_AGENT'] ?? null,
    ]);

    $lastId = $pdo->lastInsertId();

} catch (Throwable $e) {
    http_response_code(500);
    echo "<h1>ERRO AO CONECTAR/GRAVAR NO MYSQL</h1>";
    echo "<pre>" . html($e->getMessage()) . "</pre>";
    exit;
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>TESTE BEM SUCEDIDO</title>
  <style>
    html, body {
        height: 100%;
        margin: 0;
        font-family: Arial, sans-serif;
        background: #0b1020;
        color: #fff;
    }
    .wrap {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .card {
        max-width: 860px;
        width: calc(100% - 32px);
        padding: 28px;
        border-radius: 16px;
        background: rgba(255,255,255,0.06);
        box-shadow: 0 10px 30px rgba(0,0,0,0.35);
    }
    h1 {
        margin: 0 0 10px 0;
        font-size: clamp(28px, 4vw, 44px);
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }
    .meta {
        opacity: 0.9;
        font-size: 14px;
        line-height: 1.5;
    }
    code {
        background: rgba(255,255,255,0.12);
        padding: 2px 6px;
        border-radius: 6px;
    }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <h1>TESTE BEM SUCEDIDO</h1>
      <div class="meta">
        <div>Registro gravado no MySQL: <code>#<?php echo html($lastId); ?></code></div>
        <div>Banco: <code><?php echo html(DB_NAME); ?></code> · Tabela: <code>access_log</code></div>
        <div>Consulta: <code>SELECT * FROM access_log ORDER BY id DESC LIMIT 20;</code></div>
      </div>
    </div>
  </div>
</body>
</html>
