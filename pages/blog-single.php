<?php
/**
 * Authopic Technologies PLC - Blog Single Page (/insights/{slug})
 */
if (!defined('BASE_PATH')) exit;

$slug = db_escape($route_params['slug'] ?? '');
$post = db_fetch_one("SELECT p.*, c.name_en as cat_name, c.name_am as cat_name_am, c.slug as cat_slug, a.full_name as author_name, a.avatar as author_avatar
    FROM `blog_posts` p 
    LEFT JOIN `blog_categories` c ON p.category_id = c.id 
    LEFT JOIN `admin_users` a ON p.author_id = a.id 
    WHERE p.slug = '$slug' AND p.status = 'published' AND p.publish_date <= NOW()");

if (!$post) {
    http_response_code(404);
    require_once BASE_PATH . '/pages/404.php';
    return;
}

// Increment views
db_query("UPDATE `blog_posts` SET `views` = `views` + 1 WHERE `id` = {$post['id']}");

$page_title = get_text($post['title_en'], $post['title_am']);
$page_description = truncate(get_text($post['excerpt_en'], $post['excerpt_am']), 160);
$tags = get_json($post['tags']);

// Related posts
$related = db_fetch_all("SELECT p.*, c.name_en as cat_name, c.slug as cat_slug 
    FROM `blog_posts` p 
    LEFT JOIN `blog_categories` c ON p.category_id = c.id 
    WHERE p.id != {$post['id']} AND p.category_id = {$post['category_id']} AND p.status = 'published' 
    ORDER BY p.publish_date DESC LIMIT 3");

require_once BASE_PATH . '/includes/header.php';
?>

<!-- Article Header -->
<article>
    <header class="relative pt-32 pb-12 overflow-hidden">
        <div class="absolute inset-0">
            <div class="absolute top-1/3 left-1/4 w-96 h-96 bg-primary/10 rounded-full blur-3xl"></div>
        </div>
        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8" data-animate="slide-up">
            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-sm text-slate-400 mb-8">
                <a href="<?php echo url('/'); ?>" class="hover:text-primary transition-colors"><?php echo get_text('Home', 'መነሻ'); ?></a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="<?php echo url('/insights'); ?>" class="hover:text-primary transition-colors"><?php echo get_text('Insights', 'ግንዛቤዎች'); ?></a>
                <?php if ($post['cat_name']): ?>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="<?php echo url('/insights?category=' . $post['cat_slug']); ?>" class="hover:text-primary transition-colors"><?php echo e(get_text($post['cat_name'], $post['cat_name_am'])); ?></a>
                <?php endif; ?>
            </nav>
            
            <?php if ($post['cat_name']): ?>
            <a href="<?php echo url('/insights?category=' . $post['cat_slug']); ?>" class="inline-block px-3 py-1 bg-primary/10 text-primary text-sm font-medium rounded-full mb-4 hover:bg-primary/20 transition-colors">
                <?php echo e(get_text($post['cat_name'], $post['cat_name_am'])); ?>
            </a>
            <?php endif; ?>
            
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-800 dark:text-white mb-6 leading-tight">
                <?php echo e(get_text($post['title_en'], $post['title_am'])); ?>
            </h1>
            
            <p class="text-xl text-slate-500 dark:text-gray-400 mb-8">
                <?php echo e(get_text($post['excerpt_en'], $post['excerpt_am'])); ?>
            </p>
            
            <!-- Meta -->
            <div class="flex flex-wrap items-center gap-6 py-6 border-t border-b border-black/5 dark:border-white/5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                        <?php if ($post['author_avatar']): ?>
                        <img src="<?php echo upload_url($post['author_avatar']); ?>" class="w-full h-full rounded-full object-cover">
                        <?php else: ?>
                        <span class="text-primary font-bold"><?php echo strtoupper(substr($post['author_name'], 0, 1)); ?></span>
                        <?php endif; ?>
                    </div>
                    <div>
                        <div class="font-medium text-slate-800 dark:text-white text-sm"><?php echo e($post['author_name']); ?></div>
                        <div class="text-xs text-slate-400"><?php echo format_date($post['publish_date'], 'M d, Y'); ?></div>
                    </div>
                </div>
                <div class="flex items-center gap-4 text-sm text-slate-400">
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <?php echo $post['read_time']; ?> min read
                    </span>
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <?php echo number_format($post['views']); ?> views
                    </span>
                </div>
                
                <!-- Share -->
                <div class="flex items-center gap-2 ml-auto">
                    <span class="text-xs text-slate-400"><?php echo get_text('Share:', 'አጋራ:'); ?></span>
                    <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode(get_text($post['title_en'], $post['title_am'])); ?>&url=<?php echo urlencode(SITE_URL . '/insights/' . $post['slug']); ?>" target="_blank" class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 dark:bg-white/5 text-slate-400 hover:bg-primary hover:text-white transition-all">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(SITE_URL . '/insights/' . $post['slug']); ?>" target="_blank" class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 dark:bg-white/5 text-slate-400 hover:bg-primary hover:text-white transition-all">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode(SITE_URL . '/insights/' . $post['slug']); ?>&title=<?php echo urlencode(get_text($post['title_en'], $post['title_am'])); ?>" target="_blank" class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 dark:bg-white/5 text-slate-400 hover:bg-primary hover:text-white transition-all">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                    </a>
                    <button onclick="navigator.clipboard.writeText(window.location.href);this.innerHTML='<svg class=\'w-4 h-4\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M5 13l4 4L19 7\'/></svg>'" class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 dark:bg-white/5 text-slate-400 hover:bg-primary hover:text-white transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Featured Image -->
    <?php if ($post['featured_image']): ?>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="aspect-video rounded-2xl overflow-hidden border border-black/5 dark:border-white/5" data-animate="slide-up">
            <img src="<?php echo upload_url($post['featured_image']); ?>" alt="<?php echo e($post['title_en']); ?>" class="w-full h-full object-cover">
        </div>
    </div>
    <?php endif; ?>

    <!-- Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="prose prose-lg prose-slate dark:prose-invert max-w-none 
            prose-headings:font-extrabold prose-headings:text-slate-800 dark:prose-headings:text-white
            prose-a:text-primary prose-a:no-underline hover:prose-a:underline
            prose-img:rounded-xl prose-img:border prose-img:border-black/5
            prose-code:bg-slate-100 dark:prose-code:bg-white/5 prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded
            prose-blockquote:border-primary" data-animate="slide-up">
            <?php echo get_text($post['content_en'], $post['content_am']); ?>
        </div>
        
        <!-- Tags -->
        <?php if (!empty($tags)): ?>
        <div class="mt-12 pt-8 border-t border-black/5 dark:border-white/5" data-animate="slide-up">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-sm text-slate-400"><?php echo get_text('Tags:', 'መለያዎች:'); ?></span>
                <?php foreach ($tags as $tag): ?>
                <span class="px-3 py-1 bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-gray-300 text-sm rounded-full"><?php echo e($tag); ?></span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</article>

<!-- Related Posts -->
<?php if (!empty($related)): ?>
<section class="py-16 bg-slate-50/50 dark:bg-white/[0.02]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white mb-8" data-animate="slide-up"><?php echo get_text('Related Articles', 'ተዛማጅ ጽሑፎች'); ?></h2>
        <div class="grid sm:grid-cols-3 gap-8">
            <?php foreach ($related as $rel): ?>
            <a href="<?php echo url('/insights/' . $rel['slug']); ?>" class="group block bg-white dark:bg-white/[0.03] rounded-2xl border border-black/5 dark:border-white/5 overflow-hidden hover:shadow-xl transition-all duration-500" data-animate="slide-up">
                <div class="aspect-video bg-primary/10 overflow-hidden">
                    <?php if ($rel['featured_image']): ?>
                    <img src="<?php echo upload_url($rel['featured_image']); ?>" alt="<?php echo e($rel['title_en']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <?php endif; ?>
                </div>
                <div class="p-5">
                    <h3 class="font-bold text-slate-800 dark:text-white group-hover:text-primary transition-colors line-clamp-2"><?php echo e(get_text($rel['title_en'], $rel['title_am'])); ?></h3>
                    <div class="text-xs text-slate-400 mt-2"><?php echo format_date($rel['publish_date']); ?> · <?php echo $rel['read_time']; ?> min</div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>
