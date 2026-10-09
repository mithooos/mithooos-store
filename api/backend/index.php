<?php
/**
 * MITHOOOS — API Router
 * Entry point for all /backend/api/* requests
 */

declare(strict_types=1);

// ── Load .env for local development ──────────────────────────
$_envFile = dirname(__DIR__, 2) . '/.env';
if (file_exists($_envFile)) {
    foreach (file($_envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $_envLine) {
        if (str_starts_with(trim($_envLine), '#') || !str_contains($_envLine, '=')) continue;
        [$_k, $_v] = explode('=', $_envLine, 2);
        if (!getenv(trim($_k))) putenv(trim($_k) . '=' . trim($_v));
    }
}
unset($_envFile, $_envLine, $_k, $_v);


// ── CORS ─────────────────────────────────────────────────────
$allowed_origins = ['http://localhost', 'http://localhost:3000', 'https://mithooos.com'];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed_origins, true)) {
    header("Access-Control-Allow-Origin: $origin");
}
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Session-ID, X-CSRF-Token');
header('Access-Control-Max-Age: 86400');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

// ── Bootstrap ────────────────────────────────────────────────
require_once __DIR__ . '/config/constants.php';   // 1. App constants (JWT_SECRET, etc.)
require_once __DIR__ . '/config/database.php';    // 2. DB_* connection constants
require_once __DIR__ . '/includes/Database.php';  // 3. Database class (uses DB_* constants)
require_once __DIR__ . '/includes/Response.php';
require_once __DIR__ . '/includes/CSRF.php';
require_once __DIR__ . '/includes/Auth.php';
require_once __DIR__ . '/includes/Validator.php';
require_once __DIR__ . '/includes/Pagination.php';
require_once __DIR__ . '/includes/Product.php';
require_once __DIR__ . '/includes/Cart.php';
require_once __DIR__ . '/includes/Inventory.php';
require_once __DIR__ . '/includes/Order.php';
require_once __DIR__ . '/includes/Email.php';
require_once __DIR__ . '/includes/Blog.php';

// ── Global error handler ──
// RuntimeException is used deliberately throughout this codebase (Auth,
// Order, etc.) for safe, user-facing messages — e.g. "Invalid email or
// password", "Account is locked". Those are fine to return as-is.
//
// Anything else (PDOException, TypeError, base Error, etc.) is unexpected
// and may contain internal details — SQL fragments, table/column names,
// file paths — that shouldn't reach the client. Always log the real message
// server-side; only decide what the *client* sees based on environment.
set_exception_handler(function (\Throwable $e) {
    error_log(sprintf(
        '[%s] Uncaught %s: %s in %s:%d',
        date('c'), get_class($e), $e->getMessage(), $e->getFile(), $e->getLine()
    ));

    if ($e instanceof \RuntimeException) {
        Response::error($e->getMessage(), 400);
        return;
    }

    // Future Production Configuration:
    // In development we still show the real message to speed up local
    // debugging. Once APP_ENV=production is set, this always returns a
    // generic message instead — see backend/config/constants.php.
    $isProduction = APP_ENV === 'production';
    $message = $isProduction ? 'An unexpected error occurred. Please try again.' : $e->getMessage();
    Response::error($message, 500);
});

$db   = Database::getInstance();
$auth = new Auth($db);

// ── Parse route ──────────────────────────────────────────────
$uri    = $_GET['_url'] ?? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri    = preg_replace('#^/?(?:[^/]+/)*backend(?:/index\.php)?#', '', $uri);
$uri    = $uri === '' ? '/' : $uri;
$method = $_SERVER['REQUEST_METHOD'];
$body   = json_decode(file_get_contents('php://input'), true) ?? [];
$query  = $_GET;

// ── Router ───────────────────────────────────────────────────
$segments = array_values(array_filter(explode('/', trim($uri, '/'))));
$resource = $segments[0] ?? '';
$sub      = $segments[1] ?? '';
$id       = $segments[2] ?? null;

// Catch numeric second segment as ID
if (is_numeric($sub)) { $id = $sub; $sub = ''; }

