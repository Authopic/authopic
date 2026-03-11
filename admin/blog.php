<?php
/**
 * Authopic Technologies PLC - Admin: Blog Posts Management
 */
if (!defined('BASE_PATH')) exit;

$action = get('action') ?: 'list';
$id = (int)(get('id') ?: 0);

// ---- DELETE ----
if ($action === 'delete' && $id > 0) {
    if (!csrf_verify(get('token'))) { set_flash('error', 'Invalid token.'); redirect('/admin/blog'); }
    db_query("DELETE FROM blog_posts WHERE id=$id");
    log_activity('delete', 'blog_posts', $id, 'Deleted blog post');
    set_flash('success', 'Blog post deleted.');
    redirect('/admin/blog');
}

// ---- SAVE (Create/Update) ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['create', 'edit'])) {
    if (!csrf_verify(post('csrf_token'))) { set_flash('error', 'Invalid token.'); redirect('/admin/blog'); }

    $title = trim(post('title'));
    $slug = trim(post('slug')) ?: create_slug($title);
    $excerpt = trim(post('excerpt'));
    $content = post('content'); // Allow HTML
    $category_id = (int)post('category_id');
    $status = in_array(post('status'), ['draft', 'published', 'archived']) ? post('status') : 'draft';
    $featured_image = trim(post('featured_image'));
    $tags = trim(post('tags'));
    $meta_title = trim(post('meta_title'));
    $meta_description = trim(post('meta_description'));

    $errors = [];
    if (empty($title)) $errors[] = 'Title is required.';
    if (empty($content)) $errors[] = 'Content is required.';

    // Check slug uniqueness
    $slug_check = db_fetch_one("SELECT id FROM blog_posts WHERE slug='" . db_escape($slug) . "'" . ($id ? " AND id!=$id" : ""));
    if ($slug_check) $errors[] = 'Slug already exists.';

    if (empty($errors)) {
        $safe = [
            'title' => db_escape($title),
            'slug' => db_escape($slug),
            'excerpt' => db_escape($excerpt),
            'content' => db_escape($content),
            'featured_image' => db_escape($featured_image),
            'tags' => db_escape($tags),
            'meta_title' => db_escape($meta_title),
            'meta_description' => db_escape($meta_description),
        ];
        $admin_id = (int)$_SESSION['admin_id'];

        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $upload = handle_upload('image', 'blog');
            if ($upload['success']) {
                $safe['featured_image'] = db_escape($upload['path']);
            }
        }

        if ($action === 'create') {
            $publish_date = ($status === 'published') ? 'NOW()' : 'NULL';
            db_query("INSERT INTO blog_posts (title_en, slug, excerpt_en, content_en, category_id, author_id, featured_image, tags, status, publish_date, meta_title, meta_description) 
                      VALUES ('{$safe['title']}', '{$safe['slug']}', '{$safe['excerpt']}', '{$safe['content']}', $category_id, $admin_id, '{$safe['featured_image']}', '{$safe['tags']}', '$status', $publish_date, '{$safe['meta_title']}', '{$safe['meta_description']}')");
            log_activity('create', 'blog_posts', db_insert_id(), 'Created blog post: ' . $title);
            set_flash('success', 'Blog post created.');
        } else {
            $publish_date_sql = ($status === 'published') ? ", publish_date=NOW()" : '';
            db_query("UPDATE blog_posts SET title_en='{$safe['title']}', slug='{$safe['slug']}', excerpt_en='{$safe['excerpt']}', content_en='{$safe['content']}', category_id=$category_id, featured_image='{$safe['featured_image']}', tags='{$safe['tags']}', status='$status'$publish_date_sql, meta_title='{$safe['meta_title']}', meta_description='{$safe['meta_description']}', updated_at=NOW() WHERE id=$id");
            log_activity('update', 'blog_posts', $id, 'Updated blog post: ' . $title);
            set_flash('success', 'Blog post updated.');
        }
        redirect('/admin/blog');
    } else {
        set_flash('error', implode(' ', $errors));
    }
}

$categories = db_fetch_all("SELECT * FROM blog_categories ORDER BY name_en ASC");

// ---- CREATE/EDIT FORM ----
if (in_array($action, ['create', 'edit'])):
    $post = ['title_en'=>'','slug'=>'','excerpt_en'=>'','content_en'=>'','category_id'=>0,'featured_image'=>'','tags'=>'','status'=>'draft','meta_title'=>'','meta_description'=>''];
    if ($action === 'edit' && $id > 0) {
        $post = db_fetch_one("SELECT * FROM blog_posts WHERE id=$id");
        if (!$post) { set_flash('error', 'Post not found.'); redirect('/admin/blog'); }
    }
