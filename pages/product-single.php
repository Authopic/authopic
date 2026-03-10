<?php
/**
 * Authopic Technologies PLC - Product Single Page (/products/sms, /products/erp)
 */
if (!defined('BASE_PATH')) exit;

$slug = db_escape($route_params['slug'] ?? '');
$product = db_fetch_one("SELECT * FROM `products` WHERE `slug` = '$slug' AND `status` = 'published'");

if (!$product) {
    http_response_code(404);
    require_once BASE_PATH . '/pages/404.php';
    return;
}

// Increment views
db_query("UPDATE `products` SET `views` = `views` + 1 WHERE `id` = {$product['id']}");

$page_title = get_text($product['name_en'], $product['name_am']);
$page_description = get_text($product['tagline_en'], $product['tagline_am']);
$features = get_json($product['features']);
$pricing = get_json($product['pricing_tiers']);
$impl_steps = get_json($product['implementation_steps']);
$faq = get_json($product['faq']);
$gallery = get_json($product['gallery']);

// Get related case studies
$type = db_escape($product['type']);
$case_studies = db_fetch_all("SELECT * FROM `portfolio` WHERE `type` = '$type' AND `status` = 'published' ORDER BY `completion_date` DESC LIMIT 3");

require_once BASE_PATH . '/includes/header.php';
?>

<!-- Hero -->
<section class="relative pt-32 pb-20 overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute top-1/3 left-1/4 w-96 h-96 bg-primary/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-1/4 right-1/3 w-80 h-80 bg-secondary/10 rounded-full blur-3xl"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl" data-animate="slide-up">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-primary/10 border border-primary/20 rounded-full text-sm text-primary font-medium mb-6">
                <?php echo get_text('Product', 'ምርት'); ?>
            </span>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-800 dark:text-white mb-6 leading-tight">
                <?php echo e(get_text($product['name_en'], $product['name_am'])); ?>
            </h1>
            <p class="text-xl text-slate-500 dark:text-gray-400 mb-8 leading-relaxed">
                <?php echo e(get_text($product['tagline_en'], $product['tagline_am'])); ?>
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="#pricing" class="inline-flex items-center gap-2 px-8 py-4 bg-primary hover:bg-blue-700 text-white font-semibold rounded-xl shadow-lg shadow-primary/25 hover:shadow-primary/40 transition-all duration-300">
                    <?php echo get_text('View Pricing', 'ዋጋ ይመልከቱ'); ?>
                </a>
                <a href="#demo-form" class="inline-flex items-center gap-2 px-8 py-4 bg-slate-100 dark:bg-white/5 border border-black/5 dark:border-white/10 text-slate-700 dark:text-gray-200 font-semibold rounded-xl hover:border-primary/30 transition-all duration-300">
                    <?php echo get_text('Request Demo', 'ዲሞ ይጠይቁ'); ?>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Features Grid -->
<section class="py-20 bg-slate-50/50 dark:bg-white/[0.02]" id="features">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16" data-animate="slide-up">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-800 dark:text-white mb-4">
                <?php echo get_text('Key Features', 'ዋና ባህሪያት'); ?>
            </h2>
            <p class="text-lg text-slate-500 dark:text-gray-400">
                <?php echo get_text('Everything you need to manage your ' . ($product['type'] === 'sms' ? 'school' : 'business') . ' efficiently.', 
                    ($product['type'] === 'sms' ? 'ትምህርት ቤትዎን' : 'ንግድዎን') . ' በብቃት ለማስተዳደር የሚያስፈልግዎ ሁሉ።'); ?>
            </p>
        </div>
        
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php 
            $feature_icons = ['clipboard-check','clock','chart-bar','currency-dollar','users','desktop-computer','document-text','bell','translate','calendar','book-open','truck'];
            foreach ($features as $i => $feature): 
            ?>
            <div class="group bg-white dark:bg-white/[0.03] rounded-xl border border-black/5 dark:border-white/5 p-6 hover:border-primary/30 hover:shadow-lg hover:shadow-primary/5 transition-all duration-300" data-animate="slide-up">
                <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center mb-4 group-hover:bg-primary group-hover:text-white transition-all duration-300">
                    <svg class="w-6 h-6 text-primary group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h3 class="font-semibold text-slate-800 dark:text-white mb-2"><?php echo e($feature); ?></h3>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Feature Tabs -->
