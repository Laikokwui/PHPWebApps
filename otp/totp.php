<?php
/**
 * Simple pure-PHP TOTP/HOTP library (RFC 4226 / RFC 6238).
 *
 * Functions:
 * - generateBase32Secret($length = 16)
 * - base32Decode($base32)
 * - hotp($secretBase32, $counter, $digits = 6, $algo = 'sha1')
 * - totp($secretBase32, $timeStep = 30, $digits = 6, $algo = 'sha1', $time = null)
 * - verifyTotp($secretBase32, $code, $discrepancy = 1, $timeStep = 30, $digits = 6, $algo = 'sha1', $time = null)
 */

function generateBase32Secret($length = 16) {
    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $secret = '';
    for ($i = 0; $i < $length; $i++) {
        $secret .= $alphabet[random_int(0, 31)];
    }
    return $secret;
}

function base32Decode($base32) {
    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $base32 = strtoupper($base32);
    $base32 = preg_replace('/[^A-Z2-7]/', '', $base32);
    $bits = '';
    $out = '';
    $len = strlen($base32);
    for ($i = 0; $i < $len; $i++) {
        $val = strpos($alphabet, $base32[$i]);
        if ($val === false) {
            continue;
        }
        $bits .= str_pad(decbin($val), 5, '0', STR_PAD_LEFT);
        while (strlen($bits) >= 8) {
            $byte = substr($bits, 0, 8);
            $bits = substr($bits, 8);
            $out .= chr(bindec($byte));
        }
    }
    return $out;
}

function hotp($secretBase32, $counter, $digits = 6, $algo = 'sha1') {
    $secret = base32Decode($secretBase32);
    $counter = (int)$counter;
    $binaryCounter = '';
    for ($i = 7; $i >= 0; $i--) {
        $binaryCounter .= chr(($counter >> ($i * 8)) & 0xFF);
    }
    $hmac = hash_hmac($algo, $binaryCounter, $secret, true);
    $offset = ord(substr($hmac, -1)) & 0x0F;
    $binary = ((ord($hmac[$offset]) & 0x7F) << 24) |
              ((ord($hmac[$offset + 1]) & 0xFF) << 16) |
              ((ord($hmac[$offset + 2]) & 0xFF) << 8) |
              (ord($hmac[$offset + 3]) & 0xFF);
    $otp = $binary % pow(10, $digits);
    return str_pad($otp, $digits, '0', STR_PAD_LEFT);
}

function totp($secretBase32, $timeStep = 30, $digits = 6, $algo = 'sha1', $time = null) {
    if ($time === null) {
        $time = time();
    }
    $counter = floor($time / $timeStep);
    return hotp($secretBase32, $counter, $digits, $algo);
}

function verifyTotp($secretBase32, $code, $discrepancy = 1, $timeStep = 30, $digits = 6, $algo = 'sha1', $time = null) {
    if ($time === null) {
        $time = time();
    }
    $currentCounter = floor($time / $timeStep);
    for ($i = -$discrepancy; $i <= $discrepancy; $i++) {
        $counter = $currentCounter + $i;
        $calc = hotp($secretBase32, $counter, $digits, $algo);
        if (hash_equals($calc, (string)$code)) {
            return true;
        }
    }
    return false;
}

if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($_SERVER['argv'][0])) {
    $secret = generateBase32Secret();
    echo "Generated secret: $secret\n";
    echo "TOTP now: " . totp($secret) . "\n";
    $label = rawurlencode('example@example.com');
    $issuer = rawurlencode('ExampleApp');
    $otpauth = "otpauth://totp/{$label}?secret={$secret}&issuer={$issuer}&period=30";
    echo "otpauth URI:\n$otpauth\n";
}
