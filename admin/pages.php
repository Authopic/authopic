<?php
/**
 * Authopic Technologies PLC - Admin: Pages Management
 */
if (!defined('BASE_PATH')) exit;

$action = get('action') ?: 'list';
$id = (int)(get('id') ?: 0);

// ---- DELETE ----
if ($action === 'delete' && $id > 0) {
    if (!csrf_verify(get('token'))) { set_flash('error', 'Invalid token.'); redirect('/admin/pages'); }
    db_query("DELETE FROM page_revisions WHERE page_id=$id");
    db_query("DELETE FROM pages WHERE id=$id");
    log_activity('delete', 'pages', $id, 'Deleted page');
    set_flash('success', 'Page deleted.');
    redirect('/admin/pages');
}

// ---- SAVE ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['create', 'edit'])) {
    if (!csrf_verify(post('csrf_token'))) { set_flash('error', 'Invalid token.'); redirect('/admin/pages'); }

    $title = trim(post('title'));
    $slug = trim(post('slug')) ?: create_slug($title);
    $content = post('content');
    $content_am = post('content_am');
    $meta_title = trim(post('meta_title'));
    $meta_description = trim(post('meta_description'));
    $template = trim(post('template')) ?: 'default';
    $status = in_array(post('status'), ['draft', 'published', 'archived']) ? post('status') : 'draft';

    $errors = [];
    if (empty($title)) $errors[] = 'Title is required.';
    $slug_check = db_fetch_one("SELECT id FROM pages WHERE slug='" . db_escape($slug) . "'" . ($id ? " AND id!=$id" : ""));
    if ($slug_check) $errors[] = 'Slug already exists.';

    if (empty($errors)) {
        $safe = [];
        foreach (['title','slug','content','content_am','meta_title','meta_description','template'] as $f) {
            $safe[$f] = db_escape($$f);
        }
        $admin_id = (int)$_SESSION['admin_id'];

        if ($action === 'create') {
            db_query("INSERT INTO pages (title, slug, content, content_am, meta_title, meta_description, template, status, author_id, created_at)
                      VALUES ('{$safe['title']}', '{$safe['slug']}', '{$safe['content']}', '{$safe['content_am']}', '{$safe['meta_title']}', '{$safe['meta_description']}', '{$safe['template']}', '$status', $admin_id, NOW())");
            $new_id = db_insert_id();
            // Save revision
            db_query("INSERT INTO page_revisions (page_id, content, revised_by, created_at) VALUES ($new_id, '{$safe['content']}', $admin_id, NOW())");
            log_activity('create', 'pages', $new_id, 'Created page: ' . $title);
            set_flash('success', 'Page created.');
        } else {
            db_query("UPDATE pages SET title='{$safe['title']}', slug='{$safe['slug']}', content='{$safe['content']}', content_am='{$safe['content_am']}', meta_title='{$safe['meta_title']}', meta_description='{$safe['meta_description']}', template='{$safe['template']}', status='$status', updated_at=NOW() WHERE id=$id");
            // Save revision
            db_query("INSERT INTO page_revisions (page_id, content, revised_by, created_at) VALUES ($id, '{$safe['content']}', $admin_id, NOW())");
            log_activity('update', 'pages', $id, 'Updated page: ' . $title);
            set_flash('success', 'Page updated.');
        }
        redirect('/admin/pages');
    } else {
        set_flash('error', implode(' ', $errors));
    }
}

// ---- CREATE/EDIT FORM ----
if (in_array($action, ['create', 'edit'])):
    $item = ['title'=>'','slug'=>'','content'=>'','content_am'=>'','meta_title'=>'','meta_description'=>'','template'=>'default','status'=>'draft'];
    $revisions = [];
    if ($action === 'edit' && $id > 0) {
        $item = db_fetch_one("SELECT * FROM pages WHERE id=$id");
        if (!$item) { set_flash('error', 'Page not found.'); redirect('/admin/pages'); }
        $revisions = db_fetch_all("SELECT pr.*, au.full_name FROM page_revisions pr LEFT JOIN admin_users au ON pr.revised_by=au.id WHERE pr.page_id=$id ORDER BY pr.created_at DESC LIMIT 10");
    }
