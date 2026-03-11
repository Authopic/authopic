<?php
/**
 * Authopic Technologies PLC - Admin: Profile Management
 */
if (!defined('BASE_PATH')) exit;

$admin = get_current_admin();
if (!$admin) { redirect('/admin/login'); }

// ---- SAVE ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify(post('csrf_token'))) { set_flash('error', 'Invalid token.'); redirect('/admin/profile'); }

    $section = post('section');

    if ($section === 'info') {
        $full_name = trim(post('full_name'));
        $email = trim(post('email'));

        $errors = [];
        if (empty($full_name)) $errors[] = 'Name is required.';
        if (!is_valid_email($email)) $errors[] = 'Valid email is required.';

        // Check email unique
        $existing = db_fetch_one("SELECT id FROM admin_users WHERE email='" . db_escape($email) . "' AND id!=" . (int)$admin['id']);
        if ($existing) $errors[] = 'Email already in use.';

        if (empty($errors)) {
            db_query("UPDATE admin_users SET full_name='" . db_escape($full_name) . "', email='" . db_escape($email) . "', updated_at=NOW() WHERE id=" . (int)$admin['id']);
            $_SESSION['admin_name'] = $full_name;
            log_activity('update', 'admin_users', $admin['id'], 'Updated profile info');
            set_flash('success', 'Profile updated.');
        } else {
            set_flash('error', implode(' ', $errors));
        }

    } elseif ($section === 'password') {
        $current = post('current_password');
        $new_pass = post('new_password');
        $confirm = post('confirm_password');

        $errors = [];
        if (empty($current)) $errors[] = 'Current password is required.';
        if (strlen($new_pass) < 8) $errors[] = 'New password must be at least 8 characters.';
        if ($new_pass !== $confirm) $errors[] = 'Passwords do not match.';

        // Verify current password
        $user = db_fetch_one("SELECT password_hash FROM admin_users WHERE id=" . (int)$admin['id']);
        if (!password_verify($current, $user['password_hash'])) {
            $errors[] = 'Current password is incorrect.';
        }

        if (empty($errors)) {
            $hash = password_hash($new_pass, PASSWORD_DEFAULT);
            db_query("UPDATE admin_users SET password_hash='$hash', updated_at=NOW() WHERE id=" . (int)$admin['id']);
            log_activity('update', 'admin_users', $admin['id'], 'Changed password');
            set_flash('success', 'Password changed successfully.');
        } else {
            set_flash('error', implode(' ', $errors));
        }
    }

    redirect('/admin/profile');
}

// Refresh admin data
$admin = db_fetch_one("SELECT * FROM admin_users WHERE id=" . (int)$_SESSION['admin_id']);
$activity = db_fetch_all("SELECT * FROM activity_log WHERE admin_id=" . (int)$admin['id'] . " ORDER BY created_at DESC LIMIT 20");
?>

<div class="max-w-3xl space-y-6">

    <!-- Profile Info -->
    <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-6">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-16 h-16 rounded-full bg-primary flex items-center justify-center text-white text-2xl font-bold">
                <?php echo strtoupper(substr($admin['full_name'], 0, 1)); ?>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-800 dark:text-white"><?php echo e($admin['full_name']); ?></h2>
                <p class="text-sm text-slate-500"><?php echo e($admin['email']); ?> · <?php echo ucfirst($admin['role']); ?></p>
                <p class="text-xs text-slate-400">Member since <?php echo format_date($admin['created_at'], 'M Y'); ?> · <?php echo (int)$admin['login_count']; ?> logins</p>
            </div>
        </div>

        <form method="POST" class="space-y-4">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="section" value="info">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" value="<?php echo e($admin['full_name']); ?>" required class="form-input">
                </div>
                <div>
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="<?php echo e($admin['email']); ?>" required class="form-input">
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-sm text-slate-500">
                    <strong>Username:</strong> <?php echo e($admin['username']); ?> · 
                    <strong>Role:</strong> <?php echo ucfirst($admin['role']); ?>
                    <?php if ($admin['last_login']): ?> · <strong>Last login:</strong> <?php echo time_ago($admin['last_login']); ?><?php endif; ?>
                </div>
            </div>
            <button type="submit" class="px-5 py-2.5 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-colors">
                Update Profile
            </button>
        </form>
    </div>

    <!-- Change Password -->
    <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-6">
        <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider mb-4">Change Password</h3>
        <form method="POST" class="space-y-4">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="section" value="password">
            <div>
                <label class="form-label">Current Password</label>
                <input type="password" name="current_password" required class="form-input" autocomplete="current-password">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">New Password (min 8 chars)</label>
                    <input type="password" name="new_password" required class="form-input" minlength="8" autocomplete="new-password">
                </div>
                <div>
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="confirm_password" required class="form-input" autocomplete="new-password">
                </div>
            </div>
            <button type="submit" class="px-5 py-2.5 bg-orange-500 text-white text-sm font-semibold rounded-xl hover:bg-orange-600 transition-colors">
                Change Password
            </button>
        </form>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-6">
        <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider mb-4">Your Recent Activity</h3>
        <?php if (empty($activity)): ?>
            <p class="text-sm text-slate-400">No recent activity.</p>
        <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($activity as $a): ?>
                    <div class="flex items-start gap-3 text-sm">
                        <div class="w-2 h-2 rounded-full bg-primary mt-1.5 flex-shrink-0"></div>
                        <div class="flex-1">
                            <span class="text-slate-600 dark:text-gray-300"><?php echo e($a['description']); ?></span>
                            <span class="text-xs text-slate-400 ml-2"><?php echo time_ago($a['created_at']); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</div>
