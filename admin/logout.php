<?php
/**
 * Authopic Technologies PLC - Admin Logout
 */
if (!defined('BASE_PATH')) exit;

if (is_admin_logged_in()) {
    log_activity('logout', 'admin_users', $_SESSION['admin_id'], 'Admin logout');
}

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}
session_destroy();

redirect('/admin/login');
