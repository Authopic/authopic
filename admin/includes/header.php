<?php
/**
 * Authopic Technologies PLC - Admin Header / Layout Top
 */
if (!defined('BASE_PATH'))
    exit;
$current_admin = get_current_admin();
$admin_page = isset($admin_page) ? $admin_page : 'dashboard';
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#074DD9">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Admin - <?php echo ucfirst($admin_page); ?> | Authopic Technologies PLC</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo asset('css/tailwind.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-gray-100 font-sans min-h-screen">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php require_once BASE_PATH . '/admin/includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-h-screen ml-0 lg:ml-[260px]">
            <!-- Top Bar -->
            <header class="sticky top-0 z-30 bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border-b border-black/5 dark:border-white/10">
                <div class="flex items-center justify-between px-4 sm:px-6 h-16">
                    <div class="flex items-center gap-3">
                        <!-- Mobile sidebar toggle -->
                        <button id="admin-sidebar-toggle" class="lg:hidden p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-white/5 text-slate-600 dark:text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        <h1 class="text-lg font-bold text-slate-800 dark:text-white capitalize"><?php echo e($admin_page); ?></h1>
                    </div>

                    <div class="flex items-center gap-3">
                        <!-- Theme Toggle -->
                        <button id="admin-theme-toggle" onclick="document.documentElement.classList.toggle('dark'); localStorage.setItem('theme', document.documentElement.classList.contains('dark')?'dark':'light')" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-white/5 text-slate-600 dark:text-gray-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </button>

                        <!-- View Site -->
                        <a href="<?php echo url('/'); ?>" target="_blank" class="hidden sm:inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium text-slate-600 dark:text-gray-300 hover:text-primary rounded-lg hover:bg-slate-100 dark:hover:bg-white/5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            View Site
                        </a>

                        <!-- Profile -->
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white text-sm font-bold">
                                <?php echo strtoupper(substr($current_admin['full_name'] ?? $current_admin['username'] ?? 'A', 0, 1)); ?>
                            </div>
                            <div class="hidden sm:block">
                                <p class="text-sm font-semibold text-slate-700 dark:text-gray-200"><?php echo e($current_admin['full_name'] ?? $current_admin['username'] ?? 'Admin'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            <div class="px-4 sm:px-6 pt-4">
                <?php echo render_flash_messages(); ?>
            </div>

            <!-- Page Content -->
            <main class="flex-1 p-4 sm:p-6">
