<?php
// Developed by Yisak A. Alemayehu (yisak.dev)
/**
 * Authopic Technologies PLC - Admin Dashboard (Mobile-First)
 */
if (!defined('BASE_PATH')) exit;

// Stats
$total_leads = db_fetch_one("SELECT COUNT(*) as c FROM leads")['c'] ?? 0;
$new_leads = db_fetch_one("SELECT COUNT(*) as c FROM leads WHERE status='new'")['c'] ?? 0;
$total_demos = db_fetch_one("SELECT COUNT(*) as c FROM demo_requests")['c'] ?? 0;
$pending_demos = db_fetch_one("SELECT COUNT(*) as c FROM demo_requests WHERE status='pending'")['c'] ?? 0;
$total_subscribers = db_fetch_one("SELECT COUNT(*) as c FROM newsletter_subscribers WHERE status='active'")['c'] ?? 0;
$total_views = db_fetch_one("SELECT COUNT(*) as c FROM site_analytics WHERE DATE(created_at) = CURDATE()")['c'] ?? 0;
$total_posts = db_fetch_one("SELECT COUNT(*) as c FROM blog_posts WHERE status='published'")['c'] ?? 0;
$total_portfolio = db_fetch_one("SELECT COUNT(*) as c FROM portfolio WHERE status='published'")['c'] ?? 0;

// Recent leads
$recent_leads = db_fetch_all("SELECT * FROM leads ORDER BY created_at DESC LIMIT 5");

// Recent demo requests
$recent_demos = db_fetch_all("SELECT * FROM demo_requests ORDER BY created_at DESC LIMIT 5");

// Recent activity
$recent_activity = db_fetch_all("SELECT al.*, au.username FROM activity_log al LEFT JOIN admin_users au ON al.admin_id=au.id ORDER BY al.created_at DESC LIMIT 10");

// Top pages today
$top_pages = db_fetch_all("SELECT page_url, COUNT(*) as views FROM site_analytics WHERE DATE(created_at) = CURDATE() GROUP BY page_url ORDER BY views DESC LIMIT 5");

$admin_name = $current_admin['full_name'] ?? $current_admin['username'] ?? 'Admin';
$hour = (int) date('H');
$greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
?>

<!-- Welcome Banner -->
<div class="relative overflow-hidden bg-primary rounded-2xl p-5 sm:p-6 mb-6 text-white">
    <div class="relative z-10">
        <p class="text-blue-100 text-sm font-medium"><?php echo $greeting; ?>,</p>
        <h2 class="text-xl sm:text-2xl font-extrabold mt-0.5"><?php echo e($admin_name); ?></h2>
        <p class="text-blue-200 text-sm mt-1 hidden sm:block"><?php echo date('l, F j, Y'); ?></p>
    </div>
    <!-- Decorative -->
    <div class="absolute top-0 right-0 w-32 h-32 sm:w-40 sm:h-40 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/3"></div>
    <div class="absolute bottom-0 right-12 w-20 h-20 sm:w-24 sm:h-24 bg-white/5 rounded-full translate-y-1/2"></div>
</div>

