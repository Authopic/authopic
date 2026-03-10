<?php
/**
 * Authopic Technologies PLC - About Page (/about)
 */
if (!defined('BASE_PATH'))
    exit;

$page_title = get_text('About Us', 'ስለ እኛ');
$page_description = get_text('Learn about Authopic Technologies PLC - a leading software solutions company in Addis Ababa, Ethiopia.', 'ስለ ኦቶፒክ ቴክኖሎጂስ ፒኤልሲ - በአዲስ አበባ፣ ኢትዮጵያ ውስጥ ግንባር ቀደም የሶፍትዌር መፍትሄዎች ኩባንያ ይወቁ።');

// Team members
$team = db_fetch_all("SELECT * FROM `team_members` WHERE `is_active` = 1 ORDER BY `sort_order` ASC");

// Stats
$product_count = db_count("SELECT COUNT(*) FROM `products` WHERE `status` = 'published'");
$portfolio_count = db_count("SELECT COUNT(*) FROM `portfolio` WHERE `status` = 'published'");
$team_count = count($team);

require_once BASE_PATH . '/includes/header.php';
?>

<!-- Hero -->
<section class="relative pt-32 pb-20 overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute top-1/3 left-1/4 w-96 h-96 bg-primary/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-1/4 right-1/3 w-80 h-80 bg-secondary/10 rounded-full blur-3xl"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto text-center" data-animate="slide-up">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-primary/10 border border-primary/20 rounded-full text-sm text-primary font-medium mb-6">
                <?php echo get_text('About Authopic Technologies', 'ስለ ኦቶፒክ ቴክኖሎጂስ'); ?>
            </span>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-800 dark:text-white mb-6 leading-tight">
                <?php echo get_text('Powering Digital Transformation Through Innovation', 'በፈጠራ አማካኝነት ዲጂታል ለውጥን እንጋገራለን'); ?>
            </h1>
            <p class="text-xl text-slate-500 dark:text-gray-400 leading-relaxed">
                <?php echo get_text('We are a passionate team of developers, designers, and innovators dedicated to delivering world-class software solutions tailored to Ethiopian businesses.', 'ለኢትዮጵያ ንግዶች የተዘጋጁ ዓለም አቀፍ ደረጃ ያላቸውን የሶፍትዌር መፍትሄዎች ለማቅረብ የሚሠሩ ቀናዕ ገንቢዎች፣ ዲዛይነሮች እና ፈጣሪዎች ቡድን ነን።'); ?>
            </p>
        </div>
    </div>
</section>

<!-- Mission & Vision -->
<section class="py-20 bg-slate-50/50 dark:bg-white/[0.02]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-8">
            <div class="bg-white dark:bg-white/[0.03] rounded-2xl border border-black/5 dark:border-white/5 p-8 md:p-10" data-animate="slide-up">
                <div class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center mb-6">
                    <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white mb-4"><?php echo get_text('Our Mission', 'ተልዕኮአችን'); ?></h2>
                <p class="text-slate-500 dark:text-gray-400 leading-relaxed">
                    <?php echo get_text(
    'To empower businesses worldwide with innovative, reliable, and scalable web development, custom software, and digital transformation solutions that drive efficiency and growth.',
    'ኢትዮጵያውያን ተቋማትን እና ንግዶችን ቅልጥፍና፣ እድገት እና ዲጂታል ለውጥን የሚያንቀሳቅሱ ፈጠራ፣ አስተማማኝ እና ተመጣጣኝ ዋጋ ያላቸው የሶፍትዌር መፍትሄዎችን ለማበረታታት።'
); ?>
                </p>
            </div>
            <div class="bg-white dark:bg-white/[0.03] rounded-2xl border border-black/5 dark:border-white/5 p-8 md:p-10" data-animate="slide-up">
                <div class="w-14 h-14 rounded-2xl bg-secondary/10 flex items-center justify-center mb-6">
                    <svg class="w-7 h-7 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </div>
                <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white mb-4"><?php echo get_text('Our Vision', 'ራዕያችን'); ?></h2>
                <p class="text-slate-500 dark:text-gray-400 leading-relaxed">
                    <?php echo get_text(
    'To be a globally recognized technology company known for innovation, quality, and our commitment to transforming businesses through cutting-edge digital solutions.',
    'በምስራቅ አፍሪካ ግንባር ቀደም የሶፍትዌር መፍትሄዎች አቅራቢ መሆን፣ በፈጠራ፣ በጥራት እና ክልሉን በቴክኖሎጂ ለመቀየር ባለን ቁርጠኝነት የሚታወቅ።'
); ?>
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Values -->
<section class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16" data-animate="slide-up">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-800 dark:text-white mb-4"><?php echo get_text('Our Core Values', 'ዋና እሴቶቻችን'); ?></h2>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php
$values = [
    ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'title_en' => 'Quality First', 'title_am' => 'ጥራት ቅድሚያ', 'desc_en' => 'We never compromise on code quality, testing, and documentation.', 'desc_am' => 'በኮድ ጥራት፣ ሙከራ እና ዶክመንቴሽን ላይ ፈጽሞ ስምምነት አንደርግም።'],
    ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'title_en' => 'Client Partnership', 'title_am' => 'የደንበኛ ሽርክና', 'desc_en' => 'We treat our clients as partners, understanding their unique needs.', 'desc_am' => 'ደንበኞቻችንን ልዩ ፍላጎቶቻቸውን በመረዳት እንደ አጋሮች እንመለከታቸዋለን።'],
    ['icon' => 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z', 'title_en' => 'Innovation', 'title_am' => 'ፈጠራ', 'desc_en' => 'We stay at the forefront of technology to deliver cutting-edge solutions.', 'desc_am' => 'ዘመናዊ መፍትሄዎችን ለማቅረብ በቴክኖሎጂ ግንባር ቀደም እንቆማለን።'],
    ['icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z', 'title_en' => 'Local Impact', 'title_am' => 'የአካባቢ ተፅዕኖ', 'desc_en' => 'We are committed to making a positive impact in Ethiopia\'s tech ecosystem.', 'desc_am' => 'በኢትዮጵያ የቴክ ስነ-ምህዳር ውስጥ አዎንታዊ ተፅዕኖ ለማሳደር ቆርጠናል።']
];
foreach ($values as $val): ?>
            <div class="bg-white dark:bg-white/[0.03] rounded-2xl border border-black/5 dark:border-white/5 p-6 text-center hover:shadow-lg hover:-translate-y-1 transition-all duration-300" data-animate="slide-up">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-primary/10 flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo $val['icon']; ?>"/></svg>
                </div>
                <h3 class="font-bold text-slate-800 dark:text-white mb-2"><?php echo get_text($val['title_en'], $val['title_am']); ?></h3>
                <p class="text-sm text-slate-500 dark:text-gray-400"><?php echo get_text($val['desc_en'], $val['desc_am']); ?></p>
            </div>
            <?php