?>
<div class="max-w-4xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="<?php echo url('/admin/blog'); ?>" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-white/5 text-slate-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-slate-800 dark:text-white"><?php echo $action === 'create' ? 'New Blog Post' : 'Edit Blog Post'; ?></h2>
    </div>

    <form method="POST" enctype="multipart/form-data" class="space-y-6">
        <?php echo csrf_field(); ?>

        <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-6 space-y-4">
            <div>
                <label class="form-label">Title *</label>
                <input type="text" name="title" id="post-title" value="<?php echo e($post['title_en']); ?>" required class="form-input">
            </div>
            <div>
                <label class="form-label">Slug</label>
                <input type="text" name="slug" id="post-slug" value="<?php echo e($post['slug']); ?>" class="form-input" placeholder="auto-generated from title">
            </div>
            <script>generateSlug('post-title', 'post-slug');</script>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-input">
                        <option value="0">Uncategorized</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo (int)$post['category_id'] === (int)$cat['id'] ? 'selected' : ''; ?>><?php echo e($cat['name_en']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select name="status" class="form-input">
                        <option value="draft" <?php echo $post['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
                        <option value="published" <?php echo $post['status'] === 'published' ? 'selected' : ''; ?>>Published</option>
                        <option value="archived" <?php echo $post['status'] === 'archived' ? 'selected' : ''; ?>>Archived</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="form-label">Excerpt</label>
                <textarea name="excerpt" rows="2" class="form-input" maxlength="500"><?php echo e($post['excerpt_en']); ?></textarea>
            </div>

            <div>
                <label class="form-label">Content *</label>
                <!-- Basic toolbar -->
                <div class="flex flex-wrap gap-1 mb-2">
                    <button type="button" onclick="insertTag('post-content','<h2>','</h2>')" class="px-2 py-1 text-xs bg-slate-100 dark:bg-white/5 rounded font-bold">H2</button>
                    <button type="button" onclick="insertTag('post-content','<h3>','</h3>')" class="px-2 py-1 text-xs bg-slate-100 dark:bg-white/5 rounded font-bold">H3</button>
                    <button type="button" onclick="insertTag('post-content','<strong>','</strong>')" class="px-2 py-1 text-xs bg-slate-100 dark:bg-white/5 rounded font-bold">B</button>
                    <button type="button" onclick="insertTag('post-content','<em>','</em>')" class="px-2 py-1 text-xs bg-slate-100 dark:bg-white/5 rounded italic">I</button>
                    <button type="button" onclick="insertTag('post-content','<a href=&quot;&quot;>','</a>')" class="px-2 py-1 text-xs bg-slate-100 dark:bg-white/5 rounded">Link</button>
                    <button type="button" onclick="insertTag('post-content','<ul>\n<li>','</li>\n</ul>')" class="px-2 py-1 text-xs bg-slate-100 dark:bg-white/5 rounded">UL</button>
                    <button type="button" onclick="insertTag('post-content','<blockquote>','</blockquote>')" class="px-2 py-1 text-xs bg-slate-100 dark:bg-white/5 rounded">Quote</button>
                </div>
                <textarea name="content" id="post-content" rows="15" required class="form-input font-mono text-sm"><?php echo e($post['content_en']); ?></textarea>
            </div>

            <div>
                <label class="form-label">Featured Image</label>
                <input type="file" name="image" accept="image/*" class="form-input" onchange="previewImage(this, 'img-preview')">
                <?php if (!empty($post['featured_image'])): ?>
                    <img id="img-preview" src="<?php echo upload_url($post['featured_image']); ?>" class="mt-2 w-40 h-24 object-cover rounded-lg">
                <?php else: ?>
                    <img id="img-preview" class="mt-2 w-40 h-24 object-cover rounded-lg hidden">
                <?php endif; ?>
                <input type="hidden" name="featured_image" value="<?php echo e($post['featured_image']); ?>">
            </div>

            <div>
                <label class="form-label">Tags (comma-separated)</label>
                <input type="text" name="tags" value="<?php echo e($post['tags']); ?>" class="form-input" placeholder="php, web development, tutorial">
            </div>
        </div>

        <!-- SEO -->
        <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-6 space-y-4">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider">SEO Settings</h3>
            <div>
                <label class="form-label">Meta Title</label>
                <input type="text" name="meta_title" value="<?php echo e($post['meta_title']); ?>" class="form-input" maxlength="70">
            </div>
            <div>
                <label class="form-label">Meta Description</label>
                <textarea name="meta_description" rows="2" class="form-input" maxlength="160"><?php echo e($post['meta_description']); ?></textarea>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="px-6 py-3 bg-primary text-white font-semibold rounded-xl hover:bg-blue-700 transition-colors">
                <?php echo $action === 'create' ? 'Create Post' : 'Update Post'; ?>
            </button>
            <a href="<?php echo url('/admin/blog'); ?>" class="px-6 py-3 bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-gray-300 font-semibold rounded-xl hover:bg-slate-200 transition-colors">Cancel</a>
        </div>
    </form>
</div>

<?php else: // LIST

$status_filter = get('status') ?: '';
$where = $status_filter ? "WHERE bp.status='" . db_escape($status_filter) . "'" : '';
$total = db_fetch_one("SELECT COUNT(*) as c FROM blog_posts bp $where")['c'] ?? 0;
$pagination = paginate($total, 20);
$posts = db_fetch_all("SELECT bp.*, bc.name_en as category_name, au.full_name as author_name FROM blog_posts bp LEFT JOIN blog_categories bc ON bp.category_id=bc.id LEFT JOIN admin_users au ON bp.author_id=au.id $where ORDER BY bp.created_at DESC LIMIT {$pagination['offset']}, {$pagination['per_page']}");
?>

<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div class="flex items-center gap-2">
        <?php foreach (['', 'published', 'draft', 'archived'] as $s): ?>
            <a href="<?php echo url('/admin/blog' . ($s ? '?status=' . $s : '')); ?>"
               class="px-3 py-1.5 text-sm font-medium rounded-lg <?php echo $status_filter === $s ? 'bg-primary text-white' : 'bg-slate-100 dark:bg-white/5 text-slate-600 hover:bg-slate-200'; ?>">
                <?php echo $s ? ucfirst($s) : 'All'; ?>
            </a>
        <?php endforeach; ?>
    </div>
    <a href="<?php echo url('/admin/blog?action=create'); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-blue-700">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Post
    </a>
</div>

<div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="border-b border-black/5 dark:border-white/10">
                <th class="px-4 py-3 text-left font-semibold text-slate-500">Title</th>
                <th class="px-4 py-3 text-left font-semibold text-slate-500">Category</th>
                <th class="px-4 py-3 text-left font-semibold text-slate-500">Status</th>
                <th class="px-4 py-3 text-left font-semibold text-slate-500">Views</th>
                <th class="px-4 py-3 text-left font-semibold text-slate-500">Date</th>
                <th class="px-4 py-3 text-right font-semibold text-slate-500">Actions</th>
            </tr></thead>
            <tbody class="divide-y divide-black/5 dark:divide-white/10">
                <?php if (empty($posts)): ?>
                    <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">No blog posts.</td></tr>
                <?php else: foreach ($posts as $p): ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5">
                        <td class="px-4 py-3">
                            <div class="font-semibold text-slate-700 dark:text-gray-200"><?php echo e(truncate($p['title_en'], 50)); ?></div>
                            <div class="text-xs text-slate-400">by <?php echo e($p['author_name'] ?: 'Admin'); ?></div>
                        </td>
                        <td class="px-4 py-3 text-slate-500"><?php echo e($p['category_name'] ?: '—'); ?></td>
                        <td class="px-4 py-3">
                            <?php $c = ['published'=>'green','draft'=>'orange','archived'=>'slate'][$p['status']] ?? 'slate'; ?>
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-<?php echo $c; ?>-100 dark:bg-<?php echo $c; ?>-500/10 text-<?php echo $c; ?>-600"><?php echo ucfirst($p['status']); ?></span>
                        </td>
                        <td class="px-4 py-3 text-slate-500"><?php echo number_format($p['views']); ?></td>
                        <td class="px-4 py-3 text-slate-500"><?php echo format_date($p['created_at'], 'M j, Y'); ?></td>
                        <td class="px-4 py-3 text-right space-x-1">
                            <a href="<?php echo url('/insights/' . $p['slug']); ?>" target="_blank" class="px-2 py-1 text-xs text-slate-500 hover:text-primary rounded-lg">View</a>
                            <a href="<?php echo url('/admin/blog?action=edit&id=' . $p['id']); ?>" class="px-2 py-1 text-xs text-primary hover:bg-blue-50 rounded-lg">Edit</a>
                            <a href="<?php echo url('/admin/blog?action=delete&id=' . $p['id'] . '&token=' . csrf_token()); ?>" onclick="authConfirmDelete(this); return false;" data-label="<?php echo e(truncate($p['title_en'], 60)); ?>" class="px-2 py-1 text-xs text-red-500 hover:bg-red-50 rounded-lg">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($pagination['total_pages'] > 1): ?>
    <div class="mt-6"><?php echo render_pagination($pagination, '/admin/blog?' . ($status_filter ? 'status=' . $status_filter . '&' : '')); ?></div>
<?php endif; ?>

<?php endif; ?>
