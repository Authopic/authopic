<?php
/**
 * Authopic Technologies PLC - Insights / Blog Listing Page (/insights)
 */
if (!defined('BASE_PATH')) exit;

$page_title = get_text('Insights & Blog', 'ግንዛቤዎች እና ብሎግ');
$page_description = get_text('Tech insights, tips, and updates from the Authopic Technologies PLC team.', 'ከአናኖምስ ዴቭ ቡድን የቴክ ግንዛቤዎች፣ ምክሮች እና ዝመናዎች።');

// Filters
$filter_category = get('category', '');
$search_query = get('q', '');
$current_page = max(1, (int) get('page', 1));
$per_page = 9;

$where = "p.`status` = 'published' AND p.`publish_date` <= NOW()";
if ($filter_category) {
    $where .= " AND c.`slug` = '" . db_escape($filter_category) . "'";
}
if ($search_query) {
    $sq = db_escape($search_query);
    $where .= " AND (p.`title_en` LIKE '%$sq%' OR p.`title_am` LIKE '%$sq%' OR p.`content_en` LIKE '%$sq%')";
}

$total = db_count("SELECT COUNT(*) FROM `blog_posts` p LEFT JOIN `blog_categories` c ON p.category_id = c.id WHERE $where");
$pagination = paginate($total, $per_page, $current_page);
$offset = ($current_page - 1) * $per_page;