<section class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-12" data-animate="slide-up">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-800 dark:text-white mb-4">
                <?php echo get_text('Built for Every User', 'ለእያንዳንዱ ተጠቃሚ የተገነባ'); ?>
            </h2>
        </div>
        
        <?php if ($product['type'] === 'sms'): ?>
        <div class="feature-tabs" data-animate="slide-up">
            <div class="flex flex-wrap justify-center gap-2 mb-8">
                <button class="tab-btn active px-6 py-3 rounded-xl text-sm font-medium border border-black/5 dark:border-white/10 transition-all" data-tab="administrators"><?php echo get_text('For Administrators', 'ለአስተዳዳሪዎች'); ?></button>
                <button class="tab-btn px-6 py-3 rounded-xl text-sm font-medium border border-black/5 dark:border-white/10 transition-all" data-tab="teachers"><?php echo get_text('For Teachers', 'ለመምህራን'); ?></button>
                <button class="tab-btn px-6 py-3 rounded-xl text-sm font-medium border border-black/5 dark:border-white/10 transition-all" data-tab="parents"><?php echo get_text('For Parents', 'ለወላጆች'); ?></button>
                <button class="tab-btn px-6 py-3 rounded-xl text-sm font-medium border border-black/5 dark:border-white/10 transition-all" data-tab="accountants"><?php echo get_text('For Accountants', 'ለሂሳብ ሠራተኞች'); ?></button>
            </div>
            
            <div class="tab-content active" id="tab-administrators">
                <div class="grid lg:grid-cols-2 gap-8 items-center">
                    <div class="aspect-video bg-primary/10 rounded-2xl border border-black/5 dark:border-white/5 overflow-hidden cursor-pointer" onclick="openLightbox(this.querySelector('img').src)">
                        <img src="<?php echo asset('img/school_mgt_syst/' . rawurlencode('Screenshot 2026-02-27 at 14-47-42 Dashboard —.png')); ?>" alt="School Management System - Admin Dashboard" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" loading="lazy">
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-slate-800 dark:text-white mb-4"><?php echo get_text('Complete School Dashboard', 'ሙሉ የትምህርት ቤት ዳሽቦርድ'); ?></h3>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3"><svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="text-slate-600 dark:text-gray-300">Real-time student enrollment and attendance overview</span></li>
                            <li class="flex items-start gap-3"><svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="text-slate-600 dark:text-gray-300">Financial summaries and fee collection reports</span></li>
                            <li class="flex items-start gap-3"><svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="text-slate-600 dark:text-gray-300">Staff management and payroll integration</span></li>
                            <li class="flex items-start gap-3"><svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="text-slate-600 dark:text-gray-300">Ministry of Education compliance reports</span></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="tab-content hidden" id="tab-teachers">
                <div class="grid lg:grid-cols-2 gap-8 items-center">
                    <div class="aspect-video bg-secondary/10 rounded-2xl border border-black/5 dark:border-white/5 overflow-hidden cursor-pointer" onclick="openLightbox(this.querySelector('img').src)">
                        <img src="<?php echo asset('img/school_mgt_syst/' . rawurlencode('Screenshot 2026-02-27 at 14-50-25 Dashboard —.png')); ?>" alt="School Management System - Teacher Dashboard" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" loading="lazy">
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-slate-800 dark:text-white mb-4"><?php echo get_text('Teacher Dashboard', 'የመምህር ዳሽቦርድ'); ?></h3>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3"><svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="text-slate-600 dark:text-gray-300">Easy grade entry with bulk upload support</span></li>
                            <li class="flex items-start gap-3"><svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="text-slate-600 dark:text-gray-300">One-click attendance tracking per class</span></li>
                            <li class="flex items-start gap-3"><svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="text-slate-600 dark:text-gray-300">Direct messaging to parents</span></li>
                            <li class="flex items-start gap-3"><svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="text-slate-600 dark:text-gray-300">Lesson planning and timetable management</span></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="tab-content hidden" id="tab-parents">
                <div class="grid lg:grid-cols-2 gap-8 items-center">
                    <div class="aspect-video bg-green-500/10 rounded-2xl border border-black/5 dark:border-white/5 overflow-hidden cursor-pointer" onclick="openLightbox(this.querySelector('img').src)">
                        <img src="<?php echo asset('img/school_mgt_syst/' . rawurlencode('Screenshot 2026-02-27 at 14-53-54 Dashboard —.png')); ?>" alt="School Management System - Parent Portal" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" loading="lazy">
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-slate-800 dark:text-white mb-4"><?php echo get_text('Parent Portal', 'የወላጅ ፖርታል'); ?></h3>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3"><svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="text-slate-600 dark:text-gray-300">Live grades and progress tracking</span></li>
                            <li class="flex items-start gap-3"><svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="text-slate-600 dark:text-gray-300">Online fee payment via mobile money</span></li>
                            <li class="flex items-start gap-3"><svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="text-slate-600 dark:text-gray-300">Attendance notifications via SMS</span></li>
                            <li class="flex items-start gap-3"><svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="text-slate-600 dark:text-gray-300">Direct communication with teachers</span></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="tab-content hidden" id="tab-accountants">
                <div class="grid lg:grid-cols-2 gap-8 items-center">
                    <div class="aspect-video bg-amber-500/10 rounded-2xl border border-black/5 dark:border-white/5 overflow-hidden cursor-pointer" onclick="openLightbox(this.querySelector('img').src)">
                        <img src="<?php echo asset('img/school_mgt_syst/' . rawurlencode('Screenshot 2026-02-27 at 14-56-52 Invoice INV-2025-0001 —.png')); ?>" alt="School Management System - Financial Dashboard" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" loading="lazy">
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-slate-800 dark:text-white mb-4"><?php echo get_text('Financial Management', 'የፋይናንስ አስተዳደር'); ?></h3>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3"><svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="text-slate-600 dark:text-gray-300">Automated fee calculation and invoicing</span></li>
                            <li class="flex items-start gap-3"><svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="text-slate-600 dark:text-gray-300">Expense tracking and budget management</span></li>
                            <li class="flex items-start gap-3"><svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="text-slate-600 dark:text-gray-300">Receipt generation and payment history</span></li>
                            <li class="flex items-start gap-3"><svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="text-slate-600 dark:text-gray-300">Financial reports and analytics</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <!-- ERP Module Tabs -->
        <div class="feature-tabs" data-animate="slide-up">
            <div class="flex flex-wrap justify-center gap-2 mb-8">
                <button class="tab-btn active px-6 py-3 rounded-xl text-sm font-medium border border-black/5 dark:border-white/10 transition-all" data-tab="manufacturing">Manufacturing</button>
                <button class="tab-btn px-6 py-3 rounded-xl text-sm font-medium border border-black/5 dark:border-white/10 transition-all" data-tab="trading">Trading</button>
                <button class="tab-btn px-6 py-3 rounded-xl text-sm font-medium border border-black/5 dark:border-white/10 transition-all" data-tab="ngos">NGOs</button>
                <button class="tab-btn px-6 py-3 rounded-xl text-sm font-medium border border-black/5 dark:border-white/10 transition-all" data-tab="realestate">Real Estate</button>
            </div>
            
            <div class="tab-content active" id="tab-manufacturing">
                <div class="grid lg:grid-cols-2 gap-8 items-center">
                    <div class="aspect-video bg-primary/10 rounded-2xl border border-black/5 dark:border-white/5 flex items-center justify-center"><span class="text-slate-400 text-sm">Manufacturing Dashboard</span></div>
                    <div>
                        <h3 class="text-2xl font-bold text-slate-800 dark:text-white mb-4">Manufacturing Solutions</h3>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3"><svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="text-slate-600 dark:text-gray-300">Bill of Materials (BOM) management</span></li>
                            <li class="flex items-start gap-3"><svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="text-slate-600 dark:text-gray-300">Production planning and scheduling</span></li>
                            <li class="flex items-start gap-3"><svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="text-slate-600 dark:text-gray-300">Quality control and inspection workflows</span></li>
                            <li class="flex items-start gap-3"><svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="text-slate-600 dark:text-gray-300">Raw material and finished goods tracking</span></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="tab-content hidden" id="tab-trading">
                <div class="grid lg:grid-cols-2 gap-8 items-center">
                    <div class="aspect-video bg-secondary/10 rounded-2xl border border-black/5 dark:border-white/5 flex items-center justify-center"><span class="text-slate-400 text-sm">Trading Dashboard</span></div>
                    <div>
                        <h3 class="text-2xl font-bold text-slate-800 dark:text-white mb-4">Trading Solutions</h3>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3"><svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="text-slate-600 dark:text-gray-300">Purchase order management</span></li>
                            <li class="flex items-start gap-3"><svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="text-slate-600 dark:text-gray-300">Sales and distribution management</span></li>
                            <li class="flex items-start gap-3"><svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="text-slate-600 dark:text-gray-300">Supplier relationship management</span></li>
                            <li class="flex items-start gap-3"><svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="text-slate-600 dark:text-gray-300">Import/export documentation</span></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="tab-content hidden" id="tab-ngos">
                <div class="grid lg:grid-cols-2 gap-8 items-center">
                    <div class="aspect-video bg-green-500/10 rounded-2xl border border-black/5 dark:border-white/5 flex items-center justify-center"><span class="text-slate-400 text-sm">NGO Dashboard</span></div>
                    <div>
                        <h3 class="text-2xl font-bold text-slate-800 dark:text-white mb-4">NGO Solutions</h3>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3"><svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="text-slate-600 dark:text-gray-300">Grant tracking and fund management</span></li>
                            <li class="flex items-start gap-3"><svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="text-slate-600 dark:text-gray-300">Donor reporting and communication</span></li>
                            <li class="flex items-start gap-3"><svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="text-slate-600 dark:text-gray-300">Budget management and tracking</span></li>
                            <li class="flex items-start gap-3"><svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="text-slate-600 dark:text-gray-300">Project-based accounting and reporting</span></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="tab-content hidden" id="tab-realestate">
                <div class="grid lg:grid-cols-2 gap-8 items-center">
                    <div class="aspect-video bg-amber-500/10 rounded-2xl border border-black/5 dark:border-white/5 flex items-center justify-center"><span class="text-slate-400 text-sm">Real Estate Dashboard</span></div>
                    <div>
                        <h3 class="text-2xl font-bold text-slate-800 dark:text-white mb-4">Real Estate Solutions</h3>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3"><svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="text-slate-600 dark:text-gray-300">Property portfolio management</span></li>
                            <li class="flex items-start gap-3"><svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="text-slate-600 dark:text-gray-300">Tenant and lease tracking</span></li>
                            <li class="flex items-start gap-3"><svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="text-slate-600 dark:text-gray-300">Maintenance request management</span></li>
                            <li class="flex items-start gap-3"><svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="text-slate-600 dark:text-gray-300">Rent collection and financial reporting</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- SMS Screenshots Gallery -->
