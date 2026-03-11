<?php
/**
 * Authopic Technologies PLC - Admin: Leads Management
 */
if (!defined('BASE_PATH')) exit;

$action = get('action') ?: 'list';
$id = (int)(get('id') ?: 0);

// ---- DELETE ----
if ($action === 'delete' && $id > 0) {
    if (!csrf_verify(get('token'))) { set_flash('error', 'Invalid token.'); redirect('/admin/leads'); }
    db_query("DELETE FROM leads WHERE id=$id");
    log_activity('delete', 'leads', $id, 'Deleted lead');
    set_flash('success', 'Lead deleted.');
    redirect('/admin/leads');
}

// ---- UPDATE STATUS ----
if ($action === 'status' && $id > 0 && !empty(get('status'))) {
    $valid = ['new', 'contacted', 'qualified', 'converted', 'closed'];
    $status = get('status');
    if (in_array($status, $valid)) {
        db_query("UPDATE leads SET status='" . db_escape($status) . "' WHERE id=$id");
        log_activity('update', 'leads', $id, 'Changed status to ' . $status);
        set_flash('success', 'Lead status updated.');
    }
    redirect('/admin/leads' . ($action === 'status' && get('return') === 'view' ? '?action=view&id=' . $id : ''));
}

// ---- ADD FOLLOW-UP ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'followup' && $id > 0) {
    if (!csrf_verify(post('csrf_token'))) { set_flash('error', 'Invalid token.'); redirect('/admin/leads?action=view&id=' . $id); }
    $note = trim(post('note'));
    $type = db_escape(trim(post('type')));
    $next = trim(post('next_followup'));
    if (!empty($note)) {
        $safe_note = db_escape($note);
        $safe_next = !empty($next) ? "'" . db_escape($next) . "'" : 'NULL';
        $admin_id = (int)$_SESSION['admin_id'];
        db_query("INSERT INTO lead_followups (lead_id, admin_id, type, note, next_followup_date, created_at) VALUES ($id, $admin_id, '$type', '$safe_note', $safe_next, NOW())");
        log_activity('create', 'lead_followups', $id, 'Added follow-up');
        set_flash('success', 'Follow-up added.');
    }
    redirect('/admin/leads?action=view&id=' . $id);
}

// ---- VIEW SINGLE ----
if ($action === 'view' && $id > 0):
    $lead = db_fetch_one("SELECT * FROM leads WHERE id=$id");
    if (!$lead) { set_flash('error', 'Lead not found.'); redirect('/admin/leads'); }
    
    // Mark as read if new
    if ($lead['status'] === 'new') {
        db_query("UPDATE leads SET status='contacted' WHERE id=$id");
        $lead['status'] = 'contacted';
    }

    $followups = db_fetch_all("SELECT lf.*, au.full_name as admin_name FROM lead_followups lf LEFT JOIN admin_users au ON lf.admin_id=au.id WHERE lf.lead_id=$id ORDER BY lf.created_at DESC");
