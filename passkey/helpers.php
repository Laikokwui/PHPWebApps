<?php
require_once __DIR__ . '/config.php';
session_start();

function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode($data) {
    $remainder = strlen($data) % 4;
    if ($remainder) {
        $data .= str_repeat('=', 4 - $remainder);
    }
    return base64_decode(strtr($data, '-_', '+/'));
}

function generate_challenge($len = CHALLENGE_LENGTH) {
    return base64url_encode(random_bytes($len));
}

function send_json($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function parse_json_request() {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return $data ?? [];
}
