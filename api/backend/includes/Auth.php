<?php
/**
 * MITHOOOS — Auth (JWT + brute-force + password reset)
 */
class Auth {
    private Database $db;
    private string $secret;

    public function __construct(Database $db) {
        $this->db     = $db;
        $this->secret = JWT_SECRET;
    }

    /* ── Audit Logging ── */
    public function logAudit(?int $userId, string $action, ?string $entityType = null, ?int $entityId = null): void {
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? null;
        try {
            $this->db->insert(
                'INSERT INTO audit_log (user_id, action, entity_type, entity_id, ip_address, user_agent) VALUES (?,?,?,?,?,?)',
                [$userId, $action, $entityType, $entityId, $ip, $ua]
            );
        } catch (\Throwable $e) {
            error_log('Audit log failed: ' . $e->getMessage());
        }
    }

    /* ── Register ── */
    public function register(string $email, string $password, string $first, string $last): array {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            throw new RuntimeException('Invalid email address.');
        if (strlen($password) < 8)
            throw new RuntimeException('Password must be at least 8 characters.');

        $exists = $this->db->fetchOne('SELECT user_id FROM users WHERE email = ?', [$email]);
        if ($exists) throw new RuntimeException('Email is already registered.');

        $hash  = password_hash($password, PASSWORD_ARGON2ID);
        $token = bin2hex(random_bytes(32));

        $id = $this->db->insert(
            'INSERT INTO users (email, password_hash, first_name, last_name, verification_token) VALUES (?,?,?,?,?)',
            [$email, $hash, $first, $last, $token]
        );

        $user = ['user_id' => $id, 'email' => $email, 'first_name' => $first, 'last_name' => $last];

        try {
            $email_service = new Email();
            $email_service->welcome($user, $token);
        } catch (\Throwable $e) {
            error_log('Welcome email failed: ' . $e->getMessage());
        }

        $isProduction = defined('APP_ENV') && APP_ENV === 'production';
        $devToken     = (!$isProduction) ? $token : null;

        return ['user_id' => $id, 'message' => 'Registration successful. Please verify your email.', '_dev_token' => $devToken];
    }

    /* ── Login (with brute-force protection) ── */
    public function login(string $email, string $password): array {
        $user = $this->db->fetchOne('SELECT * FROM users WHERE email = ?', [$email]);

        if (!$user) {
            password_verify($password, '$argon2id$v=19$m=65536,t=4,p=1$fakesaltfakesalt$fakehashfakehashfakehashfakehash');
            $this->logAudit(null, 'login_failed_invalid_email', 'users', null);
            throw new RuntimeException('Invalid email or password.');
        }

        if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
            $mins = ceil((strtotime($user['locked_until']) - time()) / 60);
            $this->logAudit($user['user_id'], 'login_attempt_locked');
            throw new RuntimeException("Account locked. Try again in {$mins} minute(s).");
        }

        if (!password_verify($password, $user['password_hash'])) {
            $attempts = (int)$user['login_attempts'] + 1;
            if ($attempts >= MAX_LOGIN_ATTEMPTS) {
                $lockedUntil = date('Y-m-d H:i:s', time() + LOCKOUT_MINUTES * 60);
                $this->db->execute(
                    'UPDATE users SET login_attempts = ?, locked_until = ? WHERE user_id = ?',
                    [$attempts, $lockedUntil, $user['user_id']]
                );
                $this->logAudit($user['user_id'], 'account_lockout');
                throw new RuntimeException('Too many failed attempts. Account locked for ' . LOCKOUT_MINUTES . ' minutes.');
            }
            $this->db->execute(
                'UPDATE users SET login_attempts = ? WHERE user_id = ?',
                [$attempts, $user['user_id']]
            );
            $this->logAudit($user['user_id'], 'login_failed');
            throw new RuntimeException('Invalid email or password.');
        }

        if (!$user['is_active']) {
            $this->logAudit($user['user_id'], 'login_failed_inactive');
            throw new RuntimeException('Account is disabled. Contact support.');
        }

