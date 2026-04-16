<?php
require_once __DIR__ . '/helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_FILES['file'])) {
        $msg = 'No file uploaded.';
    } else {
        $file = $_FILES['file'];
        if (is_allowed_file($file)) {
            $saved = move_uploaded_file_safe($file);
            if ($saved !== false) {
                $msg = 'Upload successful: ' . basename($saved);
            } else {
                $msg = 'Failed to save uploaded file.';
            }
        } else {
            $msg = 'File not allowed or too large.';
        }
    }
} else {
    header('Location: index.php');
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Upload Result</title>
</head>
<body>
<p><?= htmlspecialchars($msg) ?></p>
<p><a href="index.php">Back</a></p>
</body>
</html>
