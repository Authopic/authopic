<?php
/**
 * Authopic Technologies PLC - Service Single Page (/services/{slug})
 */
if (!defined('BASE_PATH')) exit;

$slug = db_escape($route_params['slug'] ?? '');
$service = db_fetch_one("SELECT * FROM `services` WHERE `slug` = '$slug' AND `status` = 'published'");

if (!$service) {
    http_response_code(404);
    require_once BASE_PATH . '/pages/404.php';
    return;
}

db_query("UPDATE `services` SET `views` = `views` + 1 WHERE `id` = {$service['id']}");

$page_title = get_text($service['name_en'], $service['name_am']);
$page_description = get_text($service['tagline_en'], $service['tagline_am']);
$offerings = get_json($service['offerings']);
$technologies = get_json($service['technologies']);
$process = get_json($service['process_steps'] ?? null);

// Related portfolio items
$service_id = (int) $service['id'];
$related_portfolio = db_fetch_all("SELECT * FROM `portfolio` WHERE `service_id` = $service_id AND `status` = 'published' ORDER BY `completion_date` DESC LIMIT 4");

require_once BASE_PATH . '/includes/header.php';
?>

<!-- Hero -->
<section class="relative pt-32 pb-20 overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute top-1/3 left-1/4 w-96 h-96 bg-secondary/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-1/4 right-1/3 w-80 h-80 bg-primary/10 rounded-full blur-3xl"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl" data-animate="slide-up">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-secondary/10 border border-secondary/20 rounded-full text-sm text-secondary font-medium mb-6">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo e($service['icon'] ?? 'M13 10V3L4 14h7v7l9-11h-7z'); ?>"/></svg>
                <?php echo get_text('Service', 'አገልግሎት'); ?>
            </span>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-800 dark:text-white mb-6 leading-tight">
                <?php echo e(get_text($service['name_en'], $service['name_am'])); ?>
            </h1>
            <p class="text-xl text-slate-500 dark:text-gray-400 mb-8 leading-relaxed">
                <?php echo e(get_text($service['tagline_en'], $service['tagline_am'])); ?>
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="<?php echo url('/contact'); ?>" class="inline-flex items-center gap-2 px-8 py-4 bg-secondary text-white font-semibold rounded-xl shadow-lg shadow-secondary/25 hover:shadow-secondary/40 transition-all duration-300">
                    <?php echo get_text('Get a Quote', 'ዋጋ ይጠይቁ'); ?>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
                <a href="#portfolio" class="inline-flex items-center gap-2 px-8 py-4 bg-slate-100 dark:bg-white/5 border border-black/5 dark:border-white/10 text-slate-700 dark:text-gray-200 font-semibold rounded-xl hover:border-secondary/30 transition-all duration-300">
                    <?php echo get_text('View Our Work', 'ስራችንን ይመልከቱ'); ?>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Description -->
<section class="py-20 bg-slate-50/50 dark:bg-white/[0.02]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div data-animate="slide-up">
                <h2 class="text-3xl font-extrabold text-slate-800 dark:text-white mb-6">
                    <?php echo get_text('What We Offer', 'ምን እናቀርባለን'); ?>
                </h2>
                <div class="prose prose-slate dark:prose-invert max-w-none">
                    <?php echo nl2br(e(get_text($service['description_en'], $service['description_am']))); ?>
                </div>
                <?php if (!empty($service['starting_price'] ?? '') || !empty($service['timeline'] ?? '')): ?>
                <div class="flex items-center gap-6 mt-8 py-4 border-t border-black/5 dark:border-white/5">
                    <?php if (!empty($service['starting_price'] ?? '')): ?>
                    <div>
                        <div class="text-sm text-slate-400"><?php echo get_text('Starting Price', 'ጀምሮ ዋጋ'); ?></div>
                        <div class="text-xl font-bold text-primary"><?php echo e($service['starting_price']); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($service['starting_price'] ?? '') && !empty($service['timeline'] ?? '')): ?>
                    <div class="w-px h-10 bg-black/5 dark:bg-white/5"></div>
                    <?php endif; ?>
                    <?php if (!empty($service['timeline'] ?? '')): ?>
                    <div>
                        <div class="text-sm text-slate-400"><?php echo get_text('Timeline', 'የጊዜ ገደብ'); ?></div>
                        <div class="text-xl font-bold text-slate-800 dark:text-white"><?php echo e($service['timeline']); ?></div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="aspect-video bg-secondary/10 rounded-2xl border border-black/5 dark:border-white/5 flex items-center justify-center" data-animate="slide-up">
                <?php if (!empty($service['featured_image'])): ?>
                <img src="<?php echo upload_url($service['featured_image']); ?>" alt="<?php echo e($service['name_en']); ?>" class="w-full h-full object-cover rounded-2xl">
                <?php else: ?>
                <span class="text-slate-400"><?php echo e($service['name_en']); ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Service Offerings -->