        $this->db->execute(
            'UPDATE users SET login_attempts = 0, locked_until = NULL, last_login = CURRENT_TIMESTAMP WHERE user_id = ?',
            [$user['user_id']]
        );

        $token = $this->generateJWT($user);
        $isProduction = defined('APP_ENV') && APP_ENV === 'production';
        setcookie('pp_auth_token', $token, [
            'expires'  => time() + JWT_EXPIRY,
            'path'     => '/',
            'domain'   => '',
            'secure'   => $isProduction,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);

        $this->logAudit($user['user_id'], 'login_success');

        return [
            'user_id'    => $user['user_id'],
            'email'      => $user['email'],
            'first_name' => $user['first_name'],
            'last_name'  => $user['last_name'],
            'user_type'  => $user['user_type'],
        ];
    }

    /* ── Request password reset ── */
    public function requestPasswordReset(string $email): array {
        // Always return success to prevent email enumeration
        $user = $this->db->fetchOne(
            'SELECT user_id, first_name, email FROM users WHERE email = ? AND is_active = TRUE',
            [$email]
        );

        if ($user) {
            $token   = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hour

            $this->db->execute(
                'UPDATE users SET reset_token = ?, reset_expires = ? WHERE user_id = ?',
                [$token, $expires, $user['user_id']]
            );

            // Send email (gracefully fails in local dev without SMTP)
            try {
                $email_service = new Email();
                $email_service->passwordReset($user, $token);
            } catch (\Throwable $e) {
                // Log but don't fail the request
                error_log('Password reset email failed: ' . $e->getMessage());
            }
        }
        $isProduction = defined('APP_ENV') && APP_ENV === 'production';
        $devToken     = (!$isProduction && $user) ? $token : null;

        return [
            'message' => 'If that email exists, a reset link has been sent.',
            // In local dev — expose token so developer can test without SMTP
            '_dev_token' => $devToken,
        ];
    }

    /* ── Validate reset token ── */
    public function validateResetToken(string $token): array|false {
        if (strlen($token) !== 64) return false; // 32 bytes = 64 hex chars

        $user = $this->db->fetchOne(
            'SELECT user_id, email, first_name FROM users
             WHERE reset_token = ?
               AND reset_expires > CURRENT_TIMESTAMP
               AND is_active = TRUE',
            [$token]
        );

        return $user ?: false;
    }

    /* ── Reset password with token ── */
    public function resetPassword(string $token, string $newPassword): array {
        if (strlen($newPassword) < 8)
            throw new RuntimeException('Password must be at least 8 characters.');

        $user = $this->validateResetToken($token);
        if (!$user)
            throw new RuntimeException('Invalid or expired reset link. Please request a new one.');

        $hash = password_hash($newPassword, PASSWORD_ARGON2ID);

        $this->db->execute(
            'UPDATE users
             SET password_hash = ?, reset_token = NULL, reset_expires = NULL,
                 login_attempts = 0, locked_until = NULL
             WHERE user_id = ?',
            [$hash, $user['user_id']]
        );
        $this->logAudit($user['user_id'], 'password_reset_success');

        return ['message' => 'Password updated. You can now sign in with your new password.'];
    }

