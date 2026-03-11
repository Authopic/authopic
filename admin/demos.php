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
    $valid = ['pending', 'confirmed', 'completed', 'cancelled', 'rescheduled'];
    $status = get('status');
    if (in_array($status, $valid)) {
        db_query("UPDATE demo_requests SET status='" . db_escape($status) . "' WHERE id=$id");
        log_activity('update', 'demo_requests', $id, 'Changed status to ' . $status);
        set_flash('success', 'Status updated.');
    }
    redirect('/admin/demos');
}

// ---- CREATE (POST) ----
if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify(post('csrf_token'))) { set_flash('error', 'Invalid token.'); redirect('/admin/demos?action=create'); }

    $first_name     = trim(post('first_name'));
    $last_name      = trim(post('last_name'));
    $email          = trim(post('email'));
    $phone          = trim(post('phone'));
    $company        = trim(post('company'));
    $product        = trim(post('product'));
    $preferred_date = trim(post('preferred_date'));
    $preferred_time = trim(post('preferred_time')) ?: '09:00:00';
    $notes          = trim(post('notes'));
    $status         = trim(post('status'));
    $admin_notes    = trim(post('admin_notes'));

    $errors = [];
    if (empty($first_name))                              $errors[] = 'First name is required.';
    if (empty($last_name))                               $errors[] = 'Last name is required.';
    if (empty($email) || !is_valid_email($email))        $errors[] = 'Valid email is required.';
    if (empty($preferred_date))                          $errors[] = 'Preferred date is required.';
    if (!in_array($product, ['sms','erp','both']))        $errors[] = 'Please select a product.';
    if (!in_array($status, ['pending','confirmed','completed','cancelled','rescheduled'])) $status = 'pending';

    if (empty($errors)) {
        $f  = db_escape($first_name);
        $l  = db_escape($last_name);
        $em = db_escape($email);
        $ph = db_escape($phone);
        $co = db_escape($company);
        $pr = db_escape($product);
        $pd = db_escape($preferred_date);
        $pt = db_escape($preferred_time);
        $no = db_escape($notes);
        $st = db_escape($status);
        $an = db_escape($admin_notes);

        db_query("INSERT INTO demo_requests (first_name, last_name, email, phone, company, product, preferred_date, preferred_time, notes, status, admin_notes, created_at)
                  VALUES ('$f','$l','$em','$ph','$co','$pr','$pd','$pt','$no','$st','$an', NOW())");
        $new_id = db_insert_id();
        log_activity('create', 'demo_requests', $new_id, "Admin manually added demo request for $first_name $last_name");
        set_flash('success', 'Demo request added successfully.');
        redirect('/admin/demos');
    }
    // Fall through to show form with errors
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
            <div><span class="text-xs font-semibold text-slate-400 uppercase">Name</span><p class="font-semibold text-slate-800 dark:text-white"><?php echo e(trim($demo['first_name'] . ' ' . $demo['last_name'])); ?></p></div>
            <div><span class="text-xs font-semibold text-slate-400 uppercase">Email</span><p><a href="mailto:<?php echo e($demo['email']); ?>" class="text-primary hover:underline"><?php echo e($demo['email']); ?></a></p></div>
            <div><span class="text-xs font-semibold text-slate-400 uppercase">Phone</span><p class="text-slate-800 dark:text-white"><?php echo e($demo['phone']); ?></p></div>
            <div><span class="text-xs font-semibold text-slate-400 uppercase">Organization</span><p class="text-slate-800 dark:text-white"><?php echo e($demo['company'] ?: '—'); ?></p></div>
            <div><span class="text-xs font-semibold text-slate-400 uppercase">Size</span><p class="text-slate-800 dark:text-white"><?php echo e($demo['organization_size'] ?? '—'); ?></p></div>
            <div><span class="text-xs font-semibold text-slate-400 uppercase">Role</span><p class="text-slate-800 dark:text-white"><?php echo e($demo['role'] ?? '—'); ?></p></div>
            <div><span class="text-xs font-semibold text-slate-400 uppercase">Product Interest</span><p class="text-slate-800 dark:text-white font-semibold"><?php echo e($demo['product']); ?></p></div>
            <div><span class="text-xs font-semibold text-slate-400 uppercase">Preferred Date/Time</span><p class="text-slate-800 dark:text-white"><?php echo e(($demo['preferred_date'] ?: '—') . ' ' . ($demo['preferred_time'] ?: '')); ?></p></div>
            <div><span class="text-xs font-semibold text-slate-400 uppercase">Submitted</span><p class="text-slate-800 dark:text-white"><?php echo format_date($demo['created_at']); ?></p></div>
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase">Status</span>
                <div class="flex gap-2 mt-1">
                    <?php foreach (['pending', 'confirmed', 'completed', 'cancelled', 'rescheduled'] as $s): ?>
                        <a href="<?php echo url('/admin/demos?action=status&id=' . $id . '&status=' . $s . '&token=' . csrf_token()); ?>"
                           class="px-2 py-0.5 text-xs font-semibold rounded-full <?php echo $demo['status'] === $s ? 'bg-primary text-white' : 'bg-slate-100 dark:bg-white/5 text-slate-500 hover:bg-slate-200'; ?>">
                            <?php echo ucfirst($s); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php if (!empty($demo['notes'])): ?>
            <div><span class="text-xs font-semibold text-slate-400 uppercase">Notes</span><p class="text-slate-600 dark:text-gray-300 mt-1 whitespace-pre-wrap"><?php echo e($demo['notes']); ?></p></div>
        <?php endif; ?>
        <?php if (!empty($demo['admin_notes'])): ?>
            <div class="mt-4 pt-4 border-t border-black/5 dark:border-white/10"><span class="text-xs font-semibold text-slate-400 uppercase">Admin Notes</span><p class="text-slate-600 dark:text-gray-300 mt-1 whitespace-pre-wrap"><?php echo e($demo['admin_notes']); ?></p></div>
        <?php endif; ?>
    </div>
</div>

<?php elseif ($action === 'create'): // CREATE FORM
$errors = $errors ?? [];
?>
<div class="max-w-3xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="<?php echo url('/admin/demos'); ?>" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-white/5 text-slate-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-slate-800 dark:text-white">Add Demo Request</h2>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-xl p-4 mb-6">
        <?php foreach ($errors as $err): ?><p class="text-sm text-red-600 dark:text-red-400"><?php echo e($err); ?></p><?php endforeach; ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo url('/admin/demos?action=create'); ?>" class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-6 space-y-6">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

        <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Contact Information</h3>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-1">First Name <span class="text-red-500">*</span></label>
                <input type="text" name="first_name" value="<?php echo e(post('first_name')); ?>" required class="w-full px-3 py-2.5 bg-slate-50 dark:bg-white/5 border border-black/5 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/50">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-1">Last Name <span class="text-red-500">*</span></label>
                <input type="text" name="last_name" value="<?php echo e(post('last_name')); ?>" required class="w-full px-3 py-2.5 bg-slate-50 dark:bg-white/5 border border-black/5 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/50">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-1">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="<?php echo e(post('email')); ?>" required class="w-full px-3 py-2.5 bg-slate-50 dark:bg-white/5 border border-black/5 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/50">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-1">Phone</label>
                <input type="tel" name="phone" value="<?php echo e(post('phone')); ?>" placeholder="+251-9XX-XXXXXX" class="w-full px-3 py-2.5 bg-slate-50 dark:bg-white/5 border border-black/5 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/50">
            </div>
        </div>

        <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider pt-2">Organization</h3>
        <div class="grid sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-1">Company / Organization</label>
                <input type="text" name="company" value="<?php echo e(post('company')); ?>" class="w-full px-3 py-2.5 bg-slate-50 dark:bg-white/5 border border-black/5 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/50">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-1">Product Interest <span class="text-red-500">*</span></label>
                <select name="product" required class="w-full px-3 py-2.5 bg-slate-50 dark:bg-white/5 border border-black/5 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/50">
                    <option value="">Select...</option>
                    <option value="sms"  <?php echo post('product') === 'sms'  ? 'selected' : ''; ?>>SMS / School Management</option>
                    <option value="erp"  <?php echo post('product') === 'erp'  ? 'selected' : ''; ?>>ERP System</option>
                    <option value="both" <?php echo post('product') === 'both' ? 'selected' : ''; ?>>Both</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2.5 bg-slate-50 dark:bg-white/5 border border-black/5 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/50">
                    <?php foreach (['pending','confirmed','completed','cancelled','rescheduled'] as $s): ?>
                        <option value="<?php echo $s; ?>" <?php echo (post('status') ?: 'pending') === $s ? 'selected' : ''; ?>><?php echo ucfirst($s); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider pt-2">Preferred Schedule</h3>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-1">Preferred Date <span class="text-red-500">*</span></label>
                <input type="date" name="preferred_date" value="<?php echo e(post('preferred_date') ?: date('Y-m-d', strtotime('+1 day'))); ?>" required min="<?php echo date('Y-m-d'); ?>" class="w-full px-3 py-2.5 bg-slate-50 dark:bg-white/5 border border-black/5 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/50">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-1">Preferred Time</label>
                <select name="preferred_time" class="w-full px-3 py-2.5 bg-slate-50 dark:bg-white/5 border border-black/5 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/50">
                    <option value="09:00:00" <?php echo post('preferred_time') === '09:00:00' ? 'selected' : ''; ?>>9:00 AM – 12:00 PM</option>
                    <option value="14:00:00" <?php echo post('preferred_time') === '14:00:00' ? 'selected' : ''; ?>>2:00 PM – 5:00 PM</option>
                    <option value="09:00:00">Flexible</option>
                </select>
            </div>
        </div>

        <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider pt-2">Notes</h3>
        <div class="grid gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-1">Client Notes</label>
                <textarea name="notes" rows="3" class="w-full px-3 py-2.5 bg-slate-50 dark:bg-white/5 border border-black/5 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/50 resize-none" placeholder="Any notes from the client..."><?php echo e(post('notes')); ?></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-1">Admin Notes</label>
                <textarea name="admin_notes" rows="3" class="w-full px-3 py-2.5 bg-slate-50 dark:bg-white/5 border border-black/5 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/50 resize-none" placeholder="Internal notes (not visible to client)..."><?php echo e(post('admin_notes')); ?></textarea>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="px-6 py-2.5 bg-primary hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-primary/20 transition-all">Save Demo Request</button>
            <a href="<?php echo url('/admin/demos'); ?>" class="px-6 py-2.5 bg-slate-100 dark:bg-white/5 hover:bg-slate-200 text-slate-700 dark:text-gray-300 text-sm font-semibold rounded-xl transition-all">Cancel</a>
        </div>
    </form>
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
        <?php foreach (['', 'pending', 'confirmed', 'completed', 'cancelled', 'rescheduled'] as $s): ?>
            <a href="<?php echo url('/admin/demos' . ($s ? '?status=' . $s : '')); ?>"
               class="px-3 py-1.5 text-sm font-medium rounded-lg transition-colors <?php echo $status_filter === $s ? 'bg-primary text-white' : 'bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-gray-300 hover:bg-slate-200'; ?>">
                <?php echo $s ? ucfirst($s) : 'All'; ?>
            </a>
        <?php endforeach; ?>
    </div>
    <a href="<?php echo url('/admin/demos?action=create'); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-primary hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-primary/20 transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Demo Request
    </a>
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
                            <div class="font-semibold text-slate-700 dark:text-gray-200"><?php echo e(trim($d['first_name'] . ' ' . $d['last_name'])); ?></div>
                            <div class="text-xs text-slate-400"><?php echo e($d['company'] ?? ''); ?></div>
                        </td>
                        <td class="px-4 py-3 text-slate-600 dark:text-gray-300"><?php echo e($d['product'] ?? ''); ?></td>
                        <td class="px-4 py-3 text-slate-500"><?php echo e($d['preferred_date'] ?: time_ago($d['created_at'])); ?></td>
                        <td class="px-4 py-3">
                            <?php $c = ['pending'=>'orange','scheduled'=>'blue','completed'=>'green','cancelled'=>'red'][$d['status']] ?? 'slate'; ?>
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-<?php echo $c; ?>-100 dark:bg-<?php echo $c; ?>-500/10 text-<?php echo $c; ?>-600"><?php echo ucfirst($d['status']); ?></span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="<?php echo url('/admin/demos?action=view&id=' . $d['id']); ?>" class="px-2 py-1 text-xs text-primary hover:bg-blue-50 rounded-lg">View</a>
                            <a href="<?php echo url('/admin/demos?action=delete&id=' . $d['id'] . '&token=' . csrf_token()); ?>" onclick="authConfirmDelete(this); return false;" data-label="<?php echo e($d['first_name'] . ' ' . $d['last_name']); ?>" class="px-2 py-1 text-xs text-red-500 hover:bg-red-50 rounded-lg">Delete</a>
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