<?php if (!empty($offerings)): ?>
<section class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16" data-animate="slide-up">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-800 dark:text-white mb-4">
                <?php echo get_text('Our Offerings', 'አቅርቦቶቻችን'); ?>
            </h2>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($offerings as $offering): ?>
            <div class="group bg-white dark:bg-white/[0.03] rounded-2xl border border-black/5 dark:border-white/5 p-8 hover:border-secondary/30 hover:shadow-xl hover:shadow-secondary/5 transition-all duration-500 hover:-translate-y-1" data-animate="slide-up">
                <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-3"><?php echo e($offering['name']); ?></h3>
                <p class="text-slate-500 dark:text-gray-400 mb-4">
                    <?php echo e($offering['description']); ?>
                </p>
                <?php if (!empty($offering['price'] ?? '')): ?>
                <div class="text-secondary font-bold"><?php echo e($offering['price']); ?></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Technologies -->
<?php if (!empty($technologies)): ?>
<section class="py-20 bg-slate-50/50 dark:bg-white/[0.02]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16" data-animate="slide-up">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-800 dark:text-white mb-4">
                <?php echo get_text('Technologies We Use', 'የምንጠቀማቸው ቴክኖሎጂዎች'); ?>
            </h2>
        </div>
        <div class="flex flex-wrap justify-center gap-4" data-animate="slide-up">
            <?php foreach ($technologies as $tech): ?>
            <span class="px-6 py-3 bg-white dark:bg-white/[0.05] rounded-xl border border-black/5 dark:border-white/5 text-sm font-medium text-slate-700 dark:text-gray-300 hover:border-secondary/30 hover:shadow-md transition-all duration-300"><?php echo e($tech); ?></span>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Development Process -->
<?php if (!empty($process)): ?>
<section class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16" data-animate="slide-up">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-800 dark:text-white mb-4">
                <?php echo get_text('Our Process', 'የአሠራር ሂደታችን'); ?>
            </h2>
        </div>
        <div class="relative">
            <div class="hidden md:block absolute top-24 left-0 right-0 h-0.5 bg-secondary/20"></div>
            <div class="grid md:grid-cols-<?php echo min(count($process), 6); ?> gap-8">
                <?php foreach ($process as $step): ?>
                <div class="relative text-center" data-animate="slide-up">
                    <div class="w-12 h-12 mx-auto rounded-full bg-secondary text-white font-bold flex items-center justify-center text-lg mb-4 relative z-10 shadow-lg shadow-secondary/25">
                        <?php echo e($step['step']); ?>
                    </div>
                    <h3 class="font-bold text-slate-800 dark:text-white mb-2"><?php echo e($step['title']); ?></h3>
                    <p class="text-sm text-slate-500 dark:text-gray-400"><?php echo e($step['description']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Related Portfolio -->
<?php if (!empty($related_portfolio)): ?>
<section class="py-20 bg-slate-50/50 dark:bg-white/[0.02]" id="portfolio">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16" data-animate="slide-up">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-800 dark:text-white mb-4">
                <?php echo get_text('Our Work', 'ስራዎቻችን'); ?>
            </h2>
        </div>
        <div class="grid sm:grid-cols-2 gap-8">
            <?php foreach ($related_portfolio as $item): ?>
            <a href="<?php echo url('/portfolio/' . $item['slug']); ?>" class="group block bg-white dark:bg-white/[0.03] rounded-2xl border border-black/5 dark:border-white/5 overflow-hidden hover:shadow-xl transition-all duration-500" data-animate="slide-up">
                <div class="aspect-video bg-secondary/10">
                    <?php if ($item['thumbnail']): ?>
                    <img src="<?php echo upload_url($item['thumbnail']); ?>" alt="<?php echo e($item['title_en']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <?php endif; ?>
                </div>
                <div class="p-6">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white group-hover:text-secondary transition-colors"><?php echo e(get_text($item['title_en'], $item['title_am'])); ?></h3>
                    <p class="text-sm text-slate-500 dark:text-gray-400 mt-1"><?php echo e($item['client_name']); ?></p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA -->
<section class="py-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative rounded-3xl overflow-hidden" data-animate="slide-up">
            <div class="absolute inset-0 bg-secondary"></div>
            <div class="absolute inset-0 bg-[url('data:image/svg+xml,...')] opacity-10"></div>
            <div class="relative p-12 md:p-16 text-center">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-4">
                    <?php echo get_text('Ready to Start Your Project?', 'ፕሮጀክትዎን ለመጀመር ዝግጁ ነዎት?'); ?>
                </h2>
                <p class="text-white/80 text-lg mb-8 max-w-2xl mx-auto">
                    <?php echo get_text('Let\'s discuss how we can bring your vision to life with our expert development team.', 'ባለሙያ የልማት ቡድናችን ራዕይዎን ወደ እውነት እንዴት ማምጣት እንደምንችል እንወያይ።'); ?>
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="<?php echo url('/contact'); ?>" class="inline-flex items-center gap-2 px-8 py-4 bg-white text-secondary font-semibold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300">
                        <?php echo get_text('Contact Us', 'ያግኙን'); ?>
                    </a>
                    <a href="tel:+251911234567" class="inline-flex items-center gap-2 px-8 py-4 bg-white/10 border border-white/20 text-white font-semibold rounded-xl hover:bg-white/20 transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <?php echo get_text('Call Now', 'አሁን ይደውሉ'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>
