<?php
/**
 * Authopic Technologies PLC - Admin: Testimonials Management
 */
if (!defined('BASE_PATH')) exit;

$action = get('action') ?: 'list';
$id = (int)(get('id') ?: 0);

// ---- DELETE ----
if ($action === 'delete' && $id > 0) {
    if (!csrf_verify(get('token'))) { set_flash('error', 'Invalid token.'); redirect('/admin/testimonials'); }
    db_query("DELETE FROM testimonials WHERE id=$id");
    log_activity('delete', 'testimonials', $id, 'Deleted testimonial');
    set_flash('success', 'Testimonial deleted.');
    redirect('/admin/testimonials');
}

// ---- SAVE ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['create', 'edit'])) {
    if (!csrf_verify(post('csrf_token'))) { set_flash('error', 'Invalid token.'); redirect('/admin/testimonials'); }

    $client_name = trim(post('client_name'));
    $client_role = trim(post('client_role'));
    $company = trim(post('company'));
    $content = trim(post('content'));
    $photo = trim(post('photo'));
    $rating = max(1, min(5, (int)post('rating')));
    $is_featured = post('is_featured') ? 1 : 0;
    $sort_order = (int)post('sort_order');
    $status = in_array(post('status'), ['pending', 'approved', 'rejected']) ? post('status') : 'pending';

    $errors = [];
    if (empty($client_name)) $errors[] = 'Client name is required.';
    if (empty($content)) $errors[] = 'Testimonial content is required.';

    if (empty($errors)) {
        $safe = [];
        foreach (['client_name','client_role','company','content','photo'] as $f) {
            $safe[$f] = db_escape($$f);
        }

        if (!empty($_FILES['photo_file']['name'])) {
            $upload = handle_upload('photo_file', 'testimonials');
            if ($upload['success']) $safe['photo'] = db_escape($upload['path']);
        }

        if ($action === 'create') {
            db_query("INSERT INTO testimonials (client_name, client_position, company_name, quote_en, photo, rating, is_featured, sort_order, status)
                      VALUES ('{$safe['client_name']}', '{$safe['client_role']}', '{$safe['company']}', '{$safe['content']}', '{$safe['photo']}', $rating, $is_featured, $sort_order, '$status')");
            log_activity('create', 'testimonials', db_insert_id(), 'Added testimonial from: ' . $client_name);
            set_flash('success', 'Testimonial added.');
        } else {
            db_query("UPDATE testimonials SET client_name='{$safe['client_name']}', client_position='{$safe['client_role']}', company_name='{$safe['company']}', quote_en='{$safe['content']}', photo='{$safe['photo']}', rating=$rating, is_featured=$is_featured, sort_order=$sort_order, status='$status', updated_at=NOW() WHERE id=$id");
            log_activity('update', 'testimonials', $id, 'Updated testimonial from: ' . $client_name);
            set_flash('success', 'Testimonial updated.');
        }
        redirect('/admin/testimonials');
    } else {
        set_flash('error', implode(' ', $errors));
    }
}

// ---- CREATE/EDIT FORM ----
if (in_array($action, ['create', 'edit'])):
    $item = ['client_name'=>'','client_role'=>'','company'=>'','content'=>'','photo'=>'','rating'=>5,'is_featured'=>0,'sort_order'=>0,'status'=>'pending'];
    if ($action === 'edit' && $id > 0) {
        $row = db_fetch_one("SELECT * FROM testimonials WHERE id=$id");
        if (!$row) { set_flash('error', 'Testimonial not found.'); redirect('/admin/testimonials'); }
        $item = [
            'client_name' => $row['client_name'],
            'client_role' => $row['client_position'],
            'company'     => $row['company_name'],
            'content'     => $row['quote_en'],
            'photo'       => $row['photo'],
            'rating'      => $row['rating'],
            'is_featured' => $row['is_featured'],
            'sort_order'  => $row['sort_order'],
            'status'      => $row['status'],
        ];
    }
