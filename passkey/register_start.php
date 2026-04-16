<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';
$data = parse_json_request();
$user = isset($data['username']) ? trim($data['username']) : null;
if (!$user) {
    send_json(['error' => 'username required']);
}
$challenge = generate_challenge();
$_SESSION['challenge'] = $challenge;
$_SESSION['register_user'] = $user;
$user_id = base64url_encode($user);
$publicKey = [
    'rp' => ['name' => RP_NAME, 'id' => RP_ID],
    'user' => ['id' => $user_id, 'name' => $user, 'displayName' => $user],
    'challenge' => $challenge,
    'pubKeyCredParams' => [['type' => 'public-key', 'alg' => -7]],
    'timeout' => TIMEOUT,
    'attestation' => 'none',
];

send_json(['publicKey' => $publicKey]);
