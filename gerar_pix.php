<?php
// Permitir CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Responder a requisições OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit();
}

// Ler os dados JSON da requisição
$input = json_decode(file_get_contents('php://input'), true);

// Verificar se os dados foram recebidos corretamente
if (!$input || !isset($input['valor']) || !isset($input['descricao'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit();
}

// Dados fornecidos no formulário
$valor = intval($input['valor']); // Valor já em centavos
$descricao = $input['descricao'];

// Validações
if ($valor < 50) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Valor mínimo é 50 centavos']);
    exit();
}

if (empty($descricao)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Descrição é obrigatória']);
    exit();
}

// Chave de API da PushinPay
$apiKey = '40131|AXGqOSedEmE8mP4qqSHCxbbAkizbnCU0siqEsRzt32eee2c2';

// URL da API da PushinPay
$url = 'https://api.pushinpay.com.br/api/pix/cashIn';

// Dados para enviar à API
$postData = [
    'value' => $valor,
    'webhook_url' => '', // Opcional
    'split_rules' => []
];

// Configurações do cURL para fazer a requisição
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    'Authorization: Bearer ' . $apiKey
]);

// Executa a requisição
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Verifica se houve erro no cURL
if ($response === false) {
    $error = curl_error($ch);
    curl_close($ch);
    
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro de conexão: ' . $error]);
    exit();
}

// Fecha a conexão cURL
curl_close($ch);

// Decodifica a resposta JSON
$responseData = json_decode($response, true);

// Verifica o código de resposta HTTP
if ($httpCode !== 200) {
    http_response_code($httpCode);
    
    $errorMessage = 'Erro na API';
    if (isset($responseData['message'])) {
        $errorMessage = $responseData['message'];
    } elseif (isset($responseData['error'])) {
        $errorMessage = $responseData['error'];
    }
    
    echo json_encode(['success' => false, 'message' => $errorMessage]);
    exit();
}

// Verifica se a resposta contém os dados esperados
if (!$responseData || !isset($responseData['qr_code'])) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Resposta inválida da API']);
    exit();
}

// Retorna os dados do PIX gerado
echo json_encode([
    'success' => true,
    'id' => $responseData['id'],
    'qr_code' => $responseData['qr_code'],
    'qr_code_base64' => $responseData['qr_code_base64'],
    'status' => $responseData['status'],
    'value' => $responseData['value'],
    'webhook_url' => $responseData['webhook_url'] ?? null,
    'split_rules' => $responseData['split_rules'] ?? [],
    'end_to_end_id' => $responseData['end_to_end_id'] ?? null,
    'payer_name' => $responseData['payer_name'] ?? null,
    'payer_national_registration' => $responseData['payer_national_registration'] ?? null
]);
?>

