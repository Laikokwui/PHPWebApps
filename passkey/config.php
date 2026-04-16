<?php
if (!defined('PASSKEY_DB')) {
    define('PASSKEY_DB', __DIR__ . '/passkey.sqlite');
}
if (!defined('RP_ID')) {
    define('RP_ID', $_SERVER['HTTP_HOST'] ?? 'localhost');
}
if (!defined('RP_NAME')) {
    define('RP_NAME', 'PHP WebAuthn Demo');
}
if (!defined('CHALLENGE_LENGTH')) {
    define('CHALLENGE_LENGTH', 32);
}
if (!defined('TIMEOUT')) {
    define('TIMEOUT', 60000);
}
