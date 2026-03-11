<?php
/**
 * ============================================
 * Authopic Technologies PLC - Helper Functions
 * Pure Procedural PHP - No OOP
 * ============================================
 */

if (!defined('BASE_PATH')) {
    exit('Direct access not allowed.');
}

// ============================================
// DATABASE HELPER FUNCTIONS
// ============================================

/**
 * Escape string for SQL
 */
function db_escape($value) {
    global $db;
    return mysqli_real_escape_string($db, trim($value));
}

/**
 * Execute a query and return result
 */
function db_query($sql) {
    global $db;
    $result = mysqli_query($db, $sql);
    if (!$result && DEBUG_MODE) {
        error_log('SQL Error: ' . mysqli_error($db) . ' | Query: ' . $sql);
    }
    return $result;
}

/**
 * Fetch single row as associative array
 */
function db_fetch_one($sql) {
    $result = db_query($sql);
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    return null;
}

/**
 * Fetch all rows as array of associative arrays
 */
function db_fetch_all($sql) {
    $result = db_query($sql);
    $rows = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
    }
    return $rows;
}

/**
 * Get the last insert ID
 */
function db_insert_id() {
    global $db;
    return mysqli_insert_id($db);
}

/**
 * Get affected rows count
 */
function db_affected_rows() {
    global $db;
    return mysqli_affected_rows($db);
}

/**
 * Count rows from a query
 * Accepts either a full SQL query or (table, where) pair
 */
function db_count($table_or_query, $where = '1=1') {
    if (stripos(trim($table_or_query), 'SELECT') === 0) {
        $row = db_fetch_one($table_or_query);
        $key = $row ? array_key_first($row) : null;
        return $key ? (int)$row[$key] : 0;
    }
    $row = db_fetch_one("SELECT COUNT(*) as cnt FROM `" . db_escape($table_or_query) . "` WHERE $where");
    return $row ? (int)$row['cnt'] : 0;
}

// ============================================
// SECURITY FUNCTIONS
// ============================================

/**
 * Sanitize output for HTML
 */
