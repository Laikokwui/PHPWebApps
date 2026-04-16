<?php
require_once __DIR__ . '/config.php';

function get_db() {
    static $db = null;
    if ($db === null) {
        $db = new PDO('sqlite:' . PASSKEY_DB);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->exec("CREATE TABLE IF NOT EXISTS credentials (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_handle TEXT NOT NULL,
            credential_id TEXT NOT NULL UNIQUE,
            public_key TEXT,
            sign_count INTEGER DEFAULT 0,
            raw_attestation TEXT,
            created_at TEXT DEFAULT (datetime('now'))
        )");
    }
    return $db;
}

function store_credential($user_handle, $credential_id_b64, $public_key = null, $raw_attestation_b64 = null, $sign_count = 0) {
    $db = get_db();
    $stmt = $db->prepare('INSERT OR REPLACE INTO credentials (user_handle, credential_id, public_key, sign_count, raw_attestation) VALUES (:user_handle, :credential_id, :public_key, :sign_count, :raw_attestation)');
    $stmt->execute([
        ':user_handle' => $user_handle,
        ':credential_id' => $credential_id_b64,
        ':public_key' => $public_key,
        ':sign_count' => $sign_count,
        ':raw_attestation' => $raw_attestation_b64,
    ]);
    return $db->lastInsertId();
}

function get_credentials_by_user($user_handle) {
    $db = get_db();
    $stmt = $db->prepare('SELECT * FROM credentials WHERE user_handle = :user_handle');
    $stmt->execute([':user_handle' => $user_handle]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function find_credential_by_id($credential_id_b64) {
    $db = get_db();
    $stmt = $db->prepare('SELECT * FROM credentials WHERE credential_id = :credential_id');
    $stmt->execute([':credential_id' => $credential_id_b64]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
