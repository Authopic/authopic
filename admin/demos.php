<?php
/**
 * Authopic Technologies PLC - Admin: Demo Requests Management
 */
if (!defined('BASE_PATH')) exit;

$action = get('action') ?: 'list';
$id = (int)(get('id') ?: 0);

// ---- DELETE ----
if ($action === 'delete' && $id > 0) {
    if (!csrf_verify(get('token'))) { set_flash('error', 'Invalid token.'); redirect('/admin/demos'); }
    db_query("DELETE FROM demo_requests WHERE id=$id");
    log_activity('delete', 'demo_requests', $id, 'Deleted demo request');
    set_flash('success', 'Demo request deleted.');
    redirect('/admin/demos');
}

// ---- UPDATE STATUS ----
if ($action === 'status' && $id > 0 && !empty(get('status'))) {
    $valid = ['pending', 'scheduled', 'completed', 'cancelled'];
    $status = get('status');
    if (in_array($status, $valid)) {
        db_query("UPDATE demo_requests SET status='" . db_escape($status) . "' WHERE id=$id");
        log_activity('update', 'demo_requests', $id, 'Changed status to ' . $status);
        set_flash('success', 'Status updated.');
    }
    redirect('/admin/demos');
}

// ---- VIEW SINGLE ----
if ($action === 'view' && $id > 0):
    $demo = db_fetch_one("SELECT * FROM demo_requests WHERE id=$id");
    if (!$demo) { set_flash('error', 'Not found.'); redirect('/admin/demos'); }
?>
<div class="max-w-3xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="<?php echo url('/admin/demos'); ?>" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-white/5 text-slate-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-slate-800 dark:text-white">Demo Request Details</h2>
    </div>

    <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div><span class="text-xs font-semibold text-slate-400 uppercase">Name</span><p class="font-semibold text-slate-800 dark:text-white"><?php echo e($demo['name']); ?></p></div>
            <div><span class="text-xs font-semibold text-slate-400 uppercase">Email</span><p><a href="mailto:<?php echo e($demo['email']); ?>" class="text-primary hover:underline"><?php echo e($demo['email']); ?></a></p></div>
            <div><span class="text-xs font-semibold text-slate-400 uppercase">Phone</span><p class="text-slate-800 dark:text-white"><?php echo e($demo['phone']); ?></p></div>
            <div><span class="text-xs font-semibold text-slate-400 uppercase">Organization</span><p class="text-slate-800 dark:text-white"><?php echo e($demo['organization'] ?: '—'); ?></p></div>
            <div><span class="text-xs font-semibold text-slate-400 uppercase">Size</span><p class="text-slate-800 dark:text-white"><?php echo e($demo['organization_size'] ?: '—'); ?></p></div>
            <div><span class="text-xs font-semibold text-slate-400 uppercase">Role</span><p class="text-slate-800 dark:text-white"><?php echo e($demo['role'] ?: '—'); ?></p></div>
            <div><span class="text-xs font-semibold text-slate-400 uppercase">Product Interest</span><p class="text-slate-800 dark:text-white font-semibold"><?php echo e($demo['product_interest']); ?></p></div>
            <div><span class="text-xs font-semibold text-slate-400 uppercase">Preferred Date/Time</span><p class="text-slate-800 dark:text-white"><?php echo e(($demo['preferred_date'] ?: '—') . ' ' . ($demo['preferred_time'] ?: '')); ?></p></div>
            <div><span class="text-xs font-semibold text-slate-400 uppercase">Submitted</span><p class="text-slate-800 dark:text-white"><?php echo format_date($demo['created_at']); ?></p></div>
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase">Status</span>
                <div class="flex gap-2 mt-1">
                    <?php foreach (['pending', 'scheduled', 'completed', 'cancelled'] as $s): ?>
                        <a href="<?php echo url('/admin/demos?action=status&id=' . $id . '&status=' . $s . '&token=' . csrf_token()); ?>"
                           class="px-2 py-0.5 text-xs font-semibold rounded-full <?php echo $demo['status'] === $s ? 'bg-primary text-white' : 'bg-slate-100 dark:bg-white/5 text-slate-500 hover:bg-slate-200'; ?>">
                            <?php echo ucfirst($s); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php if (!empty($demo['message'])): ?>
            <div><span class="text-xs font-semibold text-slate-400 uppercase">Message</span><p class="text-slate-600 dark:text-gray-300 mt-1 whitespace-pre-wrap"><?php echo e($demo['message']); ?></p></div>
        <?php endif; ?>
    </div>