function e($string) {
    return htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSRF token
 */
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Output CSRF hidden field
 */
function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

/**
 * Verify CSRF token
 */
function csrf_verify($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Rate limit check
 */
function rate_limit_check($action, $max_attempts, $cooldown) {
    global $db;
    $ip = db_escape(get_client_ip());
    $action = db_escape($action);
    
    // Clean old entries
    db_query("DELETE FROM `rate_limits` WHERE `action` = '$action' AND `first_attempt` < DATE_SUB(NOW(), INTERVAL $cooldown SECOND)");
    
    $row = db_fetch_one("SELECT * FROM `rate_limits` WHERE `ip_address` = '$ip' AND `action` = '$action'");
    
    if ($row) {
        if ((int)$row['attempts'] >= $max_attempts) {
            return false; // Rate limited
        }
        db_query("UPDATE `rate_limits` SET `attempts` = `attempts` + 1, `last_attempt` = NOW() WHERE `id` = {$row['id']}");
    } else {
        db_query("INSERT INTO `rate_limits` (`ip_address`, `action`, `attempts`) VALUES ('$ip', '$action', 1)");
    }
    
    return true;
}

/**
 * Get client IP address
 */
function get_client_ip() {
    $headers = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
    foreach ($headers as $header) {
        if (!empty($_SERVER[$header])) {
            $ip = explode(',', $_SERVER[$header])[0];
            $ip = trim($ip);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }
    return '0.0.0.0';
}

/**
 * Validate email
 */
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone (Ethiopian format)
 */
function is_valid_phone($phone) {
    $phone = preg_replace('/[\s\-\(\)]/', '', $phone);
    return preg_match('/^(\+251|0)[0-9]{9}$/', $phone);
}

/**
 * Check if honeypot field is filled (spam detection)
 */
function is_spam_honeypot() {
    return !empty($_POST['website_url_hp']);
}

// ============================================
// URL & ROUTING FUNCTIONS
// ============================================

/**
 * Generate a URL from a path
 */
function url($path = '') {
    $path = ltrim($path, '/');
    return SITE_URL . '/' . $path;
}

/**
 * Generate an asset URL
 */
function asset($path) {
    return ASSETS_URL . '/' . ltrim($path, '/');
}

/**
 * Generate upload URL
 */
function upload_url($path) {
    if (empty($path)) return asset('images/placeholder.svg');
    if (strpos($path, 'http') === 0) return $path;
    return UPLOADS_URL . '/' . ltrim($path, '/');
}

/**
 * Redirect to a URL
 */
function redirect($path, $status = 302) {
    $url = (strpos($path, 'http') === 0) ? $path : url($path);
    header("Location: $url", true, $status);
    exit;
}

/**
 * Get current URL path
 */
function current_path() {
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $uri = parse_url($uri, PHP_URL_PATH);
    $base = parse_url(SITE_URL, PHP_URL_PATH);
    if ($base && $base !== '/') {
        $uri = substr($uri, strlen($base));
    }
    return '/' . trim($uri, '/');
}

/**
 * Check if current page matches
 */
function is_active($path) {
    $current = current_path();
    if ($path === '/') return $current === '/';
    return strpos($current, $path) === 0;
}

/**
 * Generate a slug from string
 */
function create_slug($string) {
    $slug = strtolower(trim($string));
    $slug = preg_replace('/[^a-z0-9\-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    return trim($slug, '-');
}

// ============================================
// CONTENT & DISPLAY FUNCTIONS
// ============================================

/**
 * Get a setting value from database
 */
function get_setting($key, $default = '') {
    static $settings_cache = [];
    
    if (empty($settings_cache)) {
        $rows = db_fetch_all("SELECT `setting_key`, `setting_value` FROM `site_settings`");
        foreach ($rows as $row) {
            $settings_cache[$row['setting_key']] = $row['setting_value'];
        }
    }
    
    return $settings_cache[$key] ?? $default;
}

/**
 * Get content based on current language
 */
function get_text($en_text, $am_text = '') {
    $lang = $_SESSION['lang'] ?? 'en';
    if ($lang === 'am' && !empty($am_text)) {
        return $am_text;
    }
    return $en_text;
}

/**
 * Get current language
 */
function current_lang() {
    return $_SESSION['lang'] ?? 'en';
}

/**
 * Truncate text to specified length
 */
function truncate($text, $length = 150, $suffix = '...') {
    $text = strip_tags($text ?? '');
    if (strlen($text) <= $length) return $text;
    return substr($text, 0, $length) . $suffix;
}

/**
 * Format date
 */
function format_date($date, $format = 'M d, Y') {
    if (empty($date)) return '';
    return date($format, strtotime($date));
}

/**
 * Format Ethiopian Birr currency
 */
function format_etb($amount) {
    return number_format((float)$amount, 0, '.', ',') . ' ETB';
}

/**
 * Get navigation menu items
 */
function get_nav_menu($location = 'header') {
    $location = db_escape($location);
    return db_fetch_all("SELECT * FROM `navigation_menus` WHERE `location` = '$location' AND `is_active` = 1 ORDER BY `sort_order` ASC");
}

/**
 * Build hierarchical menu from flat list
 */
function build_menu_tree($items, $parent_id = null) {
    $tree = [];
    foreach ($items as $item) {
        $item_parent = $item['parent_id'] ? (int)$item['parent_id'] : null;
        if ($item_parent === $parent_id) {
            $item['children'] = build_menu_tree($items, (int)$item['id']);
            $tree[] = $item;
        }
    }
    return $tree;
}

// ============================================
// FILE UPLOAD FUNCTIONS
// ============================================

/**
 * Handle file upload
 */
function handle_upload($field_name, $upload_dir = 'images', $allowed_types = null, $max_size = null) {
    if (!isset($_FILES[$field_name]) || $_FILES[$field_name]['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'No file uploaded or upload error.'];
    }
    
    $file = $_FILES[$field_name];
    $allowed_types = $allowed_types ?? ALLOWED_IMAGE_TYPES;
    $max_size = $max_size ?? MAX_IMAGE_SIZE;
    
    // Validate MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime, $allowed_types)) {
        return ['success' => false, 'error' => 'Invalid file type: ' . $mime];
    }
    
    // Validate size
    if ($file['size'] > $max_size) {
        return ['success' => false, 'error' => 'File too large. Maximum size: ' . ($max_size / 1024 / 1024) . 'MB'];
    }
    
    // Validate extension
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'pdf', 'doc', 'docx'];
    if (!in_array($ext, $allowed_exts)) {
        return ['success' => false, 'error' => 'Invalid file extension.'];
    }
    
    // Create upload directory
    $target_dir = UPLOADS_DIR . '/' . $upload_dir;
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    
    // Generate unique filename
    $new_filename = date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $target_path = $target_dir . '/' . $new_filename;
    
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        $relative_path = $upload_dir . '/' . $new_filename;
        
        // Insert into media table
        global $db;
        $admin_id = $_SESSION['admin_id'] ?? 0;
        $original = db_escape($file['name']);
        $file_type = db_escape($ext);
        $file_size = (int)$file['size'];
        $mime_esc = db_escape($mime);
        
        $width = null;
        $height = null;
        if (strpos($mime, 'image/') === 0 && $ext !== 'svg') {
            $dims = getimagesize($target_path);
            if ($dims) {
                $width = $dims[0];
                $height = $dims[1];
            }
        }
        
        $w_sql = $width ? $width : 'NULL';
        $h_sql = $height ? $height : 'NULL';
        
        db_query("INSERT INTO `media` (`filename`, `original_name`, `file_path`, `file_type`, `file_size`, `mime_type`, `width`, `height`, `uploaded_by`) 
                  VALUES ('$new_filename', '$original', '$relative_path', '$file_type', $file_size, '$mime_esc', $w_sql, $h_sql, $admin_id)");
        
        return [
            'success' => true,
            'filename' => $new_filename,
            'path' => $relative_path,
            'url' => upload_url($relative_path),
            'media_id' => db_insert_id()
        ];
    }
    
    return ['success' => false, 'error' => 'Failed to move uploaded file.'];
}

// ============================================
// SESSION & AUTH FUNCTIONS
// ============================================

/**
 * Check if user is logged in as admin
 */
function is_admin_logged_in() {
    return !empty($_SESSION['admin_id']) && !empty($_SESSION['admin_role']);
}

/**
 * Get current admin user data
 */
function get_current_admin() {
    if (!is_admin_logged_in()) return null;
    $id = (int)$_SESSION['admin_id'];
    return db_fetch_one("SELECT `id`, `username`, `email`, `full_name`, `role`, `avatar` FROM `admin_users` WHERE `id` = $id AND `is_active` = 1");
}

/**
 * Check admin permission
 */
function admin_has_permission($required_role) {
    $roles = ['viewer' => 1, 'editor' => 2, 'admin' => 3];
    $user_role = $_SESSION['admin_role'] ?? 'viewer';
    return ($roles[$user_role] ?? 0) >= ($roles[$required_role] ?? 999);
}

/**
 * Require admin authentication
 */
function require_admin_auth($required_role = 'viewer') {
    if (!is_admin_logged_in()) {
        $_SESSION['redirect_after_login'] = current_path();
        redirect('/admin/login');
    }
    if (!admin_has_permission($required_role)) {
        set_flash('error', 'You do not have permission to access this page.');
        redirect('/admin/dashboard');
    }
}

/**
 * Log admin activity
 */
function log_activity($action, $entity_type = null, $entity_id = null, $description = null) {
    $admin_id = (int)($_SESSION['admin_id'] ?? 0);
    $action = db_escape($action);
    $entity_type = $entity_type ? "'" . db_escape($entity_type) . "'" : 'NULL';
    $entity_id = $entity_id ? (int)$entity_id : 'NULL';
    $description = $description ? "'" . db_escape($description) . "'" : 'NULL';
    $ip = db_escape(get_client_ip());
    
    db_query("INSERT INTO `activity_log` (`admin_id`, `action`, `entity_type`, `entity_id`, `description`, `ip_address`) 
              VALUES ($admin_id, '$action', $entity_type, $entity_id, $description, '$ip')");
}

// ============================================
// FLASH MESSAGES
// ============================================

/**
 * Set a flash message
 */
function set_flash($type, $message) {
    $_SESSION['flash'][$type] = $message;
}

/**
 * Get and clear flash messages
 */
function get_flash($type = null) {
    if ($type) {
        $msg = $_SESSION['flash'][$type] ?? null;
        unset($_SESSION['flash'][$type]);
        return $msg;
    }
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

/**
 * Render flash messages HTML
 * Outputs hidden data elements picked up by JS and shown as beautiful toasts.
 */
function render_flash_messages() {
    $messages = get_flash();
    if (empty($messages)) return '';
    $html = '';
    foreach ($messages as $type => $msg) {
        $safeType = htmlspecialchars($type, ENT_QUOTES, 'UTF-8');
        $safeMsg  = htmlspecialchars($msg,  ENT_QUOTES, 'UTF-8');
        $html .= '<div class="js-flash-init" data-type="' . $safeType . '" data-message="' . $safeMsg . '" hidden></div>' . "\n";
    }
    return $html;
}

// ============================================
// ANALYTICS TRACKING
// ============================================

/**
 * Track page view
 */
function track_page_view($page_title = '') {
    // Respect Do Not Track
    if (isset($_SERVER['HTTP_DNT']) && $_SERVER['HTTP_DNT'] == '1') return;
    
    $page_url = db_escape(current_path());
    $page_title = db_escape($page_title);
    $referrer = db_escape($_SERVER['HTTP_REFERER'] ?? '');
    $utm_source = db_escape($_GET['utm_source'] ?? '');
    $utm_medium = db_escape($_GET['utm_medium'] ?? '');
    $utm_campaign = db_escape($_GET['utm_campaign'] ?? '');
    $ip = db_escape(get_client_ip());
    $ua = db_escape($_SERVER['HTTP_USER_AGENT'] ?? '');
    $session_id = db_escape(session_id());
    
    // Detect device type
    $device = 'desktop';
    if (preg_match('/Mobile|Android|iPhone|iPad/i', $ua)) {
        $device = preg_match('/iPad|Tablet/i', $ua) ? 'tablet' : 'mobile';
    }
    
    // Detect browser
    $browser = 'Other';
    if (preg_match('/Chrome/i', $ua)) $browser = 'Chrome';
    elseif (preg_match('/Firefox/i', $ua)) $browser = 'Firefox';
    elseif (preg_match('/Safari/i', $ua)) $browser = 'Safari';
    elseif (preg_match('/Edge/i', $ua)) $browser = 'Edge';
    
    // Detect OS
    $os = 'Other';
    if (preg_match('/Windows/i', $ua)) $os = 'Windows';
    elseif (preg_match('/Mac/i', $ua)) $os = 'macOS';
    elseif (preg_match('/Linux/i', $ua)) $os = 'Linux';
    elseif (preg_match('/Android/i', $ua)) $os = 'Android';
    elseif (preg_match('/iPhone|iPad/i', $ua)) $os = 'iOS';
    
    db_query("INSERT INTO `site_analytics` (`page_url`, `page_title`, `referrer`, `utm_source`, `utm_medium`, `utm_campaign`, `ip_address`, `user_agent`, `device_type`, `browser`, `os`, `session_id`) 
              VALUES ('$page_url', '$page_title', '$referrer', '$utm_source', '$utm_medium', '$utm_campaign', '$ip', '$ua', '$device', '$browser', '$os', '$session_id')");
}

// ============================================
// PAGINATION
// ============================================

/**
 * Generate pagination data
 */
function paginate($total, $per_page, $current_page = null, $base_url = '') {
    if ($current_page === null) {
        $current_page = max(1, (int)($_GET['page'] ?? 1));
    }
    if (empty($base_url)) {
        $base_url = strtok($_SERVER['REQUEST_URI'] ?? '/', '?') ?: '/';
    }
    $total_pages = max(1, ceil($total / $per_page));
    $current_page = max(1, min($current_page, $total_pages));
    $offset = ($current_page - 1) * $per_page;
    
    return [
        'total' => $total,
        'per_page' => $per_page,
        'current_page' => $current_page,
        'total_pages' => $total_pages,
        'offset' => $offset,
        'has_prev' => $current_page > 1,
        'has_next' => $current_page < $total_pages,
        'prev_url' => $current_page > 1 ? $base_url . '?page=' . ($current_page - 1) : null,
        'next_url' => $current_page < $total_pages ? $base_url . '?page=' . ($current_page + 1) : null,
        'base_url' => $base_url
    ];
}

/**
 * Render pagination HTML
 */
function render_pagination($pagination) {
    if ($pagination['total_pages'] <= 1) return '';
    
    $html = '<nav class="flex items-center justify-center gap-2 mt-12">';
    
    // Previous
    if ($pagination['has_prev']) {
        $html .= '<a href="' . e($pagination['prev_url']) . '" class="px-4 py-2 rounded-lg border border-white/10 hover:border-primary/50 hover:bg-primary/10 transition-all duration-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>';
    }
    
    // Page numbers
    for ($i = 1; $i <= $pagination['total_pages']; $i++) {
        if ($i == $pagination['current_page']) {
            $html .= '<span class="px-4 py-2 rounded-lg bg-primary text-white font-medium">' . $i . '</span>';
        } elseif ($i <= 2 || $i >= $pagination['total_pages'] - 1 || abs($i - $pagination['current_page']) <= 2) {
            $html .= '<a href="' . e($pagination['base_url']) . '?page=' . $i . '" class="px-4 py-2 rounded-lg border border-white/10 hover:border-primary/50 hover:bg-primary/10 transition-all duration-300">' . $i . '</a>';
        } elseif ($i == 3 || $i == $pagination['total_pages'] - 2) {
            $html .= '<span class="px-2">...</span>';
        }
    }
    
    // Next
    if ($pagination['has_next']) {
        $html .= '<a href="' . e($pagination['next_url']) . '" class="px-4 py-2 rounded-lg border border-white/10 hover:border-primary/50 hover:bg-primary/10 transition-all duration-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>';
    }
    
    $html .= '</nav>';
    return $html;
}

// ============================================
// EMAIL FUNCTIONS
// ============================================

/**
 * Send email via SMTP (no external library required)
 */
function send_email($to, $subject, $html_body, $from_name = null, $from_email = null) {
    if (!MAIL_ENABLED) {
        error_log("[Mail disabled] To: $to | Subject: $subject");
        return true;
    }

    $from_name  = $from_name  ?? MAIL_FROM_NAME;
    $from_email = $from_email ?? MAIL_FROM_EMAIL;
    $host       = MAIL_HOST;
    $port       = MAIL_PORT;
    $user       = MAIL_USER;
    $pass       = MAIL_PASS;
    $enc        = strtolower(MAIL_ENCRYPTION); // 'tls', 'ssl', or ''

    // Open TCP connection
    $prefix = ($enc === 'ssl') ? 'ssl://' : '';
    $socket = @fsockopen($prefix . $host, $port, $errno, $errstr, 15);
    if (!$socket) {
        error_log("[SMTP] Connect failed to $host:$port — $errstr ($errno)");
        return false;
    }

    $read = function() use ($socket) {
        $data = '';
        while ($line = fgets($socket, 512)) {
            $data .= $line;
            if (substr($line, 3, 1) === ' ') break;
        }
        return $data;
    };
    $write = function($cmd) use ($socket) {
        fputs($socket, $cmd . "\r\n");
    };

    $read(); // 220 greeting
    $write('EHLO ' . (gethostname() ?: 'localhost'));
    $read();

    // Upgrade to TLS for STARTTLS connections (port 587)
    if ($enc === 'tls') {
        $write('STARTTLS');
        $read();
        if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            error_log('[SMTP] STARTTLS failed');
            fclose($socket);
            return false;
        }
        $write('EHLO ' . (gethostname() ?: 'localhost'));
        $read();
    }

    // Authenticate
    $write('AUTH LOGIN');
    $read();
    $write(base64_encode($user));
    $read();
    $write(base64_encode($pass));
    $auth = $read();
    if (strpos($auth, '235') === false) {
        error_log('[SMTP] Auth failed: ' . trim($auth));
        fclose($socket);
        return false;
    }

    // Envelope
    $write("MAIL FROM:<$from_email>"); $read();
    $write("RCPT TO:<$to>");           $read();
    $write('DATA');                    $read();

    // Headers + body
    $enc_name    = '=?UTF-8?B?' . base64_encode($from_name) . '?=';
    $enc_subject = '=?UTF-8?B?' . base64_encode($subject)   . '?=';
    $msg  = "From: $enc_name <$from_email>\r\n";
    $msg .= "To: <$to>\r\n";
    $msg .= "Subject: $enc_subject\r\n";
    $msg .= "MIME-Version: 1.0\r\n";
    $msg .= "Content-Type: text/html; charset=UTF-8\r\n";
    $msg .= "\r\n";
    // Escape lone dots per SMTP spec
    $msg .= preg_replace('/^\./m', '..', $html_body);
    $msg .= "\r\n.";
    $write($msg);
    $read();

    $write('QUIT');
    fclose($socket);
    return true;
}

/**
 * Send notification to admin
 */
function notify_admin($subject, $message) {
    $admin_email = get_setting('site_email', 'admin@authopic.com');
    $html = email_template($subject, $message);
    return send_email($admin_email, $subject, $html);
}

/**
 * Basic email template
 */
function email_template($title, $content) {
    $site_name = get_setting('site_name', 'Authopic Technologies PLC');
    return '<!DOCTYPE html><html><head><meta charset="utf-8"><style>
        body{font-family:Arial,sans-serif;background:#f4f4f4;margin:0;padding:20px;}
        .container{max-width:600px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,0.1);}
        .header{background:linear-gradient(135deg,#0066FF,#06B6D4);color:#fff;padding:30px;text-align:center;}
        .header h1{margin:0;font-size:24px;}
        .body{padding:30px;}
        .footer{background:#f8f8f8;padding:20px;text-align:center;font-size:12px;color:#888;}
    </style></head><body>
    <div class="container">
        <div class="header"><h1>' . e($title) . '</h1></div>
        <div class="body">' . $content . '</div>
        <div class="footer">&copy; ' . date('Y') . ' ' . e($site_name) . '. All rights reserved.</div>
    </div></body></html>';
}

// ============================================
// MISC HELPERS
// ============================================

/**
 * Get JSON from database field safely
 */
function get_json($json_string, $as_array = true) {
    if (empty($json_string)) return $as_array ? [] : null;
    $data = json_decode($json_string, $as_array);
    return $data ?? ($as_array ? [] : null);
}

/**
 * Check if request is AJAX
 */
function is_ajax() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Return JSON response
 */
function json_response($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($data);
    exit;
}

/**
 * Get POST value with default
 */
function post($key, $default = '') {
    return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
}

/**
 * Get GET value with default
 */
function get($key, $default = '') {
    return isset($_GET[$key]) ? trim($_GET[$key]) : $default;
}

/**
 * Generate random string
 */
function random_string($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Time ago helper
 */
function time_ago($datetime) {
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    
    if ($diff->y > 0) return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
    if ($diff->m > 0) return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
    if ($diff->d > 0) return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
    if ($diff->h > 0) return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
    if ($diff->i > 0) return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
    return 'just now';
}
