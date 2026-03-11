<?php
/**
 * Authopic Technologies PLC - Admin: Portfolio Management
 */
if (!defined('BASE_PATH')) exit;

$action = get('action') ?: 'list';
$id = (int)(get('id') ?: 0);

// ---- DELETE ----
if ($action === 'delete' && $id > 0) {
    if (!csrf_verify(get('token'))) { set_flash('error', 'Invalid token.'); redirect('/admin/portfolio'); }
    db_query("DELETE FROM portfolio WHERE id=$id");
    log_activity('delete', 'portfolio', $id, 'Deleted portfolio project');
    set_flash('success', 'Project deleted.');
    redirect('/admin/portfolio');
}

// ---- SAVE ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['create', 'edit'])) {
    if (!csrf_verify(post('csrf_token'))) { set_flash('error', 'Invalid token.'); redirect('/admin/portfolio'); }

    $title = trim(post('title'));
    $slug = trim(post('slug')) ?: create_slug($title);
    $type = in_array(post('type'), ['sms', 'erp', 'website', 'webapp']) ? post('type') : 'website';
    $client_name = trim(post('client_name'));
    $challenge = post('challenge');
    $solution = post('solution');
    $results = post('results');
    $technologies = trim(post('technologies'));
    $featured_image = trim(post('featured_image'));
    $gallery = trim(post('gallery'));
    $is_featured = post('is_featured') ? 1 : 0;
    $status = in_array(post('status'), ['draft', 'published']) ? post('status') : 'draft';

    $errors = [];
    if (empty($title)) $errors[] = 'Title is required.';
    $slug_check = db_fetch_one("SELECT id FROM portfolio WHERE slug='" . db_escape($slug) . "'" . ($id ? " AND id!=$id" : ""));
    if ($slug_check) $errors[] = 'Slug already exists.';

    if (empty($errors)) {
        $safe = [];
        foreach (['title','slug','client_name','challenge','solution','results','technologies','featured_image','gallery'] as $f) {
            $safe[$f] = db_escape($$f);
        }

        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $upload = handle_upload('image', 'portfolio');
            if ($upload['success']) $safe['featured_image'] = db_escape($upload['path']);
        }

        if ($action === 'create') {
            db_query("INSERT INTO portfolio (title_en, slug, type, client_name, challenge_en, solution_en, results_en, technologies, featured_image, gallery, is_featured, status) 
                      VALUES ('{$safe['title']}', '{$safe['slug']}', '$type', '{$safe['client_name']}', '{$safe['challenge']}', '{$safe['solution']}', '{$safe['results']}', '{$safe['technologies']}', '{$safe['featured_image']}', '{$safe['gallery']}', $is_featured, '$status')");
            log_activity('create', 'portfolio', db_insert_id(), 'Created project: ' . $title);
            set_flash('success', 'Project created.');
        } else {
            db_query("UPDATE portfolio SET title_en='{$safe['title']}', slug='{$safe['slug']}', type='$type', client_name='{$safe['client_name']}', challenge_en='{$safe['challenge']}', solution_en='{$safe['solution']}', results_en='{$safe['results']}', technologies='{$safe['technologies']}', featured_image='{$safe['featured_image']}', gallery='{$safe['gallery']}', is_featured=$is_featured, status='$status', updated_at=NOW() WHERE id=$id");
            log_activity('update', 'portfolio', $id, 'Updated project: ' . $title);
            set_flash('success', 'Project updated.');
        }
        redirect('/admin/portfolio');
    } else {
        set_flash('error', implode(' ', $errors));
    }
}

$services = [];

// ---- CREATE/EDIT FORM ----
if (in_array($action, ['create', 'edit'])):
    $item = ['title_en'=>'','slug'=>'','type'=>'website','client_name'=>'','challenge_en'=>'','solution_en'=>'','results_en'=>'','technologies'=>'','featured_image'=>'','gallery'=>'','is_featured'=>0,'status'=>'draft'];
    if ($action === 'edit' && $id > 0) {
        $item = db_fetch_one("SELECT * FROM portfolio WHERE id=$id");
        if (!$item) { set_flash('error', 'Project not found.'); redirect('/admin/portfolio'); }
    }
