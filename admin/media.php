<?php
/**
 * Authopic Technologies PLC - Admin: Media Library
 */
if (!defined('BASE_PATH')) exit;

$action = get('action') ?: 'list';
$id = (int)(get('id') ?: 0);

// ---- DELETE ----
if ($action === 'delete' && $id > 0) {
    if (!csrf_verify(get('token'))) { set_flash('error', 'Invalid token.'); redirect('/admin/media'); }
    $file = db_fetch_one("SELECT * FROM media WHERE id=$id");
    if ($file) {
        $filepath = BASE_PATH . '/uploads/' . $file['file_path'];
        if (file_exists($filepath)) unlink($filepath);
        // Delete thumbnail if exists
        if (!empty($file['thumbnail_path'])) {
            $thumbpath = BASE_PATH . '/uploads/' . $file['thumbnail_path'];
            if (file_exists($thumbpath)) unlink($thumbpath);
        }
        db_query("DELETE FROM media WHERE id=$id");
        log_activity('delete', 'media', $id, 'Deleted file: ' . $file['original_name']);
    }
    set_flash('success', 'File deleted.');
    redirect('/admin/media');
}

// ---- UPLOAD ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'upload') {
    if (!csrf_verify(post('csrf_token'))) { set_flash('error', 'Invalid token.'); redirect('/admin/media'); }

    $folder = trim(post('folder')) ?: 'general';
    $folder = preg_replace('/[^a-z0-9_-]/', '', strtolower($folder));

    if (!empty($_FILES['files']['name'][0])) {
        $success = 0;
        $fail = 0;
        $file_count = count($_FILES['files']['name']);

        for ($i = 0; $i < $file_count; $i++) {
            // Build single-file array for handle_upload
            $_FILES['single_file'] = [
                'name' => $_FILES['files']['name'][$i],
                'type' => $_FILES['files']['type'][$i],
                'tmp_name' => $_FILES['files']['tmp_name'][$i],
                'error' => $_FILES['files']['error'][$i],
                'size' => $_FILES['files']['size'][$i],
            ];

            $upload = handle_upload('single_file', $folder);
            if ($upload['success']) {
                $original = db_escape($_FILES['files']['name'][$i]);
                $path = db_escape($upload['path']);
                $mime = db_escape($_FILES['files']['type'][$i]);
                $size = (int)$_FILES['files']['size'][$i];
                $admin_id = (int)$_SESSION['admin_id'];

                db_query("INSERT INTO media (file_path, original_name, mime_type, file_size, uploaded_by, created_at) 
                          VALUES ('$path', '$original', '$mime', $size, $admin_id, NOW())");
                $success++;
            } else {
                $fail++;
            }
        }

        if ($success > 0) {
            log_activity('upload', 'media', 0, "Uploaded $success file(s)");
            set_flash('success', "$success file(s) uploaded." . ($fail ? " $fail failed." : ''));
        } else {
            set_flash('error', 'Upload failed. Check file types and sizes.');
        }
    } else {
        set_flash('error', 'No files selected.');
    }
    redirect('/admin/media');
}

// ---- LIST ----
$type_filter = get('type') ?: '';
$search = trim(get('q') ?: '');
$where_parts = [];
if ($type_filter === 'image') $where_parts[] = "mime_type LIKE 'image/%'";
elseif ($type_filter === 'document') $where_parts[] = "mime_type NOT LIKE 'image/%'";
if ($search) $where_parts[] = "original_name LIKE '%" . db_escape($search) . "%'";
$where = $where_parts ? 'WHERE ' . implode(' AND ', $where_parts) : '';

$total = db_fetch_one("SELECT COUNT(*) as c FROM media $where")['c'] ?? 0;
$pagination = paginate($total, 30);
$items = db_fetch_all("SELECT m.*, au.full_name as uploader FROM media m LEFT JOIN admin_users au ON m.uploaded_by=au.id $where ORDER BY m.created_at DESC LIMIT {$pagination['offset']}, {$pagination['per_page']}");

// Storage stats
$total_size = db_fetch_one("SELECT SUM(file_size) as s FROM media")['s'] ?? 0;
$image_count = db_fetch_one("SELECT COUNT(*) as c FROM media WHERE mime_type LIKE 'image/%'")['c'] ?? 0;
?>

