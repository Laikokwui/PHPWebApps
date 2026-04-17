Simple TOTP backend example (pure PHP)

Files:
- [otp/totp.php](otp/totp.php#L1) — TOTP / HOTP functions.

Quick usage:

1. Generate and store a secret per user:

```php
require 'otp/totp.php';
$secret = generateBase32Secret(); // store in DB
```

2. Verify a code submitted by the user:

```php
require 'otp/totp.php';
if (verifyTotp($userSecretFromDb, $_POST['otp_code'])) {
    // 2FA success
} else {
    // invalid code
}
```

3. To provision an authenticator app, create an `otpauth://` URI:

```
otpauth://totp/ExampleApp:alice@example.com?secret=SECRET&issuer=ExampleApp
```
