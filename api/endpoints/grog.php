<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// ── Load .env ────────────────────────────────────────────────────────────────
function loadEnv(string $path): array {
    if (!file_exists($path)) return [];
    $env = [];
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        $pos = strpos($line, '=');
        if ($pos === false) continue;
        $key = trim(substr($line, 0, $pos));
        $val = trim(substr($line, $pos + 1));
        if (strlen($val) >= 2 && $val[0] === '"' && $val[-1] === '"') {
            $val = substr($val, 1, -1);
        } elseif (strlen($val) >= 2 && $val[0] === "'" && $val[-1] === "'") {
            $val = substr($val, 1, -1);
        }
        $env[$key] = $val;
    }
    return $env;
}

$env    = loadEnv(__DIR__ . '/../../.env');
$apiKey = $env['GROQ_API_KEY'] ?? '';
$model  = $env['GROQ_MODEL']   ?? 'llama-3.3-70b-versatile';

if (!$apiKey) {
    http_response_code(500);
    echo json_encode(['error' => 'API key not configured on server']);
    exit;
}

// ── Validate request ─────────────────────────────────────────────────────────
$body = json_decode(file_get_contents('php://input'), true);

if (!$body || !isset($body['messages']) || !is_array($body['messages']) || empty($body['messages'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request: messages array required']);
    exit;
}

// Whitelist message fields to prevent prompt injection via extra keys
$messages = [];
foreach ($body['messages'] as $msg) {
    if (!isset($msg['role'], $msg['content']) ||
        !in_array($msg['role'], ['system', 'user', 'assistant'], true) ||
        !is_string($msg['content'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid message format']);
        exit;
    }
    $messages[] = ['role' => $msg['role'], 'content' => $msg['content']];
}

$payload = [
    'model'       => $model,
    'messages'    => $messages,
    'max_tokens'  => min((int)($body['max_tokens']  ?? 500), 1000),
    'temperature' => max(0.0, min(2.0, (float)($body['temperature'] ?? 0.65))),
    'top_p'       => max(0.0, min(1.0, (float)($body['top_p']       ?? 0.9))),
];

// ── Call Groq API ─────────────────────────────────────────────────────────────
$ch = curl_init('https://api.groq.com/openai/v1/chat/completions');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey,
    ],
    CURLOPT_POSTFIELDS     => json_encode($payload),
    CURLOPT_TIMEOUT        => 30,
]);

$result   = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr  = curl_error($ch);
curl_close($ch);

if ($curlErr) {
    http_response_code(502);
    echo json_encode(['error' => 'Error de red al contactar la API de Groq']);
    exit;
}

http_response_code($httpCode);
echo $result;