?>
<div class="max-w-4xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="<?php echo url('/admin/portfolio'); ?>" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-white/5 text-slate-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-slate-800 dark:text-white"><?php echo $action === 'create' ? 'New Project' : 'Edit Project'; ?></h2>
    </div>

    <form method="POST" enctype="multipart/form-data" class="space-y-6">
        <?php echo csrf_field(); ?>
        <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-6 space-y-4">
            <div>
                <label class="form-label">Title *</label>
                <input type="text" name="title" id="proj-title" value="<?php echo e($item['title_en']); ?>" required class="form-input">
            </div>
            <div>
                <label class="form-label">Slug</label>
                <input type="text" name="slug" id="proj-slug" value="<?php echo e($item['slug']); ?>" class="form-input" placeholder="auto-generated">
            </div>
            <script>generateSlug('proj-title', 'proj-slug');</script>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="form-label">Type</label>
                    <select name="type" class="form-input">
                        <?php foreach (['sms'=>'SMS System','erp'=>'ERP System','website'=>'Website','webapp'=>'Web App'] as $k=>$v): ?>
                            <option value="<?php echo $k; ?>" <?php echo $item['type'] === $k ? 'selected' : ''; ?>><?php echo $v; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="form-label">Service</label>
                    <select name="service_id" class="form-input">
                        <option value="0">None</option>
                        <?php foreach ($services as $s): ?>
                            <option value="<?php echo $s['id']; ?>" <?php echo (int)$item['service_id'] === (int)$s['id'] ? 'selected' : ''; ?>><?php echo e($s['title']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select name="status" class="form-input">
                        <option value="draft" <?php echo $item['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
                        <option value="published" <?php echo $item['status'] === 'published' ? 'selected' : ''; ?>>Published</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Client Name</label>
                    <input type="text" name="client_name" value="<?php echo e($item['client_name']); ?>" class="form-input">
                </div>
            </div>

            <div>
                <label class="form-label">Challenge</label>
                <textarea name="challenge" rows="3" class="form-input"><?php echo e($item['challenge_en']); ?></textarea>
            </div>
            <div>
                <label class="form-label">Solution</label>
                <textarea name="solution" rows="3" class="form-input"><?php echo e($item['solution_en']); ?></textarea>
            </div>
            <div>
                <label class="form-label">Results (JSON)</label>
                <textarea name="results" rows="3" class="form-input font-mono text-sm" placeholder='[{"label":"Metric","value":"Result"}]'><?php echo e($item['results_en']); ?></textarea>
            </div>
            <div>
                <label class="form-label">Technologies (comma-separated)</label>
                <input type="text" name="technologies" value="<?php echo e($item['technologies']); ?>" class="form-input" placeholder="PHP, MySQL, Tailwind CSS">
            </div>
            <div>
                <label class="form-label">Featured Image</label>
                <input type="file" name="image" accept="image/*" class="form-input" onchange="previewImage(this, 'proj-img')">
                <?php if (!empty($item['featured_image'])): ?>
                    <img id="proj-img" src="<?php echo upload_url($item['featured_image']); ?>" class="mt-2 w-40 h-24 object-cover rounded-lg">
                <?php else: ?>
                    <img id="proj-img" class="mt-2 w-40 h-24 object-cover rounded-lg hidden">
                <?php endif; ?>
                <input type="hidden" name="featured_image" value="<?php echo e($item['featured_image']); ?>">
            </div>
            <div>
                <label class="form-label">Gallery Images (JSON array or comma-separated paths)</label>
                <textarea name="gallery" rows="2" class="form-input font-mono text-sm"><?php echo e($item['gallery']); ?></textarea>
            </div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_featured" value="1" <?php echo $item['is_featured'] ? 'checked' : ''; ?> class="w-4 h-4 rounded text-primary focus:ring-primary">
                <span class="text-sm text-slate-600 dark:text-gray-300">Featured project</span>
            </label>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="px-6 py-3 bg-primary text-white font-semibold rounded-xl hover:bg-blue-700 transition-colors">
                <?php echo $action === 'create' ? 'Create Project' : 'Update Project'; ?>
            </button>
            <a href="<?php echo url('/admin/portfolio'); ?>" class="px-6 py-3 bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-gray-300 font-semibold rounded-xl hover:bg-slate-200 transition-colors">Cancel</a>
        </div>
    </form>
</div>

<?php else: // LIST

$type_filter = get('type') ?: '';
$where = $type_filter ? "WHERE type='" . db_escape($type_filter) . "'" : '';
$total = db_fetch_one("SELECT COUNT(*) as c FROM portfolio $where")['c'] ?? 0;
$pagination = paginate($total, 20);
$items = db_fetch_all("SELECT * FROM portfolio $where ORDER BY created_at DESC LIMIT {$pagination['offset']}, {$pagination['per_page']}");
?>

<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div class="flex items-center gap-2">
        <?php foreach ([''=> 'All', 'sms'=>'SMS', 'erp'=>'ERP', 'website'=>'Website', 'webapp'=>'Web App'] as $k=>$v): ?>
            <a href="<?php echo url('/admin/portfolio' . ($k ? '?type=' . $k : '')); ?>"
               class="px-3 py-1.5 text-sm font-medium rounded-lg <?php echo $type_filter === $k ? 'bg-primary text-white' : 'bg-slate-100 dark:bg-white/5 text-slate-600 hover:bg-slate-200'; ?>">
                <?php echo $v; ?>
            </a>
        <?php endforeach; ?>
    </div>
    <a href="<?php echo url('/admin/portfolio?action=create'); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-blue-700">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Project
    </a>
</div>

<div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="border-b border-black/5 dark:border-white/10">
                <th class="px-4 py-3 text-left font-semibold text-slate-500">Project</th>
                <th class="px-4 py-3 text-left font-semibold text-slate-500">Type</th>
                <th class="px-4 py-3 text-left font-semibold text-slate-500">Client</th>
                <th class="px-4 py-3 text-left font-semibold text-slate-500">Status</th>
                <th class="px-4 py-3 text-right font-semibold text-slate-500">Actions</th>
            </tr></thead>
            <tbody class="divide-y divide-black/5 dark:divide-white/10">
                <?php if (empty($items)): ?>
                    <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">No portfolio projects.</td></tr>
                <?php else: foreach ($items as $p): ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <?php if ($p['featured_image']): ?>
                                    <img src="<?php echo upload_url($p['featured_image']); ?>" class="w-12 h-8 rounded object-cover">
                                <?php endif; ?>
                                <div>
                                    <div class="font-semibold text-slate-700 dark:text-gray-200"><?php echo e(truncate($p['title_en'], 40)); ?></div>
                                    <?php if ($p['is_featured']): ?><span class="text-xs text-yellow-500">★ Featured</span><?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-slate-500 capitalize"><?php echo e($p['type']); ?></td>
                        <td class="px-4 py-3 text-slate-500"><?php echo e($p['client_name'] ?: '—'); ?></td>
                        <td class="px-4 py-3">
                            <?php $c = $p['status'] === 'published' ? 'green' : 'orange'; ?>
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-<?php echo $c; ?>-100 dark:bg-<?php echo $c; ?>-500/10 text-<?php echo $c; ?>-600"><?php echo ucfirst($p['status']); ?></span>
                        </td>
                        <td class="px-4 py-3 text-right space-x-1">
                            <a href="<?php echo url('/admin/portfolio?action=edit&id=' . $p['id']); ?>" class="px-2 py-1 text-xs text-primary hover:bg-blue-50 rounded-lg">Edit</a>
                            <a href="<?php echo url('/admin/portfolio?action=delete&id=' . $p['id'] . '&token=' . csrf_token()); ?>" onclick="authConfirmDelete(this); return false;" data-label="<?php echo e($p['title_en']); ?>" class="px-2 py-1 text-xs text-red-500 hover:bg-red-50 rounded-lg">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($pagination['total_pages'] > 1): ?>
    <div class="mt-6"><?php echo render_pagination($pagination, '/admin/portfolio?' . ($type_filter ? 'type=' . $type_filter . '&' : '')); ?></div>
<?php endif; ?>

<?php endif; ?>
