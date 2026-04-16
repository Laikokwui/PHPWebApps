<?php
require_once __DIR__ . '/helpers.php';
$files = get_uploaded_files();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>File Upload</title>
    <style>body{font-family:Arial,Helvetica,sans-serif;padding:16px}</style>
</head>
<body>
<h1>File Upload</h1>
<form action="upload.php" method="post" enctype="multipart/form-data">
    <label for="file">Choose file</label>
    <input type="file" name="file" id="file" required>
    <button type="submit">Upload</button>
</form>

<h2>Uploaded Files</h2>
<?php if (empty($files)): ?>
    <p>No files uploaded yet.</p>
<?php else: ?>
    <ul>
    <?php foreach ($files as $file): ?>
        <li><a href="uploads/<?= rawurlencode($file) ?>"><?= htmlspecialchars($file) ?></a></li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>
</body>
</html>