?>
<div class="max-w-4xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="<?php echo url('/admin/leads'); ?>" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-white/5 text-slate-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-slate-800 dark:text-white">Lead Details</h2>
    </div>

    <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-6 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div><span class="text-xs font-semibold text-slate-400 uppercase">Name</span><p class="text-slate-800 dark:text-white font-semibold"><?php echo e($lead['name']); ?></p></div>
            <div><span class="text-xs font-semibold text-slate-400 uppercase">Email</span><p class="text-slate-800 dark:text-white"><a href="mailto:<?php echo e($lead['email']); ?>" class="text-primary hover:underline"><?php echo e($lead['email']); ?></a></p></div>
            <div><span class="text-xs font-semibold text-slate-400 uppercase">Phone</span><p class="text-slate-800 dark:text-white"><?php echo e($lead['phone'] ?: '—'); ?></p></div>
            <div><span class="text-xs font-semibold text-slate-400 uppercase">Subject</span><p class="text-slate-800 dark:text-white"><?php echo e($lead['subject']); ?></p></div>
            <div><span class="text-xs font-semibold text-slate-400 uppercase">Source</span><p class="text-slate-800 dark:text-white"><?php echo e($lead['source']); ?></p></div>
            <div><span class="text-xs font-semibold text-slate-400 uppercase">Date</span><p class="text-slate-800 dark:text-white"><?php echo format_date($lead['created_at']); ?></p></div>
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase">Status</span>
                <div class="flex items-center gap-2 mt-1">
                    <?php
                    $statuses = ['new', 'contacted', 'qualified', 'converted', 'closed'];
                    foreach ($statuses as $s):
                        $active = $lead['status'] === $s;
                    ?>
                        <a href="<?php echo url('/admin/leads?action=status&id=' . $id . '&status=' . $s . '&return=view&token=' . csrf_token()); ?>"
                           class="px-2 py-0.5 text-xs font-semibold rounded-full transition-all <?php echo $active ? 'bg-primary text-white' : 'bg-slate-100 dark:bg-white/5 text-slate-500 hover:bg-slate-200 dark:hover:bg-white/10'; ?>">
                            <?php echo ucfirst($s); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div>
            <span class="text-xs font-semibold text-slate-400 uppercase">Message</span>
            <p class="text-slate-600 dark:text-gray-300 mt-1 whitespace-pre-wrap"><?php echo e($lead['message']); ?></p>
        </div>
    </div>

    <!-- Follow-ups -->
    <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-6 mb-6">
        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4">Follow-ups</h3>
        <form method="POST" action="<?php echo url('/admin/leads?action=followup&id=' . $id); ?>" class="mb-6">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">
                <select name="type" class="form-input text-sm">
                    <option value="note">Note</option>
                    <option value="call">Call</option>
                    <option value="email">Email</option>
                    <option value="meeting">Meeting</option>
                </select>
                <input type="date" name="next_followup" class="form-input text-sm" placeholder="Next follow-up">
                <button type="submit" class="px-4 py-2 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-colors">Add Follow-up</button>
            </div>
            <textarea name="note" rows="2" required class="form-input text-sm" placeholder="Write your follow-up note..."></textarea>
        </form>

        <?php if (empty($followups)): ?>
            <p class="text-slate-400 text-sm">No follow-ups yet.</p>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($followups as $fu): ?>
                    <div class="flex gap-3 p-3 rounded-xl bg-slate-50 dark:bg-white/5">
                        <div class="w-2 h-2 rounded-full bg-primary mt-2 flex-shrink-0"></div>
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-bold uppercase text-primary"><?php echo e($fu['type']); ?></span>
                                <span class="text-xs text-slate-400"><?php echo time_ago($fu['created_at']); ?> by <?php echo e($fu['admin_name'] ?? 'Admin'); ?></span>
                            </div>
                            <p class="text-sm text-slate-600 dark:text-gray-300"><?php echo e($fu['note']); ?></p>
                            <?php if (!empty($fu['next_followup_date'])): ?>
                                <p class="text-xs text-orange-500 mt-1">Next: <?php echo format_date($fu['next_followup_date']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php else: // ---- LIST ----

$status_filter = get('status') ?: '';
$search = get('search') ?: '';
$where = "WHERE 1=1";
if (!empty($status_filter)) $where .= " AND status='" . db_escape($status_filter) . "'";
if (!empty($search)) { $s = '%' . db_escape($search) . '%'; $where .= " AND (name LIKE '$s' OR email LIKE '$s' OR interest LIKE '$s')"; }

$total = db_fetch_one("SELECT COUNT(*) as c FROM leads $where")['c'] ?? 0;
$pagination = paginate($total, 20);
$leads = db_fetch_all("SELECT * FROM leads $where ORDER BY created_at DESC LIMIT {$pagination['offset']}, {$pagination['per_page']}");
?>

<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div class="flex items-center gap-2">
        <?php foreach (['', 'new', 'contacted', 'qualified', 'converted', 'closed'] as $s): ?>
            <a href="<?php echo url('/admin/leads' . ($s ? '?status=' . $s : '')); ?>"
               class="px-3 py-1.5 text-sm font-medium rounded-lg transition-colors <?php echo $status_filter === $s ? 'bg-primary text-white' : 'bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-gray-300 hover:bg-slate-200 dark:hover:bg-white/10'; ?>">
                <?php echo $s ? ucfirst($s) : 'All'; ?>
            </a>
        <?php endforeach; ?>
    </div>
    <form method="GET" action="<?php echo url('/admin/leads'); ?>" class="flex items-center gap-2">
        <input type="text" name="search" value="<?php echo e($search); ?>" placeholder="Search leads..." class="form-input text-sm py-1.5 w-48">
        <button class="p-2 bg-primary text-white rounded-lg"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg></button>
    </form>
</div>

<div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-black/5 dark:border-white/10">
                    <th class="px-4 py-3 text-left font-semibold text-slate-500 dark:text-gray-400" data-sort>Name</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-500 dark:text-gray-400">Subject</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-500 dark:text-gray-400">Status</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-500 dark:text-gray-400" data-sort>Date</th>
                    <th class="px-4 py-3 text-right font-semibold text-slate-500 dark:text-gray-400">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5 dark:divide-white/10">
                <?php if (empty($leads)): ?>
                    <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">No leads found.</td></tr>
                <?php else: ?>
                    <?php foreach ($leads as $lead): ?>
                        <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-4 py-3">
                                <div class="font-semibold text-slate-700 dark:text-gray-200"><?php echo e($lead['name']); ?></div>
                                <div class="text-xs text-slate-400"><?php echo e($lead['email']); ?></div>
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-gray-300"><?php echo e(truncate($lead['subject'], 40)); ?></td>
                            <td class="px-4 py-3">
                                <?php
                                $colors = ['new' => 'green', 'contacted' => 'blue', 'qualified' => 'purple', 'converted' => 'cyan', 'closed' => 'slate'];
                                $c = $colors[$lead['status']] ?? 'slate';
                                ?>
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-<?php echo $c; ?>-100 dark:bg-<?php echo $c; ?>-500/10 text-<?php echo $c; ?>-600 dark:text-<?php echo $c; ?>-400">
                                    <?php echo ucfirst($lead['status']); ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-500 dark:text-gray-400"><?php echo time_ago($lead['created_at']); ?></td>
                            <td class="px-4 py-3 text-right">
                                <a href="<?php echo url('/admin/leads?action=view&id=' . $lead['id']); ?>" class="inline-flex items-center px-2 py-1 text-xs text-primary hover:bg-blue-50 dark:hover:bg-blue-500/10 rounded-lg transition-colors">View</a>
                                <a href="<?php echo url('/admin/leads?action=delete&id=' . $lead['id'] . '&token=' . csrf_token()); ?>" onclick="authConfirmDelete(this); return false;" data-label="<?php echo e($lead['name']); ?>" class="inline-flex items-center px-2 py-1 text-xs text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($pagination['total_pages'] > 1): ?>
    <div class="mt-6"><?php echo render_pagination($pagination, '/admin/leads?' . ($status_filter ? 'status=' . $status_filter . '&' : '') . ($search ? 'search=' . urlencode($search) . '&' : '')); ?></div>
<?php endif; ?>

<?php endif; ?>
