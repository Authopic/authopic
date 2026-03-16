<?php
// Developed by Yisak A. Alemayehu (yisak.dev)
/**
 * Authopic Technologies PLC - Portfolio Page (/portfolio)
 */
if (!defined('BASE_PATH')) exit;

$page_title = get_text('Our Portfolio', 'ስራዎቻችን');
$page_description = get_text('Explore our latest projects and success stories.', 'የቅርብ ጊዜ ፕሮጄክቶቻችንን እና የስኬት ታሪኮችን ያስሱ።');

// Filters
$filter_type = get('type', '');
$filter_service = get('service', '');
$current_page = max(1, (int) get('page', 1));
$per_page = 9;

// Build query
$where = "p.`status` = 'published'";
if ($filter_type) {
    $where .= " AND p.`type` = '" . db_escape($filter_type) . "'";
}

$total = db_count("SELECT COUNT(*) FROM `portfolio` p WHERE $where");
$pagination = paginate($total, $per_page, $current_page);
$offset = ($current_page - 1) * $per_page;

$items = db_fetch_all("SELECT * FROM `portfolio` p WHERE $where ORDER BY p.`is_featured` DESC, p.`completion_date` DESC LIMIT $per_page OFFSET $offset");

require_once BASE_PATH . '/includes/header.php';
?>

<!-- Hero -->
<section class="relative pt-32 pb-16 overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute top-1/3 left-1/4 w-96 h-96 bg-primary/10 rounded-full blur-3xl"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center" data-animate="slide-up">
        <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-800 dark:text-white mb-4">
            <?php echo get_text('Our Portfolio', 'ስራዎቻችን'); ?>
        </h1>
        <p class="text-xl text-slate-500 dark:text-gray-400 max-w-2xl mx-auto">
            <?php echo get_text('Real results for real businesses across Ethiopia.', 'በኢትዮጵያ ውስጥ ለእውነተኛ ንግዶች እውነተኛ ውጤቶች።'); ?>
        </p>
    </div>
</section>

<!-- Filters -->
<section class="pb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap justify-center gap-2" data-animate="slide-up">
            <a href="<?php echo url('/portfolio'); ?>" class="px-5 py-2.5 rounded-xl text-sm font-medium border transition-all duration-300 <?php echo !$filter_type && !$filter_service ? 'bg-primary text-white border-primary shadow-lg shadow-primary/25' : 'bg-white dark:bg-white/5 text-slate-600 dark:text-gray-300 border-black/5 dark:border-white/10 hover:border-primary/30'; ?>">
                <?php echo get_text('All', 'ሁሉም'); ?>
            </a>
            <a href="<?php echo url('/portfolio?type=sms'); ?>" class="px-5 py-2.5 rounded-xl text-sm font-medium border transition-all duration-300 <?php echo $filter_type === 'sms' ? 'bg-primary text-white border-primary shadow-lg shadow-primary/25' : 'bg-white dark:bg-white/5 text-slate-600 dark:text-gray-300 border-black/5 dark:border-white/10 hover:border-primary/30'; ?>">
                SMS
            </a>
            <a href="<?php echo url('/portfolio?type=erp'); ?>" class="px-5 py-2.5 rounded-xl text-sm font-medium border transition-all duration-300 <?php echo $filter_type === 'erp' ? 'bg-primary text-white border-primary shadow-lg shadow-primary/25' : 'bg-white dark:bg-white/5 text-slate-600 dark:text-gray-300 border-black/5 dark:border-white/10 hover:border-primary/30'; ?>">
                ERP
            </a>
            <?php foreach ([] as $svc): ?>
            <a href="<?php echo url('/portfolio?service=' . $svc['id']); ?>" class="px-5 py-2.5 rounded-xl text-sm font-medium border transition-all duration-300 <?php echo $filter_service == $svc['id'] ? 'bg-primary text-white border-primary shadow-lg shadow-primary/25' : 'bg-white dark:bg-white/5 text-slate-600 dark:text-gray-300 border-black/5 dark:border-white/10 hover:border-primary/30'; ?>">
                <?php echo e(get_text($svc['name_en'], $svc['name_am'])); ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Portfolio Grid -->
<section class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if (empty($items)): ?>
        <div class="text-center py-20">
            <p class="text-xl text-slate-400"><?php echo get_text('No projects found for this filter.', 'ለዚህ ማጣሪያ ፕሮጀክቶች አልተገኙም።'); ?></p>
        </div>
        <?php else: ?>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($items as $item): ?>
            <a href="<?php echo url('/portfolio/' . $item['slug']); ?>" class="group block bg-white dark:bg-white/[0.03] rounded-2xl border border-black/5 dark:border-white/5 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-500" data-animate="slide-up">
                <div class="relative aspect-video bg-primary/10 overflow-hidden">
                    <?php if (!empty($item['featured_image'])): ?>
                    <img src="<?php echo upload_url($item['featured_image']); ?>" alt="<?php echo e($item['title_en']); ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    <?php endif; ?>
                    <?php if ($item['is_featured']): ?>
                    <div class="absolute top-3 left-3">
                        <span class="px-3 py-1 bg-amber-500 text-white text-xs font-bold rounded-full">Featured</span>
                    </div>
                    <?php endif; ?>
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
                        <span class="text-white font-medium text-sm"><?php echo get_text('View Project', 'ፕሮጀክቱን ይመልከቱ'); ?> →</span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="px-2.5 py-1 bg-primary/10 text-primary text-xs font-medium rounded-full"><?php echo e(strtoupper($item['type'])); ?></span>
                        <?php if (!empty($item['industry'])): ?>
                        <span class="px-2.5 py-1 bg-secondary/10 text-secondary text-xs font-medium rounded-full"><?php echo e($item['industry']); ?></span>
                        <?php endif; ?>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white group-hover:text-primary transition-colors mb-2">
                        <?php echo e(get_text($item['title_en'], $item['title_am'])); ?>
                    </h3>
                    <p class="text-sm text-slate-500 dark:text-gray-400 mb-3">
                        <?php echo truncate(get_text($item['challenge_en'] ?? '', $item['challenge_am'] ?? ''), 100); ?>
                    </p>
                    <div class="flex items-center justify-between text-xs text-slate-400">
                        <span><?php echo e($item['client_name']); ?></span>
                        <span><?php echo format_date($item['completion_date'], 'M Y'); ?></span>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        
        <?php echo render_pagination($pagination, current_path()); ?>
        <?php endif; ?>
    </div>
</section>

<!-- CTA -->
<section class="py-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center" data-animate="slide-up">
        <h2 class="text-3xl font-extrabold text-slate-800 dark:text-white mb-4">
            <?php echo get_text('Want to Be Our Next Success Story?', 'ቀጣዩ የስኬት ታሪካችን መሆን ይፈልጋሉ?'); ?>
        </h2>
        <p class="text-lg text-slate-500 dark:text-gray-400 mb-8">
            <?php echo get_text('Let\'s discuss your project and create something amazing together.', 'ስለ ፕሮጀክትዎ እንወያይ እና አስደናቂ ነገር በጋራ እንፍጠር።'); ?>
        </p>
        <a href="<?php echo url('/contact'); ?>" class="inline-flex items-center gap-2 px-8 py-4 bg-primary hover:bg-blue-700 text-white font-semibold rounded-xl shadow-lg shadow-primary/25 hover:shadow-primary/40 transition-all duration-300">
            <?php echo get_text('Start Your Project', 'ፕሮጀክትዎን ይጀምሩ'); ?>
        </a>
    </div>
</section>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>
