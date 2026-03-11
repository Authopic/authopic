<?php
/**
 * Authopic Technologies PLC - Admin: Products Management
 */
if (!defined('BASE_PATH'))
    exit;

$action = get('action') ?: 'list';
$id = (int)(get('id') ?: 0);

// ---- DELETE ----
if ($action === 'delete' && $id > 0) {
    if (!csrf_verify(get('token'))) {
        set_flash('error', 'Invalid token.');
        redirect('/admin/products');
    }
    db_query("DELETE FROM products WHERE id=$id");
    log_activity('delete', 'products', $id, 'Deleted product');
    set_flash('success', 'Product deleted.');
    redirect('/admin/products');
}

// ---- SAVE ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['create', 'edit'])) {
    if (!csrf_verify(post('csrf_token'))) {
        set_flash('error', 'Invalid token.');
        redirect('/admin/products');
    }

    $name = trim(post('name'));
    $slug = trim(post('slug')) ?: create_slug($name);
    $tagline = trim(post('tagline'));
    $description = post('description');
    $features = post('features');
    $pricing = post('pricing');
    $faq = post('faq');
    $status = in_array(post('status'), ['draft', 'published', 'archived']) ? post('status') : 'draft';

    $errors = [];
    if (empty($name))
        $errors[] = 'Product name is required.';
    $slug_check = db_fetch_one("SELECT id FROM products WHERE slug='" . db_escape($slug) . "'" . ($id ? " AND id!=$id" : ""));
    if ($slug_check)
        $errors[] = 'Slug already exists.';

    if (empty($errors)) {
        $safe = [];
        foreach (['name', 'slug', 'tagline', 'description', 'features', 'pricing', 'faq'] as $f) {
            $safe[$f] = db_escape($$f);
        }

        if ($action === 'create') {
            db_query("INSERT INTO products (name_en, slug, tagline_en, description_en, features, pricing_tiers, faq, status)
                      VALUES ('{$safe['name']}', '{$safe['slug']}', '{$safe['tagline']}', '{$safe['description']}', '{$safe['features']}', '{$safe['pricing']}', '{$safe['faq']}', '$status')");
            log_activity('create', 'products', db_insert_id(), 'Created product: ' . $name);
            set_flash('success', 'Product created.');
        }
        else {
            db_query("UPDATE products SET name_en='{$safe['name']}', slug='{$safe['slug']}', tagline_en='{$safe['tagline']}', description_en='{$safe['description']}', features='{$safe['features']}', pricing_tiers='{$safe['pricing']}', faq='{$safe['faq']}', status='$status', updated_at=NOW() WHERE id=$id");
            log_activity('update', 'products', $id, 'Updated product: ' . $name);
            set_flash('success', 'Product updated.');
        }
        redirect('/admin/products');
    }
    else {
        set_flash('error', implode(' ', $errors));
    }
}

// ---- CREATE/EDIT FORM ----
if (in_array($action, ['create', 'edit'])):
    $item = ['name' => '', 'slug' => '', 'tagline' => '', 'description' => '', 'features' => '', 'pricing' => '', 'faq' => '', 'status' => 'draft'];
    if ($action === 'edit' && $id > 0) {
        $row = db_fetch_one("SELECT * FROM products WHERE id=$id");
        if (!$row) {
            set_flash('error', 'Product not found.');
            redirect('/admin/products');
        }
        $item = [
            'name'        => $row['name_en'],
            'slug'        => $row['slug'],
            'tagline'     => $row['tagline_en'],
            'description' => $row['description_en'],
            'features'    => $row['features'],
            'pricing'     => $row['pricing_tiers'],
            'faq'         => $row['faq'],
            'status'      => $row['status'],
        ];
    }
