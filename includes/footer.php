<?php
/**
 * Authopic Technologies PLC - Footer Template
 */
if (!defined('BASE_PATH'))
    exit;

$footer_nav = get_nav_menu('footer');
$year = date('Y');
?>
    </main>

    <!-- ============================================ -->
    <!-- FOOTER -->
    <!-- ============================================ -->
    <footer class="relative bg-slate-50 dark:bg-[#0B132B] border-t border-black/5 dark:border-white/5">
        
        <!-- Top border accent -->
        <div class="absolute top-0 left-0 right-0 h-px bg-primary/30"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-8">
            
            <!-- Footer Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
                
                <!-- Company Info -->
                <div class="lg:col-span-1">
                    <a href="<?php echo url('/'); ?>" class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center text-white font-bold text-lg">A</div>
                        <span class="text-xl font-bold text-primary">Authopic Technologies</span>
                    </a>
                    <p class="text-slate-500 dark:text-gray-400 text-sm leading-relaxed mb-6">
                        <?php echo get_text(
    'A modern technology company focused on delivering powerful digital solutions. Web development, custom software, and cutting-edge digital technologies for startups, businesses, and enterprises.',
    'ዘመናዊ ቴክኖሎጂ ኩባንያ ኃይለኛ ዲጂታል መፍትሔዎችን ለማቅረብ ያተኮረ። ለስታርትአፖች፣ ንግዶች እና ኢንተርፕራይዞች የዌብ ልማት፣ ብጁ ሶፍትዌር እና ዘመናዊ ዲጂታል ቴክኖሎጂዎች።'
); ?>
                    </p>
                    
                    <!-- Social Links -->
                    <div class="flex items-center gap-3">
                        <?php if ($linkedin = get_setting('social_linkedin')): ?>
                        <a href="<?php echo e($linkedin); ?>" target="_blank" rel="noopener" class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-white/5 border border-black/5 dark:border-white/5 flex items-center justify-center text-slate-400 dark:text-gray-400 hover:text-primary dark:hover:text-primary hover:border-primary/30 hover:bg-primary/5 transition-all duration-300">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                        </a>
                        <?php
endif; ?>
                        
                        <?php if ($telegram = get_setting('social_telegram')): ?>
                        <a href="<?php echo e($telegram); ?>" target="_blank" rel="noopener" class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-white/5 border border-black/5 dark:border-white/5 flex items-center justify-center text-slate-400 dark:text-gray-400 hover:text-primary dark:hover:text-primary hover:border-primary/30 hover:bg-primary/5 transition-all duration-300">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                        </a>
                        <?php
endif; ?>

                        <?php if ($fb = get_setting('social_facebook')): ?>
                        <a href="<?php echo e($fb); ?>" target="_blank" rel="noopener" class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-white/5 border border-black/5 dark:border-white/5 flex items-center justify-center text-slate-400 dark:text-gray-400 hover:text-primary dark:hover:text-primary hover:border-primary/30 hover:bg-primary/5 transition-all duration-300">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <?php
