<?php
/**
 * Authopic Technologies PLC - Admin: Newsletter Subscribers
 */
if (!defined('BASE_PATH')) exit;

$action = get('action') ?: 'list';
$id = (int)(get('id') ?: 0);

// ---- DELETE ----
if ($action === 'delete' && $id > 0) {
    if (!csrf_verify(get('token'))) { set_flash('error', 'Invalid token.'); redirect('/admin/subscribers'); }
    db_query("DELETE FROM newsletter_subscribers WHERE id=$id");
    log_activity('delete', 'newsletter_subscribers', $id, 'Deleted subscriber');
    set_flash('success', 'Subscriber deleted.');
    redirect('/admin/subscribers');
}

// ---- EXPORT CSV ----
if ($action === 'export') {
    $subs = db_fetch_all("SELECT email, status, subscribed_at FROM newsletter_subscribers WHERE status='active' ORDER BY subscribed_at DESC");
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="subscribers_' . date('Y-m-d') . '.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Email', 'Status', 'Subscribed Date']);
    foreach ($subs as $s) {
        fputcsv($output, [$s['email'], $s['status'], $s['subscribed_at']]);
    }
    fclose($output);
    exit;
}

// ---- LIST ----
$status_filter = get('status') ?: '';
$search = trim(get('q') ?: '');
$where_parts = [];
if ($status_filter) $where_parts[] = "status='" . db_escape($status_filter) . "'";
if ($search) $where_parts[] = "email LIKE '%" . db_escape($search) . "%'";
$where = $where_parts ? 'WHERE ' . implode(' AND ', $where_parts) : '';

$total = db_fetch_one("SELECT COUNT(*) as c FROM newsletter_subscribers $where")['c'] ?? 0;
$pagination = paginate($total, 50);
$items = db_fetch_all("SELECT * FROM newsletter_subscribers $where ORDER BY subscribed_at DESC LIMIT {$pagination['offset']}, {$pagination['per_page']}");

// Stats
$total_active = db_fetch_one("SELECT COUNT(*) as c FROM newsletter_subscribers WHERE status='active'")['c'] ?? 0;
$total_unsubscribed = db_fetch_one("SELECT COUNT(*) as c FROM newsletter_subscribers WHERE status='unsubscribed'")['c'] ?? 0;
$this_month = db_fetch_one("SELECT COUNT(*) as c FROM newsletter_subscribers WHERE status='active' AND subscribed_at >= DATE_FORMAT(NOW(), '%Y-%m-01')")['c'] ?? 0;
?>

<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-4 text-center">
        <div class="text-2xl font-bold text-green-500"><?php echo number_format($total_active); ?></div>
        <div class="text-xs text-slate-500">Active Subscribers</div>
    </div>
    <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-4 text-center">
        <div class="text-2xl font-bold text-slate-400"><?php echo number_format($total_unsubscribed); ?></div>
        <div class="text-xs text-slate-500">Unsubscribed</div>
    </div>
    <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-4 text-center">
        <div class="text-2xl font-bold text-primary"><?php echo number_format($this_month); ?></div>
        <div class="text-xs text-slate-500">This Month</div>
    </div>
</div>

<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div class="flex items-center gap-2">
        <?php foreach ([''=>'All', 'active'=>'Active', 'unsubscribed'=>'Unsubscribed'] as $k=>$v): ?>
            <a href="<?php echo url('/admin/subscribers' . ($k ? '?status=' . $k : '')); ?>"
               class="px-3 py-1.5 text-sm font-medium rounded-lg <?php echo $status_filter === $k ? 'bg-primary text-white' : 'bg-slate-100 dark:bg-white/5 text-slate-600 hover:bg-slate-200'; ?>">
                <?php echo $v; ?>
            </a>
        <?php endforeach; ?>
    </div>
    <div class="flex items-center gap-2">
        <form method="GET" action="<?php echo url('/admin/subscribers'); ?>" class="flex items-center gap-2">
            <input type="text" name="q" value="<?php echo e($search); ?>" placeholder="Search email..." class="form-input !py-1.5 text-sm w-48">
            <button type="submit" class="px-3 py-1.5 bg-slate-100 dark:bg-white/5 text-slate-600 text-sm rounded-lg hover:bg-slate-200">Search</button>
        </form>
        <a href="<?php echo url('/admin/subscribers?action=export'); ?>" class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-500 text-white text-sm font-semibold rounded-lg hover:bg-green-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Export CSV
        </a>
    </div>
</div>

<div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="border-b border-black/5 dark:border-white/10">
                <th class="px-4 py-3 text-left font-semibold text-slate-500">Email</th>
                <th class="px-4 py-3 text-left font-semibold text-slate-500">Status</th>
                <th class="px-4 py-3 text-left font-semibold text-slate-500">Subscribed</th>
                <th class="px-4 py-3 text-right font-semibold text-slate-500">Actions</th>
            </tr></thead>
            <tbody class="divide-y divide-black/5 dark:divide-white/10">
                <?php if (empty($items)): ?>
                    <tr><td colspan="4" class="px-4 py-8 text-center text-slate-400">No subscribers found.</td></tr>
                <?php else: foreach ($items as $s): ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5">
                        <td class="px-4 py-3 font-medium text-slate-700 dark:text-gray-200"><?php echo e($s['email']); ?></td>
                        <td class="px-4 py-3">
                            <?php $c = $s['status'] === 'active' ? 'green' : 'slate'; ?>
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-<?php echo $c; ?>-100 dark:bg-<?php echo $c; ?>-500/10 text-<?php echo $c; ?>-600"><?php echo ucfirst($s['status']); ?></span>
                        </td>
                        <td class="px-4 py-3 text-slate-500"><?php echo format_date($s['subscribed_at'], 'M j, Y g:ia'); ?></td>
                        <td class="px-4 py-3 text-right">
                            <a href="<?php echo url('/admin/subscribers?action=delete&id=' . $s['id'] . '&token=' . csrf_token()); ?>" onclick="authConfirmDelete(this); return false;" data-label="<?php echo e($s['email']); ?>" class="px-2 py-1 text-xs text-red-500 hover:bg-red-50 rounded-lg">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($pagination['total_pages'] > 1): ?>
    <div class="mt-6"><?php echo render_pagination($pagination, '/admin/subscribers?' . ($status_filter ? 'status=' . $status_filter . '&' : '') . ($search ? 'q=' . urlencode($search) . '&' : '')); ?></div>
<?php endif; ?>
