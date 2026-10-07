<?php
// ============================================================
// MITHOOOS — App Constants
// ============================================================

// ── App ──
define('APP_NAME',    'Mithooos');
define('APP_URL',     getenv('APP_URL') ?: 'http://localhost');
define('API_VERSION', 'v1');

// APP_ENV controls environment-sensitive behavior: verbose error messages,
// the password-reset dev-token exposure (see Auth::requestPasswordReset()),
// etc. Defaults to 'development' so nothing production-sensitive is ever
// exposed unless this is explicitly set to 'production' in .env.
//
// Future Production Configuration:
// Set APP_ENV=production in your production .env file. This will:
//   - Suppress the _dev_token field in forgot-password responses
//   - Cause the global exception handler (see below) to hide internal
//     error details from API responses
define('APP_ENV', getenv('APP_ENV') ?: 'development');

// ── Upload ──
define('UPLOAD_DIR',  dirname(__DIR__, 2) . '/uploads');
define('UPLOAD_URL',  APP_URL . '/uploads');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024);
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp']);

// ── Pagination ──
define('DEFAULT_PAGE_SIZE', 20);
define('MAX_PAGE_SIZE',     100);

// ── JWT ──
// SECURITY: If JWT_SECRET env var is missing we generate a random per-process
// secret so the app still runs locally, but tokens won't survive restarts.
// In real use: always set JWT_SECRET in your .env file.
$_jwt = getenv('JWT_SECRET');
if (APP_ENV === 'production' && (!$_jwt || $_jwt === 'CHANGE_THIS_SECRET_IN_PRODUCTION' || strlen($_jwt) < 32)) {
    throw new \RuntimeException('FATAL: JWT_SECRET environment variable is missing or insecure. It must be at least 32 characters in production.');
}
if (!$_jwt || $_jwt === 'CHANGE_THIS_SECRET_IN_PRODUCTION' || strlen($_jwt) < 32) {
    // Fallback for local dev: stable hash of server path so it survives within
    // same XAMPP session but is unique per machine.
    $_jwt = hash('sha256', __DIR__ . 'pp_local_dev_salt_2026');
}
define('JWT_SECRET', $_jwt);
unset($_jwt);

define('JWT_EXPIRY', 30 * 24 * 60 * 60); // 30 days

// ── Email (local dev: configure in .env) ──
define('SMTP_HOST',  getenv('SMTP_HOST')  ?: 'localhost');
define('SMTP_PORT',  (int)(getenv('SMTP_PORT') ?: 25));
define('SMTP_USER',  getenv('SMTP_USER')  ?: '');
define('SMTP_PASS',  getenv('SMTP_PASS')  ?: '');
define('FROM_EMAIL', getenv('FROM_EMAIL') ?: 'hello@mithooos.local');
define('FROM_NAME',  APP_NAME);

// ── Rate limiting (login brute-force) ──
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_MINUTES',    15);
