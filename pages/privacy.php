<?php
// Developed by Yisak A. Alemayehu (yisak.dev)
/**
 * Authopic Technologies PLC - Privacy Policy Page
 */
if (!defined('BASE_PATH'))
    exit;

$page_title = get_text('Privacy Policy', 'የግላዊነት ፖሊሲ');
require_once BASE_PATH . '/includes/header.php';
$company = get_setting('company_name') ?: 'Authopic Technologies PLC';
?>

<section class="relative py-16 bg-slate-50 dark:bg-slate-900">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-800 dark:text-white mb-4">
            <?php echo get_text('Privacy Policy', 'የግላዊነት ፖሊሲ'); ?>
        </h1>
        <p class="text-slate-500 dark:text-gray-400"><?php echo get_text('Last updated: January 2025', 'መጨረሻ የተዘመነው: ጃንዋሪ 2025'); ?></p>
    </div>
</section>

<section class="py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <div class="prose prose-lg dark:prose-invert prose-headings:text-slate-800 dark:prose-headings:text-white prose-a:text-primary max-w-none
                    bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 rounded-3xl p-8 sm:p-12">

            <h2><?php echo get_text('1. Introduction', '1. መግቢያ'); ?></h2>
            <p><?php echo get_text(
    $company . ' ("we", "our", "us") respects your privacy and is committed to protecting your personal data. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website or use our services.',
    $company . ' ("እኛ") ግላዊነትዎን ያከብራል እና የግል መረጃዎን ለመጠበቅ ቁርጠኛ ነው። ይህ የግላዊነት ፖሊሲ ድህረ ገጻችንን ሲጎበኙ ወይም አገልግሎቶቻችንን ሲጠቀሙ መረጃዎን እንዴት እንደምንሰበስብ፣ እንደምንጠቀም እና እንደምንጠብቅ ያብራራል።'
); ?></p>

            <h2><?php echo get_text('2. Information We Collect', '2. የምንሰበስበው መረጃ'); ?></h2>
            <h3><?php echo get_text('Personal Information', 'የግል መረጃ'); ?></h3>
            <p><?php echo get_text('When you interact with us, we may collect:', 'ከኛ ጋር ሲገናኙ የሚከተሉትን ልንሰበስብ እንችላለን:'); ?></p>
            <ul>
                <li><?php echo get_text('Name and contact information (email, phone number)', 'ስም እና የመገናኛ መረጃ (ኢሜይል፣ ስልክ)'); ?></li>
                <li><?php echo get_text('Organization name and job title', 'የድርጅት ስም እና የሥራ ማዕረግ'); ?></li>
                <li><?php echo get_text('Messages and inquiries you send us', 'የሚልኩልን መልዕክቶች'); ?></li>
                <li><?php echo get_text('Demo request details', 'የዴሞ ጥያቄ ዝርዝሮች'); ?></li>
            </ul>

            <h3><?php echo get_text('Automatically Collected Information', 'በራስ-ሰር የተሰበሰበ መረጃ'); ?></h3>
            <ul>
                <li><?php echo get_text('Browser type and version', 'የአሳሽ ዓይነት እና ስሪት'); ?></li>
                <li><?php echo get_text('Device type and operating system', 'የመሣሪያ ዓይነት'); ?></li>
                <li><?php echo get_text('Pages visited and time spent on our website', 'የተጎበኙ ገጾች'); ?></li>
                <li><?php echo get_text('IP address (anonymized for analytics)', 'IP አድራሻ (ለትንታኔ ስም-አልባ)'); ?></li>
            </ul>

            <h2><?php echo get_text('3. How We Use Your Information', '3. መረጃዎን እንዴት እንጠቀማለን'); ?></h2>
            <ul>
                <li><?php echo get_text('To respond to your inquiries and provide customer support', 'ለጥያቄዎችዎ ምላሽ ለመስጠት'); ?></li>
                <li><?php echo get_text('To process demo requests and service inquiries', 'የዴሞ ጥያቄዎችን ለማስተናገድ'); ?></li>
                <li><?php echo get_text('To send newsletters (only with your explicit consent)', 'ጋዜጣ ለመላክ (በእርስዎ ፈቃድ ብቻ)'); ?></li>
                <li><?php echo get_text('To improve our website and services', 'ድህረ ገጻችንን እና አገልግሎቶቻችንን ለማሻሻል'); ?></li>
                <li><?php echo get_text('To protect against fraud and abuse', 'ከማጭበርበር ለመጠበቅ'); ?></li>
            </ul>

            <h2><?php echo get_text('4. Data Sharing', '4. የመረጃ ማጋራት'); ?></h2>
            <p><?php echo get_text(
    'We do not sell, trade, or rent your personal information to third parties. We may share your data only with trusted service providers who assist in operating our website or serving you, subject to confidentiality agreements.',
    'የግል መረጃዎን ለሶስተኛ ወገኖች አንሸጥም፣ አንለውጥም ወይም አንከራይም። ድህረ ገጻችንን በማስኬድ ወይም እርስዎን በማገልገል ላይ ለሚረዱ ታማኝ የአገልግሎት አቅራቢዎች ብቻ ልናጋራ እንችላለን።'
); ?></p>

            <h2><?php echo get_text('5. Data Security', '5. የመረጃ ደህንነት'); ?></h2>
            <p><?php echo get_text(
    'We implement appropriate technical and organizational measures to protect your personal data, including encryption, secure servers, and access controls. However, no method of transmission over the Internet is 100% secure.',
    'ምስጠራን፣ ደህንነቱ የተጠበቁ ሰርቨሮችን እና የመዳረሻ ቁጥጥሮችን ጨምሮ የግል መረጃዎን ለመጠበቅ ተገቢ ቴክኒካዊ እና ድርጅታዊ እርምጃዎችን እንተገብራለን።'
); ?></p>

            <h2><?php echo get_text('6. Your Rights', '6. መብቶችዎ'); ?></h2>
            <ul>
                <li><?php echo get_text('Access your personal data', 'የግል መረጃዎን ማግኘት'); ?></li>
                <li><?php echo get_text('Request correction of inaccurate data', 'ትክክል ያልሆነ መረጃ እንዲስተካከል መጠየቅ'); ?></li>
                <li><?php echo get_text('Request deletion of your data', 'መረጃዎ እንዲሰረዝ መጠየቅ'); ?></li>
                <li><?php echo get_text('Unsubscribe from newsletters at any time', 'በማንኛውም ጊዜ ከጋዜጣ ምዝገባ መውጣት'); ?></li>
            </ul>

            <h2><?php echo get_text('7. Cookies', '7. ኩኪዎች'); ?></h2>
            <p><?php echo get_text(
    'Our website uses essential cookies to ensure proper functionality, such as session management and security tokens. We do not use third-party tracking cookies.',
    'ድህረ ገጻችን ለትክክለኛ ተግባር አስፈላጊ የሆኑ ኩኪዎችን ይጠቀማል። የሶስተኛ ወገን ክትትል ኩኪዎችን አንጠቀምም።'
); ?></p>

            <h2><?php echo get_text('8. Contact Us', '8. ያግኙን'); ?></h2>
            <p><?php echo get_text(
    'If you have questions about this Privacy Policy or wish to exercise your rights, please contact us:',
    'ስለዚህ የግላዊነት ፖሊሲ ጥያቄዎች ካሉዎት ያግኙን:'
); ?></p>
            <ul>
                <li><strong><?php echo get_text('Email', 'ኢሜይል'); ?>:</strong> <?php echo e(get_setting('contact_email') ?: 'info@authopic.com'); ?></li>
                <li><strong><?php echo get_text('Phone', 'ስልክ'); ?>:</strong> <?php echo e(get_setting('contact_phone') ?: '+251 91 234 5678'); ?></li>
                <li><strong><?php echo get_text('Address', 'አድራሻ'); ?>:</strong> <?php echo e(get_setting('address') ?: 'Addis Ababa, Ethiopia'); ?></li>
            </ul>
        </div>
    </div>
</section>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>