<!-- Stats Grid - 2 cols on mobile, 4 on desktop -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    <!-- Leads -->
    <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-4 sm:p-5 relative overflow-hidden group">
        <div class="flex items-center justify-between mb-2 sm:mb-3">
            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <?php if ($new_leads > 0): ?>
                <span class="text-[10px] sm:text-xs font-bold bg-red-100 dark:bg-red-500/10 text-red-600 dark:text-red-400 px-1.5 sm:px-2 py-0.5 rounded-full"><?php echo $new_leads; ?> new</span>
            <?php endif; ?>
        </div>
        <p class="text-xl sm:text-2xl font-extrabold text-slate-800 dark:text-white"><?php echo $total_leads; ?></p>
        <p class="text-xs sm:text-sm text-slate-500 dark:text-gray-400">Leads</p>
        <div class="absolute inset-0 bg-blue-500/5 opacity-0 group-hover:opacity-100 transition-opacity rounded-2xl"></div>
    </div>

    <!-- Demos -->
    <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-4 sm:p-5 relative overflow-hidden group">
        <div class="flex items-center justify-between mb-2 sm:mb-3">
            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-purple-50 dark:bg-purple-500/10 flex items-center justify-center">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <?php if ($pending_demos > 0): ?>
                <span class="text-[10px] sm:text-xs font-bold bg-orange-100 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 px-1.5 sm:px-2 py-0.5 rounded-full"><?php echo $pending_demos; ?> pending</span>
            <?php endif; ?>
        </div>
        <p class="text-xl sm:text-2xl font-extrabold text-slate-800 dark:text-white"><?php echo $total_demos; ?></p>
        <p class="text-xs sm:text-sm text-slate-500 dark:text-gray-400">Demos</p>
        <div class="absolute inset-0 bg-purple-500/5 opacity-0 group-hover:opacity-100 transition-opacity rounded-2xl"></div>
    </div>

    <!-- Subscribers -->
    <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-4 sm:p-5 relative overflow-hidden group">
        <div class="flex items-center justify-between mb-2 sm:mb-3">
            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-green-50 dark:bg-green-500/10 flex items-center justify-center">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </div>
        </div>
        <p class="text-xl sm:text-2xl font-extrabold text-slate-800 dark:text-white"><?php echo $total_subscribers; ?></p>
        <p class="text-xs sm:text-sm text-slate-500 dark:text-gray-400">Subscribers</p>
        <div class="absolute inset-0 bg-green-500/5 opacity-0 group-hover:opacity-100 transition-opacity rounded-2xl"></div>
    </div>

    <!-- Today's Views -->
    <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-4 sm:p-5 relative overflow-hidden group">
        <div class="flex items-center justify-between mb-2 sm:mb-3">
            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-cyan-50 dark:bg-cyan-500/10 flex items-center justify-center">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </div>
        </div>
        <p class="text-xl sm:text-2xl font-extrabold text-slate-800 dark:text-white"><?php echo $total_views; ?></p>
        <p class="text-xs sm:text-sm text-slate-500 dark:text-gray-400">Views Today</p>
        <div class="absolute inset-0 bg-cyan-500/5 opacity-0 group-hover:opacity-100 transition-opacity rounded-2xl"></div>
    </div>
</div>

<!-- Secondary Stats - Horizontal Scroll on Mobile -->
<div class="flex gap-3 mb-6 overflow-x-auto pb-2 -mx-4 px-4 sm:mx-0 sm:px-0 snap-x snap-mandatory scrollbar-hide">
    <div class="flex-shrink-0 snap-start flex items-center gap-3 bg-white/60 dark:bg-white/5 border border-black/5 dark:border-white/10 rounded-xl px-4 py-3 min-w-[160px]">
        <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center">
            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
        </div>
        <div>
            <p class="text-lg font-bold text-slate-800 dark:text-white"><?php echo $total_posts; ?></p>
            <p class="text-xs text-slate-500 dark:text-gray-400">Blog Posts</p>
        </div>
    </div>
    <div class="flex-shrink-0 snap-start flex items-center gap-3 bg-white/60 dark:bg-white/5 border border-black/5 dark:border-white/10 rounded-xl px-4 py-3 min-w-[160px]">
        <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center">
            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
        </div>
        <div>
            <p class="text-lg font-bold text-slate-800 dark:text-white"><?php echo $total_portfolio; ?></p>
            <p class="text-xs text-slate-500 dark:text-gray-400">Projects</p>
        </div>
    </div>
    <div class="flex-shrink-0 snap-start flex items-center gap-3 bg-white/60 dark:bg-white/5 border border-black/5 dark:border-white/10 rounded-xl px-4 py-3 min-w-[160px]">
        <div class="w-8 h-8 rounded-lg bg-rose-50 dark:bg-rose-500/10 flex items-center justify-center">
            <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <p class="text-lg font-bold text-slate-800 dark:text-white"><?php echo date('g:i A'); ?></p>
            <p class="text-xs text-slate-500 dark:text-gray-400">Server Time</p>
        </div>
    </div>
</div>

