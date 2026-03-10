<?php
/**
 * Authopic Technologies PLC - Homepage
 */
if (!defined('BASE_PATH'))
    exit;

$page_title = null; // Use default
$page_description = get_setting('meta_description');

// Fetch data for homepage
$products = db_fetch_all("SELECT * FROM `products` WHERE `status` = 'published' ORDER BY `sort_order` ASC");
$services = db_fetch_all("SELECT * FROM `services` WHERE `status` = 'published' ORDER BY `sort_order` ASC");
$featured_portfolio = db_fetch_all("SELECT * FROM `portfolio` WHERE `status` = 'published' AND `is_featured` = 1 ORDER BY `completion_date` DESC LIMIT 3");
$testimonials = db_fetch_all("SELECT * FROM `testimonials` WHERE `status` = 'approved' AND `is_featured` = 1 ORDER BY `sort_order` ASC LIMIT 3");
$recent_posts = db_fetch_all("SELECT p.*, c.name_en as category_name FROM `blog_posts` p LEFT JOIN `blog_categories` c ON p.category_id = c.id WHERE p.status = 'published' AND p.publish_date <= NOW() ORDER BY p.publish_date DESC LIMIT 3");

require_once BASE_PATH . '/includes/header.php';
?>

<!-- ============================================ -->
<!-- HERO SECTION -->
<!-- ============================================ -->
<section class="relative min-h-screen flex items-center overflow-hidden pt-20" id="hero">
    
    <!-- Animated Background -->
    <div class="absolute inset-0">
        <!-- Gradient Orbs -->
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-primary/20 rounded-full blur-3xl animate-float"></div>
        <div class="absolute bottom-1/4 right-1/4 w-80 h-80 bg-secondary/15 rounded-full blur-3xl animate-float" style="animation-delay: -3s;"></div>
        <div class="absolute top-1/2 left-1/2 w-64 h-64 bg-purple-500/10 rounded-full blur-3xl animate-float" style="animation-delay: -1.5s;"></div>
        
        <!-- Grid pattern -->
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiMyMDIwMjAiIGZpbGwtb3BhY2l0eT0iMC4wMyI+PHBhdGggZD0iTTM2IDE4YzEuMSAwIDItLjkgMi0ycy0uOS0yLTItMi0yIC45LTIgMiAuOSAyIDIgMnoiLz48L2c+PC9nPjwvc3ZnPg==')] opacity-50 dark:opacity-100"></div>
    </div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-32">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            
            <!-- Left Content -->
            <div class="text-center lg:text-left" data-animate="slide-up">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-primary/10 border border-primary/20 rounded-full text-sm text-primary font-medium mb-6">
                    <span class="w-2 h-2 bg-primary rounded-full animate-pulse"></span>
                    <?php echo get_text('Innovative Digital Solutions', 'ፈጠራዊ ዲጂታል መፍትሔዎች'); ?>
                </div>
                
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-tight mb-6">
                    <span class="text-slate-800 dark:text-white"><?php echo get_text('Building Powerful', 'ኃይለኛ ዲጂታል'); ?></span><br>
                    <span class="text-primary"><?php echo get_text('Digital Solutions', 'መፍትሔዎችን እንገነባለን'); ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl text-slate-500 dark:text-gray-400 mb-8 max-w-xl mx-auto lg:mx-0 leading-relaxed">
                    <?php echo get_text(
    'Authopic Technologies PLC helps businesses grow through innovative web development, scalable software systems, and cutting-edge digital technologies.',
    'ኦቶፒክ ቴክኖሎጂስ ፒኤልሲ ፈጠራዊ ዌብ ልማት፣ ሊስፋፉ የሚችሉ ሶፍትዌር ሲስተሞች እና ዘመናዊ ዲጂታል ቴክኖሎጂዎች አማካኝነት ንግዶች እንዲያድጉ ይረዳል።'
); ?>
                </p>
                
                <div class="flex flex-col sm:flex-row items-center gap-4 justify-center lg:justify-start">
                    <a href="<?php echo url('/request-demo'); ?>" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-4 bg-primary hover:bg-blue-700 text-white font-semibold rounded-xl shadow-lg shadow-primary/25 hover:shadow-primary/40 transition-all duration-300 hover:-translate-y-0.5 text-lg">
                        <?php echo get_text('Start Your Project', 'ፕሮጀክትዎን ይጀምሩ'); ?>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                    <a href="<?php echo url('/#services'); ?>" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-4 bg-slate-100 dark:bg-white/5 border border-black/5 dark:border-white/10 text-slate-700 dark:text-gray-200 font-semibold rounded-xl hover:border-primary/30 hover:bg-primary/5 transition-all duration-300 text-lg">
                        <?php echo get_text('View Our Services', 'አገልግሎቶቻችንን ይመልከቱ'); ?>
                    </a>
                </div>
            </div>
            
            <!-- Right Visual -->
            <div class="hidden lg:flex justify-center" data-animate="fade-in">
                <div class="relative">
                    <!-- Main SMS Dashboard Screenshot -->
                    <div class="w-[500px] relative">
                        <div class="rounded-2xl border border-black/5 dark:border-white/10 shadow-2xl overflow-hidden transform rotate-1 hover:rotate-0 transition-transform duration-500">
                            <img src="<?php echo asset('img/school_mgt_syst/' . rawurlencode('Screenshot 2026-02-27 at 14-47-42 Dashboard —.png')); ?>" alt="School Management System Dashboard" class="w-full h-auto" loading="eager">
                        </div>
                        
                        <!-- Secondary screenshot peek -->
                        <div class="absolute -bottom-6 -left-6 w-48 rounded-xl border border-black/5 dark:border-white/10 shadow-xl overflow-hidden transform -rotate-3 hover:rotate-0 transition-transform duration-500">
                            <img src="<?php echo asset('img/school_mgt_syst/' . rawurlencode('Screenshot 2026-02-27 at 14-56-52 Invoice INV-2025-0001 —.png')); ?>" alt="SMS Invoice" class="w-full h-auto" loading="eager">
                        </div>
                        
                        <!-- Floating badge -->
                        <div class="absolute -top-4 -right-4 bg-white dark:bg-[#1A1A1A] rounded-2xl shadow-xl border border-black/5 dark:border-white/10 p-4 animate-float">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                                <span class="text-sm font-medium">3+ Years</span>
                            </div>
                        </div>
                        
                        <!-- Stats badge -->
                        <div class="absolute -bottom-4 -right-4 bg-white dark:bg-[#1A1A1A] rounded-2xl shadow-xl border border-black/5 dark:border-white/10 p-4 animate-float" style="animation-delay: -2s;">
                            <div class="text-2xl font-bold text-primary">98%</div>
                            <div class="text-xs text-slate-500 dark:text-gray-400">Client Satisfaction</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scroll indicator -->
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce">
        <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
    </div>
