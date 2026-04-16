<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';
$data = parse_json_request();
if (empty($_SESSION['challenge']) || empty($_SESSION['register_user'])) {
    send_json(['error' => 'no challenge in session']);
}
$user = $_SESSION['register_user'];
$rawId = $data['rawId'] ?? null;
$attestationObject = $data['attestationObject'] ?? null;
$clientDataJSON = $data['clientDataJSON'] ?? null;
if (!$rawId || !$attestationObject || !$clientDataJSON) {
    send_json(['error' => 'missing fields']);
}

store_credential($user, $rawId, null, $attestationObject, 0);
send_json(['ok' => true]);
