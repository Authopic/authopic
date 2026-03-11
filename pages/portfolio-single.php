<?php
/**
 * Authopic Technologies PLC - Portfolio Single Page (/portfolio/{slug})
 */
if (!defined('BASE_PATH')) exit;

$slug = db_escape($route_params['slug'] ?? '');
$project = db_fetch_one("SELECT * FROM `portfolio` WHERE `slug` = '$slug' AND `status` = 'published'");

if (!$project) {
    http_response_code(404);
    require_once BASE_PATH . '/pages/404.php';
    return;
}

$page_title = get_text($project['title_en'], $project['title_am']);
$page_description = truncate(get_text($project['challenge_en'] ?? '', $project['challenge_am'] ?? ''), 160);
$technologies = get_json($project['technologies']);
$gallery = get_json($project['gallery']);
$metrics = get_json($project['metrics'] ?? null);

// Related projects
$related = db_fetch_all("SELECT * FROM `portfolio` WHERE `id` != {$project['id']} AND `type` = '" . db_escape($project['type']) . "' AND `status` = 'published' ORDER BY `completion_date` DESC LIMIT 3");

require_once BASE_PATH . '/includes/header.php';
?>

<!-- Breadcrumb -->
<div class="pt-28 pb-4">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-sm text-slate-400">
            <a href="<?php echo url('/'); ?>" class="hover:text-primary transition-colors"><?php echo get_text('Home', 'መነሻ'); ?></a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="<?php echo url('/portfolio'); ?>" class="hover:text-primary transition-colors"><?php echo get_text('Portfolio', 'ስራዎች'); ?></a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-slate-600 dark:text-gray-300"><?php echo e(get_text($project['title_en'], $project['title_am'])); ?></span>
        </nav>
    </div>
</div>

<!-- Hero -->
<section class="pb-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 items-center" data-animate="slide-up">
            <div>
                <div class="flex items-center gap-2 mb-6">
                    <span class="px-3 py-1 bg-primary/10 text-primary text-sm font-medium rounded-full"><?php echo e(strtoupper($project['type'])); ?></span>
                    <?php if (!empty($project['industry'])): ?>
                    <span class="px-3 py-1 bg-secondary/10 text-secondary text-sm font-medium rounded-full"><?php echo e($project['industry']); ?></span>
                    <?php endif; ?>
                </div>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-800 dark:text-white mb-6 leading-tight">
                    <?php echo e(get_text($project['title_en'], $project['title_am'])); ?>
                </h1>
                
                <div class="grid grid-cols-2 gap-4 mb-8">
                    <div class="bg-slate-50 dark:bg-white/[0.03] rounded-xl p-4 border border-black/5 dark:border-white/5">
                        <div class="text-xs text-slate-400 mb-1"><?php echo get_text('Client', 'ደንበኛ'); ?></div>
                        <div class="font-semibold text-slate-800 dark:text-white"><?php echo e($project['client_name']); ?></div>
                    </div>
                    <div class="bg-slate-50 dark:bg-white/[0.03] rounded-xl p-4 border border-black/5 dark:border-white/5">
                        <div class="text-xs text-slate-400 mb-1"><?php echo get_text('Completed', 'የተጠናቀቀ'); ?></div>
                        <div class="font-semibold text-slate-800 dark:text-white"><?php echo format_date($project['completion_date'], 'M Y'); ?></div>
                    </div>
                    <?php if (!empty($project['industry'])): ?>
                    <div class="bg-slate-50 dark:bg-white/[0.03] rounded-xl p-4 border border-black/5 dark:border-white/5">
                        <div class="text-xs text-slate-400 mb-1"><?php echo get_text('Industry', 'ኢንዱስትሪ'); ?></div>
                        <div class="font-semibold text-slate-800 dark:text-white"><?php echo e($project['industry']); ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="aspect-video bg-primary/10 rounded-2xl border border-black/5 dark:border-white/5 overflow-hidden">
                <?php if (!empty($project['featured_image'])): ?>
                <img src="<?php echo upload_url($project['featured_image']); ?>" alt="<?php echo e($project['title_en']); ?>" class="w-full h-full object-cover">
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Description -->
<section class="py-16 bg-slate-50/50 dark:bg-white/[0.02]">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div data-animate="slide-up">
            <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white mb-6"><?php echo get_text('Project Overview', 'የፕሮጀክት አጠቃላይ እይታ'); ?></h2>
            <div class="prose prose-lg prose-slate dark:prose-invert max-w-none">
                <?php echo nl2br(e(get_text($project['challenge_en'] ?? '', $project['challenge_am'] ?? ''))); ?>
            </div>
        </div>
        
        <?php if (!empty($project['challenge_en'])): ?>
        <div class="mt-12" data-animate="slide-up">
            <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white mb-6"><?php echo get_text('The Challenge', '\u1348\u1270\u1293\u12cd'); ?></h2>
            <div class="prose prose-lg prose-slate dark:prose-invert max-w-none">
                <?php echo nl2br(e(get_text($project['challenge_en'], $project['challenge_am'] ?? ''))); ?>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($project['solution_en'])): ?>
        <div class="mt-12" data-animate="slide-up">
            <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white mb-6"><?php echo get_text('Our Solution', '\u1218\u134d\u1274\u12eb\u127d\u1295'); ?></h2>
            <div class="prose prose-lg prose-slate dark:prose-invert max-w-none">
                <?php echo nl2br(e(get_text($project['solution_en'], $project['solution_am'] ?? ''))); ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Results -->
