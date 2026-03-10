<?php
/**
 * Authopic Technologies PLC - Admin: Services Management
 */
if (!defined('BASE_PATH')) exit;

$action = get('action') ?: 'list';
$id = (int)(get('id') ?: 0);

// ---- DELETE ----
if ($action === 'delete' && $id > 0) {
    if (!csrf_verify(get('token'))) { set_flash('error', 'Invalid token.'); redirect('/admin/services'); }
    db_query("DELETE FROM services WHERE id=$id");
    log_activity('delete', 'services', $id, 'Deleted service');
    set_flash('success', 'Service deleted.');
    redirect('/admin/services');
}

// ---- SAVE ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['create', 'edit'])) {
    if (!csrf_verify(post('csrf_token'))) { set_flash('error', 'Invalid token.'); redirect('/admin/services'); }

    $title = trim(post('title'));
    $slug = trim(post('slug')) ?: create_slug($title);
    $short_description = trim(post('short_description'));
    $description = post('description');
    $icon = trim(post('icon'));
    $offerings = post('offerings');
    $technologies = post('technologies');
    $process = post('process');
    $price_range = trim(post('price_range'));
    $timeline = trim(post('timeline'));
    $status = in_array(post('status'), ['draft', 'active', 'archived']) ? post('status') : 'draft';

    $errors = [];
    if (empty($title)) $errors[] = 'Title is required.';
    $slug_check = db_fetch_one("SELECT id FROM services WHERE slug='" . db_escape($slug) . "'" . ($id ? " AND id!=$id" : ""));
    if ($slug_check) $errors[] = 'Slug already exists.';

    if (empty($errors)) {
        $safe = [];
        foreach (['title','slug','short_description','description','icon','offerings','technologies','process','price_range','timeline'] as $f) {
            $safe[$f] = db_escape($$f);
        }

        if ($action === 'create') {
            db_query("INSERT INTO services (title, slug, short_description, description, icon, offerings, technologies, process, price_range, timeline, status, created_at)
                      VALUES ('{$safe['title']}', '{$safe['slug']}', '{$safe['short_description']}', '{$safe['description']}', '{$safe['icon']}', '{$safe['offerings']}', '{$safe['technologies']}', '{$safe['process']}', '{$safe['price_range']}', '{$safe['timeline']}', '$status', NOW())");
            log_activity('create', 'services', db_insert_id(), 'Created service: ' . $title);
            set_flash('success', 'Service created.');
        } else {
            db_query("UPDATE services SET title='{$safe['title']}', slug='{$safe['slug']}', short_description='{$safe['short_description']}', description='{$safe['description']}', icon='{$safe['icon']}', offerings='{$safe['offerings']}', technologies='{$safe['technologies']}', process='{$safe['process']}', price_range='{$safe['price_range']}', timeline='{$safe['timeline']}', status='$status', updated_at=NOW() WHERE id=$id");
            log_activity('update', 'services', $id, 'Updated service: ' . $title);
            set_flash('success', 'Service updated.');
        }
        redirect('/admin/services');
    } else {
        set_flash('error', implode(' ', $errors));
    }
}

// ---- CREATE/EDIT FORM ----
if (in_array($action, ['create', 'edit'])):
    $item = ['title'=>'','slug'=>'','short_description'=>'','description'=>'','icon'=>'','offerings'=>'','technologies'=>'','process'=>'','price_range'=>'','timeline'=>'','status'=>'draft'];
    if ($action === 'edit' && $id > 0) {
        $item = db_fetch_one("SELECT * FROM services WHERE id=$id");
        if (!$item) { set_flash('error', 'Service not found.'); redirect('/admin/services'); }
    }
