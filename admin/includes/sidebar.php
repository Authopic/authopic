<?php
/**
 * Authopic Technologies PLC - Admin Sidebar Navigation
 */
if (!defined('BASE_PATH'))
    exit;
$admin_page = isset($admin_page) ? $admin_page : 'dashboard';

// Count new items for badges
$new_leads = db_fetch_one("SELECT COUNT(*) as c FROM leads WHERE status='new'");
$new_leads_count = $new_leads ? (int)$new_leads['c'] : 0;

$new_demos = db_fetch_one("SELECT COUNT(*) as c FROM demo_requests WHERE status='pending'");
$new_demos_count = $new_demos ? (int)$new_demos['c'] : 0;

$sidebar_items = [
    ['icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'label' => 'Dashboard', 'page' => 'dashboard', 'badge' => 0],
    ['divider' => true, 'label' => 'Content'],
    ['icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'label' => 'Pages', 'page' => 'pages', 'badge' => 0],
    ['icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'label' => 'Products', 'page' => 'products', 'badge' => 0],
    ['icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z', 'label' => 'Services', 'page' => 'services', 'badge' => 0],
    ['icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', 'label' => 'Portfolio', 'page' => 'portfolio', 'badge' => 0],
    ['icon' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z', 'label' => 'Blog Posts', 'page' => 'blog', 'badge' => 0],
    ['divider' => true, 'label' => 'People'],
    ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'label' => 'Team', 'page' => 'team', 'badge' => 0],
    ['icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z', 'label' => 'Testimonials', 'page' => 'testimonials', 'badge' => 0],
    ['divider' => true, 'label' => 'Leads & Requests'],
    ['icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'label' => 'Leads', 'page' => 'leads', 'badge' => $new_leads_count],
    ['icon' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'label' => 'Demo Requests', 'page' => 'demos', 'badge' => $new_demos_count],
    ['icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9', 'label' => 'Subscribers', 'page' => 'subscribers', 'badge' => 0],
    ['divider' => true, 'label' => 'System'],
    ['icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z', 'label' => 'Media', 'page' => 'media', 'badge' => 0],
    ['icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'label' => 'Analytics', 'page' => 'analytics', 'badge' => 0],
    ['icon' => 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4', 'label' => 'Settings', 'page' => 'settings', 'badge' => 0],
    ['icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'label' => 'Profile', 'page' => 'profile', 'badge' => 0],
];
?>

<!-- Mobile overlay -->
<div id="admin-sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 lg:hidden hidden"></div>

<!-- Sidebar -->
<aside id="admin-sidebar" class="fixed left-0 top-0 bottom-0 w-[260px] bg-white dark:bg-slate-800/50 border-r border-black/5 dark:border-white/10 z-40 overflow-y-auto transform -translate-x-full lg:translate-x-0 transition-transform duration-300">
    <!-- Logo -->
    <div class="flex items-center gap-2 px-5 h-16 border-b border-black/5 dark:border-white/10">
        <div class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center">
            <span class="text-white font-black text-sm">A</span>
        </div>
        <span class="font-extrabold text-slate-800 dark:text-white">Authopic</span>
        <span class="font-extrabold text-primary text-sm">Admin</span>
    </div>

    <!-- Nav Items -->
    <nav class="px-3 py-4 space-y-1">
        <?php foreach ($sidebar_items as $item): ?>
            <?php if (isset($item['divider'])): ?>
                <div class="pt-4 pb-2 px-3">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:text-gray-400"><?php echo $item['label']; ?></span>
                </div>
            <?php
    else: ?>
                <a href="<?php echo url('/admin/' . $item['page']); ?>"
                   class="admin-sidebar-link <?php echo $admin_page === $item['page'] ? 'active' : ''; ?>">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo $item['icon']; ?>"/>
                    </svg>
                    <span class="flex-1"><?php echo $item['label']; ?></span>
                    <?php if ($item['badge'] > 0): ?>
                        <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold bg-red-500 text-white rounded-full"><?php echo $item['badge']; ?></span>
                    <?php
        endif; ?>
                </a>
            <?php
    endif; ?>
        <?php
endforeach; ?>

        <!-- Logout -->
        <div class="pt-4 border-t border-black/5 dark:border-white/10 mt-4">
            <a href="<?php echo url('/admin/logout'); ?>" class="admin-sidebar-link text-red-500 hover:!text-red-600 hover:!bg-red-50 dark:hover:!bg-red-500/10">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span>Sign Out</span>
            </a>
        </div>
    </nav>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var sidebar = document.getElementById('admin-sidebar');
    var overlay = document.getElementById('admin-sidebar-overlay');
    var toggle = document.getElementById('admin-sidebar-toggle');

    function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
        document.body.style.overflow = '';
    }

    if (toggle) toggle.addEventListener('click', openSidebar);
    if (overlay) overlay.addEventListener('click', closeSidebar);

    // Close on link click (mobile)
    sidebar.querySelectorAll('a').forEach(function(link) {
        link.addEventListener('click', function() {
            if (window.innerWidth < 1024) closeSidebar();
        });
    });

    // Close sidebar on swipe left
    var touchStartX = 0;
    sidebar.addEventListener('touchstart', function(e) {
        touchStartX = e.touches[0].clientX;
    }, { passive: true });
    sidebar.addEventListener('touchend', function(e) {
        var dx = e.changedTouches[0].clientX - touchStartX;
        if (dx < -60) closeSidebar();
    }, { passive: true });
});
</script>
