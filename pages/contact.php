<?php
// Developed by Yisak A. Alemayehu (yisak.dev)
/**
 * Authopic Technologies PLC - Contact Page (/contact)
 */
if (!defined('BASE_PATH')) exit;

$page_title = get_text('Contact Us', 'ያግኙን');
$page_description = get_text('Get in touch with Authopic Technologies PLC. We\'d love to hear from you.', 'አናኖምስ ዴቭን ያግኙ። ከእርስዎ ለመስማት እንፈልጋለን።');

// Handle form submission
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify(post('csrf_token'))) {
        $errors[] = 'Invalid request. Please try again.';
    } elseif (is_spam_honeypot('website_url_hp')) {
        $errors[] = 'Spam detected.';
    } elseif (!rate_limit_check('contact_form', 3, 3600)) {
        $errors[] = get_text('Too many submissions. Please try again later.', 'ብዛት ያላቸው ማስገቢያዎች። እባክዎ ቆይተው ይሞክሩ።');
    } else {
        $name = trim(post('name'));
        $email = trim(post('email'));
        $phone = trim(post('phone'));
        $company = trim(post('company'));
        $subject = trim(post('subject'));
        $message = trim(post('message'));

        if (empty($name)) $errors[] = get_text('Name is required.', 'ስም ያስፈልጋል።');
        if (!is_valid_email($email)) $errors[] = get_text('Valid email is required.', 'ትክክለኛ ኢሜይል ያስፈልጋል።');
        if (!is_valid_phone($phone)) $errors[] = get_text('Valid phone number is required.', 'ትክክለኛ ስልክ ቁጥር ያስፈልጋል።');
        if (empty($message)) $errors[] = get_text('Message is required.', 'መልዕክት ያስፈልጋል።');

        if (empty($errors)) {
            $name = db_escape($name);
            $email_esc = db_escape($email);
            $phone = db_escape($phone);
            $company = db_escape($company);
            $subject = db_escape($subject);
            $message_esc = db_escape($message);

            db_query("INSERT INTO `leads` (`name`, `email`, `phone`, `company`, `message`, `source`, `created_at`) 
                VALUES ('$name', '$email_esc', '$phone', '$company', '$message_esc', 'contact-form', NOW())");

            notify_admin('New Contact Form Submission', "Name: $name\nEmail: $email\nPhone: $phone\nCompany: $company\nSubject: $subject\nMessage: $message");
            
            redirect('/thank-you/contact');
        }
    }
}

require_once BASE_PATH . '/includes/header.php';
?>

<!-- Hero -->
<section class="relative pt-32 pb-16 overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute top-1/3 right-1/4 w-96 h-96 bg-primary/10 rounded-full blur-3xl"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center" data-animate="slide-up">
        <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-800 dark:text-white mb-4">
            <?php echo get_text('Get in Touch', 'ያግኙን'); ?>
        </h1>
        <p class="text-xl text-slate-500 dark:text-gray-400 max-w-2xl mx-auto">
            <?php echo get_text('Have a project in mind? Let\'s bring it to life together.', 'ፕሮጀክት በአእምሮዎ ውስጥ አለ? በጋራ ወደ ህይወት እናምጣው።'); ?>
        </p>
    </div>
</section>

<!-- Contact Info Cards -->
<section class="pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-white/[0.03] rounded-2xl border border-black/5 dark:border-white/5 p-6 text-center hover:shadow-lg hover:-translate-y-1 transition-all duration-300" data-animate="slide-up">
                <div class="w-12 h-12 mx-auto rounded-xl bg-primary/10 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="font-bold text-slate-800 dark:text-white mb-1"><?php echo get_text('Visit Us', 'ይጎብኙን'); ?></h3>
                <p class="text-sm text-slate-500 dark:text-gray-400"><?php echo e(get_setting('address')); ?></p>
            </div>
            <div class="bg-white dark:bg-white/[0.03] rounded-2xl border border-black/5 dark:border-white/5 p-6 text-center hover:shadow-lg hover:-translate-y-1 transition-all duration-300" data-animate="slide-up">
                <div class="w-12 h-12 mx-auto rounded-xl bg-secondary/10 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                </div>
                <h3 class="font-bold text-slate-800 dark:text-white mb-1"><?php echo get_text('Call Us', 'ይደውሉልን'); ?></h3>
                <p class="text-sm text-slate-500 dark:text-gray-400">
                    <a href="tel:<?php echo e(get_setting('phone_primary')); ?>" class="hover:text-primary transition-colors"><?php echo e(get_setting('phone_primary')); ?></a>
                </p>
            </div>
            <div class="bg-white dark:bg-white/[0.03] rounded-2xl border border-black/5 dark:border-white/5 p-6 text-center hover:shadow-lg hover:-translate-y-1 transition-all duration-300" data-animate="slide-up">
                <div class="w-12 h-12 mx-auto rounded-xl bg-green-500/10 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="font-bold text-slate-800 dark:text-white mb-1"><?php echo get_text('Email Us', 'ኢሜይል ይላኩ'); ?></h3>
                <p class="text-sm text-slate-500 dark:text-gray-400">
                    <a href="mailto:<?php echo e(get_setting('email_primary')); ?>" class="hover:text-primary transition-colors"><?php echo e(get_setting('email_primary')); ?></a>
                </p>
            </div>
            <div class="bg-white dark:bg-white/[0.03] rounded-2xl border border-black/5 dark:border-white/5 p-6 text-center hover:shadow-lg hover:-translate-y-1 transition-all duration-300" data-animate="slide-up">
                <div class="w-12 h-12 mx-auto rounded-xl bg-amber-500/10 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="font-bold text-slate-800 dark:text-white mb-1"><?php echo get_text('Office Hours', 'የስራ ሰዓት'); ?></h3>
                <p class="text-sm text-slate-500 dark:text-gray-400"><?php echo e(get_setting('office_hours')); ?></p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form & Map -->