<?php if (!empty($metrics)): ?>
<section class="py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white mb-8 text-center" data-animate="slide-up"><?php echo get_text('Results Achieved', '\u12e8\u1270\u1308\u1299 \u12cd\u1324\u1276\u127d'); ?></h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($metrics as $result): ?>
            <div class="text-center bg-white dark:bg-white/[0.03] rounded-2xl border border-black/5 dark:border-white/5 p-6" data-animate="slide-up">
                <div class="text-3xl font-extrabold text-primary mb-2"><?php echo e($result['value'] ?? ''); ?></div>
                <div class="text-sm text-slate-500 dark:text-gray-400"><?php echo e($result['label'] ?? ''); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Technologies -->
<?php if (!empty($technologies)): ?>
<section class="py-16 bg-slate-50/50 dark:bg-white/[0.02]">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white mb-6 text-center" data-animate="slide-up"><?php echo get_text('Technologies Used', 'ጥቅም ላይ የዋሉ ቴክኖሎጂዎች'); ?></h2>
        <div class="flex flex-wrap justify-center gap-3" data-animate="slide-up">
            <?php foreach ($technologies as $tech): ?>
            <span class="px-4 py-2 bg-white dark:bg-white/[0.05] rounded-lg border border-black/5 dark:border-white/5 text-sm font-medium text-slate-600 dark:text-gray-300"><?php echo e($tech); ?></span>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Gallery -->
<?php if (!empty($gallery)): ?>
<section class="py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white mb-8 text-center" data-animate="slide-up"><?php echo get_text('Project Gallery', 'የፕሮጀክት ማዕከለ-ስዕላት'); ?></h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4" data-animate="slide-up">
            <?php foreach ($gallery as $img): ?>
            <div class="aspect-video bg-primary/10 rounded-xl overflow-hidden border border-black/5 dark:border-white/5 cursor-pointer hover:shadow-lg transition-all" onclick="openLightbox('<?php echo upload_url($img); ?>')">
                <img src="<?php echo upload_url($img); ?>" alt="Project screenshot" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Testimonial -->
<?php if (!empty($project['testimonial_quote'])): ?>
<section class="py-16 bg-slate-50/50 dark:bg-white/[0.02]">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center" data-animate="slide-up">
        <svg class="w-12 h-12 mx-auto text-primary/20 mb-6" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10H14.017zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151C7.563 6.068 6 8.789 6 11h4v10H0z"/></svg>
        <blockquote class="text-xl text-slate-700 dark:text-gray-200 italic mb-6">"<?php echo e($project['testimonial_quote']); ?>"</blockquote>
        <?php if (!empty($project['testimonial_name'])): ?>
        <div class="text-slate-500 dark:text-gray-400 font-medium">— <?php echo e($project['testimonial_name']); ?><?php if (!empty($project['testimonial_position'])): ?>, <?php echo e($project['testimonial_position']); ?><?php endif; ?></div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<!-- Related Projects -->
<?php if (!empty($related)): ?>
<section class="py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white mb-8 text-center" data-animate="slide-up"><?php echo get_text('Related Projects', 'ተዛማጅ ፕሮጀክቶች'); ?></h2>
        <div class="grid sm:grid-cols-3 gap-8">
            <?php foreach ($related as $item): ?>
            <a href="<?php echo url('/portfolio/' . $item['slug']); ?>" class="group block bg-white dark:bg-white/[0.03] rounded-2xl border border-black/5 dark:border-white/5 overflow-hidden hover:shadow-xl transition-all duration-500" data-animate="slide-up">
                <div class="aspect-video bg-primary/10 overflow-hidden">
                    <?php if (!empty($item['featured_image'])): ?>
                    <img src="<?php echo upload_url($item['featured_image']); ?>" alt="<?php echo e($item['title_en']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <?php endif; ?>
                </div>
                <div class="p-5">
                    <h3 class="font-bold text-slate-800 dark:text-white group-hover:text-primary transition-colors"><?php echo e(get_text($item['title_en'], $item['title_am'])); ?></h3>
                    <p class="text-sm text-slate-400 mt-1"><?php echo e($item['client_name']); ?></p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Lightbox -->
<div id="lightbox" class="fixed inset-0 z-50 hidden bg-black/90 flex items-center justify-center p-4 cursor-pointer" onclick="closeLightbox()">
    <img id="lightbox-img" src="" alt="" class="max-w-full max-h-full object-contain rounded-lg">
    <button class="absolute top-6 right-6 text-white/80 hover:text-white" onclick="closeLightbox()">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
</div>

<script>
function openLightbox(src) {
    document.getElementById('lightbox-img').src = src;
    document.getElementById('lightbox').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeLightbox() {
    document.getElementById('lightbox').classList.add('hidden');
    document.body.style.overflow = '';
}
</script>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>
