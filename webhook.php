<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Importa configuração
$config = require __DIR__ . '/config.php';
$expectedToken = $config['webhook_token'] ?? '';

if (!isset($_GET['token']) || $_GET['token'] !== $expectedToken) {
    http_response_code(403);
    exit('Acesso negado');
}

$payload = file_get_contents('php://input');
$data = json_decode($payload, true);

if (!$data || !isset($data['id'])) {
    http_response_code(400);
    exit('Payload inválido');
}

$db = new SQLite3(__DIR__ . '/transacoes.db');
$db->exec("CREATE TABLE IF NOT EXISTS transacoes (
    id TEXT PRIMARY KEY,
    status TEXT,
    valor INTEGER,
    data_hora TEXT
)");

$stmt = $db->prepare("INSERT OR REPLACE INTO transacoes (id, status, valor, data_hora) VALUES (:id, :status, :valor, :data_hora)");
$stmt->bindValue(':id', $data['id'], SQLITE3_TEXT);
$stmt->bindValue(':status', $data['status'] ?? 'indefinido', SQLITE3_TEXT);
$stmt->bindValue(':valor', $data['value'] ?? 0, SQLITE3_INTEGER);
$stmt->bindValue(':data_hora', date('Y-m-d H:i:s'), SQLITE3_TEXT);
$stmt->execute();

http_response_code(200);
echo '✅ Webhook recebido com sucesso';
