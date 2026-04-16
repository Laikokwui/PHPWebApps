<?php
require_once __DIR__ . '/config.php';

function ensure_upload_dir() {
    if (!is_dir(UPLOAD_DIR)) {
        return mkdir(UPLOAD_DIR, 0755, true);
    }
    return is_writable(UPLOAD_DIR);
}

function sanitize_filename($filename) {
    return preg_replace('/[^A-Za-z0-9_.-]/', '_', $filename);
}

function is_allowed_file($file) {
    global $ALLOWED_MIME_TYPES, $ALLOWED_EXTENSIONS;
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    if (!isset($file['size']) || $file['size'] > MAX_FILE_SIZE) {
        return false;
    }
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return false;
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!in_array($mime, $ALLOWED_MIME_TYPES, true)) {
        return false;
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $ALLOWED_EXTENSIONS, true)) {
        return false;
    }
    return true;
}

function move_uploaded_file_safe($file) {
    if (!ensure_upload_dir()) return false;
    $name = sanitize_filename(basename($file['name']));
    $target = rtrim(UPLOAD_DIR, '/\\') . '/' . uniqid('', true) . '_' . $name;
    if (move_uploaded_file($file['tmp_name'], $target)) {
        return $target;
    }
    return false;
}

function get_uploaded_files() {
    if (!is_dir(UPLOAD_DIR)) return [];
    $entries = scandir(UPLOAD_DIR);
    $files = [];
    foreach ($entries as $e) {
        if ($e === '.' || $e === '..') continue;
        if (is_file(UPLOAD_DIR . '/' . $e)) $files[] = $e;
    }
    return $files;
}
