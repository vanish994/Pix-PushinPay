<?php
$apiKey = '40131|AXGqOSedEmE8mP4qqSHCxbbAkizbnCU0siqEsRzt32eee2c2';

$payload = [
    'value' => 1000,
    'description' => 'Teste com webhook',
    'webhook_url' => 'https://pix-pushinpay.onrender.com/webhook.php?token=meu-token-seguro'
];

$ch = curl_init('https://api.pushinpay.com.br/api/v1/pix/charges');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $apiKey",
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

header('Content-Type: application/json');
echo json_encode([
    'http_code' => $httpCode,
    'response' => json_decode($response, true),
    'raw' => $response,
    'error' => $error
], JSON_PRETTY_PRINT);
