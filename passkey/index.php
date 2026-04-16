<?php
require_once __DIR__ . '/helpers.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Passkey (WebAuthn) Demo</title>
  <style>body{font-family:Arial,Helvetica,sans-serif;padding:16px}</style>
</head>
<body>
  <h1>Passkey (WebAuthn) Demo</h1>
  <label>Username <input id="username" value="demo"></label>
  <button id="registerBtn">Register Passkey</button>
  <button id="loginBtn">Login with Passkey</button>
  <div id="status" style="margin-top:12px;color:green"></div>
  <script src="webauthn.js"></script>
  <script>
    const status = document.getElementById('status');
    document.getElementById('registerBtn').addEventListener('click', async () => {
      const user = document.getElementById('username').value || 'demo';
      try { const r = await passkey.register(user); status.textContent = r.ok ? 'Registered' : JSON.stringify(r); } catch (e) { status.textContent = 'Error: '+e; }
    });
    document.getElementById('loginBtn').addEventListener('click', async () => {
      const user = document.getElementById('username').value || 'demo';
      try { const r = await passkey.login(user); status.textContent = r.ok ? 'Logged in' : JSON.stringify(r); } catch (e) { status.textContent = 'Error: '+e; }
    });
  </script>
</body>
</html>
