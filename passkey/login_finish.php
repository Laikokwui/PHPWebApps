<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';
$data = parse_json_request();
if (empty($_SESSION['challenge']) || empty($_SESSION['login_user'])) {
    send_json(['error' => 'no challenge in session']);
}
$user = $_SESSION['login_user'];
$rawId = $data['rawId'] ?? null;
$clientDataJSON = $data['clientDataJSON'] ?? null;
$authenticatorData = $data['authenticatorData'] ?? null;
$signature = $data['signature'] ?? null;
if (!$rawId || !$clientDataJSON || !$authenticatorData || !$signature) {
    send_json(['error' => 'missing fields']);
}

$cred = find_credential_by_id($rawId);
if (!$cred) {
    send_json(['error' => 'unknown credential']);
}

send_json(['ok' => true]);
