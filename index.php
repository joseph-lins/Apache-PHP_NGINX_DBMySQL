<?php
require_once __DIR__ . '/config.php';

header('Content-Type: text/html; charset=utf-8');

function html($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$xff = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? null;
$forwarded_for = $xff ? trim(explode(',', $xff)[0]) : null;

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
