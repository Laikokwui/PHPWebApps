<?php
require __DIR__ . '/totp.php';

$dataFile = __DIR__ . '/secrets.json';
$secrets = file_exists($dataFile) ? (json_decode(file_get_contents($dataFile), true) ?: []) : [];

$error = '';
$result = '';
$debug = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $code = trim($_POST['code'] ?? '');
    if ($username === '') {
        $error = 'Username is required.';
    } elseif ($code === '') {
        $error = 'Code is required.';
    } elseif (empty($secrets[$username])) {
        $error = 'No secret for that user. Please register first.';
    } else {
        $secret = $secrets[$username];
        if (verifyTotp($secret, $code)) {
            $result = 'Code valid — authentication successful.';
        } else {
            $result = 'Invalid code.';
        }
        $debug = 'Current TOTP (debug): ' . totp($secret);
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Verify TOTP - Example</title>
</head>
<body>
  <h1>Verify TOTP</h1>

  <?php if ($error): ?>
    <p style="color:darkred"><?php echo htmlspecialchars($error); ?></p>
  <?php endif; ?>

  <?php if ($result): ?>
    <p style="color:green"><?php echo htmlspecialchars($result); ?></p>
  <?php endif; ?>

  <form method="post">
    <label>Username: <input type="text" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"></label><br>
    <label>Code: <input type="text" name="code" value="<?php echo htmlspecialchars($_POST['code'] ?? ''); ?>"></label><br>
    <button type="submit">Verify</button>
  </form>

  <?php if ($debug): ?>
    <p><em><?php echo htmlspecialchars($debug); ?></em></p>
  <?php endif; ?>

  <p><a href="register.php">Register / Provision</a> — <a href="index.php">Back to index</a></p>
</body>
</html>
