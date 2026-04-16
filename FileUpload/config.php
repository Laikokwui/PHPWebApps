<?php
if (!defined('UPLOAD_DIR')) {
    define('UPLOAD_DIR', __DIR__ . '/uploads');
}
if (!defined('MAX_FILE_SIZE')) {
    define('MAX_FILE_SIZE', 5 * 1024 * 1024);
}
$ALLOWED_MIME_TYPES = [
    'image/jpeg',
    'image/png',
    'image/gif',
    'application/pdf',
    'text/plain',
];
$ALLOWED_EXTENSIONS = [
    'jpg',
    'jpeg',
    'png',
    'gif',
    'pdf',
    'txt',
];
