<?php
// config.php deve estar incluído para acessar o webhook_token
require_once 'config.php';

// Verifica o token na URL
if (!isset($_GET['token']) || $_GET['token'] !== $webhook_token) {
    http_response_code(403);
    echo json_encode(['error' => 'Token inválido']);
    exit;
}

// Lê o corpo da requisição JSON
$data = json_decode(file_get_contents('php://input'), true);

// Verifica se os dados principais foram enviados
if (!isset($data['id'], $data['status'], $data['value'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Dados incompletos']);
    exit;
}

// Dados do pagamento
$id     = $data['id'];
$status = $data['status'];
$value  = $data['value'] / 100; // converte de centavos para reais

// Mensagem para o WhatsApp
$mensagem = "💰 Novo pagamento recebido!\n\nID: $id\nStatus: $status\nValor: R$ " . number_format($value, 2, ',', '.');

// Envio via CallMeBot
$numero_whatsapp = '558893662653'; // seu número com DDI
$apikey          = '8631999';

$url = "https://api.callmebot.com/whatsapp.php?phone=$numero_whatsapp&text=" . urlencode($mensagem) . "&apikey=$apikey";

// Executa requisição
file_get_contents($url);

// Retorno para quem chamou o webhook
echo json_encode(['status' => 'Webhook processado e mensagem enviada!']);
