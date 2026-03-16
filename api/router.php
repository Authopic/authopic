<?php
// Developed by Yisak A. Alemayehu (yisak.dev)
/**
 * Authopic Technologies PLC - API Router
 * Handles AJAX form submissions
 */

// Prevent direct access
if (!defined('BASE_PATH')) exit;

header('Content-Type: application/json; charset=utf-8');

$api_path = isset($route_params['endpoint']) ? $route_params['endpoint'] : (isset($route_params['path']) ? $route_params['path'] : '');
$method = $_SERVER['REQUEST_METHOD'];

// ---- Newsletter Subscribe ----
if ($api_path === 'newsletter' && $method === 'POST') {
    $email = trim(post('email'));

    if (empty($email) || !is_valid_email($email)) {
        json_response(['success' => false, 'message' => 'Please enter a valid email address.'], 400);
    }

    // Check rate limit
    if (!rate_limit_check('newsletter', 5, 3600)) {
        json_response(['success' => false, 'message' => 'Too many requests. Please try again later.'], 429);
    }

    // Check if already subscribed
    $existing = db_fetch_one("SELECT id, status FROM newsletter_subscribers WHERE email = '" . db_escape($email) . "'");
    if ($existing) {
        if ($existing['status'] === 'active') {
            json_response(['success' => false, 'message' => 'This email is already subscribed.']);
        } else {
            // Resubscribe
            db_query("UPDATE newsletter_subscribers SET status='active', subscribed_at=NOW() WHERE id=" . (int)$existing['id']);
            send_welcome_email($email);
            json_response(['success' => true, 'message' => 'Welcome back! Check your inbox for our latest updates.']);
        }
    }

    // Subscribe
    $safe_email = db_escape($email);
    db_query("INSERT INTO newsletter_subscribers (email, status, subscribed_at) VALUES ('$safe_email', 'active', NOW())");

    // Welcome email to subscriber
    send_welcome_email($email);

    // Notify admin
    notify_admin('New Newsletter Subscriber', "New subscriber: $email");

    json_response(['success' => true, 'message' => 'Successfully subscribed! Check your inbox for a welcome email.']);
}

