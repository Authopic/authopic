<?php
/**
 * Authopic Technologies PLC - Header Template
 * Included at the top of every public page
 */
if (!defined('BASE_PATH'))
    exit;

$page_title = isset($page_title) ? $page_title . ' | ' . get_setting('site_name', 'Authopic Technologies PLC') : get_setting('meta_title', 'Authopic Technologies PLC - Web Development & Software Solutions');
$page_description = $page_description ?? get_setting('meta_description', '');
$page_image = $page_image ?? asset('images/og-image.jpg');
$body_class = $body_class ?? '';
$lang = current_lang();
$nav_items = get_nav_menu('header');
$nav_tree = build_menu_tree($nav_items);
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" class="scroll-smooth" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="description" content="<?php echo e($page_description); ?>">
    <meta name="keywords" content="<?php echo e(get_setting('meta_keywords', '')); ?>">
    <meta name="author" content="Authopic Technologies PLC">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo e($page_title); ?>">
    <meta property="og:description" content="<?php echo e($page_description); ?>">
    <meta property="og:image" content="<?php echo e($page_image); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo e(SITE_URL . current_path()); ?>">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo e($page_title); ?>">
    <meta name="twitter:description" content="<?php echo e($page_description); ?>">
    
    <title><?php echo e($page_title); ?></title>
    
    <!-- PWA -->
    <link rel="manifest" href="<?php echo url('manifest.json'); ?>">
    <meta name="theme-color" content="#074DD9" id="themeColor">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="<?php echo asset('icons/icon-192x192.png'); ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo asset('images/favicon-32x32.png'); ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo asset('images/favicon-16x16.png'); ?>">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Noto+Sans+Ethiopic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (compiled) -->
    <link rel="stylesheet" href="<?php echo asset('css/tailwind.css'); ?>">
    
    <!-- Custom Styles -->
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
</head>
<body class="font-sans antialiased transition-colors duration-300 bg-white dark:bg-[#0B132B] text-slate-800 dark:text-gray-100 <?php echo e($body_class); ?>">
    
    <!-- Skip to content -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-primary focus:text-white focus:rounded-lg">Skip to content</a>

    <!-- ============================================ -->
    <!-- HEADER / NAVIGATION -->
    <!-- ============================================ -->
    <header id="site-header" class="fixed top-0 left-0 right-0 z-50 transition-all duration-500">
        <div class="header-backdrop absolute inset-0 bg-white/80 dark:bg-[#0B132B]/80 backdrop-blur-xl border-b border-black/5 dark:border-white/5 transition-all duration-300"></div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 lg:h-20">
                
                <!-- Logo -->
                <a href="<?php echo url('/'); ?>" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center text-white font-bold text-lg group-hover:shadow-lg group-hover:shadow-primary/30 transition-all duration-300">
                        A
                    </div>
                    <span class="text-xl font-bold text-primary hidden sm:inline">
                        Authopic Technologies
                    </span>
                </a>
                
                <!-- Desktop Navigation -->
                <nav class="hidden lg:flex items-center gap-1" role="navigation" aria-label="Main">
                    <?php foreach ($nav_tree as $item): ?>
                        <?php if (!empty($item['children'])): ?>
                            <div class="relative group">
                                <button class="nav-link px-4 py-2 rounded-lg text-sm font-medium text-slate-600 dark:text-gray-300 hover:text-primary dark:hover:text-primary hover:bg-primary/5 transition-all duration-200 flex items-center gap-1 <?php echo is_active($item['url']) ? 'text-primary dark:text-primary' : ''; ?>">
                                    <?php echo e(get_text($item['label_en'], $item['label_am'])); ?>
                                    <svg class="w-4 h-4 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div class="absolute top-full left-0 mt-1 w-56 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform origin-top scale-95 group-hover:scale-100">
                                    <div class="py-2 bg-white dark:bg-[#1A1A1A] rounded-xl shadow-xl border border-black/5 dark:border-white/10 backdrop-blur-xl">
                                        <?php foreach ($item['children'] as $child): ?>
                                            <a href="<?php echo url($child['url']); ?>" class="block px-4 py-2.5 text-sm text-slate-600 dark:text-gray-300 hover:text-primary dark:hover:text-primary hover:bg-primary/5 transition-all duration-200 <?php echo is_active($child['url']) ? 'text-primary bg-primary/5' : ''; ?>">
                                                <?php echo e(get_text($child['label_en'], $child['label_am'])); ?>
                                            </a>
                                        <?php
        endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php
    else: ?>
                            <?php if ($item['url'] !== '#'): ?>
                                <a href="<?php echo url($item['url']); ?>" class="nav-link px-4 py-2 rounded-lg text-sm font-medium text-slate-600 dark:text-gray-300 hover:text-primary dark:hover:text-primary hover:bg-primary/5 transition-all duration-200 <?php echo is_active($item['url']) ? 'text-primary dark:text-primary bg-primary/5' : ''; ?>">
                                    <?php echo e(get_text($item['label_en'], $item['label_am'])); ?>
                                </a>
                            <?php
        endif; ?>
                        <?php
    endif; ?>
                    <?php