?>
<div class="max-w-4xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="<?php echo url('/admin/pages'); ?>" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-white/5 text-slate-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-slate-800 dark:text-white"><?php echo $action === 'create' ? 'New Page' : 'Edit Page'; ?></h2>
    </div>

    <form method="POST" class="space-y-6">
        <?php echo csrf_field(); ?>
        <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-6 space-y-4">
            <div>
                <label class="form-label">Title *</label>
                <input type="text" name="title" id="page-title" value="<?php echo e($item['title']); ?>" required class="form-input">
            </div>
            <div>
                <label class="form-label">Slug</label>
                <input type="text" name="slug" id="page-slug" value="<?php echo e($item['slug']); ?>" class="form-input">
            </div>
            <script>generateSlug('page-title', 'page-slug');</script>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Template</label>
                    <select name="template" class="form-input">
                        <option value="default" <?php echo $item['template'] === 'default' ? 'selected' : ''; ?>>Default</option>
                        <option value="full-width" <?php echo $item['template'] === 'full-width' ? 'selected' : ''; ?>>Full Width</option>
                        <option value="sidebar" <?php echo $item['template'] === 'sidebar' ? 'selected' : ''; ?>>With Sidebar</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select name="status" class="form-input">
                        <option value="draft" <?php echo $item['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
                        <option value="published" <?php echo $item['status'] === 'published' ? 'selected' : ''; ?>>Published</option>
                        <option value="archived" <?php echo $item['status'] === 'archived' ? 'selected' : ''; ?>>Archived</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="form-label">Content (English/HTML)</label>
                <div class="flex flex-wrap gap-1 mb-2">
                    <button type="button" onclick="insertTag('page-content','<h2>','</h2>')" class="px-2 py-1 text-xs bg-slate-100 dark:bg-white/5 rounded font-bold">H2</button>
                    <button type="button" onclick="insertTag('page-content','<h3>','</h3>')" class="px-2 py-1 text-xs bg-slate-100 dark:bg-white/5 rounded font-bold">H3</button>
                    <button type="button" onclick="insertTag('page-content','<p>','</p>')" class="px-2 py-1 text-xs bg-slate-100 dark:bg-white/5 rounded">P</button>
                    <button type="button" onclick="insertTag('page-content','<strong>','</strong>')" class="px-2 py-1 text-xs bg-slate-100 dark:bg-white/5 rounded font-bold">B</button>
                    <button type="button" onclick="insertTag('page-content','<a href=&quot;&quot;>','</a>')" class="px-2 py-1 text-xs bg-slate-100 dark:bg-white/5 rounded">Link</button>
                </div>
                <textarea name="content" id="page-content" rows="12" class="form-input font-mono text-sm"><?php echo e($item['content']); ?></textarea>
            </div>

            <div>
                <label class="form-label">Content (Amharic)</label>
                <textarea name="content_am" rows="8" class="form-input font-mono text-sm" dir="auto"><?php echo e($item['content_am']); ?></textarea>
            </div>
        </div>

        <!-- SEO -->
        <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-6 space-y-4">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider">SEO</h3>
            <div>
                <label class="form-label">Meta Title</label>
                <input type="text" name="meta_title" value="<?php echo e($item['meta_title']); ?>" class="form-input" maxlength="70">
            </div>
            <div>
                <label class="form-label">Meta Description</label>
                <textarea name="meta_description" rows="2" class="form-input" maxlength="160"><?php echo e($item['meta_description']); ?></textarea>
            </div>
        </div>

        <?php if (!empty($revisions)): ?>
        <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-6">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider mb-3">Recent Revisions</h3>
            <div class="space-y-2">
                <?php foreach ($revisions as $rev): ?>
                    <div class="flex justify-between items-center text-sm text-slate-500">
                        <span><?php echo e($rev['full_name'] ?: 'Admin'); ?></span>
                        <span><?php echo time_ago($rev['created_at']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="flex items-center gap-3">
            <button type="submit" class="px-6 py-3 bg-primary text-white font-semibold rounded-xl hover:bg-blue-700 transition-colors">
                <?php echo $action === 'create' ? 'Create Page' : 'Update Page'; ?>
            </button>
            <a href="<?php echo url('/admin/pages'); ?>" class="px-6 py-3 bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-gray-300 font-semibold rounded-xl hover:bg-slate-200 transition-colors">Cancel</a>
        </div>
    </form>
</div>

<?php else: // LIST

$total = db_fetch_one("SELECT COUNT(*) as c FROM pages")['c'] ?? 0;
$items = db_fetch_all("SELECT p.*, au.full_name as author_name FROM pages p LEFT JOIN admin_users au ON p.author_id=au.id ORDER BY p.created_at DESC");
?>

<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <h2 class="text-lg font-bold text-slate-800 dark:text-white">Pages (<?php echo $total; ?>)</h2>
    <a href="<?php echo url('/admin/pages?action=create'); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-blue-700">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Page
    </a>
</div>

<div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="border-b border-black/5 dark:border-white/10">
                <th class="px-4 py-3 text-left font-semibold text-slate-500">Title</th>
                <th class="px-4 py-3 text-left font-semibold text-slate-500">Slug</th>
                <th class="px-4 py-3 text-left font-semibold text-slate-500">Author</th>
                <th class="px-4 py-3 text-left font-semibold text-slate-500">Status</th>
                <th class="px-4 py-3 text-left font-semibold text-slate-500">Updated</th>
                <th class="px-4 py-3 text-right font-semibold text-slate-500">Actions</th>
            </tr></thead>
            <tbody class="divide-y divide-black/5 dark:divide-white/10">
                <?php if (empty($items)): ?>
                    <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">No pages yet.</td></tr>
                <?php else: foreach ($items as $p): ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5">
                        <td class="px-4 py-3 font-semibold text-slate-700 dark:text-gray-200"><?php echo e($p['title']); ?></td>
                        <td class="px-4 py-3 text-slate-400 font-mono text-xs">/<?php echo e($p['slug']); ?></td>
                        <td class="px-4 py-3 text-slate-500"><?php echo e($p['author_name'] ?: '—'); ?></td>
                        <td class="px-4 py-3">
                            <?php $c = ['published'=>'green','draft'=>'orange','archived'=>'slate'][$p['status']] ?? 'slate'; ?>
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-<?php echo $c; ?>-100 dark:bg-<?php echo $c; ?>-500/10 text-<?php echo $c; ?>-600"><?php echo ucfirst($p['status']); ?></span>
                        </td>
                        <td class="px-4 py-3 text-slate-500 text-xs"><?php echo $p['updated_at'] ? time_ago($p['updated_at']) : format_date($p['created_at'], 'M j, Y'); ?></td>
                        <td class="px-4 py-3 text-right space-x-1">
                            <a href="<?php echo url('/admin/pages?action=edit&id=' . $p['id']); ?>" class="px-2 py-1 text-xs text-primary hover:bg-blue-50 rounded-lg">Edit</a>
                            <a href="<?php echo url('/admin/pages?action=delete&id=' . $p['id'] . '&token=' . csrf_token()); ?>" onclick="return confirmDelete()" class="px-2 py-1 text-xs text-red-500 hover:bg-red-50 rounded-lg">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php endif; ?>