    /* ── JWT generate ── */
    public function generateJWT(array $user): string {
        $header  = $this->b64(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = $this->b64([
            'user_id'   => $user['user_id'],
            'email'     => $user['email'],
              'user_type' => $user['user_type'],
            'iat'       => time(),
            'exp'       => time() + JWT_EXPIRY,
        ]);
        $sig = $this->b64(hash_hmac('sha256', "$header.$payload", $this->secret, true));
        return "$header.$payload.$sig";
    }

    /* ── JWT verify ── */
    public function verifyJWT(string $token): array|false {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return false;
        [$h, $p, $s] = $parts;
        $expected = $this->b64(hash_hmac('sha256', "$h.$p", $this->secret, true));
        if (!hash_equals($expected, $s)) return false;
        $data = json_decode(base64_decode(strtr($p, '-_', '+/')), true);
        if (!$data || $data['exp'] < time()) return false;
        return $data;
    }

    /* ── Get authenticated user from Authorization header or HttpOnly Cookie ── */
    public function getUser(): array|null {
        $token = $_COOKIE['pp_auth_token'] ?? null;
        if (!$token) {
            $header = $_SERVER['HTTP_AUTHORIZATION']
                ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
                ?? '';
            if (!$header && function_exists('getallheaders')) {
                $headers = getallheaders();
                $header = $headers['Authorization'] ?? $headers['authorization'] ?? '';
            }
            if (preg_match('/Bearer\s+(.+)/i', $header, $m)) {
                $token = $m[1];
            }
        }
        if (!$token) return null;
        $payload = $this->verifyJWT($token);
        if (!$payload) return null;
        return $this->db->fetchOne(
            'SELECT * FROM users WHERE user_id = ? AND is_active = TRUE',
            [$payload['user_id']]
        ) ?: null;
    }
    

    /* ── Logout ── */
    public function logout(): void {
        $user = $this->getUser();
        if ($user) {
            $this->logAudit($user['user_id'], 'logout');
        }
        setcookie('pp_auth_token', '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'domain'   => '',
            'secure'   => defined('APP_ENV') && APP_ENV === 'production',
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
    }

    /* ── Require auth — exits with 401 on failure ── */
    public function require(): array {
        $user = $this->getUser();
        if (!$user) { Response::error('Unauthorized.', 401); exit; }
        return $user;
    }

    /* ── Require admin — exits with 403 if not admin ── */
    public function requireAdmin(): array {
        $user = $this->require();
        if ($user['user_type'] !== 'admin') { Response::error('Forbidden.', 403); exit; }
        return $user;
    }

    /* ── JWT Helpers ── */
    private function b64(array|string $data): string {
        $str = is_array($data) ? json_encode($data, JSON_UNESCAPED_SLASHES) : $data;
        return str_replace(['+','/','='], ['-','_',''], base64_encode($str));
    }
    
    /* ── Profile Update ── */
    public function updateProfile(int $userId, string $first, string $last, string $phone): void {
        $this->db->execute(
            'UPDATE users SET first_name = ?, last_name = ?, phone = ?, updated_at = CURRENT_TIMESTAMP WHERE user_id = ?',
            [$first, $last, $phone, $userId]
        );
        $this->logAudit($userId, 'profile_updated');
    }

    /* ── Change Password ── */
    public function changePassword(int $userId, string $currentPassword, string $newPassword): array {
        $user = $this->db->fetchOne('SELECT password_hash FROM users WHERE user_id = ?', [$userId]);
        if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
            throw new RuntimeException('Current password is incorrect.');
        }
        if (strlen($newPassword) < 8) {
            throw new RuntimeException('New password must be at least 8 characters.');
        }

        $hash = password_hash($newPassword, PASSWORD_ARGON2ID);
        $this->db->execute(
            'UPDATE users SET password_hash = ?, updated_at = CURRENT_TIMESTAMP WHERE user_id = ?',
            [$hash, $userId]
        );
        $this->logAudit($userId, 'password_changed');

        return ['message' => 'Password updated successfully.'];
    }

    /* ── Update Preferences ── */
    public function updatePreferences(int $userId, bool $newsletter, bool $orderNotif, bool $restockAlerts): void {
        $this->db->execute(
            'UPDATE users SET prefs_newsletter = ?, prefs_order_notifications = ?, prefs_restock_alerts = ?, updated_at = CURRENT_TIMESTAMP WHERE user_id = ?',
            [$newsletter ? 1 : 0, $orderNotif ? 1 : 0, $restockAlerts ? 1 : 0, $userId]
        );
        $this->logAudit($userId, 'preferences_updated');
    }

    /* ── Deactivate Account ── */
    public function deactivateAccount(int $userId): void {
        $this->db->execute(
            'UPDATE users SET is_active = FALSE, updated_at = CURRENT_TIMESTAMP WHERE user_id = ?',
            [$userId]
        );
        $this->logAudit($userId, 'account_deactivated');
        $this->logout();
    }
}