<section class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12">
            <!-- Form -->
            <div class="bg-white dark:bg-white/[0.03] rounded-2xl border border-black/5 dark:border-white/5 p-8 md:p-10" data-animate="slide-up">
                <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white mb-6"><?php echo get_text('Send us a Message', 'መልዕክት ይላኩልን'); ?></h2>
                
                <?php if (!empty($errors)): ?>
                <div class="bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-xl p-4 mb-6">
                    <?php foreach ($errors as $error): ?>
                    <p class="text-sm text-red-600 dark:text-red-400"><?php echo e($error); ?></p>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <form method="POST" action="<?php echo url('/contact'); ?>" id="contactForm">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                    <input type="text" name="website_url_hp" class="hidden" tabindex="-1" autocomplete="off">
                    
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
                            <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2"><?php echo get_text('Company', 'ኩባንያ'); ?></label>
                            <input type="text" name="company" value="<?php echo e(post('company')); ?>" class="w-full px-4 py-3 bg-slate-50 dark:bg-white/5 border border-black/5 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all">
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2"><?php echo get_text('Subject', 'ርዕሰ ጉዳይ'); ?></label>
                        <select name="subject" class="w-full px-4 py-3 bg-slate-50 dark:bg-white/5 border border-black/5 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all">
                            <option value=""><?php echo get_text('Select a topic...', 'ርዕስ ይምረጡ...'); ?></option>
                            <option value="general" <?php echo post('subject') === 'general' ? 'selected' : ''; ?>><?php echo get_text('General Inquiry', 'አጠቃላይ ጥያቄ'); ?></option>
                            <option value="sms" <?php echo post('subject') === 'sms' ? 'selected' : ''; ?>>School Management System</option>
                            <option value="erp" <?php echo post('subject') === 'erp' ? 'selected' : ''; ?>>ERP System</option>
                            <option value="web-dev" <?php echo post('subject') === 'web-dev' ? 'selected' : ''; ?>><?php echo get_text('Website Development', 'ድህረ ገጽ ልማት'); ?></option>
                            <option value="support" <?php echo post('subject') === 'support' ? 'selected' : ''; ?>><?php echo get_text('Technical Support', 'ቴክኒካል ድጋፍ'); ?></option>
                            <option value="career" <?php echo post('subject') === 'career' ? 'selected' : ''; ?>><?php echo get_text('Career / Jobs', 'ስራ'); ?></option>
                            <option value="partnership" <?php echo post('subject') === 'partnership' ? 'selected' : ''; ?>><?php echo get_text('Partnership', 'ሽርክና'); ?></option>
                        </select>
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2"><?php echo get_text('Message', 'መልዕክት'); ?> *</label>
                        <textarea name="message" rows="5" required class="w-full px-4 py-3 bg-slate-50 dark:bg-white/5 border border-black/5 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all resize-none" placeholder="<?php echo get_text('Tell us about your project or question...', 'ስለ ፕሮጀክትዎ ወይም ጥያቄዎ ይንገሩን...'); ?>"><?php echo e(post('message')); ?></textarea>
                    </div>
                    
                    <button type="submit" class="w-full px-8 py-4 bg-primary hover:bg-blue-700 text-white font-semibold rounded-xl shadow-lg shadow-primary/25 hover:shadow-primary/40 transition-all duration-300 text-lg">
                        <?php echo get_text('Send Message', 'መልዕክት ላክ'); ?>
                        <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    </button>
                </form>
            </div>
            
            <!-- Map -->
            <div data-animate="slide-up">
                <div class="rounded-2xl overflow-hidden border border-black/5 dark:border-white/5 h-full min-h-[400px]">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3940.5!2d38.7468!3d9.0227!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2sAddis+Ababa!5e0!3m2!1sen!2set!4v1" 
                        width="100%" 
                        height="100%" 
                        style="border:0; min-height: 400px;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
                
                <!-- Social Links -->
                <div class="mt-6 bg-white dark:bg-white/[0.03] rounded-2xl border border-black/5 dark:border-white/5 p-6">
                    <h3 class="font-bold text-slate-800 dark:text-white mb-4"><?php echo get_text('Follow Us', 'ይከተሉን'); ?></h3>
                    <div class="flex items-center gap-3">
                        <?php 
                        $socials = [
                            'facebook' => ['icon' => 'M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z'],
                            'telegram' => ['icon' => 'M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.479.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z'],
                            'linkedin' => ['icon' => 'M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z'],
                            'instagram' => ['icon' => 'M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678a6.162 6.162 0 100 12.324 6.162 6.162 0 100-12.324zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405a1.441 1.441 0 11-2.882 0 1.441 1.441 0 012.882 0z']
                        ];
                        foreach ($socials as $platform => $data): 
                            $social_url = get_setting('social_' . $platform);
                            if (!$social_url) continue;
                        ?>
                        <a href="<?php echo e($social_url); ?>" target="_blank" class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-100 dark:bg-white/5 text-slate-400 hover:bg-primary hover:text-white transition-all duration-300">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="<?php echo $data['icon']; ?>"/></svg>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>