<?php if ($product['type'] === 'sms'): ?>
<section class="py-20 bg-slate-50/50 dark:bg-white/[0.02]" id="screenshots">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16" data-animate="slide-up">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-800 dark:text-white mb-4">
                <?php echo get_text('SMS Overview', 'SMS አጠቃላይ እይታ'); ?>
            </h2>
            <p class="text-lg text-slate-500 dark:text-gray-400">
                <?php echo get_text('See the School Management System in action.', 'የትምህርት ቤት አስተዳደር ሲስተሙን በተግባር ይመልከቱ።'); ?>
            </p>
        </div>
        
        <?php
        $sms_screenshots = [
            ['file' => 'Screenshot 2026-02-27 at 14-47-42 Dashboard —.png', 'caption_en' => 'Main Dashboard Overview', 'caption_am' => 'ዋና ዳሽቦርድ አጠቃላይ እይታ'],
            ['file' => 'Screenshot 2026-02-27 at 14-48-08 Dashboard —.png', 'caption_en' => 'Dashboard Analytics', 'caption_am' => 'የዳሽቦርድ ትንተና'],
            ['file' => 'Screenshot 2026-02-27 at 14-50-25 Dashboard —.png', 'caption_en' => 'Teacher Dashboard', 'caption_am' => 'የመምህር ዳሽቦርድ'],
            ['file' => 'Screenshot 2026-02-27 at 14-52-53 Dashboard —.png', 'caption_en' => 'Attendance Tracking', 'caption_am' => 'የመገኘት ክትትል'],
            ['file' => 'Screenshot 2026-02-27 at 14-53-54 Dashboard —.png', 'caption_en' => 'Parent Portal', 'caption_am' => 'የወላጅ ፖርታል'],
            ['file' => 'Screenshot 2026-02-27 at 14-54-29 Dashboard —.png', 'caption_en' => 'Reports & Analytics', 'caption_am' => 'ሪፖርቶች እና ትንተና'],
            ['file' => 'Screenshot 2026-02-27 at 14-55-02 Dashboard —.png', 'caption_en' => 'Fee Management', 'caption_am' => 'የክፍያ አስተዳደር'],
            ['file' => 'Screenshot 2026-02-27 at 14-55-32 Dashboard —.png', 'caption_en' => 'Communication Portal', 'caption_am' => 'የግንኙነት ፖርታል'],
            ['file' => 'Screenshot 2026-02-27 at 14-56-19 Dashboard —.png', 'caption_en' => 'Settings & Configuration', 'caption_am' => 'ቅንብሮች እና ውቅር'],
            ['file' => 'Screenshot 2026-02-27 at 14-56-41 Dashboard —.png', 'caption_en' => 'User Management', 'caption_am' => 'የተጠቃሚ አስተዳደር'],
            ['file' => 'Screenshot 2026-02-27 at 14-56-52 Invoice INV-2025-0001 —.png', 'caption_en' => 'Invoice Generation', 'caption_am' => 'ደረሰኝ ማመንጨት'],
        ];
        ?>
        
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($sms_screenshots as $screenshot): ?>
            <div class="group bg-white dark:bg-white/[0.03] rounded-2xl border border-black/5 dark:border-white/5 overflow-hidden hover:shadow-xl hover:shadow-primary/5 transition-all duration-500 cursor-pointer" data-animate="slide-up" onclick="openLightbox(this.querySelector('img').src)">
                <div class="aspect-video overflow-hidden">
                    <img src="<?php echo asset('img/school_mgt_syst/' . rawurlencode($screenshot['file'])); ?>" 
                         alt="<?php echo e($screenshot['caption_en']); ?>" 
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                         loading="lazy">
                </div>
                <div class="p-4">
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-gray-200">
                        <?php echo e(get_text($screenshot['caption_en'], $screenshot['caption_am'])); ?>
                    </h3>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Pricing -->
