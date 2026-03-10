<?php
/**
 * Authopic Technologies PLC - Admin: Team Management
 */
if (!defined('BASE_PATH')) exit;

$action = get('action') ?: 'list';
$id = (int)(get('id') ?: 0);

// ---- DELETE ----
if ($action === 'delete' && $id > 0) {
    if (!csrf_verify(get('token'))) { set_flash('error', 'Invalid token.'); redirect('/admin/team'); }
    db_query("DELETE FROM team_members WHERE id=$id");
    log_activity('delete', 'team_members', $id, 'Deleted team member');
    set_flash('success', 'Team member deleted.');
    redirect('/admin/team');
}

// ---- SAVE ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['create', 'edit'])) {
    if (!csrf_verify(post('csrf_token'))) { set_flash('error', 'Invalid token.'); redirect('/admin/team'); }

    $full_name = trim(post('full_name'));
    $role = trim(post('role'));
    $bio = trim(post('bio'));
    $photo = trim(post('photo'));
    $email = trim(post('email'));
    $linkedin = trim(post('linkedin'));
    $github = trim(post('github'));
    $twitter = trim(post('twitter'));
    $sort_order = (int)post('sort_order');
    $status = in_array(post('status'), ['active', 'inactive']) ? post('status') : 'active';

    $errors = [];
    if (empty($full_name)) $errors[] = 'Name is required.';
    if (empty($role)) $errors[] = 'Role is required.';

    if (empty($errors)) {
        $safe = [];
        foreach (['full_name','role','bio','photo','email','linkedin','github','twitter'] as $f) {
            $safe[$f] = db_escape($$f);
        }

        if (!empty($_FILES['photo_file']['name'])) {
            $upload = handle_upload('photo_file', 'team');
            if ($upload['success']) $safe['photo'] = db_escape($upload['path']);
        }

        if ($action === 'create') {
            db_query("INSERT INTO team_members (full_name, role, bio, photo, email, linkedin, github, twitter, sort_order, status, created_at)
                      VALUES ('{$safe['full_name']}', '{$safe['role']}', '{$safe['bio']}', '{$safe['photo']}', '{$safe['email']}', '{$safe['linkedin']}', '{$safe['github']}', '{$safe['twitter']}', $sort_order, '$status', NOW())");
            log_activity('create', 'team_members', db_insert_id(), 'Added team member: ' . $full_name);
            set_flash('success', 'Team member added.');
        } else {
            db_query("UPDATE team_members SET full_name='{$safe['full_name']}', role='{$safe['role']}', bio='{$safe['bio']}', photo='{$safe['photo']}', email='{$safe['email']}', linkedin='{$safe['linkedin']}', github='{$safe['github']}', twitter='{$safe['twitter']}', sort_order=$sort_order, status='$status', updated_at=NOW() WHERE id=$id");
            log_activity('update', 'team_members', $id, 'Updated team member: ' . $full_name);
            set_flash('success', 'Team member updated.');
        }
        redirect('/admin/team');
    } else {
        set_flash('error', implode(' ', $errors));
    }
}

// ---- CREATE/EDIT FORM ----
if (in_array($action, ['create', 'edit'])):
    $item = ['full_name'=>'','role'=>'','bio'=>'','photo'=>'','email'=>'','linkedin'=>'','github'=>'','twitter'=>'','sort_order'=>0,'status'=>'active'];
    if ($action === 'edit' && $id > 0) {
        $item = db_fetch_one("SELECT * FROM team_members WHERE id=$id");
        if (!$item) { set_flash('error', 'Member not found.'); redirect('/admin/team'); }
    }
