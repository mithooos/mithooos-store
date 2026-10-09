<?php
/**
 * MITHOOOS — CSRF Protection
 *
 * Strategy: Double-Submit Cookie pattern.
 * 1. On any GET, we issue a signed CSRF token in a cookie.
 * 2. On POST/PUT/DELETE the client must echo that token in the
 *    X-CSRF-Token header (or _csrf body field).
 * 3. We verify header value === cookie value (both signed with JWT_SECRET).
 *
 * For a pure JS/SPA front-end using Bearer JWT this is secondary
 * protection — JWT already protects endpoints. But it defends
 * against CSRF in browsers that auto-attach cookies.
 *
 * State-changing endpoints that require auth (Bearer) are already
 * protected because cross-origin requests cannot set Authorization.
 * CSRF tokens add protection for any cookie-based session path.
 */
class CSRF
{
    private static string $cookieName  = 'pp_csrf';
    private static string $headerName  = 'HTTP_X_CSRF_TOKEN';
    private static string $fieldName   = '_csrf';
    private static int    $expiry      = 3600; // 1 hour

    /* ── Generate a signed token ── */
    public static function generate(): string
    {
        $nonce     = bin2hex(random_bytes(16));
        $timestamp = time();
        $sig       = hash_hmac('sha256', $nonce . '|' . $timestamp, JWT_SECRET);
        $token     = base64_encode($nonce . '|' . $timestamp . '|' . $sig);

        setcookie(
            self::$cookieName,
            $token,
            [
                'expires'  => time() + self::$expiry,
                'path'     => '/',
                'httponly' => false,   // JS must read it to send in header
                'samesite' => 'Lax',   // Lax blocks cross-site POST
                'secure'   => isset($_SERVER['HTTPS']),
            ]
        );

        return $token;
    }

    /* ── Validate the token from header or body ── */
    public static function validate(): bool
    {
        // Retrieve token from X-CSRF-Token header, fallback to body field
        $submitted = $_SERVER[self::$headerName]
            ?? (json_decode(file_get_contents('php://input'), true)[self::$fieldName] ?? '');

        $cookie = $_COOKIE[self::$cookieName] ?? '';

        if (!$submitted || !$cookie || !hash_equals($cookie, $submitted)) {
            return false;
        }

        // Verify signature and expiry
        $decoded = base64_decode($submitted);
        $parts   = explode('|', $decoded);
        if (count($parts) !== 3) return false;

        [$nonce, $timestamp, $sig] = $parts;
        $expected = hash_hmac('sha256', $nonce . '|' . $timestamp, JWT_SECRET);
        if (!hash_equals($expected, $sig)) return false;
        if ((time() - (int)$timestamp) > self::$expiry) return false;

        return true;
    }

    /* ── Require valid CSRF — exits with 403 on failure ── */
    public static function require(): void
    {
        if (!self::validate()) {
            Response::error('Invalid or missing CSRF token.', 403);
            exit;
        }
    }

    /* ── Issue a fresh token and return it (for GET /csrf-token endpoint) ── */
    public static function token(): string
    {
        return self::generate();
    }
}