<?php if (!empty($pricing)): ?>
<section class="py-20 bg-slate-50/50 dark:bg-white/[0.02]" id="pricing">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16" data-animate="slide-up">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-800 dark:text-white mb-4">
                <?php echo get_text('Pricing Plans', 'የዋጋ ዕቅዶች'); ?>
            </h2>
            <p class="text-lg text-slate-500 dark:text-gray-400">
                <?php echo get_text('Choose the plan that fits your needs.', 'ለፍላጎትዎ የሚስማማውን ዕቅድ ይምረጡ።'); ?>
            </p>
            <?php if ($product['type'] === 'sms'): ?>
            <div class="inline-flex items-center gap-3 mt-6">
                <span class="text-sm font-medium text-slate-700 dark:text-gray-300"><?php echo get_text('Monthly', 'ወርሃዊ'); ?></span>
                <div id="pricing-toggle" role="switch" aria-checked="false" tabindex="0" style="position:relative;width:56px;height:28px;background:#e2e8f0;border-radius:9999px;cursor:pointer;transition:background 0.3s ease;flex-shrink:0"><span id="pricing-knob" style="position:absolute;top:2px;left:2px;width:24px;height:24px;background:white;border-radius:50%;transition:transform 0.3s ease;box-shadow:0 2px 4px rgba(0,0,0,0.15);display:block"></span></div>
                <span class="text-sm font-medium text-slate-700 dark:text-gray-300"><?php echo get_text('Annual', 'ዓመታዊ'); ?> <span class="text-green-500 text-xs font-semibold"><?php echo get_text('Save 17%', '17% ይቆጥቡ'); ?></span></span>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            <?php foreach ($pricing as $tier): ?>
            <div class="relative bg-white dark:bg-white/[0.03] rounded-2xl border <?php echo (!empty($tier['popular'])) ? 'border-primary shadow-xl shadow-primary/10' : 'border-black/5 dark:border-white/5'; ?> p-8 hover:shadow-xl transition-all duration-500" data-animate="slide-up">
                
                <?php if (!empty($tier['popular'])): ?>
                <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                    <span class="px-4 py-1 bg-primary hover:bg-blue-700 text-white text-xs font-bold rounded-full shadow-lg"><?php echo get_text('Most Popular', 'በጣም ተወዳጅ'); ?></span>
                </div>
                <?php endif; ?>
                
                <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2"><?php echo e($tier['name']); ?></h3>
                
                <?php if (isset($tier['price_setup'])): ?>
                <div class="mb-6">
                    <!-- Recurring fee (toggles between monthly and annual) -->
                    <div class="text-3xl font-extrabold text-slate-800 dark:text-white">
                        <span class="pricing-monthly"><?php echo format_etb($tier['price_monthly']); ?><span class="text-sm font-normal text-slate-400">/<?php echo get_text('mo', 'ወር'); ?></span></span>
                        <span class="pricing-annual" style="display:none"><?php echo format_etb($tier['price_annual']); ?><span class="text-sm font-normal text-slate-400">/<?php echo get_text('yr', 'ዓመት'); ?></span></span>
                    </div>
                    <div class="pricing-annual text-xs text-green-500 font-medium mt-1" style="display:none">
                        <?php echo get_text(
                            'Instead of ' . format_etb($tier['price_monthly'] * 12) . '/yr — Save ' . format_etb(($tier['price_monthly'] * 12) - $tier['price_annual']),
                            'ከ ' . format_etb($tier['price_monthly'] * 12) . '/ዓመት በድል — ' . format_etb(($tier['price_monthly'] * 12) - $tier['price_annual']) . ' ይቆጥቡ'
                        ); ?>
                    </div>
                    <!-- One-time setup fee (constant) -->
                    <div class="flex items-center gap-1.5 mt-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm text-slate-500 dark:text-gray-400"><?php echo format_etb($tier['price_setup']); ?> <?php echo get_text('one-time setup fee', 'የአንዴ መከፍያ ክፍያ'); ?></span>
                    </div>
                    <?php if (isset($tier['students'])): ?>
                    <div class="text-sm text-primary font-medium mt-1"><?php echo e($tier['students']); ?></div>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <div class="mb-6">
                    <div class="text-2xl font-extrabold text-slate-800 dark:text-white"><?php echo e($tier['price_range']); ?></div>
                    <?php if (isset($tier['modules'])): ?>
                    <div class="text-sm text-primary font-medium mt-1"><?php echo e($tier['modules']); ?> · <?php echo e($tier['users']); ?></div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <ul class="space-y-3 mb-8">
                    <?php foreach ($tier['features'] as $feature): ?>
                    <li class="flex items-start gap-2 text-sm text-slate-600 dark:text-gray-300">
                        <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <?php echo e($feature); ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
                
                <a href="#demo-form" class="block text-center px-6 py-3 <?php echo (!empty($tier['popular'])) ? 'bg-primary hover:bg-blue-700 text-white shadow-lg shadow-primary/25' : 'bg-slate-100 dark:bg-white/5 text-slate-700 dark:text-gray-200 border border-black/5 dark:border-white/10'; ?> font-semibold rounded-xl hover:shadow-lg transition-all duration-300">
                    <?php echo get_text('Get Started', 'ይጀምሩ'); ?>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<script>
