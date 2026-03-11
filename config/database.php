<?php
/**
 * ============================================
 * Authopic Technologies PLC - Database Configuration
 * Loads settings from .env (local or production)
 * ============================================
 */

// Prevent direct access
if (!defined('BASE_PATH')) {
    exit('Direct access not allowed.');
}

// ============================================
// Load .env files (.env, then .env.production overrides)
// ============================================
function _load_env_file($path) {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $_line) {
        $_line = trim($_line);
        if ($_line === '' || $_line[0] === '#') continue;
        if (strpos($_line, '=') === false) continue;
        list($_key, $_val) = explode('=', $_line, 2);
        $_key = trim($_key);
        $_val = trim($_val);
        // Strip inline comments (e.g.  value   # comment)
        if (($p = strpos($_val, ' #')) !== false) {
            $_val = trim(substr($_val, 0, $p));
        }
        // Strip surrounding quotes
        if (strlen($_val) >= 2) {
            $_f = $_val[0]; $_l = $_val[strlen($_val) - 1];
            if (($_f === '"' && $_l === '"') || ($_f === "'" && $_l === "'")) {
                $_val = substr($_val, 1, -1);
            }
        }
        // Always override — later files win
        $_ENV[$_key] = $_val;
        putenv("$_key=$_val");
    }
}
_load_env_file(BASE_PATH . '/.env');
_load_env_file(BASE_PATH . '/.env.production');

/**
 * Read a value from the loaded .env / server environment.
 * Falls back to $default when the key is not set.
 */
function env(string $key, $default = null) {
    if (isset($_ENV[$key])) return $_ENV[$key];
    $v = getenv($key);
    return ($v !== false) ? $v : $default;
}

// ============================================
// Database Credentials
// ============================================
define('DB_HOST',    env('DB_HOST', 'localhost'));
define('DB_NAME',    env('DB_NAME', 'authopic_db'));
define('DB_USER',    env('DB_USER', 'root'));
define('DB_PASS',    env('DB_PASS', ''));
define('DB_CHARSET', 'utf8mb4');

// ============================================
// Site Configuration
// ============================================
define('SITE_URL',     rtrim(env('SITE_URL', 'https://authopic.com'), '/'));
define('SITE_NAME',    env('SITE_NAME', 'Authopic Technologies PLC'));
define('SITE_VERSION', '1.0.0');

// ============================================
// File Paths
// ============================================
define('UPLOADS_DIR', BASE_PATH . '/uploads');
define('UPLOADS_URL', SITE_URL . '/uploads');
define('ASSETS_URL',  SITE_URL . '/assets');

// ============================================
// Session Configuration
// ============================================
define('SESSION_LIFETIME', 1800); // 30 minutes
define('SESSION_NAME', env('SESSION_NAME', 'Authopic_SESSION'));

// ============================================
// File Upload Limits
// ============================================
define('MAX_IMAGE_SIZE', 5 * 1024 * 1024); // 5MB
define('MAX_PDF_SIZE',   20 * 1024 * 1024); // 20MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml']);
define('ALLOWED_DOC_TYPES',   ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);

// ============================================
// Rate Limiting
// ============================================
define('LOGIN_MAX_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900);  // 15 minutes
define('FORM_MAX_SUBMISSIONS', 10);
define('FORM_COOLDOWN_TIME', 3600); // 1 hour

// ============================================
// Email Configuration (SMTP)
// ============================================
define('MAIL_ENABLED',    filter_var(env('MAIL_ENABLED', 'false'), FILTER_VALIDATE_BOOLEAN));
define('MAIL_HOST',       env('MAIL_HOST', 'smtp.gmail.com'));
define('MAIL_PORT',       (int) env('MAIL_PORT', '587'));
define('MAIL_ENCRYPTION', env('MAIL_ENCRYPTION', 'tls'));
define('MAIL_USER',       env('MAIL_USER', ''));
define('MAIL_PASS',       env('MAIL_PASS', ''));
define('MAIL_FROM_NAME',  env('MAIL_FROM_NAME',  'Authopic Technologies PLC'));
define('MAIL_FROM_EMAIL', env('MAIL_FROM_EMAIL', 'noreply@authopic.com'));

// ============================================
// Debug Mode (set APP_DEBUG=false in production)
// ============================================
define('DEBUG_MODE', filter_var(env('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN));

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// ============================================
// Database Connection
// ============================================
$db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$db) {
    if (DEBUG_MODE) {
        die('Database connection failed: ' . mysqli_connect_error());
    } else {
        die('Service temporarily unavailable. Please try again later.');
    }
}

mysqli_set_charset($db, DB_CHARSET);
mysqli_query($db, "SET time_zone = '+03:00'");