</section>

<!-- ============================================ -->
<!-- TRUST BAR -->
<!-- ============================================ -->
<section class="py-12 border-y border-black/5 dark:border-white/5 bg-slate-50/50 dark:bg-white/[0.02]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <p class="text-center text-sm text-slate-400 dark:text-gray-400 mb-8 font-medium uppercase tracking-wider">
            <?php echo get_text('Trusted by Leading Businesses & Organizations', 'በግንባር ቀደም ንግዶች እና ድርጅቶች የታመነ'); ?>
        </p>
        <div class="flex flex-wrap items-center justify-center gap-8 lg:gap-16 opacity-50 hover:opacity-80 transition-opacity">
            <?php for ($i = 1; $i <= 8; $i++): ?>
            <div class="h-8 w-24 bg-slate-300 dark:bg-gray-700 rounded-lg opacity-40 hover:opacity-100 transition-all duration-300 grayscale hover:grayscale-0"></div>
            <?php
endfor; ?>
        </div>
    </div>
</section>

<!-- ============================================ -->
<!-- PRODUCTS SECTION -->
<!-- ============================================ -->
<section class="py-20 lg:py-32" id="products">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Section Header -->
        <div class="text-center max-w-3xl mx-auto mb-16" data-animate="slide-up">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-primary/10 border border-primary/20 rounded-full text-sm text-primary font-medium mb-4">
                <?php echo get_text('Our Products', 'ምርቶቻችን'); ?>
            </span>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-800 dark:text-white mb-4">
                <?php echo get_text('Ready-to-Deploy Solutions', 'ለመጠቀም ዝግጁ መፍትሔዎች'); ?>
            </h2>
            <p class="text-lg text-slate-500 dark:text-gray-400">
                <?php echo get_text('Purpose-built software for Ethiopian schools and businesses, with full local language support and compliance.', 'ለኢትዮጵያ ትምህርት ቤቶች እና ንግዶች ልዩ የተገነቡ ሶፍትዌሮች ከሙሉ የአካባቢ ቋንቋ ድጋፍ ጋር።'); ?>
            </p>
        </div>
        
        <!-- Products Grid -->
        <div class="grid md:grid-cols-2 gap-8">
            <?php foreach ($products as $product): ?>
            <div class="group relative bg-white dark:bg-white/[0.03] rounded-2xl border border-black/5 dark:border-white/5 p-8 hover:border-primary/30 dark:hover:border-primary/20 transition-all duration-500 hover:shadow-xl hover:shadow-primary/5" data-animate="slide-up">
                
                <!-- Glow effect on hover -->
                <div class="absolute inset-0 rounded-2xl bg-primary/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                
                <div class="relative">
                    <!-- Icon -->
                    <div class="w-14 h-14 rounded-2xl bg-primary/10 border border-primary/10 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <?php if ($product['type'] === 'sms'): ?>
                        <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        <?php
    else: ?>
                        <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <?php
    endif; ?>
                    </div>
                    
                    <!-- Content -->
                    <h3 class="text-2xl font-bold text-slate-800 dark:text-white mb-3">
                        <?php echo e(get_text($product['name_en'], $product['name_am'])); ?>
                    </h3>
                    <p class="text-primary font-medium mb-4">
                        <?php echo e(get_text($product['tagline_en'], $product['tagline_am'])); ?>
                    </p>
                    <p class="text-slate-500 dark:text-gray-400 leading-relaxed mb-6">
                        <?php echo e(truncate(get_text($product['description_en'], $product['description_am']), 180)); ?>
                    </p>
                    
                    <!-- Product screenshot preview -->
                    <?php if ($product['type'] === 'sms'): ?>
                    <div class="mb-6 rounded-xl overflow-hidden border border-black/5 dark:border-white/5 shadow-sm">
                        <img src="<?php echo asset('img/school_mgt_syst/' . rawurlencode('Screenshot 2026-02-27 at 14-48-08 Dashboard —.png')); ?>" alt="School Management System Preview" class="w-full h-40 object-cover object-top group-hover:scale-105 transition-transform duration-500" loading="lazy">
                    </div>
                    <?php
    endif; ?>
                    
                    <!-- Features preview -->
                    <?php $features = get_json($product['features']); ?>
                    <div class="flex flex-wrap gap-2 mb-6">
                        <?php foreach (array_slice($features, 0, 4) as $feature): ?>
                        <span class="px-3 py-1 bg-slate-100 dark:bg-white/5 border border-black/5 dark:border-white/5 rounded-lg text-xs text-slate-600 dark:text-gray-400">
                            <?php echo e(is_string($feature) ? $feature : ''); ?>
                        </span>
                        <?php
    endforeach; ?>
                        <?php if (count($features) > 4): ?>
                        <span class="px-3 py-1 text-xs text-primary font-medium">+<?php echo count($features) - 4; ?> more</span>
                        <?php
    endif; ?>
                    </div>
                    
                    <!-- CTA -->
                    <a href="<?php echo url('/products/' . $product['slug']); ?>" class="inline-flex items-center gap-2 text-primary font-semibold hover:gap-3 transition-all duration-300">
                        <?php echo get_text('Learn More', 'ተጨማሪ ይመልከቱ'); ?>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>
            </div>
            <?php
endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================================ -->
<!-- SERVICES SECTION -->
<!-- ============================================ -->
<section class="py-20 lg:py-32 bg-slate-50/50 dark:bg-white/[0.02]" id="services">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-3xl mx-auto mb-16" data-animate="slide-up">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-secondary/10 border border-secondary/20 rounded-full text-sm text-secondary font-medium mb-4">
                <?php echo get_text('Our Services', 'አገልግሎቶቻችን'); ?>
            </span>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-800 dark:text-white mb-4">
                <?php echo get_text('Custom Development Services', 'ብጁ የልማት አገልግሎቶች'); ?>
            </h2>
            <p class="text-lg text-slate-500 dark:text-gray-400">
                <?php echo get_text('From professional websites to complex web applications, we build digital solutions tailored to your needs.', 'ከፕሮፌሽናል ዌብሳይቶች እስከ ውስብስብ ዌብ አፕሊኬሽኖች ድረስ ለፍላጎትዎ የተበጁ ዲጂታል መፍትሔዎችን እንገነባለን።'); ?>
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 gap-8">
            <?php foreach ($services as $service): ?>
            <div class="group relative overflow-hidden rounded-2xl bg-white/80 dark:bg-white/[0.03] backdrop-blur-sm border border-black/5 dark:border-white/5 p-8 hover:border-secondary/30 transition-all duration-500" data-animate="slide-up">
                
                <div class="absolute inset-0 bg-secondary/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                
                <div class="relative">
                    <div class="w-14 h-14 rounded-2xl bg-secondary/10 border border-secondary/10 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <?php if (strpos($service['slug'], 'website') !== false): ?>
                        <svg class="w-7 h-7 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                        <?php
    else: ?>
                        <svg class="w-7 h-7 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                        <?php
    endif; ?>
                    </div>
                    
                    <h3 class="text-2xl font-bold text-slate-800 dark:text-white mb-3">
                        <?php echo e(get_text($service['name_en'], $service['name_am'])); ?>
                    </h3>
                    <p class="text-slate-500 dark:text-gray-400 leading-relaxed mb-6">
                        <?php echo e(truncate(get_text($service['description_en'], $service['description_am']), 180)); ?>
                    </p>
                    
                    <?php $techs = get_json($service['technologies']); ?>
                    <div class="flex flex-wrap gap-2 mb-6">
                        <?php foreach (array_slice($techs, 0, 5) as $tech): ?>
                        <span class="px-3 py-1 bg-slate-100 dark:bg-white/5 border border-black/5 dark:border-white/5 rounded-lg text-xs text-slate-600 dark:text-gray-400"><?php echo e($tech); ?></span>
                        <?php
    endforeach; ?>
                    </div>
                    
                    <a href="<?php echo url('/services/' . $service['slug']); ?>" class="inline-flex items-center gap-2 text-secondary font-semibold hover:gap-3 transition-all duration-300">
                        <?php echo get_text('Learn More', 'ተጨማሪ ይመልከቱ'); ?>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>
            </div>
            <?php
endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================================ -->
<!-- STATS COUNTER -->
<!-- ============================================ -->
<section class="py-20 relative overflow-hidden">
    <div class="absolute inset-0 bg-primary"></div>
    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSI+PHBhdGggZD0iTTM2IDE4YzEuMSAwIDItLjkgMi0ycy0uOS0yLTItMi0yIC45LTIgMiAuOSAyIDIgMnoiLz48L2c+PC9nPjwvc3ZnPg==')] "></div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center" data-animate="slide-up">
                <div class="text-4xl sm:text-5xl font-extrabold text-white mb-2" data-counter="100">0</div>
                <div class="text-sm text-blue-100 font-medium"><?php echo get_text('Projects Delivered', 'የተላለፉ ፕሮጀክቶች'); ?></div>
            </div>
            <div class="text-center" data-animate="slide-up">
                <div class="text-4xl sm:text-5xl font-extrabold text-white mb-2" data-counter="50">0</div>
                <div class="text-sm text-blue-100 font-medium"><?php echo get_text('Happy Clients', 'ደስተኛ ደንበኞች'); ?></div>
            </div>
            <div class="text-center" data-animate="slide-up">
                <div class="text-4xl sm:text-5xl font-extrabold text-white mb-2" data-counter="5">0</div>
                <div class="text-sm text-blue-100 font-medium"><?php echo get_text('Years Experience', 'ዓመታት ልምድ'); ?></div>
            </div>
            <div class="text-center" data-animate="slide-up">
                <div class="text-4xl sm:text-5xl font-extrabold text-white mb-2" data-counter="25">0</div>
                <div class="text-sm text-blue-100 font-medium"><?php echo get_text('Team Members', 'የቡድን አባላት'); ?></div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================ -->
