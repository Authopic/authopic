<?php
/**
 * Authopic Technologies PLC - Admin: Analytics Dashboard
 */
if (!defined('BASE_PATH')) exit;

$period = get('period') ?: '7d';
$days_map = ['1d'=>1, '7d'=>7, '30d'=>30, '90d'=>90, '365d'=>365];
$days = $days_map[$period] ?? 7;
$date_from = date('Y-m-d', strtotime("-{$days} days"));

// Key metrics
$total_views = db_fetch_one("SELECT COUNT(*) as c FROM site_analytics WHERE DATE(created_at) >= '$date_from'")['c'] ?? 0;
$unique_visitors = db_fetch_one("SELECT COUNT(DISTINCT ip_address) as c FROM site_analytics WHERE DATE(created_at) >= '$date_from'")['c'] ?? 0;
$avg_daily = $days > 0 ? round($total_views / $days) : 0;

// Previous period for comparison
$prev_from = date('Y-m-d', strtotime("-" . ($days * 2) . " days"));
$prev_views = db_fetch_one("SELECT COUNT(*) as c FROM site_analytics WHERE DATE(created_at) >= '$prev_from' AND DATE(created_at) < '$date_from'")['c'] ?? 0;
$growth = $prev_views > 0 ? round((($total_views - $prev_views) / $prev_views) * 100, 1) : 0;

// Daily views for chart
$daily = db_fetch_all("SELECT DATE(created_at) as day, COUNT(*) as views FROM site_analytics WHERE DATE(created_at) >= '$date_from' GROUP BY DATE(created_at) ORDER BY day ASC");

// Top pages
$top_pages = db_fetch_all("SELECT page_url, COUNT(*) as views FROM site_analytics WHERE DATE(created_at) >= '$date_from' GROUP BY page_url ORDER BY views DESC LIMIT 15");

// Top referrers
$referrers = db_fetch_all("SELECT referrer, COUNT(*) as visits FROM site_analytics WHERE referrer IS NOT NULL AND referrer != '' AND DATE(created_at) >= '$date_from' GROUP BY referrer ORDER BY visits DESC LIMIT 10");

// Devices
$devices = db_fetch_all("SELECT device_type, COUNT(*) as cnt FROM site_analytics WHERE DATE(created_at) >= '$date_from' AND device_type IS NOT NULL GROUP BY device_type ORDER BY cnt DESC");

// Browsers
$browsers = db_fetch_all("SELECT browser, COUNT(*) as cnt FROM site_analytics WHERE DATE(created_at) >= '$date_from' AND browser IS NOT NULL GROUP BY browser ORDER BY cnt DESC LIMIT 8");

// OS
$os_list = db_fetch_all("SELECT os, COUNT(*) as cnt FROM site_analytics WHERE DATE(created_at) >= '$date_from' AND os IS NOT NULL GROUP BY os ORDER BY cnt DESC LIMIT 8");

// Recent leads/demos count  
$new_leads = db_fetch_one("SELECT COUNT(*) as c FROM leads WHERE DATE(created_at) >= '$date_from'")['c'] ?? 0;
$new_demos = db_fetch_one("SELECT COUNT(*) as c FROM demo_requests WHERE DATE(created_at) >= '$date_from'")['c'] ?? 0;
$new_subs = db_fetch_one("SELECT COUNT(*) as c FROM newsletter_subscribers WHERE DATE(subscribed_at) >= '$date_from'")['c'] ?? 0;
?>

<!-- Period Filter -->
<div class="flex items-center gap-2 mb-6">
    <?php foreach (['1d'=>'Today', '7d'=>'7 Days', '30d'=>'30 Days', '90d'=>'90 Days', '365d'=>'1 Year'] as $k=>$v): ?>
        <a href="<?php echo url('/admin/analytics?period=' . $k); ?>"
           class="px-3 py-1.5 text-sm font-medium rounded-lg <?php echo $period === $k ? 'bg-primary text-white' : 'bg-slate-100 dark:bg-white/5 text-slate-600 hover:bg-slate-200'; ?>">
            <?php echo $v; ?>
        </a>
    <?php endforeach; ?>
</div>

<!-- Key Metrics -->
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
    <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-4 text-center">
        <div class="text-2xl font-bold text-primary"><?php echo number_format($total_views); ?></div>
        <div class="text-xs text-slate-500">Page Views</div>
        <?php if ($growth != 0): ?>
            <div class="text-xs mt-1 <?php echo $growth >= 0 ? 'text-green-500' : 'text-red-500'; ?>"><?php echo ($growth >= 0 ? '+' : '') . $growth; ?>%</div>
        <?php endif; ?>
    </div>
    <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-4 text-center">
        <div class="text-2xl font-bold text-secondary"><?php echo number_format($unique_visitors); ?></div>
        <div class="text-xs text-slate-500">Unique Visitors</div>
    </div>
    <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-4 text-center">
        <div class="text-2xl font-bold text-slate-600 dark:text-gray-300"><?php echo number_format($avg_daily); ?></div>
        <div class="text-xs text-slate-500">Avg Daily Views</div>
    </div>
    <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-4 text-center">
        <div class="text-2xl font-bold text-green-500"><?php echo number_format($new_leads); ?></div>
        <div class="text-xs text-slate-500">New Leads</div>
    </div>
    <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-4 text-center">
        <div class="text-2xl font-bold text-orange-500"><?php echo number_format($new_demos); ?></div>
        <div class="text-xs text-slate-500">Demo Requests</div>
    </div>
    <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-4 text-center">
        <div class="text-2xl font-bold text-cyan-500"><?php echo number_format($new_subs); ?></div>
        <div class="text-xs text-slate-500">Subscribers</div>
    </div>