endif; ?>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h4 class="text-sm font-semibold text-slate-800 dark:text-gray-200 uppercase tracking-wider mb-6">
                        <?php echo get_text('Quick Links', 'ፈጣን ማስፋፊያዎች'); ?>
                    </h4>
                    <ul class="space-y-3">
                        <li><a href="<?php echo url('/about'); ?>" class="text-sm text-slate-500 dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors"><?php echo get_text('About Us', 'ስለ እኛ'); ?></a></li>
                        <li><a href="<?php echo url('/portfolio'); ?>" class="text-sm text-slate-500 dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors"><?php echo get_text('Portfolio', 'ፖርትፎሊዮ'); ?></a></li>
                        <li><a href="<?php echo url('/insights'); ?>" class="text-sm text-slate-500 dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors"><?php echo get_text('Blog & Insights', 'ብሎግ እና ግንዛቤዎች'); ?></a></li>
                        <li><a href="<?php echo url('/contact'); ?>" class="text-sm text-slate-500 dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors"><?php echo get_text('Contact Us', 'ያግኙን'); ?></a></li>
                        <li><a href="<?php echo url('/request-demo'); ?>" class="text-sm text-slate-500 dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors"><?php echo get_text('Request Demo', 'ዲሞ ይጠይቁ'); ?></a></li>
                    </ul>
                </div>
                
                <!-- Products & Services -->
                <div>
                    <h4 class="text-sm font-semibold text-slate-800 dark:text-gray-200 uppercase tracking-wider mb-6">
                        <?php echo get_text('Solutions', 'መፍትሔዎች'); ?>
                    </h4>
                    <ul class="space-y-3">
                        <li><a href="<?php echo url('/products/sms'); ?>" class="text-sm text-slate-500 dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors"><?php echo get_text('School Management', 'የትምህርት ቤት አስተዳደር'); ?></a></li>
                        <li><a href="<?php echo url('/products/erp'); ?>" class="text-sm text-slate-500 dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors"><?php echo get_text('ERP System', 'ኢአርፒ ሲስተም'); ?></a></li>
                        <li><a href="<?php echo url('/services/website-development'); ?>" class="text-sm text-slate-500 dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors"><?php echo get_text('Website Development', 'ዌብሳይት ልማት'); ?></a></li>
                        <li><a href="<?php echo url('/services/web-application-development'); ?>" class="text-sm text-slate-500 dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors"><?php echo get_text('Web App Development', 'ዌብ አፕ ልማት'); ?></a></li>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div>
                    <h4 class="text-sm font-semibold text-slate-800 dark:text-gray-200 uppercase tracking-wider mb-6">
                        <?php echo get_text('Contact', 'ያግኙን'); ?>
                    </h4>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-primary mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span class="text-sm text-slate-500 dark:text-gray-400"><?php echo e(get_setting('site_address', 'Bole Road, Addis Ababa, Ethiopia')); ?></span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <a href="tel:<?php echo e(get_setting('site_phone')); ?>" class="text-sm text-slate-500 dark:text-gray-400 hover:text-primary transition-colors"><?php echo e(get_setting('site_phone', '+251-904-455302')); ?></a>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <a href="mailto:<?php echo e(get_setting('site_email')); ?>" class="text-sm text-slate-500 dark:text-gray-400 hover:text-primary transition-colors"><?php echo e(get_setting('site_email', 'info@authopic.com')); ?></a>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-sm text-slate-500 dark:text-gray-400">
                                <?php echo get_text('Mon-Fri: ' . get_setting('office_hours_weekday', '8:30 AM - 5:30 PM'), 'ሰኞ-ዓርብ: ' . get_setting('office_hours_weekday', '8:30 AM - 5:30 PM')); ?>
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Newsletter -->
            <div class="border-t border-black/5 dark:border-white/5 pt-8 mb-8">
                <div class="max-w-xl mx-auto text-center">
                    <h4 class="text-lg font-semibold text-slate-800 dark:text-gray-200 mb-2"><?php echo get_text('Stay Updated', 'ለዘመኑ ተከታተሉ'); ?></h4>
                    <p class="text-sm text-slate-500 dark:text-gray-400 mb-4"><?php echo get_text('Subscribe to our newsletter for the latest updates and insights.', 'ለቅርብ ዜናዎች እና ግንዛቤዎች ኢሜይልዎን ያስገቡ።'); ?></p>
                    <form id="newsletterForm" class="flex gap-2 max-w-md mx-auto" onsubmit="return handleNewsletter(event)">
                        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                        <input type="email" name="email" required placeholder="<?php echo get_text('Your email address', 'ኢሜይልዎ'); ?>" class="flex-1 px-4 py-3 bg-slate-100 dark:bg-white/5 border border-black/5 dark:border-white/10 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all placeholder:text-slate-400 dark:placeholder:text-gray-500">
                        <button type="submit" class="px-6 py-3 bg-primary hover:bg-blue-700 text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:shadow-primary/25 transition-all duration-300">
                            <?php echo get_text('Subscribe', 'ይመዝገቡ'); ?>
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Bottom Bar -->
            <div class="border-t border-black/5 dark:border-white/5 pt-8 flex flex-col md:flex-row items-center justify-between gap-4">
                <p class="text-sm text-slate-400 dark:text-gray-400">
                    &copy; <?php echo $year; ?> <?php echo e(get_setting('site_name', 'Authopic Technologies PLC')); ?>. <?php echo get_text('All rights reserved.', 'ሁሉም መብቶች የተጠበቁ ናቸው።'); ?>
                </p>
                <div class="flex items-center gap-4 text-sm text-slate-400 dark:text-gray-400">
                    <a href="<?php echo url('/privacy'); ?>" class="hover:text-primary transition-colors"><?php echo get_text('Privacy Policy', 'የግላዊነት ፖሊሲ'); ?></a>
                    <span class="text-slate-300 dark:text-gray-700">|</span>
                    <span><?php echo get_text('Made with', 'የተሠራ በ'); ?> <span class="text-red-500">❤</span></span>
                </div>
            </div>
        </div>
    </footer>

    <!-- WhatsApp Floating Button -->
    <?php $whatsapp = get_setting('social_whatsapp');
if ($whatsapp): ?>
    <a href="https://wa.me/<?php echo e(preg_replace('/[^0-9]/', '', $whatsapp)); ?>" target="_blank" rel="noopener" class="fixed bottom-6 right-6 z-40 w-14 h-14 bg-green-500 rounded-full flex items-center justify-center text-white shadow-lg shadow-green-500/30 hover:shadow-green-500/50 hover:scale-110 transition-all duration-300 group" aria-label="Chat on WhatsApp">
        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        <span class="absolute -top-10 right-0 bg-gray-800 text-white text-xs px-3 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">Chat with us</span>
    </a>
    <?php
