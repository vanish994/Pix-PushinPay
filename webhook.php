<?php
// Ativa exibição de erros (somente para testes; remova em produção)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Caminho para o arquivo de log
$logFile = __DIR__ . '/webhook.log';

// Lê o corpo da requisição (JSON)
$payload = file_get_contents('php://input');

// Decodifica o JSON recebido
$data = json_decode($payload, true);

// Verifica se o JSON é válido e contém um ID de transação
if (!$data || !isset($data['id'])) {
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "⚠️ Payload inválido: $payload\n", FILE_APPEND);
    http_response_code(400);
    exit('Payload inválido');
}

// Extrai os dados principais da notificação
$transactionId = $data['id'];
$status = $data['status'] ?? 'desconhecido';
$value = $data['value'] ?? 0;
$timestamp = date('Y-m-d H:i:s');

// Monta e salva a mensagem no log
$logMessage = "[{$timestamp}] 🔔 Transação {$transactionId} - Status: {$status} - Valor: R$ " . number_format($value / 100, 2, ',', '.') . "\n";
file_put_contents($logFile, $logMessage, FILE_APPEND);

// Envia resposta de sucesso
http_response_code(200);
echo '✅ Webhook recebido com sucesso';