?>
<div class="max-w-4xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="<?php echo url('/admin/products'); ?>" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-white/5 text-slate-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-slate-800 dark:text-white"><?php echo $action === 'create' ? 'New Product' : 'Edit Product'; ?></h2>
    </div>

    <form method="POST" class="space-y-6">
        <?php echo csrf_field(); ?>
        <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-6 space-y-4">
            <div>
                <label class="form-label">Product Name *</label>
                <input type="text" name="name" id="prod-name" value="<?php echo e($item['name']); ?>" required class="form-input">
            </div>
            <div>
                <label class="form-label">Slug</label>
                <input type="text" name="slug" id="prod-slug" value="<?php echo e($item['slug']); ?>" class="form-input" placeholder="auto-generated">
            </div>
            <script>generateSlug('prod-name', 'prod-slug');</script>

            <div class="grid grid-cols-1 sm:grid-cols-1 gap-4">
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
                <label class="form-label">Tagline</label>
                <input type="text" name="tagline" value="<?php echo e($item['tagline']); ?>" class="form-input" maxlength="200">
            </div>
            <div>
                <label class="form-label">Full Description (HTML)</label>
                <textarea name="description" rows="8" class="form-input font-mono text-sm"><?php echo e($item['description']); ?></textarea>
            </div>
        </div>

        <!-- JSON Data Fields -->
        <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-6 space-y-4">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider">JSON Data</h3>
            <div>
                <label class="form-label">Features (JSON Array)</label>
                <textarea name="features" rows="6" class="form-input font-mono text-sm" placeholder='[{"title":"Feature Name","desc":"Description","icon":"icon-name"}]'><?php echo e($item['features']); ?></textarea>
            </div>
            <div>
                <label class="form-label">Pricing Tiers (JSON Array)</label>
                <textarea name="pricing" rows="6" class="form-input font-mono text-sm" placeholder='[{"plan":"Basic","monthly":99,"annual":79,"features":["Feature 1"]}]'><?php echo e($item['pricing']); ?></textarea>
            </div>
            <div>
                <label class="form-label">FAQ (JSON Array)</label>
                <textarea name="faq" rows="4" class="form-input font-mono text-sm" placeholder='[{"q":"Question?","a":"Answer."}]'><?php echo e($item['faq']); ?></textarea>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="px-6 py-3 bg-primary text-white font-semibold rounded-xl hover:bg-blue-700 transition-colors">
                <?php echo $action === 'create' ? 'Create Product' : 'Update Product'; ?>
            </button>
            <a href="<?php echo url('/admin/products'); ?>" class="px-6 py-3 bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-gray-300 font-semibold rounded-xl hover:bg-slate-200 transition-colors">Cancel</a>
        </div>
    </form>
</div>

<?php
else: // LIST
    $total = db_fetch_one("SELECT COUNT(*) as c FROM products")['c'] ?? 0;    $pagination = paginate($total, 20);    $items = db_fetch_all("SELECT * FROM products ORDER BY sort_order ASC, created_at DESC LIMIT {$pagination['offset']}, {$pagination['per_page']}");
?>

<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <h2 class="text-lg font-bold text-slate-800 dark:text-white">Products (<?php echo $total; ?>)</h2>
    <a href="<?php echo url('/admin/products?action=create'); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-blue-700">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Product
    </a>
</div>

<div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="border-b border-black/5 dark:border-white/10">
                <th class="px-4 py-3 text-left font-semibold text-slate-500">Product</th>
                <th class="px-4 py-3 text-left font-semibold text-slate-500">Tagline</th>
                <th class="px-4 py-3 text-left font-semibold text-slate-500">Status</th>
                <th class="px-4 py-3 text-left font-semibold text-slate-500">Views</th>
                <th class="px-4 py-3 text-right font-semibold text-slate-500">Actions</th>
            </tr></thead>
            <tbody class="divide-y divide-black/5 dark:divide-white/10">
                <?php if (empty($items)): ?>
                    <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">No products yet.</td></tr>
                <?php
    else:
        foreach ($items as $p): ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5">
                        <td class="px-4 py-3 font-semibold text-slate-700 dark:text-gray-200"><?php echo e($p['name_en']); ?></td>
                        <td class="px-4 py-3 text-slate-500"><?php echo e(truncate($p['tagline'] ?? '', 50)); ?></td>
                        <td class="px-4 py-3">
                            <?php $c = ['published' => 'green', 'draft' => 'orange', 'archived' => 'slate'][$p['status']] ?? 'slate'; ?>
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-<?php echo $c; ?>-100 dark:bg-<?php echo $c; ?>-500/10 text-<?php echo $c; ?>-600"><?php echo ucfirst($p['status']); ?></span>
                        </td>
                        <td class="px-4 py-3 text-slate-500"><?php echo number_format($p['views']); ?></td>
                        <td class="px-4 py-3 text-right space-x-1">
                            <a href="<?php echo url('/products/' . $p['slug']); ?>" target="_blank" class="px-2 py-1 text-xs text-slate-500 hover:text-primary rounded-lg">View</a>
                            <a href="<?php echo url('/admin/products?action=edit&id=' . $p['id']); ?>" class="px-2 py-1 text-xs text-primary hover:bg-blue-50 rounded-lg">Edit</a>
                            <a href="<?php echo url('/admin/products?action=delete&id=' . $p['id'] . '&token=' . csrf_token()); ?>" onclick="authConfirmDelete(this); return false;" data-label="<?php echo e($p['name_en']); ?>" class="px-2 py-1 text-xs text-red-500 hover:bg-red-50 rounded-lg">Delete</a>
                        </td>
                    </tr>
                <?php
        endforeach;
    endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
endif; ?>