// ── CSRF protection ──
// /js/api-client.js has always correctly fetched a token from
// GET /api/csrf-token and sent it back as X-CSRF-Token on every
// state-changing request — this was previously the unused half of that
// round trip. Scope carefully:
//   - Only state-changing methods; GETs never need a CSRF check.
//   - Skip the csrf-token endpoint itself (issuing a token sets the cookie
//     that later requests are validated against).
//   - Skip /admin — the admin panel's JS (admin-panel/js/*.js) has no
//     CSRF-token handling at all; it authenticates purely via Bearer JWT
//     from an already-locked-down origin. Wiring this in for admin routes
//     without first updating that JS would break every admin write
//     operation. Left as a known follow-up rather than silently included.
$stateChanging = in_array($method, ['POST', 'PUT', 'DELETE', 'PATCH'], true);
$isCsrfTokenEndpoint = $resource === 'api' && $sub === 'csrf-token';
$isAuthEndpoint = $resource === 'api' && $sub === 'auth';
$isAdminEndpoint = $resource === 'admin';

// ── Rate Limiting (for Auth) ──
if ($resource === 'api' && $sub === 'auth' && $stateChanging) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $db->execute("UPDATE rate_limits SET requests = 0, locked_until = NULL WHERE (locked_until IS NOT NULL AND locked_until < datetime('now')) OR (locked_until IS NULL AND updated_at < datetime('now', '-15 minutes'))");
    $limit = $db->fetchOne('SELECT * FROM rate_limits WHERE ip_address = ?', [$ip]);
    if ($limit) {
        if ($limit['locked_until'] && strtotime($limit['locked_until']) > time()) {
            Response::error('Too many requests. Please try again later.', 429);
            exit;
        }
        $requests = $limit['requests'] + 1;
        if ($requests > 20) {
            $db->execute("UPDATE rate_limits SET requests = ?, locked_until = datetime('now', '+15 minutes'), updated_at = datetime('now') WHERE ip_address = ?", [$requests, $ip]);
            Response::error('Too many requests. Please try again later.', 429);
            exit;
        }
        $db->execute("UPDATE rate_limits SET requests = ?, updated_at = datetime('now') WHERE ip_address = ?", [$requests, $ip]);
    } else {
        $db->insert('INSERT INTO rate_limits (ip_address, requests) VALUES (?, 1)', [$ip]);
    }
}

if ($stateChanging && !$isCsrfTokenEndpoint && !$isAuthEndpoint) {
    CSRF::require();
}

