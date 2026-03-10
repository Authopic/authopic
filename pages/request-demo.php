<?php
/**
 * Authopic Technologies PLC - Request Demo Page (/request-demo)
 */
if (!defined('BASE_PATH')) exit;

$page_title = get_text('Request a Demo', 'ዲሞ ይጠይቁ');
$page_description = get_text('Schedule a live demo of our SMS, ERP, or other solutions.', 'የ SMS፣ ERP ወይም ሌሎች መፍትሄዎቻችንን ቀጥታ ዲሞ ያቅዱ።');

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify(post('csrf_token'))) {
        $errors[] = 'Invalid request. Please try again.';
    } elseif (is_spam_honeypot('website_url_hp')) {
        $errors[] = 'Spam detected.';
    } elseif (!rate_limit_check('demo_request', 3, 3600)) {
        $errors[] = get_text('Too many requests. Please try again later.', 'ብዛት ያላቸው ጥያቄዎች። ቆይተው ይሞክሩ።');
    } else {
        $name = trim(post('name'));
        $email = trim(post('email'));
        $phone = trim(post('phone'));
        $company = trim(post('company'));
        $product = trim(post('product'));
        $role = trim(post('role'));
        $company_size = trim(post('company_size'));
        $preferred_date = trim(post('preferred_date'));
        $preferred_time = trim(post('preferred_time'));
        $message = trim(post('message'));

        if (empty($name)) $errors[] = get_text('Name is required.', 'ስም ያስፈልጋል።');
        if (!is_valid_email($email)) $errors[] = get_text('Valid email is required.', 'ትክክለኛ ኢሜይል ያስፈልጋል።');
        if (!is_valid_phone($phone)) $errors[] = get_text('Valid phone is required.', 'ትክክለኛ ስልክ ያስፈልጋል።');
        if (empty($product)) $errors[] = get_text('Please select a product.', 'እባክዎ ምርት ይምረጡ።');

        if (empty($errors)) {
            $name = db_escape($name);
            $email_esc = db_escape($email);
            $phone = db_escape($phone);
            $company_esc = db_escape($company);
            $product_esc = db_escape($product);
            $role_esc = db_escape($role);
            $message_esc = db_escape($message);

            db_query("INSERT INTO `demo_requests` (`name`, `email`, `phone`, `company`, `product_interest`, `role`, `company_size`, `preferred_date`, `preferred_time`, `message`, `created_at`) 
                VALUES ('$name', '$email_esc', '$phone', '$company_esc', '$product_esc', '$role_esc', '" . db_escape($company_size) . "', '" . db_escape($preferred_date) . "', '" . db_escape($preferred_time) . "', '$message_esc', NOW())");

            notify_admin('New Demo Request', "Name: $name\nEmail: $email\nPhone: $phone\nProduct: $product\nCompany: $company\nPreferred Date: $preferred_date $preferred_time");

            redirect('/thank-you/demo');
        }
    }
}

// Get products
$products = db_fetch_all("SELECT name_en, name_am, slug FROM `products` WHERE `status` = 'published' ORDER BY `sort_order`");

require_once BASE_PATH . '/includes/header.php';
?>

<!-- Hero -->
<section class="relative pt-32 pb-12 overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute top-1/3 left-1/4 w-96 h-96 bg-primary/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-1/4 right-1/3 w-80 h-80 bg-secondary/10 rounded-full blur-3xl"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center" data-animate="slide-up">
        <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-800 dark:text-white mb-4">
            <?php echo get_text('Request a Live Demo', 'ቀጥታ ዲሞ ይጠይቁ'); ?>
        </h1>
        <p class="text-xl text-slate-500 dark:text-gray-400 max-w-2xl mx-auto">
            <?php echo get_text('See our solutions in action. Schedule a personalized demo with our team.', 'መፍትሄዎቻችንን በተግባር ይመልከቱ። ከቡድናችን ጋር ግላዊ ዲሞ ያቅዱ።'); ?>
        </p>
    </div>
</section>

