<?php
require __DIR__ . '/totp.php';
session_start();

$dataFile = __DIR__ . '/secrets.json';
if (!file_exists($dataFile)) {
    file_put_contents($dataFile, json_encode(new stdClass()));
}
$secrets = json_decode(file_get_contents($dataFile), true) ?: [];

$secret = null;
$otpauth = null;
$issuer = 'ExampleApp';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    if ($username === '') {
        $errors[] = 'Username is required.';
    } else {
        if (empty($secrets[$username])) {
            $secret = generateBase32Secret();
            $secrets[$username] = $secret;
            file_put_contents($dataFile, json_encode($secrets, JSON_PRETTY_PRINT));
        } else {
            $secret = $secrets[$username];
        }
        $label = rawurlencode($username);
        $issuerEnc = rawurlencode($issuer);
        $otpauth = "otpauth://totp/{$issuerEnc}:{$label}?secret={$secret}&issuer={$issuerEnc}&period=30";
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Register TOTP - Example</title>
</head>
<body>
  <h1>Register for TOTP</h1>

  <?php if (!empty($errors)): ?>
    <ul style="color:darkred">
      <?php foreach ($errors as $e): ?>
        <li><?php echo htmlspecialchars($e); ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <form method="post">
    <label>Username: <input type="text" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"></label>
    <button type="submit">Create / Show Secret</button>
  </form>

  <?php if ($secret): ?>
    <h2>Your secret</h2>
    <p><strong><?php echo htmlspecialchars($secret); ?></strong></p>
    <p>Provision URI: <a href="<?php echo htmlspecialchars($otpauth); ?>"><?php echo htmlspecialchars($otpauth); ?></a></p>
    <p>Scan this QR code with an authenticator app:</p>
    <img alt="QR code" src="https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=<?php echo urlencode($otpauth); ?>">
    <p><a href="verify.php">Verify a code</a></p>
  <?php endif; ?>

  <p><a href="index.php">Back to index</a></p>
</body>
</html>
