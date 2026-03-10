<?php
/**
 * ============================================
 * Authopic Technologies PLC - Main Router / Front Controller
 * All requests are routed through this file
 * ============================================
 */

// Define base path
define('BASE_PATH', __DIR__);

// Load configuration
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/includes/functions.php';

// ============================================
// Session Management
// ============================================
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_samesite', 'Lax');
    session_name(SESSION_NAME);
    session_start();
}

// Set default language
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'en';
}

// Handle language switch
if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'am'])) {
    $_SESSION['lang'] = $_GET['lang'];
    setcookie('lang', $_GET['lang'], time() + (365 * 24 * 60 * 60), '/');
} elseif (isset($_COOKIE['lang']) && !isset($_SESSION['lang_set'])) {
    $_SESSION['lang'] = $_COOKIE['lang'];
    $_SESSION['lang_set'] = true;
}

// ============================================
// Parse URL
// ============================================
$request_uri = current_path();
$segments = array_filter(explode('/', $request_uri));
$segments = array_values($segments);

// Remove query string artifacts
$path = $request_uri;

// ============================================
// Route Matching
// ============================================
$route_file = null;
$route_params = [];

// Admin routes
if (isset($segments[0]) && $segments[0] === 'admin') {
    $admin_route = $segments[1] ?? 'dashboard';
    $admin_action = $segments[2] ?? 'index';
    $admin_id = $segments[3] ?? null;
    
    $route_params = [
        'route' => $admin_route,
        'action' => $admin_action,
        'id' => $admin_id,
        'segments' => $segments
    ];
    
    if ($admin_route === 'login') {
        $route_file = BASE_PATH . '/admin/login.php';
    } elseif ($admin_route === 'logout') {
        $route_file = BASE_PATH . '/admin/logout.php';
    } else {
        // All admin routes need authentication
        $route_file = BASE_PATH . '/admin/router.php';
    }
}
// API routes
elseif (isset($segments[0]) && $segments[0] === 'api') {
    $api_endpoint = $segments[1] ?? '';
    $route_params = ['endpoint' => $api_endpoint, 'segments' => $segments];
    $route_file = BASE_PATH . '/api/router.php';
}
// Public routes
else {
    switch ($path) {
        case '/':
            $route_file = BASE_PATH . '/pages/home.php';
            break;
        
        case '/about':
            $route_file = BASE_PATH . '/pages/about.php';
            break;
        
        case '/contact':
            $route_file = BASE_PATH . '/pages/contact.php';
            break;
        
        case '/portfolio':
            $route_file = BASE_PATH . '/pages/portfolio.php';
            break;
        
        case '/insights':
            $route_file = BASE_PATH . '/pages/insights.php';
            break;
        
        case '/request-demo':
            $route_file = BASE_PATH . '/pages/request-demo.php';
            break;

        case '/search':
            $route_file = BASE_PATH . '/pages/search.php';
            break;
        
        case '/privacy':
            $route_file = BASE_PATH . '/pages/privacy.php';
            break;
        
        case '/offline':
            $route_file = BASE_PATH . '/pages/offline.php';
            break;
        
        default:
            // Product pages: /products/sms, /products/erp
            if (isset($segments[0]) && $segments[0] === 'products' && isset($segments[1])) {
                $route_params['slug'] = $segments[1];
                $route_file = BASE_PATH . '/pages/product-single.php';
            }
            // Service pages: /services/website-development etc
            elseif (isset($segments[0]) && $segments[0] === 'services' && isset($segments[1])) {
                $route_params['slug'] = $segments[1];
                $route_file = BASE_PATH . '/pages/service-single.php';
            }
            // Portfolio single: /portfolio/project-name
            elseif (isset($segments[0]) && $segments[0] === 'portfolio' && isset($segments[1])) {
                $route_params['slug'] = $segments[1];
                $route_file = BASE_PATH . '/pages/portfolio-single.php';
            }
            // Blog single: /insights/post-title
            elseif (isset($segments[0]) && $segments[0] === 'insights' && isset($segments[1])) {
                $route_params['slug'] = $segments[1];
                $route_file = BASE_PATH . '/pages/blog-single.php';
            }
            // Thank you pages: /thank-you/contact, /thank-you/demo etc
            elseif (isset($segments[0]) && $segments[0] === 'thank-you') {
                $route_params['type'] = $segments[1] ?? 'contact';
                $route_file = BASE_PATH . '/pages/thank-you.php';
            }
            break;
    }
}

// ============================================
// Execute Route
// ============================================
if ($route_file && file_exists($route_file)) {
    // Track analytics for public pages
    if (!isset($segments[0]) || $segments[0] !== 'admin') {
        track_page_view($path);
    }
    
    require_once $route_file;
} else {
    // 404 Page
    http_response_code(404);
    require_once BASE_PATH . '/pages/404.php';
}

// Close database connection
if (isset($db) && $db) {
    mysqli_close($db);
}
