<?php
// Developed by Yisak A. Alemayehu (yisak.dev)
/**
 * Authopic Technologies PLC - Search Results Page
 */
if (!defined('BASE_PATH')) exit;

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$page_title = get_text('Search Results', 'የፍለጋ ውጤቶች');
$results = [];
$total = 0;

if (!empty($query) && strlen($query) >= 2) {
    $safe_q = db_escape($query);
    $like = '%' . $safe_q . '%';

    // Search products
    $products = db_fetch_all("SELECT name AS title, slug, short_description AS excerpt, 'product' AS type FROM products WHERE status='active' AND (name LIKE '$like' OR short_description LIKE '$like' OR description LIKE '$like')");
    foreach ($products as $p) {
        $p['url'] = url('/products/' . $p['slug']);
        $p['icon'] = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>';
        $results[] = $p;
    }

    // Search services
    $services = db_fetch_all("SELECT title, slug, short_description AS excerpt, 'service' AS type FROM services WHERE status='active' AND (title LIKE '$like' OR short_description LIKE '$like' OR description LIKE '$like')");
    foreach ($services as $s) {
        $s['url'] = url('/services/' . $s['slug']);
        $s['icon'] = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>';
        $results[] = $s;
    }

    // Search portfolio
    $portfolio = db_fetch_all("SELECT title, slug, short_description AS excerpt, 'portfolio' AS type FROM portfolio WHERE status='published' AND (title LIKE '$like' OR short_description LIKE '$like' OR description LIKE '$like')");
    foreach ($portfolio as $pf) {
        $pf['url'] = url('/portfolio/' . $pf['slug']);
        $pf['icon'] = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>';
        $results[] = $pf;
    }

    // Search blog posts
    $posts = db_fetch_all("SELECT title, slug, excerpt, 'blog' AS type FROM blog_posts WHERE status='published' AND (title LIKE '$like' OR excerpt LIKE '$like' OR content LIKE '$like')");
    foreach ($posts as $bp) {
        $bp['url'] = url('/insights/' . $bp['slug']);
        $bp['icon'] = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>';
        $results[] = $bp;
    }

    // Search pages
    $pages = db_fetch_all("SELECT title, slug, SUBSTRING(content, 1, 200) AS excerpt, 'page' AS type FROM pages WHERE status='published' AND (title LIKE '$like' OR content LIKE '$like')");
    foreach ($pages as $pg) {
        $pg['url'] = url('/' . $pg['slug']);
        $pg['icon'] = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>';
        $results[] = $pg;
    }

    $total = count($results);
}

require_once BASE_PATH . '/includes/header.php';
?>

<!-- Hero -->
<section class="relative py-16 bg-slate-50 dark:bg-slate-900">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-800 dark:text-white mb-6">
            <?php echo get_text('Search', 'ፍለጋ'); ?>
        </h1>
        <form action="<?php echo url('/search'); ?>" method="GET" class="relative max-w-xl mx-auto">
            <input type="text" name="q" value="<?php echo e($query); ?>" placeholder="<?php echo get_text('Search products, services, projects, articles...', 'ምርቶችን፣ አገልግሎቶችን፣ ፕሮጀክቶችን፣ ጽሑፎችን ይፈልጉ...'); ?>"
                   class="w-full px-6 py-4 pr-14 rounded-2xl bg-white/80 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 text-slate-800 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-primary/30 focus:border-primary/50 outline-none transition-all text-lg">
            <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 p-2 rounded-xl bg-primary text-white hover:bg-blue-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </button>
        </form>
    </div>
</section>

<!-- Results -->
<section class="py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <?php if (!empty($query)): ?>
            <p class="text-slate-500 dark:text-gray-400 mb-8">
                <?php echo get_text(
                    $total . ' result' . ($total !== 1 ? 's' : '') . ' found for "<strong>' . e($query) . '</strong>"',
                    '"<strong>' . e($query) . '</strong>" ለ ' . $total . ' ውጤቶች ተገኝተዋል'
                ); ?>
            </p>

            <?php if ($total > 0): ?>
                <div class="space-y-4">
                    <?php foreach ($results as $r): ?>
                        <a href="<?php echo $r['url']; ?>" class="block group p-6 bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-2xl hover:border-primary/30 hover:shadow-lg hover:shadow-primary/5 transition-all duration-300">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><?php echo $r['icon']; ?></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-xs font-semibold uppercase tracking-wider text-primary/70"><?php echo ucfirst($r['type']); ?></span>
                                    </div>
                                    <h3 class="text-lg font-bold text-slate-800 dark:text-white group-hover:text-primary transition-colors">
                                        <?php echo e($r['title']); ?>
                                    </h3>
                                    <?php if (!empty($r['excerpt'])): ?>
                                        <p class="text-slate-500 dark:text-gray-400 mt-1 line-clamp-2"><?php echo truncate(strip_tags($r['excerpt']), 200); ?></p>
                                    <?php endif; ?>
                                </div>
                                <svg class="w-5 h-5 text-slate-300 dark:text-gray-600 group-hover:text-primary transition-colors flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-16">
                    <svg class="w-16 h-16 mx-auto text-slate-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2"><?php echo get_text('No results found', 'ምንም ውጤት አልተገኘም'); ?></h3>
                    <p class="text-slate-500 dark:text-gray-400 mb-6"><?php echo get_text('Try different keywords or browse our pages.', 'የተለያዩ ቃላትን ይሞክሩ ወይም ገጾቻችንን ያስሱ።'); ?></p>
                    <div class="flex flex-wrap justify-center gap-3">
                        <a href="<?php echo url('/'); ?>#products" class="px-4 py-2 bg-slate-100 dark:bg-white/5 rounded-lg text-sm font-medium text-slate-600 dark:text-gray-300 hover:text-primary transition-colors"><?php echo get_text('Products', 'ምርቶች'); ?></a>
                        <a href="<?php echo url('/'); ?>#services" class="px-4 py-2 bg-slate-100 dark:bg-white/5 rounded-lg text-sm font-medium text-slate-600 dark:text-gray-300 hover:text-primary transition-colors"><?php echo get_text('Services', 'አገልግሎቶች'); ?></a>
                        <a href="<?php echo url('/portfolio'); ?>" class="px-4 py-2 bg-slate-100 dark:bg-white/5 rounded-lg text-sm font-medium text-slate-600 dark:text-gray-300 hover:text-primary transition-colors"><?php echo get_text('Portfolio', 'ፖርትፎሊዮ'); ?></a>
                        <a href="<?php echo url('/insights'); ?>" class="px-4 py-2 bg-slate-100 dark:bg-white/5 rounded-lg text-sm font-medium text-slate-600 dark:text-gray-300 hover:text-primary transition-colors"><?php echo get_text('Blog', 'ብሎግ'); ?></a>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="text-center py-16">
                <svg class="w-16 h-16 mx-auto text-slate-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2"><?php echo get_text('Start searching', 'መፈለግ ይጀምሩ'); ?></h3>
                <p class="text-slate-500 dark:text-gray-400"><?php echo get_text('Type something in the search box above to find what you\'re looking for.', 'የሚፈልጉትን ነገር ለማግኘት ከላይ ባለው የፍለጋ ሳጥን ውስጥ ይተይቡ።'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>