<!-- FEATURED PORTFOLIO -->
<!-- ============================================ -->
<?php if (!empty($featured_portfolio)): ?>
<section class="py-20 lg:py-32" id="portfolio">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-3xl mx-auto mb-16" data-animate="slide-up">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-primary/10 border border-primary/20 rounded-full text-sm text-primary font-medium mb-4">
                <?php echo get_text('Featured Work', 'ተመራጭ ስራዎች'); ?>
            </span>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-800 dark:text-white mb-4">
                <?php echo get_text('Our Success Stories', 'የስኬት ታሪኮቻችን'); ?>
            </h2>
            <p class="text-lg text-slate-500 dark:text-gray-400">
                <?php echo get_text('Real results for real Ethiopian businesses.', 'ለእውነተኛ ኢትዮጵያዊ ንግዶች እውነተኛ ውጤቶች።'); ?>
            </p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8">
            <?php foreach ($featured_portfolio as $portfolio): ?>
            <a href="<?php echo url('/portfolio/' . $portfolio['slug']); ?>" class="group block" data-animate="slide-up">
                <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-white/[0.03] border border-black/5 dark:border-white/5 hover:border-primary/30 transition-all duration-500 hover:shadow-xl hover:shadow-primary/5">
                    
                    <!-- Image placeholder -->
                    <div class="aspect-video bg-primary/20 relative overflow-hidden">
                        <?php if ($portfolio['featured_image']): ?>
                        <img src="<?php echo e(upload_url($portfolio['featured_image'])); ?>" alt="<?php echo e($portfolio['title_en']); ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" loading="lazy">
                        <?php
        else: ?>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <svg class="w-16 h-16 text-primary/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <?php
        endif; ?>
                        
                        <!-- Type badge -->
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1 bg-white/90 dark:bg-black/70 backdrop-blur-sm rounded-full text-xs font-medium text-slate-700 dark:text-gray-200 uppercase">
                                <?php echo e($portfolio['type']); ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-2 group-hover:text-primary transition-colors">
                            <?php echo e(get_text($portfolio['title_en'], $portfolio['title_am'])); ?>
                        </h3>
                        <p class="text-sm text-slate-500 dark:text-gray-400 mb-4">
                            <?php echo e($portfolio['client_name']); ?>
                        </p>
                        
                        <?php $metrics = get_json($portfolio['metrics']); ?>
                        <?php if (!empty($metrics)): ?>
                        <div class="flex flex-wrap gap-3">
                            <?php foreach (array_slice($metrics, 0, 2) as $metric): ?>
                            <div class="px-3 py-1 bg-primary/5 border border-primary/10 rounded-lg">
                                <span class="text-sm font-bold text-primary"><?php echo e($metric['value'] ?? ''); ?></span>
                                <span class="text-xs text-slate-500 dark:text-gray-400 ml-1"><?php echo e($metric['label'] ?? ''); ?></span>
                            </div>
                            <?php
            endforeach; ?>
                        </div>
                        <?php
        endif; ?>
                    </div>
                </div>
            </a>
            <?php
    endforeach; ?>
        </div>
        
        <div class="text-center mt-12">
            <a href="<?php echo url('/portfolio'); ?>" class="inline-flex items-center gap-2 px-8 py-4 bg-slate-100 dark:bg-white/5 border border-black/5 dark:border-white/10 text-slate-700 dark:text-gray-200 font-semibold rounded-xl hover:border-primary/30 hover:bg-primary/5 transition-all duration-300">
                <?php echo get_text('View All Projects', 'ሁሉንም ፕሮጀክቶች ይመልከቱ'); ?>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </div>