<!-- Quick Actions - Mobile optimized grid -->
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
    <a href="<?php echo url('/admin/blog'); ?>?action=create" class="flex flex-col sm:flex-row items-center gap-2 sm:gap-3 p-4 bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl hover:border-primary/30 hover:shadow-lg hover:shadow-primary/5 transition-all active:scale-95">
        <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center">
            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        </div>
        <span class="text-xs sm:text-sm font-semibold text-slate-700 dark:text-gray-200 text-center sm:text-left">New Post</span>
    </a>
    <a href="<?php echo url('/admin/portfolio'); ?>?action=create" class="flex flex-col sm:flex-row items-center gap-2 sm:gap-3 p-4 bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl hover:border-primary/30 hover:shadow-lg hover:shadow-primary/5 transition-all active:scale-95">
        <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-500/10 flex items-center justify-center">
            <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        </div>
        <span class="text-xs sm:text-sm font-semibold text-slate-700 dark:text-gray-200 text-center sm:text-left">New Project</span>
    </a>
    <a href="<?php echo url('/admin/settings'); ?>" class="flex flex-col sm:flex-row items-center gap-2 sm:gap-3 p-4 bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl hover:border-primary/30 hover:shadow-lg hover:shadow-primary/5 transition-all active:scale-95">
        <div class="w-10 h-10 rounded-xl bg-green-50 dark:bg-green-500/10 flex items-center justify-center">
            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
        </div>
        <span class="text-xs sm:text-sm font-semibold text-slate-700 dark:text-gray-200 text-center sm:text-left">Settings</span>
    </a>
    <a href="<?php echo url('/admin/analytics'); ?>" class="flex flex-col sm:flex-row items-center gap-2 sm:gap-3 p-4 bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl hover:border-primary/30 hover:shadow-lg hover:shadow-primary/5 transition-all active:scale-95">
        <div class="w-10 h-10 rounded-xl bg-cyan-50 dark:bg-cyan-500/10 flex items-center justify-center">
            <svg class="w-5 h-5 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        </div>
        <span class="text-xs sm:text-sm font-semibold text-slate-700 dark:text-gray-200 text-center sm:text-left">Analytics</span>
    </a>
</div>