(function() {
    // Pricing Toggle - inline to guarantee execution
    var toggle = document.getElementById('pricing-toggle');
    var knob = document.getElementById('pricing-knob');
    if (toggle && knob) {
        var monthly = document.querySelectorAll('.pricing-monthly');
        var annual = document.querySelectorAll('.pricing-annual');
        var isAnnual = false;

        // Initialize: show monthly, hide annual
        monthly.forEach(function(el) { el.style.display = ''; });
        annual.forEach(function(el) { el.style.display = 'none'; });

        function updateToggle() {
            isAnnual = !isAnnual;
            // Move knob
            knob.style.transform = isAnnual ? 'translateX(28px)' : 'translateX(0)';
            // Change track color
            toggle.style.background = isAnnual ? 'var(--color-primary, #0066FF)' : '#e2e8f0';
            toggle.setAttribute('aria-checked', isAnnual ? 'true' : 'false');
            // Toggle prices
            monthly.forEach(function(el) { el.style.display = isAnnual ? 'none' : ''; });
            annual.forEach(function(el) { el.style.display = isAnnual ? '' : 'none'; });
        }

        toggle.addEventListener('click', updateToggle);
        toggle.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); updateToggle(); }
        });
    }

    // Tab buttons for feature tabs
    document.querySelectorAll('.feature-tabs').forEach(function(group) {
        var buttons = group.querySelectorAll('.tab-btn');
        buttons.forEach(function(btn) {
            btn.addEventListener('click', function() {
                var tabId = btn.getAttribute('data-tab');
                buttons.forEach(function(b) { b.classList.remove('active'); });
                btn.classList.add('active');
                var parent = group.closest('section') || group.parentElement;
                if (parent) {
                    parent.querySelectorAll('.tab-content').forEach(function(c) {
                        c.classList.remove('active');
                        c.classList.add('hidden');
                    });
                }
                var target = document.getElementById('tab-' + tabId);
                if (target) {
                    target.classList.add('active');
                    target.classList.remove('hidden');
                }
            });
        });
    });
})();
</script>
<?php endif; ?>

