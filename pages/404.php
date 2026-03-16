<?php
// Developed by Yisak A. Alemayehu (yisak.dev)
/**
 * Authopic Technologies PLC - 404 Page
 */
if (!defined('BASE_PATH')) exit;

$page_title = get_text('Page Not Found', 'ገጽ አልተገኘም');
require_once BASE_PATH . '/includes/header.php';
?>

<section class="min-h-[70vh] flex items-center justify-center py-20">
    <div class="max-w-lg mx-auto px-4 text-center" data-animate="slide-up">
        <div class="text-[120px] sm:text-[160px] font-extrabold text-primary leading-none mb-4">
            404
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-800 dark:text-white mb-4">
            <?php echo get_text('Page Not Found', 'ገጽ አልተገኘም'); ?>
        </h1>
        <p class="text-lg text-slate-500 dark:text-gray-400 mb-8">
            <?php echo get_text('The page you\'re looking for doesn\'t exist or has been moved.', 'እየፈለጉት ያለው ገጽ አይገኝም ወይም ተዘዋውሯል።'); ?>
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="<?php echo url('/'); ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-primary hover:bg-blue-700 text-white font-semibold rounded-xl shadow-lg shadow-primary/25 hover:shadow-primary/40 transition-all duration-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <?php echo get_text('Go Home', 'ወደ መነሻ'); ?>
            </a>
            <a href="<?php echo url('/contact'); ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-slate-100 dark:bg-white/5 border border-black/5 dark:border-white/10 text-slate-700 dark:text-gray-200 font-semibold rounded-xl hover:border-primary/30 transition-all duration-300">
                <?php echo get_text('Contact Us', 'ያግኙን'); ?>
            </a>
        </div>
    </div>
</section>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>
