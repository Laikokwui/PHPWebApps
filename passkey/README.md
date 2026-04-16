# Passkey (WebAuthn) module

This is a minimal scaffold demonstrating WebAuthn (passkey) flows with a PHP backend and browser JS client.

What is included
- `index.php` — small demo UI to register and login
- `webauthn.js` — client helper converting values and calling WebAuthn APIs
- `register_start.php`, `register_finish.php` — registration endpoints
- `login_start.php`, `login_finish.php` — authentication endpoints
- `helpers.php`, `config.php`, `db.php` — simple server utilities and SQLite storage

Notes & limitations
- This is a scaffold for development and learning. Proper attestation and assertion verification (CBOR/COSE parsing, signature verification, trust anchors, replay protection) is NOT implemented here and must be added before using in production.
- Requires HTTPS in browsers (localhost allowed for development).
- Browsers must support WebAuthn; use a modern browser.

Next steps
- Add full attestation/assertion verification (use a proven PHP WebAuthn library if possible).
- Add account recovery and fallback (password) flows.
- Harden session & CSRF protections.
