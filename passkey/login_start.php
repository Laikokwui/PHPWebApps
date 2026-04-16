<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';
$data = parse_json_request();
$user = isset($data['username']) ? trim($data['username']) : null;
if (!$user) {
    send_json(['error' => 'username required']);
}
$creds = get_credentials_by_user($user);
if (empty($creds)) {
    send_json(['error' => 'no credentials']);
}
$allow = [];
foreach ($creds as $c) {
    $allow[] = ['type' => 'public-key', 'id' => $c['credential_id']];
}
$challenge = generate_challenge();
$_SESSION['challenge'] = $challenge;
$_SESSION['login_user'] = $user;

$publicKey = [
    'challenge' => $challenge,
    'timeout' => TIMEOUT,
    'rpId' => RP_ID,
    'allowCredentials' => $allow,
    'userVerification' => 'preferred',
];

send_json(['publicKey' => $publicKey]);