match (true) {

    /* ── CSRF TOKEN (GET — returns a fresh signed token) ── */
    $resource === 'api' && $sub === 'csrf-token' => (function() {
        $token = CSRF::token();
        Response::success(['token' => $token], 'CSRF token issued.');
    })(),

    /* ── AUTH ── */
    $resource === 'api' && $sub === 'auth' => (function() use ($method, $body, $db, $auth, $segments) {
        require __DIR__ . '/api/auth.php';
    })(),

    /* ── PRODUCTS ── */
    $resource === 'api' && $sub === 'products' => (function() use ($method, $body, $query, $id, $db, $auth) {
        require __DIR__ . '/api/products.php';
    })(),

    /* ── CATEGORIES ── */
    $resource === 'api' && $sub === 'categories' => (function() use ($method, $db) {
        $cats = $db->fetchAll(
            "SELECT category_id, category_name, slug, description, parent_category_id, icon_url,
                    CASE
                        WHEN cover_image_url LIKE 'data:image/%'
                        THEN '/backend/api/image.php?type=category&id=' || category_id
                        ELSE cover_image_url
                    END AS cover_image_url,
                    display_order, is_active, created_at, updated_at
             FROM categories
             WHERE is_active = TRUE
             ORDER BY display_order"
        );
        Response::success($cats);
    })(),

    /* ── COLLECTIONS ── */
    $resource === 'api' && $sub === 'collections' => (function() use ($method, $body, $query, $id, $db, $auth) {
        require __DIR__ . '/api/collections.php';
    })(),

    /* ── CART ── */
    $resource === 'api' && $sub === 'cart' => (function() use ($method, $body, $id, $db, $auth) {
        require __DIR__ . '/api/cart.php';
    })(),

    /* ── ORDERS ── */
    $resource === 'api' && $sub === 'orders' => (function() use ($method, $body, $query, $id, $db, $auth) {
        require __DIR__ . '/api/orders.php';
    })(),

    /* ── INVENTORY ── */
    $resource === 'api' && $sub === 'inventory' => (function() use ($method, $body, $query, $id, $db, $auth) {
        $pathParts = explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
        require __DIR__ . '/api/inventory.php';
    })(),

    /* ── SAVED ADDRESSES ── */
    $resource === 'api' && $sub === 'addresses' => (function() use ($method, $body, $id, $db, $auth) {
        require __DIR__ . '/api/addresses.php';
    })(),

    /* ── REVIEWS ── */
    $resource === 'api' && $sub === 'reviews' => (function() use ($method, $body, $query, $id, $db, $auth) {
        require __DIR__ . '/api/reviews.php';
    })(),

    /* ── WISHLIST ── */
    $resource === 'api' && $sub === 'wishlist' => (function() use ($method, $body, $id, $db, $auth) {
        require __DIR__ . '/api/wishlist.php';
    })(),

    /* ── BLOG ── */
    $resource === 'api' && $sub === 'blog' => (function() use ($method, $query, $id, $db, $auth) {
        require __DIR__ . '/api/blog.php';
    })(),

    /* ── SEARCH ── */
    $resource === 'api' && $sub === 'search' => (function() use ($query, $db) {
        $q = trim($query['q'] ?? '');

        // Enforce server-side minimum (client may be bypassed)
        if (strlen($q) < 2) { Response::success([]); return; }

        // Hard cap — prevent enormous queries
        $q = mb_substr($q, 0, 100);

        // Escape LIKE special chars so a search for "%" doesn't match everything
        $safe = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $q);

        $results = $db->fetchAll(
            "SELECT product_id, product_name, slug,
             ROUND(price - (price * discount_percentage / 100), 2) AS final_price,
             (SELECT image_url FROM product_images
              WHERE product_id = p.product_id AND is_primary = TRUE LIMIT 1) AS image
             FROM products p
             WHERE is_active = TRUE
               AND (product_name LIKE ? OR description LIKE ?)
             ORDER BY
               CASE WHEN product_name LIKE ? THEN 0 ELSE 1 END,
               rating DESC
             LIMIT 10",
            ["%{$safe}%", "%{$safe}%", "{$safe}%"]
        );

        Response::success($results);
    })(),

    /* ── UPLOAD PAYMENT PROOF ── */
    $resource === 'api' && $sub === 'upload-payment' => (function() use ($method, $auth) {
        require __DIR__ . '/api/upload_/payment';
    })(),

    /* ── NEWSLETTER ── */
    $resource === 'api' && $sub === 'newsletter' => (function() use ($method, $body, $db) {
        if ($method !== 'POST') { Response::error('Method not allowed.', 405); return; }
        $email = trim($body['email'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { Response::error('Invalid email address.'); return; }
        $exists = $db->fetchOne('SELECT subscriber_id FROM newsletter_subscribers WHERE email = ?', [$email]);
        if (!$exists) {
            $db->insert('INSERT INTO newsletter_subscribers (email, first_name) VALUES (?,?)', [$email, $body['first_name'] ?? null]);
        } else {
            $db->execute('UPDATE newsletter_subscribers SET is_active = TRUE WHERE email = ?', [$email]);
        }
        Response::success(null, 'Subscribed successfully!');
    })(),

    /* ── CONTACT FORM ── */
    $resource === 'api' && $sub === 'contact' => (function() use ($method, $body, $db) {
        if ($method !== 'POST') { Response::error('Method not allowed.', 405); return; }
        $v = Validator::make($body)
            ->required('name')->required('email')->required('subject')->required('message');
        if ($v->fails()) { Response::error('Please fill in all required fields.', 422, $v->errors()); return; }

        $email = filter_var(trim($body['email']), FILTER_VALIDATE_EMAIL);
        if (!$email) { Response::error('Invalid email address.'); return; }

        $db->insert(
            'INSERT INTO contact_messages (name, email, subject, message) VALUES (?,?,?,?)',
            [trim($body['name']), $email, trim($body['subject']), trim($body['message'])]
        );
        Response::success(null, "Message sent! We'll get back to you within 24 hours.");
    })(),

    /* ── PUBLIC SETTINGS ── */
    $resource === 'api' && $sub === 'settings' => (function() use ($method, $db) {
        $keys = ['promo_end_date', 'free_shipping_threshold']; // safe public keys
        $q = implode(',', array_fill(0, count($keys), '?'));
        $rows = $db->fetchAll("SELECT setting_key, setting_val FROM settings WHERE setting_key IN ($q)", $keys);
        $settings = [];
        foreach ($rows as $r) {
            $settings[$r['setting_key']] = $r['setting_val'];
        }
        Response::success($settings);
    })(),

    /* ── ADMIN ── */
    $resource === 'admin' => (function() use ($method, $body, $query, $sub, $id, $db, $auth) {
        require __DIR__ . '/admin/router.php';
    })(),

    /* ── 404 ── */
    default => Response::error("Endpoint not found: $uri", 404),
};