// ---- Contact Form (AJAX) ----
if ($api_path === 'contact' && $method === 'POST') {
    // CSRF check
    if (!csrf_verify(post('csrf_token'))) {
        json_response(['success' => false, 'message' => 'Invalid security token. Please refresh and try again.'], 403);
    }

    // Honeypot
    if (is_spam_honeypot()) {
        json_response(['success' => true, 'message' => 'Thank you for your message!']); // Fake success
    }

    // Rate limit
    if (!rate_limit_check('contact', 3, 3600)) {
        json_response(['success' => false, 'message' => 'Too many submissions. Please try again later.'], 429);
    }

    $name = trim(post('name'));
    $email = trim(post('email'));
    $phone = trim(post('phone'));
    $subject = trim(post('subject'));
    $message = trim(post('message'));

    $errors = [];
    if (empty($name) || strlen($name) < 2) $errors[] = 'Name is required.';
    if (empty($email) || !is_valid_email($email)) $errors[] = 'Valid email is required.';
    if (empty($subject)) $errors[] = 'Subject is required.';
    if (empty($message) || strlen($message) < 10) $errors[] = 'Message must be at least 10 characters.';
    if (!empty($phone) && !is_valid_phone($phone)) $errors[] = 'Invalid phone number.';

    if (!empty($errors)) {
        json_response(['success' => false, 'message' => implode(' ', $errors)], 400);
    }

    $safe = [
        'name' => db_escape($name),
        'email' => db_escape($email),
        'phone' => db_escape($phone),
        'subject' => db_escape($subject),
        'message' => db_escape($message),
        'ip' => db_escape(get_client_ip())
    ];

    db_query("INSERT INTO leads (name, email, phone, subject, message, source, ip_address, status, created_at) 
              VALUES ('{$safe['name']}', '{$safe['email']}', '{$safe['phone']}', '{$safe['subject']}', '{$safe['message']}', 'contact_form', '{$safe['ip']}', 'new', NOW())");

    notify_admin('New Contact Form Submission', "From: $name <$email>\nSubject: $subject\n\n$message");

    json_response(['success' => true, 'message' => 'Your message has been sent successfully!']);
}

// ---- Demo Request (AJAX) ----
if ($api_path === 'demo' && $method === 'POST') {
    if (!csrf_verify(post('csrf_token'))) {
        json_response(['success' => false, 'message' => 'Invalid security token.'], 403);
    }

    if (is_spam_honeypot()) {
        json_response(['success' => true, 'message' => 'Demo request received!']);
    }

    if (!rate_limit_check('demo', 3, 3600)) {
        json_response(['success' => false, 'message' => 'Too many requests. Please try again later.'], 429);
    }

    $name         = trim(post('name'));
    $email        = trim(post('email'));
    $phone        = trim(post('phone'));
    $organization = trim(post('organization'));
    $org_size     = trim(post('organization_size'));
    $role         = trim(post('role'));
    $product      = trim(post('product_interest'));
    $date         = trim(post('preferred_date'));
    $time         = trim(post('preferred_time'));
    $message      = trim(post('message'));

    $errors = [];
    if (empty($name))                                    $errors[] = 'Name is required.';
    if (empty($email) || !is_valid_email($email))        $errors[] = 'Valid email is required.';
    if (empty($phone))                                   $errors[] = 'Phone is required.';

    if (!empty($errors)) {
        json_response(['success' => false, 'message' => implode(' ', $errors)], 400);
    }

    // Split name into first / last
    $name_parts  = array_filter(explode(' ', $name, 2));
    $first_name  = trim($name_parts[0] ?? $name);
    $last_name   = trim($name_parts[1] ?? '');

    // Map product selection → ENUM
    $product_lower = strtolower($product);
    if (strpos($product_lower, 'erp') !== false) {
        $product_enum = 'erp';
    } elseif (strpos($product_lower, 'sms') !== false || strpos($product_lower, 'school') !== false) {
        $product_enum = 'sms';
    } else {
        $product_enum = 'both';
    }

    // Map time slot labels → HH:MM:SS
    $time_map         = ['morning' => '09:00:00', 'afternoon' => '14:00:00', 'flexible' => '09:00:00'];
    $preferred_time_v = $time_map[$time] ?? ($time ?: '09:00:00');
    $preferred_date_v = !empty($date) ? $date : date('Y-m-d', strtotime('+3 days'));

    // Pack extra info into notes
    $notes_parts = [];
    if (!empty($message))   $notes_parts[] = $message;
    if (!empty($role))      $notes_parts[] = "Role: $role";
    if (!empty($org_size))  $notes_parts[] = "Org size: $org_size";
    if (!empty($product))   $notes_parts[] = "Product interest: $product";
    $notes_text = implode("\n", $notes_parts);

    $f  = db_escape($first_name);
    $l  = db_escape($last_name);
    $em = db_escape($email);
    $ph = db_escape($phone);
    $co = db_escape($organization);
    $pr = db_escape($product_enum);
    $pd = db_escape($preferred_date_v);
    $pt = db_escape($preferred_time_v);
    $no = db_escape($notes_text);
    $ip = db_escape(get_client_ip());

    db_query("INSERT INTO demo_requests (first_name, last_name, email, phone, company, product, preferred_date, preferred_time, notes, ip_address, status, created_at)
              VALUES ('$f', '$l', '$em', '$ph', '$co', '$pr', '$pd', '$pt', '$no', '$ip', 'pending', NOW())");

    // Auto-subscribe to newsletter
    $existing_sub = db_fetch_one("SELECT id, status FROM newsletter_subscribers WHERE email = '$em'");
    if (!$existing_sub) {
        $sub_name = db_escape(trim($first_name . ' ' . $last_name));
        db_query("INSERT INTO newsletter_subscribers (email, name, status, subscribed_at) VALUES ('$em', '$sub_name', 'active', NOW())");
        send_welcome_email($email);
    } elseif ($existing_sub['status'] === 'unsubscribed') {
        db_query("UPDATE newsletter_subscribers SET status='active', subscribed_at=NOW() WHERE id=" . (int)$existing_sub['id']);
    }

    notify_admin('New Demo Request', "From: $name <$email>\nOrganization: $organization\nProduct: $product\nDate: $preferred_date_v $preferred_time_v");

    json_response(['success' => true, 'message' => 'Demo request submitted! We will contact you shortly.']);
}

// ---- Search API ----
if ($api_path === 'search' && $method === 'GET') {
    $q = trim(get('q'));
    if (empty($q) || strlen($q) < 2) {
        json_response(['success' => true, 'results' => [], 'total' => 0]);
    }

    $like = '%' . db_escape($q) . '%';
    $results = [];

    $products = db_fetch_all("SELECT name AS title, slug, short_description AS excerpt FROM products WHERE status='active' AND (name LIKE '$like' OR short_description LIKE '$like') LIMIT 5");
    foreach ($products as $p) {
        $p['type'] = 'product';
        $p['url'] = url('/products/' . $p['slug']);
        $results[] = $p;
    }

    $services = db_fetch_all("SELECT title, slug, short_description AS excerpt FROM services WHERE status='active' AND (title LIKE '$like' OR short_description LIKE '$like') LIMIT 5");
    foreach ($services as $s) {
        $s['type'] = 'service';
        $s['url'] = url('/services/' . $s['slug']);
        $results[] = $s;
    }

    $posts = db_fetch_all("SELECT title, slug, excerpt FROM blog_posts WHERE status='published' AND (title LIKE '$like' OR excerpt LIKE '$like') LIMIT 5");
    foreach ($posts as $p) {
        $p['type'] = 'blog';
        $p['url'] = url('/insights/' . $p['slug']);
        $results[] = $p;
    }

    json_response(['success' => true, 'results' => $results, 'total' => count($results)]);
}

// ---- 404 for unknown API routes ----
json_response(['success' => false, 'message' => 'API endpoint not found.'], 404);