<!-- Demo Form -->
<section class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-5 gap-12">
            <!-- Form -->
            <div class="lg:col-span-3 bg-white dark:bg-white/[0.03] rounded-2xl border border-black/5 dark:border-white/5 p-8 md:p-10" data-animate="slide-up">
                <?php if (!empty($errors)): ?>
                <div class="bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-xl p-4 mb-6">
                    <?php foreach ($errors as $error): ?>
                    <p class="text-sm text-red-600 dark:text-red-400"><?php echo e($error); ?></p>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <form method="POST" id="demoForm">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                    <input type="text" name="website_url_hp" class="hidden" tabindex="-1" autocomplete="off">
                    
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-6"><?php echo get_text('Your Information', 'የእርስዎ መረጃ'); ?></h3>
                    
                    <div class="grid sm:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2"><?php echo get_text('Full Name', 'ሙሉ ስም'); ?> *</label>
                            <input type="text" name="name" value="<?php echo e(post('name')); ?>" required class="w-full px-4 py-3 bg-slate-50 dark:bg-white/5 border border-black/5 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2"><?php echo get_text('Email', 'ኢሜይል'); ?> *</label>
                            <input type="email" name="email" value="<?php echo e(post('email')); ?>" required class="w-full px-4 py-3 bg-slate-50 dark:bg-white/5 border border-black/5 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2"><?php echo get_text('Phone', 'ስልክ'); ?> *</label>
                            <input type="tel" name="phone" value="<?php echo e(post('phone')); ?>" required placeholder="+251-9XX-XXXXXX" class="w-full px-4 py-3 bg-slate-50 dark:bg-white/5 border border-black/5 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2"><?php echo get_text('Your Role', 'ሚናዎ'); ?></label>
                            <select name="role" class="w-full px-4 py-3 bg-slate-50 dark:bg-white/5 border border-black/5 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all">
                                <option value=""><?php echo get_text('Select...', 'ይምረጡ...'); ?></option>
                                <option value="owner" <?php echo post('role') === 'owner' ? 'selected' : ''; ?>><?php echo get_text('Owner / Director', 'ባለቤት / ዳይሬክተር'); ?></option>
                                <option value="manager" <?php echo post('role') === 'manager' ? 'selected' : ''; ?>><?php echo get_text('Manager', 'ሥራ አስኪያጅ'); ?></option>
                                <option value="it" <?php echo post('role') === 'it' ? 'selected' : ''; ?>><?php echo get_text('IT Staff', 'የአይቲ ባለሙያ'); ?></option>
                                <option value="teacher" <?php echo post('role') === 'teacher' ? 'selected' : ''; ?>><?php echo get_text('Teacher', 'መምህር'); ?></option>
                                <option value="other" <?php echo post('role') === 'other' ? 'selected' : ''; ?>><?php echo get_text('Other', 'ሌላ'); ?></option>
                            </select>
                        </div>
                    </div>
                    
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-6 mt-8"><?php echo get_text('Organization Details', 'የድርጅት ዝርዝሮች'); ?></h3>
                    
                    <div class="grid sm:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2"><?php echo get_text('Organization Name', 'የድርጅት ስም'); ?></label>
                            <input type="text" name="company" value="<?php echo e(post('company')); ?>" class="w-full px-4 py-3 bg-slate-50 dark:bg-white/5 border border-black/5 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2"><?php echo get_text('Organization Size', 'የድርጅት መጠን'); ?></label>
                            <select name="company_size" class="w-full px-4 py-3 bg-slate-50 dark:bg-white/5 border border-black/5 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all">
                                <option value=""><?php echo get_text('Select...', 'ይምረጡ...'); ?></option>
                                <option value="1-10">1-10</option>
                                <option value="11-50">11-50</option>
                                <option value="51-200">51-200</option>
                                <option value="201-500">201-500</option>
                                <option value="500+">500+</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2"><?php echo get_text('Interested Product', 'ለሚፈልጉት ምርት'); ?> *</label>
                            <select name="product" required class="w-full px-4 py-3 bg-slate-50 dark:bg-white/5 border border-black/5 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all">
                                <option value=""><?php echo get_text('Select a product...', 'ምርት ይምረጡ...'); ?></option>
                                <?php foreach ($products as $p): ?>
                                <option value="<?php echo e($p['slug']); ?>" <?php echo post('product') === $p['slug'] ? 'selected' : ''; ?>><?php echo e(get_text($p['name_en'], $p['name_am'])); ?></option>
                                <?php endforeach; ?>
                                <option value="custom" <?php echo post('product') === 'custom' ? 'selected' : ''; ?>><?php echo get_text('Custom Solution', 'ብጁ መፍትሄ'); ?></option>
                            </select>
                        </div>
                    </div>
                    
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-6 mt-8"><?php echo get_text('Preferred Demo Time', 'ተመራጭ የዲሞ ጊዜ'); ?></h3>
                    
                    <div class="grid sm:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2"><?php echo get_text('Preferred Date', 'ተመራጭ ቀን'); ?></label>
                            <input type="date" name="preferred_date" value="<?php echo e(post('preferred_date')); ?>" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" class="w-full px-4 py-3 bg-slate-50 dark:bg-white/5 border border-black/5 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2"><?php echo get_text('Preferred Time', 'ተመራጭ ሰዓት'); ?></label>
                            <select name="preferred_time" class="w-full px-4 py-3 bg-slate-50 dark:bg-white/5 border border-black/5 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all">
                                <option value=""><?php echo get_text('Select...', 'ይምረጡ...'); ?></option>
                                <option value="morning">9:00 AM - 12:00 PM</option>
                                <option value="afternoon">2:00 PM - 5:00 PM</option>
                                <option value="flexible"><?php echo get_text('Flexible', 'ተለዋዋጭ'); ?></option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2"><?php echo get_text('Additional Notes', 'ተጨማሪ ማስታወሻዎች'); ?></label>
                        <textarea name="message" rows="4" class="w-full px-4 py-3 bg-slate-50 dark:bg-white/5 border border-black/5 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all resize-none" placeholder="<?php echo get_text('Tell us about your specific needs...', 'ስለ ልዩ ፍላጎቶችዎ ይንገሩን...'); ?>"><?php echo e(post('message')); ?></textarea>
                    </div>
                    
                    <button type="submit" class="w-full px-8 py-4 bg-primary hover:bg-blue-700 text-white font-semibold rounded-xl shadow-lg shadow-primary/25 hover:shadow-primary/40 transition-all duration-300 text-lg">
                        <?php echo get_text('Schedule Demo', 'ዲሞ ያቅዱ'); ?>
                    </button>
                </form>
            </div>
            
            <!-- Sidebar -->
            <div class="lg:col-span-2 space-y-6" data-animate="slide-up">
                <div class="bg-white dark:bg-white/[0.03] rounded-2xl border border-black/5 dark:border-white/5 p-6">
                    <h3 class="font-bold text-slate-800 dark:text-white mb-4"><?php echo get_text('What to Expect', 'ምን ይጠብቁ'); ?></h3>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0 mt-0.5"><span class="text-primary font-bold text-sm">1</span></div>
                            <div><div class="font-medium text-slate-800 dark:text-white text-sm"><?php echo get_text('Personalized Walkthrough', 'ግላዊ አጠቃቀም'); ?></div><div class="text-xs text-slate-400">30-45 min demo tailored to your needs</div></div>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0 mt-0.5"><span class="text-primary font-bold text-sm">2</span></div>
                            <div><div class="font-medium text-slate-800 dark:text-white text-sm"><?php echo get_text('Q&A Session', 'ጥያቄ እና መልስ'); ?></div><div class="text-xs text-slate-400">Ask anything about features, pricing, integration</div></div>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0 mt-0.5"><span class="text-primary font-bold text-sm">3</span></div>
                            <div><div class="font-medium text-slate-800 dark:text-white text-sm"><?php echo get_text('Custom Proposal', 'ብጁ ሐሳብ'); ?></div><div class="text-xs text-slate-400">Receive a tailored pricing proposal</div></div>
                        </li>
                    </ul>
                </div>
                
                <div class="bg-primary/10 rounded-2xl border border-primary/10 p-6">
                    <h3 class="font-bold text-slate-800 dark:text-white mb-3"><?php echo get_text('Prefer a Quick Chat?', 'ፈጣን ውይይት ይመርጣሉ?'); ?></h3>
                    <p class="text-sm text-slate-500 dark:text-gray-400 mb-4"><?php echo get_text('Call or WhatsApp us directly.', 'በቀጥታ ይደውሉልን ወይም ዋትስአፕ ያድርጉ።'); ?></p>
                    <a href="tel:<?php echo e(get_setting('phone_primary')); ?>" class="flex items-center gap-2 text-primary font-medium text-sm mb-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <?php echo e(get_setting('phone_primary')); ?>
                    </a>
                    <a href="https://wa.me/<?php echo e(str_replace(['+', '-', ' '], '', get_setting('whatsapp'))); ?>" class="flex items-center gap-2 text-green-600 font-medium text-sm">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        WhatsApp
                    </a>
                </div>
                
                <!-- Trust -->
                <div class="bg-white dark:bg-white/[0.03] rounded-2xl border border-black/5 dark:border-white/5 p-6">
                    <div class="flex items-center gap-3 mb-3">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span class="font-bold text-slate-800 dark:text-white text-sm"><?php echo get_text('No Commitment Required', 'ቁርጠኝነት አያስፈልግም'); ?></span>
                    </div>
                    <p class="text-xs text-slate-400">Free consultation, no strings attached. See the system in action before you decide.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>