</section>
<?php
endif; ?>

<!-- ============================================ -->
<!-- TESTIMONIALS -->
<!-- ============================================ -->
<?php if (!empty($testimonials)): ?>
<section class="py-20 lg:py-32 bg-slate-50/50 dark:bg-white/[0.02]" id="testimonials">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-3xl mx-auto mb-16" data-animate="slide-up">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-amber-500/10 border border-amber-500/20 rounded-full text-sm text-amber-600 dark:text-amber-400 font-medium mb-4">
                <?php echo get_text('Testimonials', 'ምስክርነቶች'); ?>
            </span>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-800 dark:text-white mb-4">
                <?php echo get_text('What Our Clients Say', 'ደንበኞቻችን ምን ይላሉ'); ?>
            </h2>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8">
            <?php foreach ($testimonials as $testimonial): ?>
            <div class="relative bg-white dark:bg-white/[0.03] rounded-2xl border border-black/5 dark:border-white/5 p-8 hover:border-amber-500/20 transition-all duration-500" data-animate="slide-up">
                
                <!-- Quote icon -->
                <div class="absolute -top-3 left-8">
                    <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/20">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10H14.017zM0 21v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151C7.563 6.068 6 8.789 6 11h4v10H0z"/></svg>
                    </div>
                </div>
                
                <!-- Stars -->
                <div class="flex items-center gap-1 mb-4 mt-4">
                    <?php for ($s = 1; $s <= 5; $s++): ?>
                    <svg class="w-4 h-4 <?php echo $s <= $testimonial['rating'] ? 'text-amber-400' : 'text-slate-200 dark:text-gray-700'; ?>" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    <?php
        endfor; ?>
                </div>
                
                <p class="text-slate-600 dark:text-gray-300 leading-relaxed mb-6 italic">
                    "<?php echo e(get_text($testimonial['quote_en'], $testimonial['quote_am'])); ?>"
                </p>
                
                <div class="flex items-center gap-3 pt-4 border-t border-black/5 dark:border-white/5">
                    <div class="w-12 h-12 rounded-full bg-primary flex items-center justify-center text-white font-bold text-lg">
                        <?php echo strtoupper(substr($testimonial['client_name'], 0, 1)); ?>
                    </div>
                    <div>
                        <div class="font-semibold text-slate-800 dark:text-white text-sm"><?php echo e($testimonial['client_name']); ?></div>
                        <div class="text-xs text-slate-500 dark:text-gray-400"><?php echo e($testimonial['client_position']); ?>, <?php echo e($testimonial['company_name']); ?></div>
                    </div>
                </div>
            </div>
            <?php
    endforeach; ?>
        </div>
    </div>
</section>
<?php
endif; ?>