endforeach; ?>
        </div>
    </div>
</section>

<!-- Story Timeline -->
<section class="py-20 bg-slate-50/50 dark:bg-white/[0.02]">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-animate="slide-up">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-800 dark:text-white mb-4"><?php echo get_text('Our Journey', 'ጉዞአችን'); ?></h2>
        </div>
        <div class="relative">
            <div class="absolute left-4 md:left-1/2 top-0 bottom-0 w-0.5 bg-primary/20"></div>
            
            <?php
$milestones = [
    ['year' => '2023', 'title_en' => 'Founded', 'title_am' => 'ተመሠረተ', 'desc_en' => 'Authopic Technologies PLC was founded in Addis Ababa with a vision to transform Ethiopian education through technology.', 'desc_am' => 'ኦቶፒክ ቴክኖሎጂስ ፒኤልሲ በአዲስ አበባ ተቋቋመ።'],
    ['year' => '2023', 'title_en' => 'First SMS Client', 'title_am' => 'የመጀመሪያ SMS ደንበኛ', 'desc_en' => 'Deployed our School Management System to the first school, beginning our journey of empowering Ethiopian schools.', 'desc_am' => 'የመጀመሪያውን ትምህርት ቤት አስተዳደር ስርዓት ለትምህርት ቤት ተሰማራ።'],
    ['year' => '2024', 'title_en' => 'ERP Launch', 'title_am' => 'ERP ተጀመረ', 'desc_en' => 'Expanded into Enterprise Resource Planning, serving manufacturing, trading, and NGO sectors.', 'desc_am' => 'ወደ ኢንተርፕራይዝ ሪሶርስ ፕላኒንግ ተስፋፋ።'],
    ['year' => '2025', 'title_en' => 'Web Services', 'title_am' => 'ዌብ አገልግሎቶች', 'desc_en' => 'Added website and web application development services to our portfolio.', 'desc_am' => 'ድህረ ገጽ እና ዌብ አፕሊኬሽን ልማት አገልግሎቶችን ጨመረ።'],
    ['year' => '2026', 'title_en' => '50+ Schools', 'title_am' => '50+ ትምህርት ቤቶች', 'desc_en' => 'Now serving over 50 schools, 15 ERP clients, and numerous web development projects across Ethiopia.', 'desc_am' => 'አሁን ከ50 በላይ ትምህርት ቤቶችን፣ 15 ERP ደንበኞችን እያገለገለ ይገኛል።']
];
foreach ($milestones as $i => $ms):
    $is_right = $i % 2 === 0;
?>
            <div class="relative flex items-center mb-12 <?php echo $is_right ? 'md:flex-row' : 'md:flex-row-reverse'; ?>" data-animate="slide-up">
                <div class="absolute left-4 md:left-1/2 w-4 h-4 bg-primary rounded-full -translate-x-1/2 border-4 border-white dark:border-dark z-10"></div>
                <div class="ml-12 md:ml-0 md:w-1/2 <?php echo $is_right ? 'md:pr-12 md:text-right' : 'md:pl-12'; ?>">
                    <span class="inline-block px-3 py-1 bg-primary/10 text-primary text-sm font-bold rounded-full mb-2"><?php echo $ms['year']; ?></span>
                    <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2"><?php echo get_text($ms['title_en'], $ms['title_am']); ?></h3>
                    <p class="text-slate-500 dark:text-gray-400"><?php echo get_text($ms['desc_en'], $ms['desc_am']); ?></p>
                </div>
            </div>
            <?php
