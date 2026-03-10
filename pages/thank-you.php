<?php
/**
 * Authopic Technologies PLC - Thank You Page (/thank-you/{type})
 */
if (!defined('BASE_PATH')) exit;

$type = $route_params['type'] ?? 'contact';
$page_title = get_text('Thank You', 'አመሰግናለሁ');

$messages = [
    'contact' => [
        'title_en' => 'Message Sent Successfully!',
        'title_am' => 'መልዕክት በተሳካ ሁኔታ ተልኳል!',
        'desc_en' => 'Thank you for reaching out. Our team will get back to you within 24 hours.',
        'desc_am' => 'ስለተገናኙን እናመሰግናለን። ቡድናችን በ24 ሰዓት ውስጥ ይመልሳል።',
        'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'
    ],
    'demo' => [
        'title_en' => 'Demo Request Received!',
        'title_am' => 'የዲሞ ጥያቄ ተቀብለናል!',
        'desc_en' => 'Thank you for your interest. Our team will contact you within 1 business day to schedule your demo.',
        'desc_am' => 'ስለ ፍላጎትዎ እናመሰግናለን። ቡድናችን ዲሞዎን ለማቀድ በ1 የስራ ቀን ውስጥ ያገኛዎታል።',
        'icon' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'
    ],
    'newsletter' => [
        'title_en' => 'Subscribed Successfully!',
        'title_am' => 'በተሳካ ሁኔታ ተመዝግበዋል!',
        'desc_en' => 'You\'re now subscribed to our newsletter. Stay tuned for tech insights and updates.',
        'desc_am' => 'አሁን ለጋዜጣችን ተመዝግበዋል። ለቴክ ግንዛቤዎች እና ዝመናዎች ይጠብቁ።',
        'icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'
    ]
];

$msg = $messages[$type] ?? $messages['contact'];

require_once BASE_PATH . '/includes/header.php';
?>

<section class="min-h-[70vh] flex items-center justify-center py-20">
    <div class="max-w-lg mx-auto px-4 text-center" data-animate="slide-up">
        <div class="w-20 h-20 mx-auto rounded-full bg-green-100 dark:bg-green-500/10 flex items-center justify-center mb-8">
            <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        
        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-800 dark:text-white mb-4">
            <?php echo get_text($msg['title_en'], $msg['title_am']); ?>
        </h1>
        
        <p class="text-lg text-slate-500 dark:text-gray-400 mb-8">
            <?php echo get_text($msg['desc_en'], $msg['desc_am']); ?>
        </p>
        
        <div class="flex flex-wrap justify-center gap-4">
            <a href="<?php echo url('/'); ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-primary hover:bg-blue-700 text-white font-semibold rounded-xl shadow-lg shadow-primary/25 hover:shadow-primary/40 transition-all duration-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <?php echo get_text('Back to Home', 'ወደ መነሻ ይመለሱ'); ?>
            </a>
            <a href="<?php echo url('/portfolio'); ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-slate-100 dark:bg-white/5 border border-black/5 dark:border-white/10 text-slate-700 dark:text-gray-200 font-semibold rounded-xl hover:border-primary/30 transition-all duration-300">
                <?php echo get_text('View Our Work', 'ስራችንን ይመልከቱ'); ?>
            </a>
        </div>
    </div>
</section>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>
