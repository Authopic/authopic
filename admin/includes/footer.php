            </main>

            <!-- Mobile Bottom Navigation -->
            <nav class="fixed bottom-0 left-0 right-0 z-30 lg:hidden bg-white/90 dark:bg-slate-900/90 backdrop-blur-xl border-t border-black/5 dark:border-white/10 safe-area-bottom">
                <div class="flex items-center justify-around h-16 px-2">
                    <a href="<?php echo url('/admin/dashboard'); ?>" class="admin-bottom-nav-item <?php echo ($admin_page ?? '') === 'dashboard' ? 'active' : ''; ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Home</span>
                    </a>
                    <a href="<?php echo url('/admin/leads'); ?>" class="admin-bottom-nav-item <?php echo ($admin_page ?? '') === 'leads' ? 'active' : ''; ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span>Leads</span>
                    </a>
                    <a href="<?php echo url('/admin/demos'); ?>" class="admin-bottom-nav-item <?php echo ($admin_page ?? '') === 'demos' ? 'active' : ''; ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span>Demos</span>
                    </a>
                    <a href="<?php echo url('/admin/analytics'); ?>" class="admin-bottom-nav-item <?php echo ($admin_page ?? '') === 'analytics' ? 'active' : ''; ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span>Stats</span>
                    </a>
                    <button id="admin-bottomnav-more" class="admin-bottom-nav-item" onclick="document.getElementById('admin-sidebar-toggle').click()">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        <span>More</span>
                    </button>
                </div>
            </nav>
        </div><!-- /.flex-1 -->
    </div><!-- /.flex -->

    <script src="<?php echo asset('js/app.js'); ?>"></script>
</body>
</html>
