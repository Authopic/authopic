<?php
/**
 * Authopic Technologies PLC - Admin Login
 */
if (!defined('BASE_PATH'))
    exit;

// Already logged in?
if (is_admin_logged_in()) {
    redirect('/admin/dashboard');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify(post('csrf_token'))) {
        $errors[] = 'Invalid security token.';
    }

    if (!rate_limit_check('admin_login', LOGIN_MAX_ATTEMPTS, 900)) {
        $errors[] = 'Too many login attempts. Please wait 15 minutes.';
    }

    if (empty($errors)) {
        $username = trim(post('username'));
        $password = post('password');

        if (empty($username) || empty($password)) {
            $errors[] = 'Username and password are required.';
        }
        else {
            $safe_user = db_escape($username);
            $admin = db_fetch_one("SELECT * FROM admin_users WHERE (username='$safe_user' OR email='$safe_user') AND is_active=1");

            if ($admin && password_verify($password, $admin['password_hash'])) {
                // Success - create session
                session_regenerate_id(true);
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_role'] = $admin['role'];
                $_SESSION['admin_login_time'] = time();

                // Update last login
                db_query("UPDATE admin_users SET last_login=NOW(), login_count=login_count+1 WHERE id=" . (int)$admin['id']);

                log_activity('login', 'admin_users', $admin['id'], 'Admin login: ' . $admin['username']);

                redirect('/admin/dashboard');
            }
            else {
                log_activity('login_failed', 'admin_users', 0, 'Failed login for: ' . $username);
                $errors[] = 'Invalid username or password.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin Login - Authopic Technologies PLC</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo asset('css/tailwind.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="bg-slate-50 dark:bg-slate-900 min-h-screen flex items-center justify-center font-['Inter']">
    <!-- Background Decoration -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="gradient-orb gradient-orb-blue w-96 h-96 -top-48 -right-48"></div>
        <div class="gradient-orb gradient-orb-purple w-96 h-96 -bottom-48 -left-48"></div>
    </div>

    <div class="relative w-full max-w-md px-4">
        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="<?php echo url('/'); ?>" class="inline-flex items-center gap-2 text-2xl font-extrabold">
                <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center">
                    <span class="text-white font-black text-lg">A</span>
                </div>
                <span class="text-slate-800 dark:text-white">Authopic</span>
                <span class="text-primary">Technologies</span>
            </a>
            <p class="text-slate-500 dark:text-gray-400 mt-2">Admin Dashboard</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-3xl p-8 shadow-xl">
            <h1 class="text-xl font-bold text-slate-800 dark:text-white mb-6">Sign in to your account</h1>

            <?php if (!empty($errors)): ?>
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-xl">
                    <?php foreach ($errors as $error): ?>
                        <p class="text-red-600 dark:text-red-400 text-sm font-medium"><?php echo e($error); ?></p>
                    <?php
    endforeach; ?>
                </div>
            <?php
endif; ?>

            <form method="POST" action="<?php echo url('/admin/login'); ?>">
                <?php echo csrf_field(); ?>

                <div class="mb-4">
                    <label class="form-label" for="username">Username or Email</label>
                    <input type="text" id="username" name="username" value="<?php echo e(post('username')); ?>" required autofocus
                           class="form-input" placeholder="Enter username or email">
                </div>

                <div class="mb-6">
                    <label class="form-label" for="password">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required
                               class="form-input pr-12" placeholder="Enter password">
                        <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-gray-300">
                            <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full py-3 bg-primary hover:bg-blue-700 text-white font-semibold rounded-xl shadow-lg shadow-primary/25 hover:shadow-primary/40 transition-all duration-300">
                    Sign In
                </button>
            </form>
        </div>

        <p class="text-center text-sm text-slate-400 dark:text-gray-400 mt-6">
            <a href="<?php echo url('/'); ?>" class="hover:text-primary transition-colors">&larr; Back to website</a>
        </p>
    </div>

    <script>
        function togglePassword() {
            var input = document.getElementById('password');
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>
</html>
