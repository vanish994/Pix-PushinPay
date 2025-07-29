<?php

// Inclui configuração com o token
require_once 'config.php';

// Dados do CallMeBot
$callmebot_phone = "558893662653";
$callmebot_apikey = "8631999";

// Verifica token da URL
if (!isset($_GET['token']) || $_GET['token'] !== $webhook_token) {
    http_response_code(403);
    echo "Token inválido";
    exit;
}

// Lê JSON recebido
$data = json_decode(file_get_contents('php://input'), true);

// Validação básica
if (!isset($data['id'], $data['status'], $data['value'])) {
    http_response_code(400);
    echo "JSON inválido";
    exit;
}

// Extrai dados
$id = $data['id'];
$status = $data['status'];
$value = $data['value'];
$valorFormatado = number_format($value / 100, 2, ',', '.');

// Monta mensagem
$msg = "💰 Novo pagamento recebido!\n\nID: $id\nStatus: $status\nValor: R$ $valorFormatado";

// Envia para o WhatsApp via CallMeBot
$callmebot_url = "https://api.callmebot.com/whatsapp.php?phone=$callmebot_phone&text=" . urlencode($msg) . "&apikey=$callmebot_apikey";

// Faz requisição para CallMeBot
file_get_contents($callmebot_url);

// Retorna sucesso
echo "✅ Webhook recebido e notificado com sucesso";
