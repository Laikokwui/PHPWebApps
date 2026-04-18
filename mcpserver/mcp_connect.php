<?php
// mcp_connect.php — pure-PHP proxy to forward JSON payloads to an MCP server endpoint.
// Algorithm (high level):
// 1) Read JSON POST from client: { endpoint, apiKey (opt), payload }
// 2) Validate endpoint URL (FILTER_VALIDATE_URL) and optional allowlist
// 3) Build HTTP POST with Content-Type: application/json and optional Authorization header
// 4) Use PHP stream context (file_get_contents) to send the request and capture response
// 5) Return a JSON envelope with status, raw response and decoded JSON when possible

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    exit;
}

$raw = file_get_contents('php://input');
if (!$raw) { http_response_code(400); echo json_encode(['ok'=>false,'error'=>'No input provided']); exit; }
$req = json_decode($raw, true);
if (!is_array($req)) { http_response_code(400); echo json_encode(['ok'=>false,'error'=>'Invalid JSON']); exit; }

$endpoint = $req['endpoint'] ?? null;
$apiKey = $req['apiKey'] ?? null;
$payload = $req['payload'] ?? null;

if (!$endpoint) { http_response_code(400); echo json_encode(['ok'=>false,'error'=>'Missing endpoint']); exit; }
if (!filter_var($endpoint, FILTER_VALIDATE_URL)) { http_response_code(400); echo json_encode(['ok'=>false,'error'=>'Invalid endpoint URL']); exit; }

// Optional: host allowlist to reduce SSRF risk. Leave empty to allow any host.
$allowed_hosts = [];
if (!empty($allowed_hosts)) {
    $host = parse_url($endpoint, PHP_URL_HOST);
    if (!in_array($host, $allowed_hosts, true)) { http_response_code(403); echo json_encode(['ok'=>false,'error'=>'Host not allowed']); exit; }
}

$payload_json = json_encode($payload, JSON_UNESCAPED_UNICODE);
$headers = "Content-Type: application/json\r\n";
if ($apiKey) { $headers .= "Authorization: Bearer " . $apiKey . "\r\n"; }

$options = [
    'http' => [
        'method' => 'POST',
        'header' => $headers,
        'content' => $payload_json,
        'timeout' => 30,
        'ignore_errors' => true,
    ]
];

$context = stream_context_create($options);
$response = @file_get_contents($endpoint, false, $context);

$status = null;
if (isset($http_response_header) && is_array($http_response_header)) {
    foreach ($http_response_header as $h) {
        if (preg_match('#^HTTP/\d+\.\d+\s+(\d+)#', $h, $m)) { $status = (int)$m[1]; break; }
    }
}

if ($response === false) {
    echo json_encode(['ok'=>false, 'status'=>$status, 'error'=>'Request failed', 'headers'=>$http_response_header ?? null], JSON_UNESCAPED_UNICODE);
    exit;
}

$decoded = json_decode($response, true);
$out = ['ok'=>true, 'status'=>$status, 'raw'=>$response];
if ($decoded !== null) { $out['json'] = $decoded; }

echo json_encode($out, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