endforeach; ?>
        </div>
    </div>
</section>

<!-- Stats -->
<section class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php
$stats = [
    ['value' => '8+', 'label_en' => 'Years Experience', 'label_am' => 'ዓመታት ልምድ'],
    ['value' => '50+', 'label_en' => 'Schools Served', 'label_am' => 'ትምህርት ቤቶች'],
    ['value' => '80+', 'label_en' => 'Projects Delivered', 'label_am' => 'ፕሮጀክቶች'],
    ['value' => '20+', 'label_en' => 'Team Members', 'label_am' => 'የቡድን አባላት']
];
foreach ($stats as $stat): ?>
            <div class="text-center bg-white dark:bg-white/[0.03] rounded-2xl border border-black/5 dark:border-white/5 p-8" data-animate="slide-up">
                <div class="text-4xl font-extrabold text-primary mb-2" data-counter="<?php echo $stat['value']; ?>"><?php echo $stat['value']; ?></div>
                <div class="text-slate-500 dark:text-gray-400"><?php echo get_text($stat['label_en'], $stat['label_am']); ?></div>
            </div>
            <?php
endforeach; ?>
        </div>
    </div>
</section>

<!-- Team -->
<?php if (!empty($team)): ?>
<section class="py-20 bg-slate-50/50 dark:bg-white/[0.02]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16" data-animate="slide-up">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-800 dark:text-white mb-4"><?php echo get_text('Meet Our Team', 'ቡድናችንን ይወቁ'); ?></h2>
            <p class="text-lg text-slate-500 dark:text-gray-400">
                <?php echo get_text('The talented people behind Authopic Technologies PLC.', 'ከአናኖምስ ዴቭ ጀርባ ያሉ ተሰጥኦ ያላቸው ሰዎች።'); ?>
            </p>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($team as $member): ?>
            <div class="group bg-white dark:bg-white/[0.03] rounded-2xl border border-black/5 dark:border-white/5 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-500" data-animate="slide-up">
                <div class="aspect-square bg-primary/10 overflow-hidden">
                    <?php if ($member['photo']): ?>
                    <img src="<?php echo upload_url($member['photo']); ?>" alt="<?php echo e(get_text($member['name_en'], $member['name_am'])); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <?php
        else: ?>
                    <div class="w-full h-full flex items-center justify-center text-6xl font-bold text-primary/20"><?php echo strtoupper(substr($member['name_en'] ?? '?', 0, 1)); ?></div>
                    <?php
        endif; ?>
                </div>
                <div class="p-6 text-center">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white"><?php echo e(get_text($member['name_en'], $member['name_am'])); ?></h3>
                    <p class="text-primary text-sm font-medium mb-2"><?php echo e(get_text($member['position_en'], $member['position_am'])); ?></p>
                    <p class="text-sm text-slate-500 dark:text-gray-400 mb-4"><?php echo e(get_text($member['bio_en'] ?? '', $member['bio_am'] ?? '')); ?></p>
                    
                    <?php if (!empty($member['linkedin']) || !empty($member['email'])): ?>
                    <div class="flex items-center justify-center gap-3">
                        <?php if (!empty($member['linkedin'])): ?>
                        <a href="<?php echo e($member['linkedin']); ?>" target="_blank" class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 dark:bg-white/5 text-slate-400 hover:bg-primary hover:text-white transition-all">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                        </a>
                        <?php
            endif; ?>
                        <?php if (!empty($member['email'])): ?>
                        <a href="mailto:<?php echo e($member['email']); ?>" class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 dark:bg-white/5 text-slate-400 hover:bg-primary hover:text-white transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </a>
                        <?php
            endif; ?>
                    </div>
                    <?php
        endif; ?>
                </div>
            </div>
            <?php
    endforeach; ?>
        </div>
    </div>
</section>
<?php
endif; ?>

<!-- CTA -->
<section class="py-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative rounded-3xl overflow-hidden" data-animate="slide-up">
            <div class="absolute inset-0 bg-primary hover:bg-blue-700"></div>
            <div class="relative p-12 md:p-16 text-center">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-4">
                    <?php echo get_text('Want to Join Our Team?', 'ቡድናችንን መቀላቀል ይፈልጋሉ?'); ?>
                </h2>
                <p class="text-white/80 text-lg mb-8 max-w-2xl mx-auto">
                    <?php echo get_text('We are always looking for talented developers, designers, and innovators. Send us your resume!', 'ተሰጥኦ ያላቸው ገንቢዎችን፣ ዲዛይነሮችን እና ፈጣሪዎችን ሁልጊዜ እንፈልጋለን። የህይወት ታሪክዎን ይላኩልን!'); ?>
                </p>
                <a href="<?php echo url('/contact'); ?>" class="inline-flex items-center gap-2 px-8 py-4 bg-white text-primary font-semibold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300">
                    <?php echo get_text('Get in Touch', 'ያግኙን'); ?>
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>