</div>

<!-- Daily Views Chart (CSS bar chart) -->
<div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-6 mb-6">
    <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider mb-4">Daily Page Views</h3>
    <?php if (!empty($daily)):
        $max_views = max(array_column($daily, 'views'));
        $max_views = max($max_views, 1);
    ?>
    <div class="flex items-end gap-1 h-40">
        <?php foreach ($daily as $d): 
            $height = ($d['views'] / $max_views) * 100;
        ?>
            <div class="flex-1 group relative">
                <div class="bg-primary/80 hover:bg-primary rounded-t transition-colors mx-auto" style="height: <?php echo max(2, $height); ?>%; min-width: 4px; max-width: 30px;"></div>
                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-slate-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap z-10">
                    <?php echo date('M j', strtotime($d['day'])); ?>: <?php echo number_format($d['views']); ?> views
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="flex justify-between mt-2 text-[10px] text-slate-400">
        <span><?php echo date('M j', strtotime($daily[0]['day'])); ?></span>
        <span><?php echo date('M j', strtotime(end($daily)['day'])); ?></span>
    </div>
    <?php else: ?>
        <p class="text-sm text-slate-400 text-center py-8">No data for this period.</p>
    <?php endif; ?>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Top Pages -->
    <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-6">
        <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider mb-4">Top Pages</h3>
        <?php if (empty($top_pages)): ?>
            <p class="text-sm text-slate-400">No data.</p>
        <?php else: ?>
            <div class="space-y-2">
                <?php foreach ($top_pages as $i => $pg): ?>
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="text-xs text-slate-400 w-5"><?php echo $i+1; ?></span>
                            <span class="text-slate-600 dark:text-gray-300 truncate"><?php echo e($pg['page_url'] ?: '/'); ?></span>
                        </div>
                        <span class="text-slate-500 font-medium ml-3"><?php echo number_format($pg['views']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Top Referrers -->
    <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-6">
        <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider mb-4">Top Referrers</h3>
        <?php if (empty($referrers)): ?>
            <p class="text-sm text-slate-400">No referrer data.</p>
        <?php else: ?>
            <div class="space-y-2">
                <?php foreach ($referrers as $r): ?>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-600 dark:text-gray-300 truncate"><?php echo e(parse_url($r['referrer'], PHP_URL_HOST) ?: $r['referrer']); ?></span>
                        <span class="text-slate-500 font-medium ml-3"><?php echo number_format($r['visits']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Device / Browser / OS breakdown -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
    <!-- Devices -->
    <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-6">
        <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider mb-4">Devices</h3>
        <?php if (empty($devices)): ?>
            <p class="text-sm text-slate-400">No data.</p>
        <?php else: 
            $dev_total = array_sum(array_column($devices, 'cnt'));
            foreach ($devices as $d):
                $pct = $dev_total > 0 ? round(($d['cnt'] / $dev_total) * 100, 1) : 0;
        ?>
            <div class="mb-3">
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-slate-600 dark:text-gray-300 capitalize"><?php echo e($d['device_type'] ?: 'Unknown'); ?></span>
                    <span class="text-slate-500"><?php echo $pct; ?>%</span>
                </div>
                <div class="h-2 bg-slate-100 dark:bg-white/5 rounded-full overflow-hidden">
                    <div class="h-full bg-primary rounded-full" style="width: <?php echo $pct; ?>%"></div>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>

    <!-- Browsers -->
    <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-6">
        <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider mb-4">Browsers</h3>
        <?php if (empty($browsers)): ?>
            <p class="text-sm text-slate-400">No data.</p>
        <?php else: 
            $br_total = array_sum(array_column($browsers, 'cnt'));
            foreach ($browsers as $b):
                $pct = $br_total > 0 ? round(($b['cnt'] / $br_total) * 100, 1) : 0;
        ?>
            <div class="mb-3">
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-slate-600 dark:text-gray-300"><?php echo e($b['browser'] ?: 'Unknown'); ?></span>
                    <span class="text-slate-500"><?php echo $pct; ?>%</span>
                </div>
                <div class="h-2 bg-slate-100 dark:bg-white/5 rounded-full overflow-hidden">
                    <div class="h-full bg-secondary rounded-full" style="width: <?php echo $pct; ?>%"></div>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>

    <!-- OS -->
    <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-6">
        <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider mb-4">Operating Systems</h3>
        <?php if (empty($os_list)): ?>
            <p class="text-sm text-slate-400">No data.</p>
        <?php else: 
            $os_total = array_sum(array_column($os_list, 'cnt'));
            foreach ($os_list as $o):
                $pct = $os_total > 0 ? round(($o['cnt'] / $os_total) * 100, 1) : 0;
        ?>
            <div class="mb-3">
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-slate-600 dark:text-gray-300"><?php echo e($o['os'] ?: 'Unknown'); ?></span>
                    <span class="text-slate-500"><?php echo $pct; ?>%</span>
                </div>
                <div class="h-2 bg-slate-100 dark:bg-white/5 rounded-full overflow-hidden">
                    <div class="h-full bg-green-500 rounded-full" style="width: <?php echo $pct; ?>%"></div>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>
</div>
