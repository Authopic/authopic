<?php
/**
 * Authopic Technologies PLC - Admin: Site Settings
 */
if (!defined('BASE_PATH')) exit;

// ---- TEST EMAIL ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('action') === 'test_email') {
    if (!csrf_verify(post('csrf_token'))) { set_flash('error', 'Invalid token.'); redirect('/admin/settings'); }
    $test_to = trim(post('test_to') ?: '');
    if (!$test_to || !filter_var($test_to, FILTER_VALIDATE_EMAIL)) {
        set_flash('error', 'Please enter a valid email address to send the test to.');
        redirect('/admin/settings');
    }
    $site_name = get_setting('site_name', 'Authopic Technologies PLC');
    $html = email_template('SMTP Test Email', "<p>This is a test email sent from <strong>$site_name</strong> admin panel.</p>"
        . "<p>If you received this, your SMTP configuration is working correctly.</p>"
        . "<p style='font-size:12px;color:#94a3b8;margin-top:16px;'>Sent: " . date('Y-m-d H:i:s') . " UTC</p>"
        . "<p style='font-size:12px;color:#94a3b8;'>Host: " . htmlspecialchars(MAIL_HOST, ENT_QUOTES) . ":" . MAIL_PORT . " / User: " . htmlspecialchars(MAIL_USER, ENT_QUOTES) . "</p>");
    $ok  = send_email($test_to, 'SMTP Test — ' . $site_name, $html);
    $err = get_last_smtp_error();
    if ($ok) {
        set_flash('success', "Test email sent to $test_to — check your inbox (and spam folder).");
    } else {
        set_flash('error', 'SMTP failed: ' . ($err ?: 'Unknown error — check PHP error_log for [SMTP] entries.'));
    }
    redirect('/admin/settings');
}

// ---- SAVE ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify(post('csrf_token'))) { set_flash('error', 'Invalid token.'); redirect('/admin/settings'); }

    $settings = [
        'site_name', 'site_tagline', 'site_description', 'site_keywords',
        'company_name', 'company_email', 'company_phone', 'company_address',
        'company_city', 'company_country',
        'social_facebook', 'social_twitter', 'social_linkedin', 'social_github',
        'social_instagram', 'social_youtube', 'social_telegram', 'social_whatsapp',
        'google_analytics_id', 'google_maps_embed',
        'footer_text', 'maintenance_mode',
    ];

    foreach ($settings as $key) {
        $val = db_escape(trim(post($key) ?: ''));
        $safe_key = db_escape($key);
        $existing = db_fetch_one("SELECT id FROM site_settings WHERE setting_key='$safe_key'");
        if ($existing) {
            db_query("UPDATE site_settings SET setting_value='$val', updated_at=NOW() WHERE setting_key='$safe_key'");
        } else {
            db_query("INSERT INTO site_settings (setting_key, setting_value) VALUES ('$safe_key', '$val')");
        }
    }

    log_activity('update', 'site_settings', 0, 'Updated site settings');
    set_flash('success', 'Settings saved successfully.');
    redirect('/admin/settings');
}

// Load all settings into associative array
$all = db_fetch_all("SELECT setting_key, setting_value FROM site_settings");
$s = [];
foreach ($all as $row) {
    $s[$row['setting_key']] = $row['setting_value'];
}
$sv = function($key) use ($s) { return $s[$key] ?? ''; };
?>

