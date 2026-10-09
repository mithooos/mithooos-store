<?php
// Config values here are kept for legacy compatibility if anything else needs them.
// But Database.php now uses getenv('TURSO_DATABASE_URL') directly.

define('DB_HOST', getenv('TURSO_DATABASE_URL') ?: 'libsql://mithooos-yourusername.turso.io');
define('DB_PORT', 443);
define('DB_NAME', 'mithooos');
define('DB_USER', 'admin');
define('DB_PASS', getenv('TURSO_AUTH_TOKEN') ?: '');
