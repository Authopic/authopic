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

// ---- SEND EMAIL (POST handler) ----
if ($action === 'send' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify(post('csrf_token'))) { set_flash('error', 'Invalid token.'); redirect('/admin/subscribers'); }

    $subject        = trim(post('subject') ?: '');
    $body_input     = trim(post('body') ?: '');
    $recipients_type = post('recipients') ?: 'selected';
    $ids_raw        = trim(post('ids') ?: '');

    if (empty($subject) || empty($body_input)) {
        set_flash('error', 'Subject and body are required.');
        redirect('/admin/subscribers?action=compose&recipients=' . urlencode($recipients_type) . '&ids=' . urlencode($ids_raw));
    }

    if ($recipients_type === 'all') {
        $target_subs = db_fetch_all("SELECT email FROM newsletter_subscribers WHERE status='active'");
    } else {
        $ids_clean = implode(',', array_filter(array_map('intval', explode(',', $ids_raw))));
        $target_subs = $ids_clean ? db_fetch_all("SELECT email FROM newsletter_subscribers WHERE id IN ($ids_clean) AND status='active'") : [];
    }

    if (empty($target_subs)) {
        set_flash('error', 'No active subscribers found for selection.');
        redirect('/admin/subscribers');
    }

    // Build branded HTML body from admin plain-text input
    $site_name = get_setting('site_name') ?: 'Authopic Technologies PLC';
    $site_url  = rtrim(defined('SITE_URL') ? SITE_URL : '', '/');
    $paragraphs = array_filter(array_map('trim', preg_split('/\n{2,}/', $body_input)));
    $body_html  = '';
    foreach ($paragraphs as $p) {
        $body_html .= '<p style="margin:0 0 18px;font-size:15px;color:#334155;line-height:1.7;">' . nl2br(htmlspecialchars($p, ENT_QUOTES, 'UTF-8')) . '</p>';
    }
    if (!$body_html) {
        $body_html = '<p style="margin:0 0 18px;font-size:15px;color:#334155;line-height:1.7;">' . nl2br(htmlspecialchars($body_input, ENT_QUOTES, 'UTF-8')) . '</p>';
    }

    $full_html = '<!DOCTYPE html><html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>' . htmlspecialchars($subject, ENT_QUOTES, 'UTF-8') . '</title></head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f1f5f9;padding:40px 16px;">
<tr><td align="center">
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;">
<tr><td style="background:linear-gradient(135deg,#0066FF 0%,#06B6D4 100%);border-radius:20px 20px 0 0;padding:40px;text-align:center;">
  <h1 style="margin:0;font-size:24px;font-weight:800;color:#fff;">' . htmlspecialchars($subject, ENT_QUOTES, 'UTF-8') . '</h1>
  <p style="margin:10px 0 0;font-size:13px;color:rgba(255,255,255,0.75);">From ' . htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8') . '</p>
</td></tr>
<tr><td style="background:#fff;padding:36px 40px;">' . $body_html . '
  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:28px;">
    <tr><td align="center">
      <a href="' . htmlspecialchars($site_url, ENT_QUOTES, 'UTF-8') . '" style="display:inline-block;padding:13px 30px;background:linear-gradient(135deg,#0066FF,#06B6D4);color:#fff;font-size:14px;font-weight:700;border-radius:10px;text-decoration:none;">Visit Our Website &rarr;</a>
    </td></tr>
  </table>
</td></tr>
<tr><td style="background:#0f172a;border-radius:0 0 20px 20px;padding:24px 40px;text-align:center;">
  <p style="margin:0 0 6px;font-size:13px;color:#94a3b8;">&copy; ' . date('Y') . ' ' . htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8') . '. All rights reserved.</p>
  <p style="margin:0;font-size:12px;color:#64748b;">You receive this because you subscribed to our newsletter.</p>