$posts = db_fetch_all("SELECT p.*, c.name_en as cat_name, c.name_am as cat_name_am, c.slug as cat_slug, a.full_name as author_name 
    FROM `blog_posts` p 
    LEFT JOIN `blog_categories` c ON p.category_id = c.id 
    LEFT JOIN `admin_users` a ON p.author_id = a.id 
    WHERE $where 
    ORDER BY p.`is_featured` DESC, p.`publish_date` DESC 
    LIMIT $per_page OFFSET $offset");

$categories = db_fetch_all("SELECT c.*, COUNT(p.id) as post_count FROM `blog_categories` c LEFT JOIN `blog_posts` p ON c.id = p.category_id AND p.status = 'published' GROUP BY c.id HAVING post_count > 0 ORDER BY c.name_en");

// Featured post (first featured or latest)
$featured = null;
if ($current_page === 1 && !$filter_category && !$search_query && !empty($posts)) {
    $featured = $posts[0];
    array_shift($posts);
}

require_once BASE_PATH . '/includes/header.php';
?>

<!-- Hero -->
<section class="relative pt-32 pb-12 overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute top-1/3 right-1/4 w-96 h-96 bg-primary/10 rounded-full blur-3xl"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" data-animate="slide-up">
        <div class="max-w-3xl">
            <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-800 dark:text-white mb-4">
                <?php echo get_text('Insights & Blog', 'ግንዛቤዎች እና ብሎግ'); ?>
            </h1>
            <p class="text-xl text-slate-500 dark:text-gray-400">
                <?php echo get_text('Tech insights, tutorials, and updates from our team.', 'ከቡድናችን የቴክ ግንዛቤዎች፣ ትምህርቶች እና ዝመናዎች።'); ?>
            </p>
        </div>
        
        <!-- Search -->
        <form action="<?php echo url('/insights'); ?>" method="GET" class="mt-8 max-w-lg">
            <div class="relative">
                <input type="text" name="q" value="<?php echo e($search_query); ?>" placeholder="<?php echo get_text('Search articles...', 'ጽሑፎችን ይፈልጉ...'); ?>" class="w-full px-5 py-3 pl-12 bg-white dark:bg-white/5 border border-black/5 dark:border-white/10 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </form>
    </div>
</section>

<!-- Category Filters -->
<section class="pb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap gap-2" data-animate="slide-up">
            <a href="<?php echo url('/insights'); ?>" class="px-4 py-2 rounded-lg text-sm font-medium border transition-all duration-300 <?php echo !$filter_category ? 'bg-primary text-white border-primary' : 'bg-white dark:bg-white/5 text-slate-600 dark:text-gray-300 border-black/5 dark:border-white/10 hover:border-primary/30'; ?>">
                <?php echo get_text('All', 'ሁሉም'); ?> (<?php echo $total; ?>)
            </a>
            <?php foreach ($categories as $cat): ?>
            <a href="<?php echo url('/insights?category=' . $cat['slug']); ?>" class="px-4 py-2 rounded-lg text-sm font-medium border transition-all duration-300 <?php echo $filter_category === $cat['slug'] ? 'bg-primary text-white border-primary' : 'bg-white dark:bg-white/5 text-slate-600 dark:text-gray-300 border-black/5 dark:border-white/10 hover:border-primary/30'; ?>">
                <?php echo e(get_text($cat['name_en'], $cat['name_am'])); ?> (<?php echo $cat['post_count']; ?>)
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Post -->
<?php if ($featured): ?>
<section class="pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="<?php echo url('/insights/' . $featured['slug']); ?>" class="group block bg-white dark:bg-white/[0.03] rounded-2xl border border-black/5 dark:border-white/5 overflow-hidden hover:shadow-xl transition-all duration-500" data-animate="slide-up">
            <div class="grid md:grid-cols-2">
                <div class="aspect-video md:aspect-auto bg-primary/10 overflow-hidden">
                    <?php if ($featured['featured_image']): ?>
                    <img src="<?php echo upload_url($featured['featured_image']); ?>" alt="<?php echo e($featured['title_en']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                    <?php endif; ?>
                </div>
                <div class="p-8 md:p-10 flex flex-col justify-center">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="px-3 py-1 bg-amber-500/10 text-amber-600 text-xs font-bold rounded-full">Featured</span>
                        <?php if ($featured['cat_name']): ?>
                        <span class="px-3 py-1 bg-primary/10 text-primary text-xs font-medium rounded-full"><?php echo e(get_text($featured['cat_name'], $featured['cat_name_am'])); ?></span>
                        <?php endif; ?>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-800 dark:text-white mb-3 group-hover:text-primary transition-colors">
                        <?php echo e(get_text($featured['title_en'], $featured['title_am'])); ?>
                    </h2>
                    <p class="text-slate-500 dark:text-gray-400 mb-4">
                        <?php echo truncate(get_text($featured['excerpt_en'], $featured['excerpt_am']), 200); ?>
                    </p>
                    <div class="flex items-center gap-4 text-sm text-slate-400">
                        <span><?php echo e($featured['author_name']); ?></span>
                        <span>·</span>
                        <span><?php echo format_date($featured['publish_date']); ?></span>
                        <span>·</span>
                        <span><?php echo $featured['read_time']; ?> min read</span>
                    </div>
                </div>
            </div>
        </a>
    </div>
</section>
<?php endif; ?>

<!-- Post Grid -->
<section class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if (empty($posts) && !$featured): ?>
        <div class="text-center py-20">
            <svg class="w-16 h-16 mx-auto text-slate-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
            <p class="text-xl text-slate-400"><?php echo get_text('No articles found.', 'ጽሑፎች አልተገኙም።'); ?></p>
        </div>
        <?php else: ?>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($posts as $post): ?>
            <a href="<?php echo url('/insights/' . $post['slug']); ?>" class="group block bg-white dark:bg-white/[0.03] rounded-2xl border border-black/5 dark:border-white/5 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-500" data-animate="slide-up">
                <div class="aspect-video bg-primary/10 overflow-hidden">
                    <?php if ($post['featured_image']): ?>
                    <img src="<?php echo upload_url($post['featured_image']); ?>" alt="<?php echo e($post['title_en']); ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    <?php endif; ?>
                </div>
                <div class="p-6">
                    <?php if ($post['cat_name']): ?>
                    <span class="inline-block px-2.5 py-1 bg-primary/10 text-primary text-xs font-medium rounded-full mb-3"><?php echo e(get_text($post['cat_name'], $post['cat_name_am'])); ?></span>
                    <?php endif; ?>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white group-hover:text-primary transition-colors mb-2 line-clamp-2">
                        <?php echo e(get_text($post['title_en'], $post['title_am'])); ?>
                    </h3>
                    <p class="text-sm text-slate-500 dark:text-gray-400 mb-4 line-clamp-2">
                        <?php echo truncate(get_text($post['excerpt_en'], $post['excerpt_am']), 120); ?>
                    </p>
                    <div class="flex items-center justify-between text-xs text-slate-400">
                        <span><?php echo format_date($post['publish_date']); ?></span>
                        <span><?php echo $post['read_time']; ?> min read</span>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        
        <?php echo render_pagination($pagination, '/insights' . ($filter_category ? '?category=' . $filter_category : '')); ?>
        <?php endif; ?>
    </div>
</section>

<!-- Newsletter CTA -->
<section class="py-20 bg-slate-50/50 dark:bg-white/[0.02]">
    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8 text-center" data-animate="slide-up">
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white mb-3"><?php echo get_text('Stay Updated', 'ዝመና ያግኙ'); ?></h2>
        <p class="text-slate-500 dark:text-gray-400 mb-6"><?php echo get_text('Get the latest insights delivered to your inbox.', 'ቅርብ ግንዛቤዎች ወደ ኢሜይልዎ ይላኩ።'); ?></p>
        <form class="newsletter-form flex gap-3">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="email" name="email" required placeholder="<?php echo get_text('Your email', 'ኢሜይልዎ'); ?>" class="flex-1 px-4 py-3 bg-white dark:bg-white/5 border border-black/5 dark:border-white/10 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
            <button type="submit" class="px-6 py-3 bg-primary text-white font-medium rounded-xl hover:shadow-lg hover:shadow-primary/25 transition-all"><?php echo get_text('Subscribe', 'ተመዝገቡ'); ?></button>
        </form>
    </div>
</section>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>
