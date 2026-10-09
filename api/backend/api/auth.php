<?php
$action = $segments[2] ?? '';
match ($method . ':' . $action) {
    'POST:register' => (function() use ($body, $auth) {
        $v = Validator::make($body)->required('email')->email('email')->required('password')->min('password',8)->required('first_name')->required('last_name');
        if ($v->fails()) { Response::error('Validation failed.',422,$v->errors()); return; }
        $r = $auth->register($body['email'],$body['password'],$body['first_name'],$body['last_name']);
        Response::success($r,'Registration successful.',201);
    })(),

    'POST:login' => (function() use ($body, $auth, $db) {
        $v = Validator::make($body)->required('email')->email('email')->required('password');
        if ($v->fails()) { Response::error('Validation failed.',422,$v->errors()); return; }
        $r = $auth->login($body['email'],$body['password']);
        
        // Merge guest cart if session_id is provided in headers
        $sessionId = $_SERVER['HTTP_X_SESSION_ID'] ?? null;
        if ($sessionId && isset($r['user']['user_id'])) {
            $cart = new Cart($db, (int)$r['user']['user_id']);
            $cart->mergeSession($sessionId);
        }
        
        Response::success($r,'Login successful.');
    })(),

    'GET:me' => (function() use ($auth) {
        $u = $auth->require();
        unset($u['password_hash'],$u['verification_token'],$u['reset_token']);
        Response::success($u);
    })(),

    'POST:logout' => (function() use ($auth) {
        $auth->logout();
        Response::success(null,'Logged out.');
    })(),

    /* ── Password reset — request link ── */
    'POST:forgot-password' => (function() use ($body, $auth) {
        $email = filter_var(trim($body['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        if (!$email) { Response::error('Invalid email address.'); return; }
        $result = $auth->requestPasswordReset($email);
        Response::success($result, $result['message']);
    })(),

    /* ── Password reset — validate token (GET) ── */
    'GET:reset-password' => (function() use ($auth) {
        $token = $_GET['token'] ?? '';
        $user  = $auth->validateResetToken($token);
        if (!$user) { Response::error('Invalid or expired reset token.', 400); return; }
        Response::success(['email' => $user['email']], 'Token valid.');
    })(),

    /* ── Password reset — set new password ── */
    'POST:reset-password' => (function() use ($body, $auth) {
        $v = Validator::make($body)->required('token')->required('password')->min('password', 8);
        if ($v->fails()) { Response::error('Validation failed.', 422, $v->errors()); return; }
        if (($body['password'] ?? '') !== ($body['password_confirm'] ?? '')) {
            Response::error('Passwords do not match.'); return;
        }
        $result = $auth->resetPassword($body['token'], $body['password']);
        Response::success(null, $result['message']);
    })(),

    /* ── Account Settings ── */
    'PUT:me' => (function() use ($body, $auth) {
        $u = $auth->require();
        $v = Validator::make($body)->required('first_name')->required('last_name');
        if ($v->fails()) { Response::error('Validation failed.', 422, $v->errors()); return; }
        $auth->updateProfile($u['user_id'], $body['first_name'], $body['last_name'], $body['phone'] ?? '');
        Response::success(null, 'Profile updated.');
    })(),

    'PUT:change-password' => (function() use ($body, $auth) {
        $u = $auth->require();
        $v = Validator::make($body)->required('current_password')->required('new_password')->min('new_password', 8);
        if ($v->fails()) { Response::error('Validation failed.', 422, $v->errors()); return; }
        $result = $auth->changePassword($u['user_id'], $body['current_password'], $body['new_password']);
        Response::success(null, $result['message']);
    })(),

    'PUT:preferences' => (function() use ($body, $auth) {
        $u = $auth->require();
        $auth->updatePreferences(
            $u['user_id'],
            !empty($body['newsletter']),
            !empty($body['order_notifications']),
            !empty($body['restock_alerts'])
        );
        Response::success(null, 'Preferences updated.');
    })(),

    'DELETE:me' => (function() use ($auth) {
        $u = $auth->require();
        $auth->deactivateAccount($u['user_id']);
        Response::success(null, 'Account deleted.');
    })(),

    default => Response::error('Auth endpoint not found.',404),
};