<!-- ============================================ -->
<!-- BLOG HIGHLIGHTS -->
<!-- ============================================ -->
<?php if (!empty($recent_posts)): ?>
<section class="py-20 lg:py-32" id="blog">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-3xl mx-auto mb-16" data-animate="slide-up">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-green-500/10 border border-green-500/20 rounded-full text-sm text-green-600 dark:text-green-400 font-medium mb-4">
                <?php echo get_text('Latest Insights', 'የቅርብ ግንዛቤዎች'); ?>
            </span>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-800 dark:text-white mb-4">
                <?php echo get_text('From Our Blog', 'ከብሎጋችን'); ?>
            </h2>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8">
            <?php foreach ($recent_posts as $post): ?>
            <a href="<?php echo url('/insights/' . $post['slug']); ?>" class="group block" data-animate="slide-up">
                <div class="bg-white dark:bg-white/[0.03] rounded-2xl border border-black/5 dark:border-white/5 overflow-hidden hover:border-primary/30 transition-all duration-500 hover:shadow-xl hover:shadow-primary/5">
                    <div class="aspect-video bg-slate-100 dark:bg-white/5 relative overflow-hidden">
                        <?php if ($post['featured_image']): ?>
                        <img src="<?php echo e(upload_url($post['featured_image'])); ?>" alt="" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" loading="lazy">
                        <?php
        endif; ?>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-3">
                            <?php if ($post['category_name']): ?>
                            <span class="px-2.5 py-0.5 bg-primary/10 text-primary text-xs font-medium rounded-full"><?php echo e($post['category_name']); ?></span>
                            <?php
        endif; ?>
                            <span class="text-xs text-slate-400 dark:text-gray-400"><?php echo e(format_date($post['publish_date'])); ?></span>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-2 group-hover:text-primary transition-colors line-clamp-2">
                            <?php echo e($post['title_en']); ?>
                        </h3>
                        <p class="text-sm text-slate-500 dark:text-gray-400 line-clamp-3">
                            <?php echo e(truncate($post['excerpt_en'], 120)); ?>
                        </p>
                    </div>
                </div>
            </a>
            <?php
    endforeach; ?>
        </div>
        
        <div class="text-center mt-12">
            <a href="<?php echo url('/insights'); ?>" class="inline-flex items-center gap-2 text-primary font-semibold hover:gap-3 transition-all duration-300">
                <?php echo get_text('View All Articles', 'ሁሉንም ጽሑፎች ይመልከቱ'); ?>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </div>
</section>
<?php
endif; ?>

<!-- ============================================ -->
<!-- CONTACT CTA -->
<!-- ============================================ -->
<section class="py-20 lg:py-32 relative overflow-hidden">
    <div class="absolute inset-0 bg-primary"></div>
    <div class="absolute inset-0 opacity-30">
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-80 h-80 bg-white/5 rounded-full blur-3xl"></div>
    </div>
    
    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white mb-6" data-animate="slide-up">
            <?php echo get_text('Ready to Build Your Next Digital Solution?', 'ቀጣዩን ዲጂታል መፍትሔዎን ለመገንባት ዝግጁ ነዎት?'); ?>
        </h2>
        <p class="text-lg text-blue-100 mb-8 max-w-2xl mx-auto" data-animate="slide-up">
            <?php echo get_text(
    'Let\'s discuss how Authopic Technologies can help your business grow with innovative web development, scalable software, and cutting-edge digital solutions.',
    'ኦቶፒክ ቴክኖሎጂስ ፈጠራዊ ዌብ ልማት፣ ሊስፋፉ የሚችሉ ሶፍትዌር እና ዘመናዊ ዲጂታል መፍትሔዎች ንግድዎን ለማሳደግ እንዴት እንደሚረዱ እንነጋገር።'
); ?>
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4" data-animate="slide-up">
            <a href="<?php echo url('/request-demo'); ?>" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-4 bg-white text-primary font-bold rounded-xl shadow-lg hover:shadow-white/20 transition-all duration-300 hover:-translate-y-0.5 text-lg">
                <?php echo get_text('Start a Project', 'ፕሮጀክት ይጀምሩ'); ?>
            </a>
            <a href="<?php echo url('/contact'); ?>" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-4 bg-white/10 border border-white/20 text-white font-bold rounded-xl hover:bg-white/20 transition-all duration-300 text-lg backdrop-blur-sm">
                <?php echo get_text('Contact Us', 'ያግኙን'); ?>
            </a>
        </div>
    </div>
</section>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>