<!-- Implementation Steps -->
<?php if (!empty($impl_steps)): ?>
<section class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16" data-animate="slide-up">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-800 dark:text-white mb-4">
                <?php echo get_text('Implementation Process', 'የአተገባበር ሂደት'); ?>
            </h2>
        </div>
        <div class="grid md:grid-cols-<?php echo count($impl_steps); ?> gap-8">
            <?php foreach ($impl_steps as $step): ?>
            <div class="text-center" data-animate="slide-up">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-primary/10 border border-primary/10 flex items-center justify-center mb-4 text-2xl font-bold text-primary">
                    <?php echo e($step['step']); ?>
                </div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-2"><?php echo e($step['title']); ?></h3>
                <p class="text-sm text-slate-500 dark:text-gray-400"><?php echo e($step['description']); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- FAQ -->
<?php if (!empty($faq)): ?>
<section class="py-20 bg-slate-50/50 dark:bg-white/[0.02]" id="faq">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-animate="slide-up">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-800 dark:text-white mb-4">
                <?php echo get_text('Frequently Asked Questions', 'በተደጋጋሚ የሚጠየቁ ጥያቄዎች'); ?>
            </h2>
        </div>
        <div class="space-y-4" data-animate="slide-up">
            <?php foreach ($faq as $item): ?>
            <div class="faq-item bg-white dark:bg-white/[0.03] rounded-xl border border-black/5 dark:border-white/5 overflow-hidden">
                <button class="faq-toggle w-full flex items-center justify-between p-6 text-left">
                    <span class="font-semibold text-slate-800 dark:text-white pr-4"><?php echo e($item['question']); ?></span>
                    <svg class="w-5 h-5 text-slate-400 transform transition-transform flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="faq-content hidden px-6 pb-6">
                    <p class="text-slate-500 dark:text-gray-400"><?php echo e($item['answer']); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Demo Request Form -->