endforeach; ?>
                </nav>
                
                <!-- Right Actions -->
                <div class="flex items-center gap-2">
                    <!-- Language Toggle -->
                    <a href="?lang=<?php echo $lang === 'en' ? 'am' : 'en'; ?>" class="p-2 rounded-lg text-sm font-medium text-slate-500 dark:text-gray-400 hover:bg-primary/5 transition-all duration-200" title="Switch Language">
                        <?php echo $lang === 'en' ? 'አማ' : 'EN'; ?>
                    </a>
                    
                    <!-- Dark/Light Toggle -->
                    <button id="themeToggle" class="p-2 rounded-lg text-slate-500 dark:text-gray-400 hover:bg-primary/5 transition-all duration-200" aria-label="Toggle theme">
                        <!-- Sun icon (shown in dark mode) -->
                        <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <!-- Moon icon (shown in light mode) -->
                        <svg class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    </button>
                    
                    <!-- Install App Button -->
                    <button id="pwa-install-btn" class="hidden items-center gap-1.5 px-3 py-1.5 text-sm font-semibold text-primary bg-primary/10 hover:bg-primary/20 rounded-xl transition-all duration-200" aria-label="Install App">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span class="hidden sm:inline">Install</span>
                    </button>
                    
                    <!-- CTA Button (Desktop) -->
                    <a href="<?php echo url('/request-demo'); ?>" class="hidden lg:inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-primary/25 hover:shadow-primary/40 transition-all duration-300 hover:-translate-y-0.5">
                        <?php echo get_text('Request Demo', 'ዲሞ ይጠይቁ'); ?>
                    </a>
                    
                    <!-- Mobile Menu Button -->
                    <button id="mobileMenuBtn" class="lg:hidden p-2 rounded-lg text-slate-500 dark:text-gray-400 hover:bg-primary/5 transition-all duration-200" aria-label="Toggle menu" aria-expanded="false">
                        <svg class="w-6 h-6" id="menuIconOpen" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        <svg class="w-6 h-6 hidden" id="menuIconClose" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile Navigation -->
        <div id="mobileMenu" class="lg:hidden hidden">
            <div class="relative bg-white dark:bg-[#0B132B] border-t border-black/5 dark:border-white/5 max-h-[80vh] overflow-y-auto">
                <nav class="max-w-7xl mx-auto px-4 py-4 space-y-1">
                    <?php foreach ($nav_tree as $item): ?>
                        <?php if (!empty($item['children'])): ?>
                            <div class="mobile-dropdown">
                                <button class="w-full flex items-center justify-between px-4 py-3 rounded-lg text-sm font-medium text-slate-600 dark:text-gray-300 hover:bg-primary/5 transition-all" onclick="this.parentElement.classList.toggle('open')">
                                    <?php echo e(get_text($item['label_en'], $item['label_am'])); ?>
                                    <svg class="w-4 h-4 transition-transform mobile-dd-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div class="mobile-dd-content hidden pl-4 space-y-1">
                                    <?php foreach ($item['children'] as $child): ?>
                                        <a href="<?php echo url($child['url']); ?>" class="block px-4 py-2.5 rounded-lg text-sm text-slate-500 dark:text-gray-400 hover:text-primary hover:bg-primary/5 transition-all">
                                            <?php echo e(get_text($child['label_en'], $child['label_am'])); ?>
                                        </a>
                                    <?php
        endforeach; ?>
                                </div>
                            </div>
                        <?php
    else: ?>
                            <?php if ($item['url'] !== '#'): ?>
                                <a href="<?php echo url($item['url']); ?>" class="block px-4 py-3 rounded-lg text-sm font-medium text-slate-600 dark:text-gray-300 hover:text-primary hover:bg-primary/5 transition-all <?php echo is_active($item['url']) ? 'text-primary bg-primary/5' : ''; ?>">
                                    <?php echo e(get_text($item['label_en'], $item['label_am'])); ?>
                                </a>
                            <?php
        endif; ?>
                        <?php
    endif; ?>
                    <?php
endforeach; ?>
                    
                    <div class="pt-4 border-t border-black/5 dark:border-white/5">
                        <a href="<?php echo url('/request-demo'); ?>" class="block text-center px-5 py-3 bg-primary hover:bg-blue-700 text-white font-semibold rounded-xl shadow-lg shadow-primary/25">
                            <?php echo get_text('Request Demo', 'ዲሞ ይጠይቁ'); ?>
                        </a>
                    </div>
                </nav>
            </div>
        </div>
    </header>

    <!-- Flash Messages -->
    <div class="fixed top-20 right-4 z-50 w-80 space-y-2" id="flashContainer">
        <?php echo render_flash_messages(); ?>
    </div>

    <!-- Main Content -->
    <main id="main-content" class="min-h-screen">