<form method="POST" class="max-w-4xl space-y-6">
    <?php echo csrf_field(); ?>

    <!-- General -->
    <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-6 space-y-4">
        <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider">General Settings</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="form-label">Site Name</label>
                <input type="text" name="site_name" value="<?php echo e($sv('site_name')); ?>" class="form-input">
            </div>
            <div>
                <label class="form-label">Site Tagline</label>
                <input type="text" name="site_tagline" value="<?php echo e($sv('site_tagline')); ?>" class="form-input">
            </div>
        </div>
        <div>
            <label class="form-label">Site Description (SEO)</label>
            <textarea name="site_description" rows="2" class="form-input" maxlength="300"><?php echo e($sv('site_description')); ?></textarea>
        </div>
        <div>
            <label class="form-label">Site Keywords (SEO, comma-separated)</label>
            <input type="text" name="site_keywords" value="<?php echo e($sv('site_keywords')); ?>" class="form-input">
        </div>
        <div>
            <label class="form-label">Footer Text</label>
            <input type="text" name="footer_text" value="<?php echo e($sv('footer_text')); ?>" class="form-input">
        </div>
        <div>
            <label class="form-label">Maintenance Mode</label>
            <select name="maintenance_mode" class="form-input w-auto">
                <option value="0" <?php echo $sv('maintenance_mode') !== '1' ? 'selected' : ''; ?>>Off</option>
                <option value="1" <?php echo $sv('maintenance_mode') === '1' ? 'selected' : ''; ?>>On</option>
            </select>
        </div>
    </div>

    <!-- Company Info -->
    <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-6 space-y-4">
        <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider">Company Information</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="form-label">Company Name</label>
                <input type="text" name="company_name" value="<?php echo e($sv('company_name')); ?>" class="form-input">
            </div>
            <div>
                <label class="form-label">Email</label>
                <input type="email" name="company_email" value="<?php echo e($sv('company_email')); ?>" class="form-input">
            </div>
            <div>
                <label class="form-label">Phone</label>
                <input type="text" name="company_phone" value="<?php echo e($sv('company_phone')); ?>" class="form-input">
            </div>
            <div>
                <label class="form-label">Address</label>
                <input type="text" name="company_address" value="<?php echo e($sv('company_address')); ?>" class="form-input">
            </div>
            <div>
                <label class="form-label">City</label>
                <input type="text" name="company_city" value="<?php echo e($sv('company_city')); ?>" class="form-input">
            </div>
            <div>
                <label class="form-label">Country</label>
                <input type="text" name="company_country" value="<?php echo e($sv('company_country')); ?>" class="form-input">
            </div>
        </div>
    </div>

    <!-- Social Media -->
    <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-6 space-y-4">
        <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider">Social Media Links</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <?php foreach ([
                'social_facebook' => 'Facebook URL',
                'social_twitter' => 'Twitter / X URL',
                'social_linkedin' => 'LinkedIn URL',
                'social_github' => 'GitHub URL',
                'social_instagram' => 'Instagram URL',
                'social_youtube' => 'YouTube URL',
                'social_telegram' => 'Telegram URL',
                'social_whatsapp' => 'WhatsApp Number',
            ] as $key => $label): ?>
                <div>
                    <label class="form-label"><?php echo $label; ?></label>
                    <input type="text" name="<?php echo $key; ?>" value="<?php echo e($sv($key)); ?>" class="form-input" placeholder="https://...">
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Analytics & Maps -->
    <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-6 space-y-4">
        <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider">Integrations</h3>
        <div>
            <label class="form-label">Google Analytics ID</label>
            <input type="text" name="google_analytics_id" value="<?php echo e($sv('google_analytics_id')); ?>" class="form-input" placeholder="G-XXXXXXXXXX">
        </div>
        <div>
            <label class="form-label">Google Maps Embed URL</label>
            <textarea name="google_maps_embed" rows="3" class="form-input font-mono text-sm" placeholder="Paste the iframe src URL from Google Maps"><?php echo e($sv('google_maps_embed')); ?></textarea>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="px-6 py-3 bg-primary text-white font-semibold rounded-xl hover:bg-blue-700 transition-colors">
            Save Settings
        </button>
    </div>
</form>

<!-- SMTP Test Card -->
<div class="max-w-4xl mt-6 bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-6">
    <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider mb-1">Test SMTP Email</h3>
    <p class="text-xs text-slate-500 mb-4">Sends a real test email using your current SMTP config (<strong><?php echo e(MAIL_ENABLED ? 'Enabled' : 'DISABLED'); ?></strong> — <?php echo e(MAIL_HOST); ?>:<?php echo e(MAIL_PORT); ?> / <?php echo e(MAIL_USER ?: 'no user set'); ?>).</p>
    <form method="POST" class="flex items-center gap-3 flex-wrap">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="action" value="test_email">
        <input type="email" name="test_to" required placeholder="recipient@example.com"
               class="form-input !py-2 text-sm w-64" value="<?php echo e($sv('site_email')); ?>">
        <button type="submit" class="px-5 py-2 bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-semibold rounded-xl transition-colors">
            Send Test Email
        </button>
    </form>
</div>