<section class="py-20" id="demo-form">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-white/[0.03] rounded-2xl border border-black/5 dark:border-white/5 p-8 md:p-12" data-animate="slide-up">
            <div class="text-center mb-8">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-800 dark:text-white mb-3">
                    <?php echo get_text('Request a Demo', 'ዲሞ ይጠይቁ'); ?>
                </h2>
                <p class="text-slate-500 dark:text-gray-400">
                    <?php echo get_text('See the ' . $product['name_en'] . ' in action. Fill out the form and we\'ll schedule a live demo.', 
                        $product['name_en'] . ' በተግባር ይመልከቱ። ቅጹን ይሙሉ እና ዲሞ እናዘጋጅለዎታለን።'); ?>
                </p>
            </div>
            
            <form id="productDemoForm" method="POST" action="<?php echo url('/api/submit-lead'); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                <input type="hidden" name="source" value="product-page">
                <input type="hidden" name="interest" value="<?php echo e($product['name_en']); ?>">
                <input type="text" name="website_url_hp" class="hidden" tabindex="-1" autocomplete="off">
                
                <div class="grid sm:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2"><?php echo get_text('Full Name', 'ሙሉ ስም'); ?> *</label>
                        <input type="text" name="name" required class="w-full px-4 py-3 bg-slate-50 dark:bg-white/5 border border-black/5 dark:border-white/10 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2"><?php echo get_text('Email', 'ኢሜይል'); ?> *</label>
                        <input type="email" name="email" required class="w-full px-4 py-3 bg-slate-50 dark:bg-white/5 border border-black/5 dark:border-white/10 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2"><?php echo get_text('Phone', 'ስልክ'); ?> *</label>
                        <input type="tel" name="phone" required class="w-full px-4 py-3 bg-slate-50 dark:bg-white/5 border border-black/5 dark:border-white/10 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all" placeholder="+251 904455302">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2"><?php echo get_text($product['type'] === 'sms' ? 'School Name' : 'Company Name', $product['type'] === 'sms' ? 'የትምህርት ቤት ስም' : 'የኩባንያ ስም'); ?></label>
                        <input type="text" name="company" class="w-full px-4 py-3 bg-slate-50 dark:bg-white/5 border border-black/5 dark:border-white/10 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all">
                    </div>
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2"><?php echo get_text('Message', 'መልዕክት'); ?></label>
                    <textarea name="message" rows="4" class="w-full px-4 py-3 bg-slate-50 dark:bg-white/5 border border-black/5 dark:border-white/10 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all resize-none" placeholder="<?php echo get_text('Tell us about your needs...', 'ስለ ፍላጎትዎ ይንገሩን...'); ?>"></textarea>
                </div>
                
                <button type="submit" class="w-full px-8 py-4 bg-primary hover:bg-blue-700 text-white font-semibold rounded-xl shadow-lg shadow-primary/25 hover:shadow-primary/40 transition-all duration-300 text-lg">
                    <?php echo get_text('Submit Request', 'ጥያቄ ያስገቡ'); ?>
                </button>
            </form>
        </div>
    </div>
</section>

<!-- Lightbox -->
<div id="lightbox" class="fixed inset-0 z-50 hidden bg-black/90 items-center justify-center p-4 cursor-pointer" onclick="closeLightbox()">
    <img id="lightbox-img" src="" alt="Screenshot" class="max-w-full max-h-full object-contain rounded-lg">
    <button class="absolute top-6 right-6 text-white/80 hover:text-white" onclick="closeLightbox()">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
</div>

<script>
function openLightbox(src) {
    document.getElementById('lightbox-img').src = src;
    const lb = document.getElementById('lightbox');
    lb.classList.remove('hidden');
    lb.classList.add('flex');
    document.body.style.overflow = 'hidden';
}
function closeLightbox() {
    const lb = document.getElementById('lightbox');
    lb.classList.add('hidden');
    lb.classList.remove('flex');
    document.body.style.overflow = '';
}
</script>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>