<!-- Main Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-6">
    <!-- Recent Leads -->
    <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl overflow-hidden">
        <div class="flex items-center justify-between p-4 sm:p-6 pb-0 sm:pb-0">
            <h2 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white">Recent Leads</h2>
            <a href="<?php echo url('/admin/leads'); ?>" class="text-xs sm:text-sm text-primary hover:underline font-medium">View All</a>
        </div>
        <?php if (empty($recent_leads)): ?>
            <div class="p-6 text-center">
                <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-white/5 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <p class="text-slate-400 dark:text-gray-400 text-sm">No leads yet</p>
            </div>
        <?php else: ?>
            <div class="p-3 sm:p-4 space-y-1">
                <?php foreach ($recent_leads as $lead): ?>
                    <div class="flex items-center gap-3 p-2.5 sm:p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                        <div class="w-9 h-9 rounded-full bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-primary font-bold text-sm flex-shrink-0">
                            <?php echo strtoupper(substr($lead['name'], 0, 1)); ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-700 dark:text-gray-200 truncate"><?php echo e($lead['name']); ?></p>
                            <p class="text-xs text-slate-400 dark:text-gray-400 truncate"><?php echo e($lead['interest']); ?></p>
                        </div>
                        <div class="text-right flex-shrink-0 hidden sm:block">
                            <span class="inline-block px-2 py-0.5 text-xs font-semibold rounded-full <?php echo $lead['status'] === 'new' ? 'bg-green-100 dark:bg-green-500/10 text-green-600 dark:text-green-400' : 'bg-slate-100 dark:bg-white/5 text-slate-500'; ?>">
                                <?php echo ucfirst($lead['status']); ?>
                            </span>
                        </div>
                        <span class="text-xs text-slate-400 dark:text-gray-500 flex-shrink-0 sm:hidden"><?php echo time_ago($lead['created_at']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Recent Demo Requests -->
    <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl overflow-hidden">
        <div class="flex items-center justify-between p-4 sm:p-6 pb-0 sm:pb-0">
            <h2 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white">Demo Requests</h2>
            <a href="<?php echo url('/admin/demos'); ?>" class="text-xs sm:text-sm text-primary hover:underline font-medium">View All</a>
        </div>
        <?php if (empty($recent_demos)): ?>
            <div class="p-6 text-center">
                <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-white/5 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <p class="text-slate-400 dark:text-gray-400 text-sm">No demo requests yet</p>
            </div>
        <?php else: ?>
            <div class="p-3 sm:p-4 space-y-1">
                <?php foreach ($recent_demos as $demo): ?>
                    <div class="flex items-center gap-3 p-2.5 sm:p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                        <div class="w-9 h-9 rounded-full bg-purple-50 dark:bg-purple-500/10 flex items-center justify-center text-secondary font-bold text-sm flex-shrink-0">
                            <?php echo strtoupper(substr($demo['first_name'], 0, 1)); ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-700 dark:text-gray-200 truncate"><?php echo e($demo['first_name'] . ' ' . $demo['last_name']); ?></p>
                            <p class="text-xs text-slate-400 dark:text-gray-400 truncate"><?php echo e($demo['company']); ?> &middot; <?php echo e($demo['product']); ?></p>
                        </div>
                        <div class="text-right flex-shrink-0 hidden sm:block">
                            <span class="inline-block px-2 py-0.5 text-xs font-semibold rounded-full <?php echo $demo['status'] === 'pending' ? 'bg-orange-100 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400' : 'bg-slate-100 dark:bg-white/5 text-slate-500'; ?>">
                                <?php echo ucfirst($demo['status']); ?>
                            </span>
                        </div>
                        <span class="text-xs text-slate-400 dark:text-gray-500 flex-shrink-0 sm:hidden"><?php echo time_ago($demo['created_at']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Bottom Grid -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-20 lg:mb-6">
    <!-- Top Pages Today -->
    <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl overflow-hidden">
        <div class="p-4 sm:p-6 pb-0 sm:pb-0">
            <h2 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white">Top Pages Today</h2>
        </div>
        <?php if (empty($top_pages)): ?>
            <div class="p-6 text-center">
                <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-white/5 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <p class="text-slate-400 dark:text-gray-400 text-sm">No traffic today</p>
            </div>
        <?php else: ?>
            <div class="p-3 sm:p-4 space-y-2">
                <?php foreach ($top_pages as $i => $pg): ?>
                    <div class="flex items-center gap-3 p-2">
                        <span class="w-6 h-6 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center text-xs font-bold text-slate-500 flex-shrink-0"><?php echo $i + 1; ?></span>
                        <span class="flex-1 text-sm text-slate-600 dark:text-gray-300 truncate"><?php echo e($pg['page_url']); ?></span>
                        <span class="text-sm font-semibold text-slate-800 dark:text-white flex-shrink-0"><?php echo $pg['views']; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl overflow-hidden">
        <div class="p-4 sm:p-6 pb-0 sm:pb-0">
            <h2 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white">Recent Activity</h2>
        </div>
        <?php if (empty($recent_activity)): ?>
            <div class="p-6 text-center">
                <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-white/5 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-slate-400 dark:text-gray-400 text-sm">No recent activity</p>
            </div>
        <?php else: ?>
            <div class="p-3 sm:p-4 space-y-1">
                <?php foreach ($recent_activity as $act): ?>
                    <div class="flex items-start gap-3 p-2">
                        <div class="w-2 h-2 rounded-full bg-primary mt-2 flex-shrink-0"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-slate-600 dark:text-gray-300">
                                <span class="font-semibold"><?php echo e($act['username'] ?? 'System'); ?></span> 
                                <?php echo e($act['action']); ?>
                                <?php if (!empty($act['description'])): ?>
                                    <span class="hidden sm:inline">- <?php echo e(truncate($act['description'], 60)); ?></span>
                                <?php endif; ?>
                            </p>
                            <p class="text-xs text-slate-400 dark:text-gray-400"><?php echo time_ago($act['created_at']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