?>
<div class="max-w-3xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="<?php echo url('/admin/testimonials'); ?>" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-white/5 text-slate-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-slate-800 dark:text-white"><?php echo $action === 'create' ? 'New Testimonial' : 'Edit Testimonial'; ?></h2>
    </div>

    <form method="POST" enctype="multipart/form-data" class="space-y-6">
        <?php echo csrf_field(); ?>
        <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-6 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="form-label">Client Name *</label>
                    <input type="text" name="client_name" value="<?php echo e($item['client_name']); ?>" required class="form-input">
                </div>
                <div>
                    <label class="form-label">Role / Title</label>
                    <input type="text" name="client_role" value="<?php echo e($item['client_role']); ?>" class="form-input" placeholder="CEO">
                </div>
                <div>
                    <label class="form-label">Company</label>
                    <input type="text" name="company" value="<?php echo e($item['company']); ?>" class="form-input">
                </div>
            </div>

            <div>
                <label class="form-label">Testimonial Content *</label>
                <textarea name="content" rows="5" required class="form-input"><?php echo e($item['content']); ?></textarea>
            </div>

            <div>
                <label class="form-label">Photo</label>
                <input type="file" name="photo_file" accept="image/*" class="form-input" onchange="previewImage(this, 'testi-photo')">
                <?php if (!empty($item['photo'])): ?>
                    <img id="testi-photo" src="<?php echo upload_url($item['photo']); ?>" class="mt-2 w-16 h-16 rounded-full object-cover">
                <?php else: ?>
                    <img id="testi-photo" class="mt-2 w-16 h-16 rounded-full object-cover hidden">
                <?php endif; ?>
                <input type="hidden" name="photo" value="<?php echo e($item['photo']); ?>">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="form-label">Rating</label>
                    <select name="rating" class="form-input">
                        <?php for ($r = 5; $r >= 1; $r--): ?>
                            <option value="<?php echo $r; ?>" <?php echo (int)$item['rating'] === $r ? 'selected' : ''; ?>><?php echo str_repeat('★', $r) . str_repeat('☆', 5 - $r); ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div>
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" value="<?php echo (int)$item['sort_order']; ?>" class="form-input" min="0">
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select name="status" class="form-input">
                        <option value="pending" <?php echo $item['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="approved" <?php echo $item['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="rejected" <?php echo $item['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                    </select>
                </div>
            </div>

            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_featured" value="1" <?php echo $item['is_featured'] ? 'checked' : ''; ?> class="w-4 h-4 rounded text-primary focus:ring-primary">
                <span class="text-sm text-slate-600 dark:text-gray-300">Featured testimonial</span>
            </label>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="px-6 py-3 bg-primary text-white font-semibold rounded-xl hover:bg-blue-700 transition-colors">
                <?php echo $action === 'create' ? 'Add Testimonial' : 'Update Testimonial'; ?>
            </button>
            <a href="<?php echo url('/admin/testimonials'); ?>" class="px-6 py-3 bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-gray-300 font-semibold rounded-xl hover:bg-slate-200 transition-colors">Cancel</a>
        </div>
    </form>
</div>

<?php else: // LIST

$items = db_fetch_all("SELECT * FROM testimonials ORDER BY sort_order ASC, created_at DESC");
?>

<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <h2 class="text-lg font-bold text-slate-800 dark:text-white">Testimonials (<?php echo count($items); ?>)</h2>
    <a href="<?php echo url('/admin/testimonials?action=create'); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-blue-700">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Testimonial
    </a>
</div>

<div class="space-y-4">
    <?php if (empty($items)): ?>
        <div class="text-center text-slate-400 py-12">No testimonials yet.</div>
    <?php else: foreach ($items as $t): ?>
        <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-5">
            <div class="flex items-start gap-4">
                <?php if ($t['photo']): ?>
                    <img src="<?php echo upload_url($t['photo']); ?>" class="w-12 h-12 rounded-full object-cover flex-shrink-0">
                <?php else: ?>
                    <div class="w-12 h-12 rounded-full bg-primary flex-shrink-0 flex items-center justify-center text-white font-bold">
                        <?php echo strtoupper(substr($t['client_name'], 0, 1)); ?>
                    </div>
                <?php endif; ?>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between mb-1">
                        <div>
                            <span class="font-semibold text-slate-700 dark:text-gray-200"><?php echo e($t['client_name']); ?></span>
                            <?php if ($t['is_featured']): ?><span class="ml-2 text-xs text-yellow-500">★ Featured</span><?php endif; ?>
                        </div>
                        <div class="flex items-center gap-2">
                            <?php $sc = $t['status'] === 'approved' ? 'green' : ($t['status'] === 'rejected' ? 'red' : 'orange'); ?>
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-<?php echo $sc; ?>-100 text-<?php echo $sc; ?>-600"><?php echo ucfirst($t['status']); ?></span>
                            <a href="<?php echo url('/admin/testimonials?action=edit&id=' . $t['id']); ?>" class="text-xs text-primary hover:underline">Edit</a>
                            <a href="<?php echo url('/admin/testimonials?action=delete&id=' . $t['id'] . '&token=' . csrf_token()); ?>" onclick="authConfirmDelete(this); return false;" data-label="<?php echo e($t['client_name']); ?>" class="text-xs text-red-500 hover:underline">Delete</a>
                        </div>
                    </div>
                    <p class="text-xs text-slate-400 mb-2"><?php echo e($t['client_position']); ?><?php echo $t['company_name'] ? ' at ' . e($t['company_name']) : ''; ?></p>
                    <p class="text-sm text-slate-600 dark:text-gray-300">"<?php echo e(truncate($t['quote_en'], 200)); ?>"</p>
                    <div class="mt-2 text-yellow-400 text-sm"><?php echo str_repeat('★', $t['rating']) . str_repeat('☆', 5 - $t['rating']); ?></div>
                </div>
            </div>
        </div>
    <?php endforeach; endif; ?>
</div>

<?php endif; ?>
