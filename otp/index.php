<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>OTP Demo — Starting Point</title>
  <style>
    body { font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; max-width:900px; margin:2rem auto; padding:0 1rem; line-height:1.5; color:#111; }
    header { display:flex; align-items:center; justify-content:space-between; gap:1rem; }
    h1 { margin:0; font-size:1.25rem; }
    .card { border:1px solid #e6e6e6; padding:1rem; border-radius:8px; box-shadow:0 1px 2px rgba(0,0,0,0.03); }
    pre { background:#f7f7f7; padding:0.75rem; border-radius:6px; overflow:auto; }
    a.button { display:inline-block; background:#0366d6; color:white; padding:0.5rem 0.75rem; border-radius:6px; text-decoration:none; }
    a.button.secondary { background:#28a745; }
    small { color:#666; }
  </style>
</head>
<body>
  <header>
    <h1>OTP Demo (pure PHP) — Starting Point</h1>
    <nav>
      <a class="button" href="register.php">Register</a>
      <a class="button secondary" href="verify.php">Verify</a>
    </nav>
  </header>

  <p>This is a minimal demo of TOTP/HOTP implemented in pure PHP. Use the links above to register a user (generate a Base32 secret and otpauth URI) and to verify codes from your authenticator app.</p>

  <h2>Quick start</h2>
  <ol>
    <li>Run the PHP built-in server in the <code>otp</code> folder:
      <pre><code>php -S localhost:8000 -t otp</code></pre>
      Open <a href="http://localhost:8000">http://localhost:8000</a> in your browser.</li>
    <li>Go to <a href="register.php">Register</a>, enter a username, and scan the QR with Google Authenticator or Authy.</li>
    <li>Go to <a href="verify.php">Verify</a>, enter the username and code from your app.</li>
  </ol>

  <h2>Files</h2>
  <ul>
    <li><a href="totp.php">totp.php</a> — TOTP/HOTP functions.</li>
    <li><a href="register.php">register.php</a> — UI to create/store secret and show otpauth URI + QR.</li>
    <li><a href="verify.php">verify.php</a> — UI to submit username + code for verification.</li>
    <li><a href="secrets.json">secrets.json</a> — simple JSON store used by the demo.</li>
    <li>See the docs: <a href="README.md">README</a>.</li>
  </ul>

  <h2>Security notes</h2>
  <ul>
    <li>This demo stores secrets in a JSON file for simplicity — do not use this in production.</li>
    <li>Use secure storage (encrypted DB) and HTTPS in real deployments.</li>
  </ul>

  <footer><small>Created for demo. Edit the files in the <strong>otp</strong> folder to adapt.</small></footer>
</body>
</html>
