<?php
/**
 * Authopic Technologies PLC - Admin Router
 */
if (!defined('BASE_PATH')) exit;

$admin_page = isset($route_params['route']) ? $route_params['route'] : 'dashboard';
$admin_action = isset($route_params['action']) ? $route_params['action'] : 'list';
$admin_id = isset($route_params['id']) ? (int)$route_params['id'] : 0;

// Public admin pages (no auth required)
$public_pages = ['login', 'logout'];

if (in_array($admin_page, $public_pages)) {
    $file = BASE_PATH . '/admin/' . $admin_page . '.php';
    if (file_exists($file)) {
        require_once $file;
    } else {
        http_response_code(404);
        echo 'Admin page not found.';
    }
    return;
}

// All other pages require authentication
require_admin_auth();

// Valid admin pages
$valid_pages = [
    'dashboard', 'pages', 'products', 'services', 'portfolio',
    'blog', 'team', 'testimonials', 'leads', 'demos',
    'media', 'settings', 'profile', 'analytics', 'subscribers'
];

if (!in_array($admin_page, $valid_pages)) {
    http_response_code(404);
    require_once BASE_PATH . '/admin/includes/header.php';
    echo '<div class="flex items-center justify-center min-h-[50vh]"><div class="text-center"><h1 class="text-3xl font-bold text-slate-800 dark:text-white mb-2">Page Not Found</h1><p class="text-slate-500">The admin page you requested does not exist.</p><a href="' . url('/admin/dashboard') . '" class="inline-block mt-4 text-primary hover:underline">Go to Dashboard</a></div></div>';
    require_once BASE_PATH . '/admin/includes/footer.php';
    return;
}

// Load admin layout + page
require_once BASE_PATH . '/admin/includes/header.php';

$page_file = BASE_PATH . '/admin/' . $admin_page . '.php';
if (file_exists($page_file)) {
    require_once $page_file;
} else {
    echo '<div class="p-8"><h1 class="text-2xl font-bold text-slate-800 dark:text-white">Coming Soon</h1><p class="text-slate-500 mt-2">This module is under development.</p></div>';
}

require_once BASE_PATH . '/admin/includes/footer.php';