</td></tr>
</table>
</td></tr>
</table>
</body></html>';

    $sent = 0; $failed = 0;
    foreach ($target_subs as $sub) {
        if (send_email($sub['email'], $subject, $full_html)) $sent++; else $failed++;
    }

    log_activity('email', 'newsletter_subscribers', 0, "Newsletter '$subject' sent to $sent subscriber(s)");
    if ($failed > 0) {
        set_flash('warning', "Sent to $sent subscriber(s). $failed failed — check SMTP configuration.");
    } else {
        set_flash('success', "Email successfully sent to $sent subscriber(s).");
    }
    redirect('/admin/subscribers');
}

// ---- COMPOSE PREP (before list queries so redirect works) ----
$compose_recipients_type = '';
$compose_ids_raw = '';
$compose_recipient_label = '';
$compose_count = 0;
if ($action === 'compose') {
    $compose_recipients_type = '';
    if (!empty($_POST['recipients'])) {
        $compose_recipients_type = $_POST['recipients'];
    } elseif (get('recipients')) {
        $compose_recipients_type = get('recipients');
    }
    $compose_recipients_type = in_array($compose_recipients_type, ['all','selected']) ? $compose_recipients_type : 'selected';

    if ($compose_recipients_type !== 'all') {
        if (!empty($_POST['ids'])) {
            $raw = is_array($_POST['ids']) ? implode(',', $_POST['ids']) : $_POST['ids'];
            $compose_ids_raw = implode(',', array_filter(array_map('intval', explode(',', $raw))));
        } elseif (get('ids')) {
            $compose_ids_raw = implode(',', array_filter(array_map('intval', explode(',', get('ids')))));
        }
        if (empty($compose_ids_raw)) {
            set_flash('error', 'No subscribers selected.');
            redirect('/admin/subscribers');
        }
        $ids_clean = $compose_ids_raw;
        $compose_count = (int)(db_fetch_one("SELECT COUNT(*) as c FROM newsletter_subscribers WHERE id IN ($ids_clean) AND status='active'")['c'] ?? 0);
        $compose_recipient_label = "$compose_count selected subscriber(s)";
    } else {
        $compose_count = (int)(db_fetch_one("SELECT COUNT(*) as c FROM newsletter_subscribers WHERE status='active'")['c'] ?? 0);
        $compose_recipient_label = "All $compose_count active subscribers";
    }
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

<?php if ($action === 'compose'): ?>
<!-- ===== COMPOSE EMAIL FORM ===== -->
<div class="max-w-3xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="<?php echo url('/admin/subscribers'); ?>" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-white/5 text-slate-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-slate-800 dark:text-white">Compose Newsletter Email</h2>
    </div>

    <div class="flex items-center gap-3 p-4 bg-primary/5 border border-primary/20 rounded-xl mb-6">
        <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-slate-800 dark:text-white">Sending to: <span class="text-primary"><?php echo htmlspecialchars($compose_recipient_label, ENT_QUOTES, 'UTF-8'); ?></span></p>
            <?php if ($compose_recipients_type !== 'all'): ?>
                <p class="text-xs text-slate-500">Only active subscribers in your selection will receive this email.</p>
            <?php else: ?>
                <p class="text-xs text-slate-500">All active subscribers will receive this email.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($compose_count === 0): ?>
        <div class="p-4 bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 rounded-xl text-sm text-amber-700 dark:text-amber-400 mb-4">
            No active subscribers match this selection. Nothing will be sent.
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo url('/admin/subscribers?action=send'); ?>" class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-6 space-y-5">
        <input type="hidden" name="csrf_token"  value="<?php echo csrf_token(); ?>">
        <input type="hidden" name="recipients"  value="<?php echo htmlspecialchars($compose_recipients_type, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="ids"          value="<?php echo htmlspecialchars($compose_ids_raw, ENT_QUOTES, 'UTF-8'); ?>">

        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-1.5">Subject Line <span class="text-red-500">*</span></label>
            <input type="text" name="subject" required placeholder="e.g. Exciting updates from Authopic!"
                   class="w-full px-4 py-2.5 bg-slate-50 dark:bg-white/5 border border-black/5 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/50">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-1.5">Email Body <span class="text-red-500">*</span></label>
            <p class="text-xs text-slate-400 mb-2">Write in plain text. Separate paragraphs with a blank line. The email will be wrapped in a professional branded template automatically.</p>
            <textarea name="body" required rows="14"
                      class="w-full px-4 py-3 bg-slate-50 dark:bg-white/5 border border-black/5 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/50 resize-y font-mono leading-relaxed"
                      placeholder="Dear Subscriber,&#10;&#10;We have exciting news to share with you...&#10;&#10;Our latest update includes...&#10;&#10;Best regards,&#10;The Authopic Team"></textarea>
        </div>

        <div class="bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 rounded-xl p-4 text-sm text-amber-700 dark:text-amber-400">
            <strong>Before you send:</strong> This will immediately email <strong><?php echo htmlspecialchars($compose_recipient_label, ENT_QUOTES, 'UTF-8'); ?></strong>. Make sure your SMTP is configured and the content is correct.
        </div>

        <div class="flex items-center gap-3 pt-1">
            <button type="submit" <?php echo $compose_count === 0 ? 'disabled' : ''; ?>
                    class="px-6 py-2.5 bg-primary hover:bg-blue-700 disabled:opacity-40 disabled:cursor-not-allowed text-white text-sm font-semibold rounded-xl shadow-lg shadow-primary/20 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                Send Email
            </button>
            <a href="<?php echo url('/admin/subscribers'); ?>" class="px-6 py-2.5 bg-slate-100 dark:bg-white/5 hover:bg-slate-200 dark:hover:bg-white/10 text-slate-700 dark:text-gray-300 text-sm font-semibold rounded-xl transition-all">Cancel</a>
        </div>
    </form>
</div>

<?php else: // ===== LIST VIEW ===== ?>

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

<!-- Bulk-action form wraps filters + table -->
<form id="subForm" method="POST" action="<?php echo url('/admin/subscribers?action=compose'); ?>">
    <input type="hidden" name="recipients" id="recipientsField" value="selected">

    <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
        <div class="flex items-center gap-2">
            <?php foreach ([''=>'All', 'active'=>'Active', 'unsubscribed'=>'Unsubscribed'] as $k=>$v): ?>
                <a href="<?php echo url('/admin/subscribers' . ($k ? '?status=' . $k : '')); ?>"
                   class="px-3 py-1.5 text-sm font-medium rounded-lg <?php echo $status_filter === $k ? 'bg-primary text-white' : 'bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-gray-300 hover:bg-slate-200 dark:hover:bg-white/10'; ?>">
                    <?php echo $v; ?>
                </a>
            <?php endforeach; ?>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <!-- Search (separate non-JS form so it doesn't interfere with bulk form) -->
            <div class="flex items-center gap-1">
                <input type="text" id="searchInput" value="<?php echo e($search); ?>" placeholder="Search email…"
                       class="form-input !py-1.5 text-sm w-44"
                       onkeydown="if(event.key==='Enter'){window.location.href='<?php echo url('/admin/subscribers'); ?>?q='+encodeURIComponent(this.value);event.preventDefault();}">
                <button type="button" onclick="window.location.href='<?php echo url('/admin/subscribers'); ?>?q='+encodeURIComponent(document.getElementById('searchInput').value)"
                        class="px-3 py-1.5 bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-gray-300 text-sm rounded-lg hover:bg-slate-200 dark:hover:bg-white/10">Search</button>
            </div>
            <!-- Selection count badge -->
            <span id="selBadge" class="hidden text-sm text-slate-500 dark:text-gray-400"><span id="selNum">0</span> selected</span>
            <!-- Email Selected -->
            <button type="submit" id="btnEmailSel" disabled
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary text-white text-sm font-semibold rounded-lg disabled:opacity-40 disabled:cursor-not-allowed hover:bg-blue-700 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Email Selected
            </button>
            <!-- Email All Active -->
            <button type="button" id="btnEmailAll"
                    onclick="document.getElementById('recipientsField').value='all';document.getElementById('subForm').submit();"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-semibold rounded-lg transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                Email All Active
            </button>
            <!-- Export -->
            <a href="<?php echo url('/admin/subscribers?action=export'); ?>"
               class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-500 text-white text-sm font-semibold rounded-lg hover:bg-green-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export CSV
            </a>
        </div>
    </div>

    <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="border-b border-black/5 dark:border-white/10">
                    <th class="px-4 py-3 w-8">
                        <input type="checkbox" id="selectAll" class="w-4 h-4 rounded accent-primary cursor-pointer" title="Select all">
                    </th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-500">Email</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-500">Status</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-500">Subscribed</th>
                    <th class="px-4 py-3 text-right font-semibold text-slate-500">Actions</th>
                </tr></thead>
                <tbody class="divide-y divide-black/5 dark:divide-white/10">
                    <?php if (empty($items)): ?>
                        <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">No subscribers found.</td></tr>
                    <?php else: foreach ($items as $s): ?>
                        <tr class="hover:bg-slate-50 dark:hover:bg-white/5">
                            <td class="px-4 py-3">
                                <input type="checkbox" name="ids[]" value="<?php echo (int)$s['id']; ?>" class="sub-check w-4 h-4 rounded accent-primary cursor-pointer">
                            </td>
                            <td class="px-4 py-3 font-medium text-slate-700 dark:text-gray-200"><?php echo e($s['email']); ?></td>
                            <td class="px-4 py-3">
                                <?php $c = $s['status'] === 'active' ? 'green' : 'slate'; ?>
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-<?php echo $c; ?>-100 dark:bg-<?php echo $c; ?>-500/10 text-<?php echo $c; ?>-600"><?php echo ucfirst($s['status']); ?></span>
                            </td>
                            <td class="px-4 py-3 text-slate-500 dark:text-gray-400"><?php echo format_date($s['subscribed_at'], 'M j, Y g:ia'); ?></td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="<?php echo url('/admin/subscribers?action=compose&recipients=selected&ids=' . (int)$s['id']); ?>"
                                       class="px-2 py-1 text-xs text-primary hover:bg-primary/10 rounded-lg font-medium"
                                       title="Send email to this subscriber">Email</a>
                                    <a href="<?php echo url('/admin/subscribers?action=delete&id=' . $s['id'] . '&token=' . csrf_token()); ?>"
                                       onclick="authConfirmDelete(this); return false;"
                                       data-label="<?php echo e($s['email']); ?>"
                                       class="px-2 py-1 text-xs text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</form>

<?php if ($pagination['total_pages'] > 1): ?>
    <div class="mt-6"><?php echo render_pagination($pagination, '/admin/subscribers?' . ($status_filter ? 'status=' . $status_filter . '&' : '') . ($search ? 'q=' . urlencode($search) . '&' : '')); ?></div>
<?php endif; ?>

<script>
(function () {
    var selectAll  = document.getElementById('selectAll');
    var btnSel     = document.getElementById('btnEmailSel');
    var selBadge   = document.getElementById('selBadge');
    var selNum     = document.getElementById('selNum');

    function getChecked() {
        return document.querySelectorAll('.sub-check:checked');
    }
    function getAll() {
        return document.querySelectorAll('.sub-check');
    }

    function updateState() {
        var checked = getChecked();
        var all     = getAll();
        var n       = checked.length;
        btnSel.disabled = n === 0;
        if (selBadge) {
            selBadge.classList.toggle('hidden', n === 0);
            if (selNum) selNum.textContent = n;
        }
        if (selectAll) {
            selectAll.indeterminate = n > 0 && n < all.length;
            selectAll.checked = all.length > 0 && n === all.length;
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            document.querySelectorAll('.sub-check').forEach(function (c) { c.checked = selectAll.checked; });
            updateState();
        });
    }

    document.querySelectorAll('.sub-check').forEach(function (c) {
        c.addEventListener('change', updateState);
    });
})();
</script>

<?php endif; // end compose/list ?>