</div>

<?php else: // LIST

$status_filter = get('status') ?: '';
$where = $status_filter ? "WHERE status='" . db_escape($status_filter) . "'" : '';
$total = db_fetch_one("SELECT COUNT(*) as c FROM demo_requests $where")['c'] ?? 0;
$pagination = paginate($total, 20);
$demos = db_fetch_all("SELECT * FROM demo_requests $where ORDER BY created_at DESC LIMIT {$pagination['offset']}, {$pagination['per_page']}");
?>

<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div class="flex items-center gap-2">
        <?php foreach (['', 'pending', 'scheduled', 'completed', 'cancelled'] as $s): ?>
            <a href="<?php echo url('/admin/demos' . ($s ? '?status=' . $s : '')); ?>"
               class="px-3 py-1.5 text-sm font-medium rounded-lg transition-colors <?php echo $status_filter === $s ? 'bg-primary text-white' : 'bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-gray-300 hover:bg-slate-200'; ?>">
                <?php echo $s ? ucfirst($s) : 'All'; ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="border-b border-black/5 dark:border-white/10">
                <th class="px-4 py-3 text-left font-semibold text-slate-500">Name</th>
                <th class="px-4 py-3 text-left font-semibold text-slate-500">Product</th>
                <th class="px-4 py-3 text-left font-semibold text-slate-500">Date</th>
                <th class="px-4 py-3 text-left font-semibold text-slate-500">Status</th>
                <th class="px-4 py-3 text-right font-semibold text-slate-500">Actions</th>
            </tr></thead>
            <tbody class="divide-y divide-black/5 dark:divide-white/10">
                <?php if (empty($demos)): ?>
                    <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">No demo requests.</td></tr>
                <?php else: foreach ($demos as $d): ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5">
                        <td class="px-4 py-3">
                            <div class="font-semibold text-slate-700 dark:text-gray-200"><?php echo e($d['name']); ?></div>
                            <div class="text-xs text-slate-400"><?php echo e($d['organization']); ?></div>
                        </td>
                        <td class="px-4 py-3 text-slate-600 dark:text-gray-300"><?php echo e($d['product_interest']); ?></td>
                        <td class="px-4 py-3 text-slate-500"><?php echo e($d['preferred_date'] ?: time_ago($d['created_at'])); ?></td>
                        <td class="px-4 py-3">
                            <?php $c = ['pending'=>'orange','scheduled'=>'blue','completed'=>'green','cancelled'=>'red'][$d['status']] ?? 'slate'; ?>
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-<?php echo $c; ?>-100 dark:bg-<?php echo $c; ?>-500/10 text-<?php echo $c; ?>-600"><?php echo ucfirst($d['status']); ?></span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="<?php echo url('/admin/demos?action=view&id=' . $d['id']); ?>" class="px-2 py-1 text-xs text-primary hover:bg-blue-50 rounded-lg">View</a>
                            <a href="<?php echo url('/admin/demos?action=delete&id=' . $d['id'] . '&token=' . csrf_token()); ?>" onclick="return confirmDelete()" class="px-2 py-1 text-xs text-red-500 hover:bg-red-50 rounded-lg">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($pagination['total_pages'] > 1): ?>
    <div class="mt-6"><?php echo render_pagination($pagination, '/admin/demos?' . ($status_filter ? 'status=' . $status_filter . '&' : '')); ?></div>
<?php endif; ?>

<?php endif; ?>