?>
<div class="max-w-4xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="<?php echo url('/admin/services'); ?>" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-white/5 text-slate-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-slate-800 dark:text-white"><?php echo $action === 'create' ? 'New Service' : 'Edit Service'; ?></h2>
    </div>

    <form method="POST" class="space-y-6">
        <?php echo csrf_field(); ?>
        <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-6 space-y-4">
            <div>
                <label class="form-label">Title *</label>
                <input type="text" name="title" id="svc-title" value="<?php echo e($item['title']); ?>" required class="form-input">
            </div>
            <div>
                <label class="form-label">Slug</label>
                <input type="text" name="slug" id="svc-slug" value="<?php echo e($item['slug']); ?>" class="form-input">
            </div>
            <script>generateSlug('svc-title', 'svc-slug');</script>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="form-label">Icon</label>
                    <input type="text" name="icon" value="<?php echo e($item['icon']); ?>" class="form-input" placeholder="code-bracket">
                </div>
                <div>
                    <label class="form-label">Price Range</label>
                    <input type="text" name="price_range" value="<?php echo e($item['price_range']); ?>" class="form-input" placeholder="50,000 - 200,000 ETB">
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select name="status" class="form-input">
                        <option value="draft" <?php echo $item['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
                        <option value="active" <?php echo $item['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="archived" <?php echo $item['status'] === 'archived' ? 'selected' : ''; ?>>Archived</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="form-label">Timeline</label>
                <input type="text" name="timeline" value="<?php echo e($item['timeline']); ?>" class="form-input" placeholder="2-4 weeks">
            </div>
            <div>
                <label class="form-label">Short Description</label>
                <textarea name="short_description" rows="2" class="form-input"><?php echo e($item['short_description']); ?></textarea>
            </div>
            <div>
                <label class="form-label">Full Description (HTML)</label>
                <textarea name="description" rows="8" class="form-input font-mono text-sm"><?php echo e($item['description']); ?></textarea>
            </div>
        </div>

        <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-6 space-y-4">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider">JSON Data</h3>
            <div>
                <label class="form-label">Offerings (JSON Array)</label>
                <textarea name="offerings" rows="5" class="form-input font-mono text-sm" placeholder='[{"title":"Offering","desc":"Description"}]'><?php echo e($item['offerings']); ?></textarea>
            </div>
            <div>
                <label class="form-label">Technologies (JSON Array)</label>
                <textarea name="technologies" rows="3" class="form-input font-mono text-sm" placeholder='["PHP","MySQL","React"]'><?php echo e($item['technologies']); ?></textarea>
            </div>
            <div>
                <label class="form-label">Process (JSON Array)</label>
                <textarea name="process" rows="5" class="form-input font-mono text-sm" placeholder='[{"step":1,"title":"Planning","desc":"Description"}]'><?php echo e($item['process']); ?></textarea>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="px-6 py-3 bg-primary text-white font-semibold rounded-xl hover:bg-blue-700 transition-colors">
                <?php echo $action === 'create' ? 'Create Service' : 'Update Service'; ?>
            </button>
            <a href="<?php echo url('/admin/services'); ?>" class="px-6 py-3 bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-gray-300 font-semibold rounded-xl hover:bg-slate-200 transition-colors">Cancel</a>
        </div>
    </form>
</div>

<?php else: // LIST

$total = db_fetch_one("SELECT COUNT(*) as c FROM services")['c'] ?? 0;
$pagination = paginate($total, 20);
$items = db_fetch_all("SELECT * FROM services ORDER BY sort_order ASC, created_at DESC LIMIT {$pagination['offset']}, {$pagination['per_page']}");
?>

<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <h2 class="text-lg font-bold text-slate-800 dark:text-white">Services (<?php echo $total; ?>)</h2>
    <a href="<?php echo url('/admin/services?action=create'); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-blue-700">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Service
    </a>
</div>

<div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="border-b border-black/5 dark:border-white/10">
                <th class="px-4 py-3 text-left font-semibold text-slate-500">Service</th>
                <th class="px-4 py-3 text-left font-semibold text-slate-500">Price Range</th>
                <th class="px-4 py-3 text-left font-semibold text-slate-500">Status</th>
                <th class="px-4 py-3 text-right font-semibold text-slate-500">Actions</th>
            </tr></thead>
            <tbody class="divide-y divide-black/5 dark:divide-white/10">
                <?php if (empty($items)): ?>
                    <tr><td colspan="4" class="px-4 py-8 text-center text-slate-400">No services yet.</td></tr>
                <?php else: foreach ($items as $s): ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5">
                        <td class="px-4 py-3">
                            <div class="font-semibold text-slate-700 dark:text-gray-200"><?php echo e($s['title']); ?></div>
                            <div class="text-xs text-slate-400"><?php echo e(truncate($s['short_description'], 60)); ?></div>
                        </td>
                        <td class="px-4 py-3 text-slate-500"><?php echo e($s['price_range'] ?: '—'); ?></td>
                        <td class="px-4 py-3">
                            <?php $c = ['active'=>'green','draft'=>'orange','archived'=>'slate'][$s['status']] ?? 'slate'; ?>
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-<?php echo $c; ?>-100 dark:bg-<?php echo $c; ?>-500/10 text-<?php echo $c; ?>-600"><?php echo ucfirst($s['status']); ?></span>
                        </td>
                        <td class="px-4 py-3 text-right space-x-1">
                            <a href="<?php echo url('/services/' . $s['slug']); ?>" target="_blank" class="px-2 py-1 text-xs text-slate-500 hover:text-primary rounded-lg">View</a>
                            <a href="<?php echo url('/admin/services?action=edit&id=' . $s['id']); ?>" class="px-2 py-1 text-xs text-primary hover:bg-blue-50 rounded-lg">Edit</a>
                            <a href="<?php echo url('/admin/services?action=delete&id=' . $s['id'] . '&token=' . csrf_token()); ?>" onclick="return confirmDelete()" class="px-2 py-1 text-xs text-red-500 hover:bg-red-50 rounded-lg">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php endif; ?>