?>
<div class="max-w-3xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="<?php echo url('/admin/team'); ?>" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-white/5 text-slate-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-slate-800 dark:text-white"><?php echo $action === 'create' ? 'Add Team Member' : 'Edit Team Member'; ?></h2>
    </div>

    <form method="POST" enctype="multipart/form-data" class="space-y-6">
        <?php echo csrf_field(); ?>
        <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-6 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="full_name" value="<?php echo e($item['full_name']); ?>" required class="form-input">
                </div>
                <div>
                    <label class="form-label">Role *</label>
                    <input type="text" name="role" value="<?php echo e($item['role']); ?>" required class="form-input" placeholder="e.g. Lead Developer">
                </div>
            </div>
            <div>
                <label class="form-label">Bio</label>
                <textarea name="bio" rows="4" class="form-input"><?php echo e($item['bio']); ?></textarea>
            </div>
            <div>
                <label class="form-label">Photo</label>
                <input type="file" name="photo_file" accept="image/*" class="form-input" onchange="previewImage(this, 'team-photo')">
                <?php if (!empty($item['photo'])): ?>
                    <img id="team-photo" src="<?php echo upload_url($item['photo']); ?>" class="mt-2 w-20 h-20 rounded-full object-cover">
                <?php else: ?>
                    <img id="team-photo" class="mt-2 w-20 h-20 rounded-full object-cover hidden">
                <?php endif; ?>
                <input type="hidden" name="photo" value="<?php echo e($item['photo']); ?>">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="<?php echo e($item['email']); ?>" class="form-input">
                </div>
                <div>
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" value="<?php echo (int)$item['sort_order']; ?>" class="form-input" min="0">
                </div>
            </div>
        </div>

        <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-6 space-y-4">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider">Social Links</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="form-label">LinkedIn URL</label>
                    <input type="url" name="linkedin" value="<?php echo e($item['linkedin']); ?>" class="form-input" placeholder="https://linkedin.com/in/...">
                </div>
                <div>
                    <label class="form-label">GitHub URL</label>
                    <input type="url" name="github" value="<?php echo e($item['github']); ?>" class="form-input" placeholder="https://github.com/...">
                </div>
                <div>
                    <label class="form-label">Twitter URL</label>
                    <input type="url" name="twitter" value="<?php echo e($item['twitter']); ?>" class="form-input" placeholder="https://twitter.com/...">
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <div>
                <label class="form-label">Status</label>
                <select name="status" class="form-input">
                    <option value="active" <?php echo $item['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo $item['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="px-6 py-3 bg-primary text-white font-semibold rounded-xl hover:bg-blue-700 transition-colors">
                <?php echo $action === 'create' ? 'Add Member' : 'Update Member'; ?>
            </button>
            <a href="<?php echo url('/admin/team'); ?>" class="px-6 py-3 bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-gray-300 font-semibold rounded-xl hover:bg-slate-200 transition-colors">Cancel</a>
        </div>
    </form>
</div>

<?php else: // LIST

$items = db_fetch_all("SELECT * FROM team_members ORDER BY sort_order ASC, created_at DESC");
?>

<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <h2 class="text-lg font-bold text-slate-800 dark:text-white">Team Members (<?php echo count($items); ?>)</h2>
    <a href="<?php echo url('/admin/team?action=create'); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-blue-700">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Member
    </a>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php if (empty($items)): ?>
        <div class="col-span-full text-center text-slate-400 py-12">No team members yet.</div>
    <?php else: foreach ($items as $m): ?>
        <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-5 text-center">
            <?php if ($m['photo']): ?>
                <img src="<?php echo upload_url($m['photo']); ?>" class="w-20 h-20 rounded-full object-cover mx-auto mb-3">
            <?php else: ?>
                <div class="w-20 h-20 rounded-full bg-primary mx-auto mb-3 flex items-center justify-center text-white text-2xl font-bold">
                    <?php echo strtoupper(substr($m['full_name'] ?? $m['name_en'] ?? '?', 0, 1)); ?>
                </div>
            <?php endif; ?>
            <h3 class="font-bold text-slate-700 dark:text-gray-200"><?php echo e($m['full_name']); ?></h3>
            <p class="text-xs text-primary mb-2"><?php echo e($m['role']); ?></p>
            <?php $c = $m['status'] === 'active' ? 'green' : 'slate'; ?>
            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-<?php echo $c; ?>-100 text-<?php echo $c; ?>-600 mb-3 inline-block"><?php echo ucfirst($m['status']); ?></span>
            <div class="flex justify-center gap-2 mt-3">
                <a href="<?php echo url('/admin/team?action=edit&id=' . $m['id']); ?>" class="px-3 py-1 text-xs text-primary bg-blue-50 dark:bg-blue-500/10 rounded-lg hover:bg-blue-100">Edit</a>
                <a href="<?php echo url('/admin/team?action=delete&id=' . $m['id'] . '&token=' . csrf_token()); ?>" onclick="return confirmDelete()" class="px-3 py-1 text-xs text-red-500 bg-red-50 dark:bg-red-500/10 rounded-lg hover:bg-red-100">Delete</a>
            </div>
        </div>
    <?php endforeach; endif; ?>
</div>

<?php endif; ?>