<!-- Upload Section -->
<div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-6 mb-6">
    <form method="POST" action="<?php echo url('/admin/media?action=upload'); ?>" enctype="multipart/form-data" class="flex flex-wrap items-end gap-4">
        <?php echo csrf_field(); ?>
        <div class="flex-1 min-w-[200px]">
            <label class="form-label">Upload Files</label>
            <input type="file" name="files[]" multiple accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.zip" class="form-input" required>
        </div>
        <div class="w-40">
            <label class="form-label">Folder</label>
            <select name="folder" class="form-input">
                <option value="general">General</option>
                <option value="blog">Blog</option>
                <option value="portfolio">Portfolio</option>
                <option value="team">Team</option>
                <option value="testimonials">Testimonials</option>
                <option value="products">Products</option>
            </select>
        </div>
        <button type="submit" class="px-5 py-2.5 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-colors">
            Upload
        </button>
    </form>
</div>

<!-- Stats -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-4 text-center">
        <div class="text-2xl font-bold text-primary"><?php echo number_format($total); ?></div>
        <div class="text-xs text-slate-500">Total Files</div>
    </div>
    <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-4 text-center">
        <div class="text-2xl font-bold text-green-500"><?php echo number_format($image_count); ?></div>
        <div class="text-xs text-slate-500">Images</div>
    </div>
    <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl p-4 text-center">
        <div class="text-2xl font-bold text-secondary"><?php echo $total_size > 1048576 ? number_format($total_size / 1048576, 1) . ' MB' : number_format($total_size / 1024, 0) . ' KB'; ?></div>
        <div class="text-xs text-slate-500">Storage Used</div>
    </div>
</div>

<!-- Filters -->
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div class="flex items-center gap-2">
        <?php foreach ([''=>'All', 'image'=>'Images', 'document'=>'Documents'] as $k=>$v): ?>
            <a href="<?php echo url('/admin/media' . ($k ? '?type=' . $k : '')); ?>"
               class="px-3 py-1.5 text-sm font-medium rounded-lg <?php echo $type_filter === $k ? 'bg-primary text-white' : 'bg-slate-100 dark:bg-white/5 text-slate-600 hover:bg-slate-200'; ?>">
                <?php echo $v; ?>
            </a>
        <?php endforeach; ?>
    </div>
    <form method="GET" action="<?php echo url('/admin/media'); ?>" class="flex items-center gap-2">
        <input type="text" name="q" value="<?php echo e($search); ?>" placeholder="Search files..." class="form-input !py-1.5 text-sm w-48">
        <button type="submit" class="px-3 py-1.5 bg-slate-100 dark:bg-white/5 text-slate-600 text-sm rounded-lg hover:bg-slate-200">Search</button>
    </form>
</div>

<!-- Media Grid -->
<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
    <?php if (empty($items)): ?>
        <div class="col-span-full text-center text-slate-400 py-12">No files found.</div>
    <?php else: foreach ($items as $f):
        $is_image = strpos($f['mime_type'], 'image/') === 0;
    ?>
        <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-xl overflow-hidden group">
            <div class="aspect-square relative">
                <?php if ($is_image): ?>
                    <img src="<?php echo upload_url($f['file_path']); ?>" class="w-full h-full object-cover" loading="lazy" alt="<?php echo e($f['original_name']); ?>">
                <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center bg-slate-50 dark:bg-white/5">
                        <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                <?php endif; ?>
                <!-- Overlay -->
                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                    <?php if ($is_image): ?>
                        <button onclick="copyToClipboard('<?php echo upload_url($f['file_path']); ?>')" class="p-2 bg-white/20 rounded-lg text-white hover:bg-white/30" title="Copy URL">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </button>
                    <?php endif; ?>
                    <a href="<?php echo url('/admin/media?action=delete&id=' . $f['id'] . '&token=' . csrf_token()); ?>" onclick="authConfirmDelete(this); return false;" data-label="<?php echo e($f['original_name']); ?>" class="p-2 bg-red-500/20 rounded-lg text-white hover:bg-red-500/40" title="Delete">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </a>
                </div>
            </div>
            <div class="p-2">
                <p class="text-xs font-medium text-slate-600 dark:text-gray-300 truncate" title="<?php echo e($f['original_name']); ?>"><?php echo e($f['original_name']); ?></p>
                <p class="text-[10px] text-slate-400"><?php echo $f['file_size'] > 1048576 ? number_format($f['file_size'] / 1048576, 1) . ' MB' : number_format($f['file_size'] / 1024, 0) . ' KB'; ?></p>
            </div>
        </div>
    <?php endforeach; endif; ?>
</div>

<?php if ($pagination['total_pages'] > 1): ?>
    <div class="mt-6"><?php echo render_pagination($pagination, '/admin/media?' . ($type_filter ? 'type=' . $type_filter . '&' : '')); ?></div>
<?php endif; ?>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        if (typeof showToast === 'function') showToast('URL copied!', 'success');
    });
}
</script>