endif; ?>

    <!-- PWA Install Prompt - Bottom Sheet -->
    <div id="pwa-install-prompt" class="fixed inset-0 z-[100] flex items-end justify-center pointer-events-none opacity-0 transition-opacity duration-300" style="display:none">
        <!-- Backdrop -->
        <div id="pwa-install-backdrop" class="absolute inset-0 bg-black/40 backdrop-blur-sm pointer-events-auto"></div>
        <!-- Sheet -->
        <div id="pwa-install-sheet" class="relative w-full max-w-lg mx-4 mb-4 sm:mb-8 bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-black/5 dark:border-white/10 pointer-events-auto transform translate-y-full transition-transform duration-300 ease-out overflow-hidden">
            <!-- Handle bar -->
            <div class="flex justify-center pt-3 pb-1"><div class="w-10 h-1 rounded-full bg-slate-300 dark:bg-slate-600"></div></div>
            <!-- Content -->
            <div class="px-6 pb-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-14 h-14 rounded-2xl bg-primary flex items-center justify-center shadow-lg shadow-primary/20 flex-shrink-0">
                        <span class="text-white font-black text-xl">A</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white">Install Authopic Technologies</h3>
                        <p class="text-sm text-slate-500 dark:text-gray-400">authopic.com</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 mb-5 p-3 bg-slate-50 dark:bg-white/5 rounded-xl">
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <p class="text-xs text-slate-600 dark:text-gray-300">Works offline &bull; Fast access from home screen &bull; No app store needed</p>
                </div>
                <div class="flex gap-3">
                    <button id="pwa-install-dismiss" class="flex-1 px-4 py-3 text-sm font-semibold text-slate-600 dark:text-gray-300 bg-slate-100 dark:bg-white/5 hover:bg-slate-200 dark:hover:bg-white/10 rounded-xl transition-colors">Not Now</button>
                    <button id="pwa-install-confirm" class="flex-1 px-4 py-3 text-sm font-semibold text-white bg-primary hover:bg-blue-700 rounded-xl shadow-lg shadow-primary/25 transition-all">Install App</button>
                </div>
            </div>
        </div>
    </div>

    <!-- iOS Install Instructions -->
    <div id="pwa-ios-prompt" class="fixed inset-0 z-[100] flex items-end justify-center pointer-events-none opacity-0 transition-opacity duration-300" style="display:none">
        <div id="pwa-ios-backdrop" class="absolute inset-0 bg-black/40 backdrop-blur-sm pointer-events-auto"></div>
        <div id="pwa-ios-sheet" class="relative w-full max-w-lg mx-4 mb-4 sm:mb-8 bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-black/5 dark:border-white/10 pointer-events-auto transform translate-y-full transition-transform duration-300 ease-out overflow-hidden">
            <div class="flex justify-center pt-3 pb-1"><div class="w-10 h-1 rounded-full bg-slate-300 dark:bg-slate-600"></div></div>
            <div class="px-6 pb-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-14 h-14 rounded-2xl bg-primary flex items-center justify-center shadow-lg shadow-primary/20 flex-shrink-0">
                        <span class="text-white font-black text-xl">A</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white">Install Authopic Technologies</h3>
                        <p class="text-sm text-slate-500 dark:text-gray-400">Add to your home screen</p>
                    </div>
                </div>
                <ol class="space-y-3 mb-5">
                    <li class="flex items-center gap-3 text-sm text-slate-600 dark:text-gray-300">
                        <span class="w-7 h-7 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-xs flex-shrink-0">1</span>
                        Tap the <svg class="inline w-5 h-5 text-primary mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg> Share button
                    </li>
                    <li class="flex items-center gap-3 text-sm text-slate-600 dark:text-gray-300">
                        <span class="w-7 h-7 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-xs flex-shrink-0">2</span>
                        Scroll down and tap <strong class="text-slate-800 dark:text-white">"Add to Home Screen"</strong>
                    </li>
                    <li class="flex items-center gap-3 text-sm text-slate-600 dark:text-gray-300">
                        <span class="w-7 h-7 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-xs flex-shrink-0">3</span>
                        Tap <strong class="text-slate-800 dark:text-white">"Add"</strong> to confirm
                    </li>
                </ol>
                <button id="pwa-ios-dismiss" class="w-full px-4 py-3 text-sm font-semibold text-slate-600 dark:text-gray-300 bg-slate-100 dark:bg-white/5 hover:bg-slate-200 dark:hover:bg-white/10 rounded-xl transition-colors">Got it</button>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="<?php echo asset('js/app.js'); ?>"></script>
    
    <!-- Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js').then(function(reg) {
                    console.log('SW registered:', reg.scope);
                }).catch(function(err) {
                    console.log('SW registration failed:', err);
                });
            });
        }
    </script>
</body>
</html>
