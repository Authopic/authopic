/**
 * Authopic Technologies PLC - Main JavaScript
 * Vanilla JS only - No frameworks
 */

(function() {
    'use strict';

    // ============================================================
    // THEME TOGGLE (Dark / Light)
    // ============================================================
    const themeToggle = document.getElementById('themeToggle');
    const themeToggleMobile = document.getElementById('theme-toggle-mobile');

    function getTheme() {
        return localStorage.getItem('theme') || 
            (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    }

    function setTheme(theme) {
        document.documentElement.classList.toggle('dark', theme === 'dark');
        localStorage.setItem('theme', theme);
        // Update icons
        document.querySelectorAll('.theme-icon-sun').forEach(function(el) {
            el.style.display = theme === 'dark' ? 'none' : 'block';
        });
        document.querySelectorAll('.theme-icon-moon').forEach(function(el) {
            el.style.display = theme === 'dark' ? 'block' : 'none';
        });
    }

    function toggleTheme() {
        var current = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
        setTheme(current === 'dark' ? 'light' : 'dark');
    }

    // Initialize theme
    setTheme(getTheme());

    if (themeToggle) themeToggle.addEventListener('click', toggleTheme);
    if (themeToggleMobile) themeToggleMobile.addEventListener('click', toggleTheme);

    // ============================================================
    // MOBILE MENU
    // ============================================================
    var menuBtn = document.getElementById('mobileMenuBtn');
    var mobileMenu = document.getElementById('mobileMenu');
    var menuIconOpen = document.getElementById('menuIconOpen');
    var menuIconClose = document.getElementById('menuIconClose');

    function openMobileMenu() {
        if (mobileMenu) mobileMenu.classList.remove('hidden');
        if (menuIconOpen) menuIconOpen.classList.add('hidden');
        if (menuIconClose) menuIconClose.classList.remove('hidden');
        if (menuBtn) menuBtn.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
    }

    function closeMobileMenu() {
        if (mobileMenu) mobileMenu.classList.add('hidden');
        if (menuIconOpen) menuIconOpen.classList.remove('hidden');
        if (menuIconClose) menuIconClose.classList.add('hidden');
        if (menuBtn) menuBtn.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
    }

    if (menuBtn) {
        menuBtn.addEventListener('click', function() {
            var isOpen = mobileMenu && !mobileMenu.classList.contains('hidden');
            if (isOpen) {
                closeMobileMenu();
            } else {
                openMobileMenu();
            }
        });
    }

    // Close menu on link click
    if (mobileMenu) {
        mobileMenu.querySelectorAll('a').forEach(function(link) {
            link.addEventListener('click', closeMobileMenu);
        });
    }

    // ============================================================
    // NAVBAR SCROLL EFFECT
    // ============================================================
    var navbar = document.getElementById('site-header');
    var lastScroll = 0;

    function handleNavScroll() {
        if (!navbar) return;
        var scrollY = window.pageYOffset || document.documentElement.scrollTop;

        if (scrollY > 50) {
            navbar.classList.add('nav-scrolled', 'scrolled');
        } else {
            navbar.classList.remove('nav-scrolled', 'scrolled');
        }

        // Hide/show on scroll direction
        if (scrollY > 300) {
            if (scrollY > lastScroll + 5) {
                navbar.style.transform = 'translateY(-100%)';
            } else if (scrollY < lastScroll - 5) {
                navbar.style.transform = 'translateY(0)';
            }
        } else {
            navbar.style.transform = 'translateY(0)';
        }

        lastScroll = scrollY;
    }

    window.addEventListener('scroll', handleNavScroll, { passive: true });
    handleNavScroll();

    // ============================================================
    // DROPDOWN MENUS (Desktop)
    // ============================================================
    document.querySelectorAll('[data-dropdown]').forEach(function(trigger) {
        var dropdownId = trigger.getAttribute('data-dropdown');
        var dropdown = document.getElementById(dropdownId);
        if (!dropdown) return;

        var timeoutId;

        trigger.addEventListener('mouseenter', function() {
            clearTimeout(timeoutId);
            // Close other dropdowns
            document.querySelectorAll('.nav-dropdown.active').forEach(function(d) {
                if (d !== dropdown) d.classList.remove('active');
            });
            dropdown.classList.add('active');
        });

        trigger.addEventListener('mouseleave', function() {
            timeoutId = setTimeout(function() {
                dropdown.classList.remove('active');
            }, 200);
        });

        dropdown.addEventListener('mouseenter', function() {
            clearTimeout(timeoutId);
        });

        dropdown.addEventListener('mouseleave', function() {
            dropdown.classList.remove('active');
        });
    });

    // ============================================================
    // SCROLL ANIMATIONS (Intersection Observer)
    // ============================================================
    function setupScrollAnimations() {
        var elements = document.querySelectorAll('[data-animate], [data-animate-stagger]');
        if (!elements.length) return;

        if ('IntersectionObserver' in window) {
            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        var delay = entry.target.getAttribute('data-delay') || 0;
                        setTimeout(function() {
                            entry.target.classList.add('animated');
                        }, parseInt(delay));
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });

            elements.forEach(function(el) {
                observer.observe(el);
            });
        } else {
            // Fallback: show all
            elements.forEach(function(el) {
                el.classList.add('animated');
            });
        }
    }

    setupScrollAnimations();

    // ============================================================
    // COUNTER ANIMATION
    // ============================================================
    function animateCounters() {
        var counters = document.querySelectorAll('[data-counter]');
        if (!counters.length) return;

        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    var el = entry.target;
                    var target = parseInt(el.getAttribute('data-counter')) || 0;
                    var suffix = el.getAttribute('data-suffix') || '';
                    var prefix = el.getAttribute('data-prefix') || '';
                    var duration = 2000;
                    var start = 0;
                    var startTime = null;

                    function step(timestamp) {
                        if (!startTime) startTime = timestamp;
                        var progress = Math.min((timestamp - startTime) / duration, 1);
                        var eased = 1 - Math.pow(1 - progress, 3); // ease-out cubic
                        var current = Math.floor(eased * target);
                        el.textContent = prefix + current.toLocaleString() + suffix;
                        if (progress < 1) {
                            requestAnimationFrame(step);
                        } else {
                            el.textContent = prefix + target.toLocaleString() + suffix;
                        }
                    }

                    requestAnimationFrame(step);
                    observer.unobserve(el);
                }
            });
        }, { threshold: 0.5 });

        counters.forEach(function(c) { observer.observe(c); });
    }

    animateCounters();

    // ============================================================
    // TESTIMONIAL CAROUSEL
    // ============================================================
    function initCarousel() {
        var track = document.getElementById('testimonial-track');
        var dots = document.querySelectorAll('.testimonial-dot');
        var prevBtn = document.getElementById('testimonial-prev');
        var nextBtn = document.getElementById('testimonial-next');
        if (!track || !dots.length) return;

        var currentSlide = 0;
        var totalSlides = dots.length;
        var autoplayInterval;

        function goToSlide(index) {
            if (index < 0) index = totalSlides - 1;
            if (index >= totalSlides) index = 0;
            currentSlide = index;
            track.style.transform = 'translateX(-' + (currentSlide * 100) + '%)';
            dots.forEach(function(d, i) {
                d.classList.toggle('active', i === currentSlide);
            });
        }

        function nextSlide() { goToSlide(currentSlide + 1); }
        function prevSlide() { goToSlide(currentSlide - 1); }

        function startAutoplay() {
            stopAutoplay();
            autoplayInterval = setInterval(nextSlide, 5000);
        }

        function stopAutoplay() {
            if (autoplayInterval) clearInterval(autoplayInterval);
        }

        if (nextBtn) nextBtn.addEventListener('click', function() { nextSlide(); startAutoplay(); });
        if (prevBtn) prevBtn.addEventListener('click', function() { prevSlide(); startAutoplay(); });

        dots.forEach(function(dot, i) {
            dot.addEventListener('click', function() { goToSlide(i); startAutoplay(); });
        });

        // Touch/swipe support
        var startX = 0;
        track.addEventListener('touchstart', function(e) {
            startX = e.touches[0].clientX;
            stopAutoplay();
        }, { passive: true });

        track.addEventListener('touchend', function(e) {
            var diff = startX - e.changedTouches[0].clientX;
            if (Math.abs(diff) > 50) {
                if (diff > 0) nextSlide();
                else prevSlide();
            }
            startAutoplay();
        }, { passive: true });

        startAutoplay();
    }

    initCarousel();

    // ============================================================
    // TABS
    // ============================================================
    document.querySelectorAll('[data-tab-group]').forEach(function(group) {
        var buttons = group.querySelectorAll('[data-tab]');
        var groupName = group.getAttribute('data-tab-group');
        
        buttons.forEach(function(btn) {
            btn.addEventListener('click', function() {
                var tabId = btn.getAttribute('data-tab');
                
                // Update buttons
                buttons.forEach(function(b) { b.classList.remove('active'); });
                btn.classList.add('active');

                // Update content
                document.querySelectorAll('[data-tab-content="' + groupName + '"]').forEach(function(content) {
                    content.classList.remove('active');
                });
                var target = document.getElementById(tabId);
                if (target) target.classList.add('active');
            });
        });
    });

    // ============================================================
    // FAQ ACCORDION
    // ============================================================
    document.querySelectorAll('.faq-header').forEach(function(header) {
        header.addEventListener('click', function() {
            var item = header.closest('.faq-item');
            var wasActive = item.classList.contains('active');

            // Close all in same group
            var parent = item.parentElement;
            if (parent) {
                parent.querySelectorAll('.faq-item').forEach(function(i) {
                    i.classList.remove('active');
                });
            }

            if (!wasActive) {
                item.classList.add('active');
            }
        });
    });

    // ============================================================
    // PRICING TOGGLE — handled inline on product-single.php
    // ============================================================

    // ============================================================
    // LIGHTBOX
    // ============================================================
    window.openLightbox = function(src) {
        var lightbox = document.getElementById('lightbox');
        var img = document.getElementById('lightbox-img');
        if (lightbox && img) {
            img.src = src;
            lightbox.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    };

    window.closeLightbox = function() {
        var lightbox = document.getElementById('lightbox');
        if (lightbox) {
            lightbox.classList.remove('active');
            document.body.style.overflow = '';
        }
    };

    // Close lightbox on backdrop click
    var lightbox = document.getElementById('lightbox');
    if (lightbox) {
        lightbox.addEventListener('click', function(e) {
            if (e.target === lightbox) closeLightbox();
        });
    }

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeLightbox();
            closeMobileMenu();
        }
    });

    // ============================================================
    // COPY TO CLIPBOARD (Share links)
    // ============================================================
    window.copyToClipboard = function(text) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(function() {
                showToast('Link copied!', 'success');
            });
        } else {
            // Fallback
            var input = document.createElement('input');
            input.value = text;
            document.body.appendChild(input);
            input.select();
            document.execCommand('copy');
            document.body.removeChild(input);
            showToast('Link copied!', 'success');
        }
    };

    // ============================================================
    // TOAST NOTIFICATION SYSTEM (Beautiful)
    // ============================================================
    var _toastContainer = null;

    var _toastIcons = {
        success: '<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>',
        error:   '<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>',
        warning: '<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>',
        info:    '<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
    };

    var _toastTitles = { success: 'Success', error: 'Error', warning: 'Warning', info: 'Info' };

    function _safeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function _getToastContainer() {
        if (!_toastContainer) {
            _toastContainer = document.createElement('div');
            _toastContainer.id = 'toast-container';
            document.body.appendChild(_toastContainer);
        }
        return _toastContainer;
    }

    function _dismissToast(toast) {
        if (!toast || toast.classList.contains('toast-hiding')) return;
        toast.classList.add('toast-hiding');
        toast.classList.remove('show');
        setTimeout(function() {
            if (toast.parentElement) toast.parentElement.removeChild(toast);
        }, 400);
    }

    function showToast(message, type, title, duration) {
        type     = type     || 'success';
        title    = title    || _toastTitles[type] || 'Notification';
        duration = duration || 4500;

        var container = _getToastContainer();

        // Cap at 5 simultaneous toasts
        var existing = container.querySelectorAll('.toast-notification');
        if (existing.length >= 5) _dismissToast(existing[0]);

        var toast = document.createElement('div');
        toast.className = 'toast-notification toast-' + type;
        toast.innerHTML =
            '<div class="toast-icon-wrap">' + (_toastIcons[type] || _toastIcons.info) + '</div>' +
            '<div class="toast-body">' +
                '<div class="toast-title">' + _safeHtml(title) + '</div>' +
                '<div class="toast-msg">' + _safeHtml(message) + '</div>' +
            '</div>' +
            '<button class="toast-close-btn" aria-label="Dismiss">' +
                '<svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>' +
            '</button>' +
            '<div class="toast-progress-bar" style="width:100%"></div>';

        container.appendChild(toast);

        toast.querySelector('.toast-close-btn').addEventListener('click', function() {
            _dismissToast(toast);
        });

        requestAnimationFrame(function() {
            requestAnimationFrame(function() { toast.classList.add('show'); });
        });

        // Animated progress bar countdown
        var progress = toast.querySelector('.toast-progress-bar');
        var startTime = null;
        var remainingDuration = duration;
        var raf;

        function animateProgress(ts) {
            if (!startTime) startTime = ts;
            var elapsed = ts - startTime;
            var pct = Math.max(0, 100 - (elapsed / remainingDuration * 100));
            progress.style.width = pct + '%';
            if (elapsed < remainingDuration) {
                raf = requestAnimationFrame(animateProgress);
            } else {
                _dismissToast(toast);
            }
        }
        raf = requestAnimationFrame(animateProgress);

        // Pause progress on hover
        toast.addEventListener('mouseenter', function() {
            cancelAnimationFrame(raf);
            remainingDuration = (parseFloat(progress.style.width) / 100) * remainingDuration;
            startTime = null;
        });
        toast.addEventListener('mouseleave', function() {
            raf = requestAnimationFrame(animateProgress);
        });
    }

    window.showToast = showToast;

    // ============================================================
    // CONFIRM DIALOG (Beautiful — replaces native browser confirm)
    // ============================================================
    var _confirmOverlay = null;
    var _confirmCallback = null;

    var _confirmIconPaths = {
        danger:  'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
        warning: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z',
        info:    'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
    };
    var _confirmOkColors = { danger: '#dc2626', warning: '#d97706', info: '#2563eb' };

    function _buildConfirmOverlay() {
        if (_confirmOverlay) return;
        _confirmOverlay = document.createElement('div');
        _confirmOverlay.id = 'confirm-overlay';
        _confirmOverlay.setAttribute('role', 'dialog');
        _confirmOverlay.setAttribute('aria-modal', 'true');
        _confirmOverlay.style.display = 'none';
        _confirmOverlay.innerHTML =
            '<div id="confirm-dialog">' +
                '<div id="confirm-icon-ring" class="confirm-icon-ring ci-danger">' +
                    '<svg id="confirm-svg" width="30" height="30" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"></svg>' +
                '</div>' +
                '<div id="confirm-title" class="confirm-title"></div>' +
                '<div id="confirm-message" class="confirm-message"></div>' +
                '<div class="confirm-actions">' +
                    '<button id="confirm-cancel-btn" class="confirm-btn confirm-btn-cancel">Cancel</button>' +
                    '<button id="confirm-ok-btn" class="confirm-btn confirm-btn-ok">Confirm</button>' +
                '</div>' +
            '</div>';
        document.body.appendChild(_confirmOverlay);

        document.getElementById('confirm-cancel-btn').addEventListener('click', _closeConfirm);
        document.getElementById('confirm-ok-btn').addEventListener('click', function() {
            var cb = _confirmCallback;
            _closeConfirm();
            if (cb) setTimeout(cb, 10);
        });
        _confirmOverlay.addEventListener('click', function(e) {
            if (e.target === _confirmOverlay) _closeConfirm();
        });
        document.addEventListener('keydown', function(e) {
            if (!_confirmOverlay || _confirmOverlay.style.display === 'none') return;
            if (e.key === 'Escape') { _closeConfirm(); }
            if (e.key === 'Enter') {
                var cb = _confirmCallback;
                _closeConfirm();
                if (cb) setTimeout(cb, 10);
            }
        });
    }

    function showConfirm(opts) {
        _buildConfirmOverlay();
        var type        = opts.type        || 'danger';
        var title       = opts.title       || 'Are you sure?';
        var message     = opts.message     || '';
        var confirmText = opts.confirmText || 'Confirm';
        var cancelText  = opts.cancelText  || 'Cancel';
        _confirmCallback = opts.onConfirm || null;

        var ring = document.getElementById('confirm-icon-ring');
        ring.className = 'confirm-icon-ring ci-' + type;

        var iconPath = _confirmIconPaths[type] || _confirmIconPaths.danger;
        document.getElementById('confirm-svg').innerHTML =
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="' + iconPath + '"/>';

        document.getElementById('confirm-title').textContent   = title;
        document.getElementById('confirm-message').textContent = message;
        document.getElementById('confirm-cancel-btn').textContent = cancelText;
        var okBtn = document.getElementById('confirm-ok-btn');
        okBtn.textContent = confirmText;
        okBtn.style.background = _confirmOkColors[type] || _confirmOkColors.danger;

        _confirmOverlay.style.display = 'flex';
        requestAnimationFrame(function() {
            requestAnimationFrame(function() { _confirmOverlay.classList.add('confirm-show'); });
        });
        setTimeout(function() {
            var cancel = document.getElementById('confirm-cancel-btn');
            if (cancel) cancel.focus();
        }, 80);
    }

    function _closeConfirm() {
        if (!_confirmOverlay) return;
        _confirmOverlay.classList.remove('confirm-show');
        _confirmCallback = null;
        setTimeout(function() {
            if (_confirmOverlay) _confirmOverlay.style.display = 'none';
        }, 320);
    }

    window.showConfirm = showConfirm;

    // ============================================================
    // DELETE CONFIRMATION (direct global — called from onclick)
    // ============================================================
    window.authConfirmDelete = function(link) {
        var href  = link.getAttribute('href');
        var label = link.getAttribute('data-label') ||
                    (link.closest('tr') && link.closest('tr').querySelector('td')
                        ? link.closest('tr').querySelector('td').textContent.trim()
                        : 'this item');
        showConfirm({
            type:        'danger',
            title:       'Delete Item',
            message:     'Are you sure you want to permanently delete \u201c' + label + '\u201d? This action cannot be undone.',
            confirmText: 'Yes, Delete',
            cancelText:  'Cancel',
            onConfirm:   function() { window.location.href = href; }
        });
    };

    // ============================================================
    // FLASH MESSAGES → TOASTS  (app.js loads at bottom of body,
    //   DOM is already populated; run immediately, no event needed)
    // ============================================================
    var _flashTitles = { success: 'Success!', error: 'Error', warning: 'Warning', info: 'Info' };

    function _initFlash() {
        document.querySelectorAll('.js-flash-init').forEach(function(el) {
            var type    = el.getAttribute('data-type')    || 'info';
            var message = el.getAttribute('data-message') || '';
            if (message) showToast(message, type, _flashTitles[type] || 'Notification');
            el.remove();
        });
        // Legacy HTML flash divs
        document.querySelectorAll('.flash-message').forEach(function(msg) {
            var text = msg.querySelector('span') ? msg.querySelector('span').textContent : msg.textContent;
            var type = 'info';
            ['success','error','warning','info'].forEach(function(t) {
                if (msg.className.indexOf(t) !== -1) type = t;
            });
            if (text.trim()) showToast(text.trim(), type);
            msg.remove();
        });
    }

    // Run immediately if DOM is ready, otherwise wait for it
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', _initFlash);
    } else {
        _initFlash();
    }

    // ============================================================
    // NEWSLETTER FORM (AJAX)
    // ============================================================
    var newsletterForms = document.querySelectorAll('.newsletter-form');
    newsletterForms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var emailInput = form.querySelector('input[name="email"]');
            var btn = form.querySelector('button[type="submit"]');
            var email = emailInput ? emailInput.value.trim() : '';

            if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                showToast('Please enter a valid email', 'error');
                return;
            }

            var originalText = btn.innerHTML;
            btn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>';
            btn.disabled = true;

            var formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.success) {
                    showToast(data.message || 'Subscribed successfully!', 'success');
                    emailInput.value = '';
                } else {
                    showToast(data.message || 'Something went wrong', 'error');
                }
            })
            .catch(function() {
                showToast('Network error. Please try again.', 'error');
            })
            .finally(function() {
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        });
    });

    // ============================================================
    // CONTACT / DEMO FORM VALIDATION
    // ============================================================
    document.querySelectorAll('form[data-validate]').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            var isValid = true;
            var firstInvalid = null;

            form.querySelectorAll('[required]').forEach(function(field) {
                var error = field.parentElement.querySelector('.form-error') || 
                            field.closest('.relative')?.querySelector('.form-error');
                
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('border-red-500');
                    if (!firstInvalid) firstInvalid = field;
                } else {
                    field.classList.remove('border-red-500');
                }

                // Email validation
                if (field.type === 'email' && field.value.trim()) {
                    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value.trim())) {
                        isValid = false;
                        field.classList.add('border-red-500');
                        if (!firstInvalid) firstInvalid = field;
                    }
                }

                // Phone validation
                if (field.type === 'tel' && field.value.trim()) {
                    if (!/^[\+]?[\d\s\-]{7,15}$/.test(field.value.trim())) {
                        isValid = false;
                        field.classList.add('border-red-500');
                        if (!firstInvalid) firstInvalid = field;
                    }
                }
            });

            if (!isValid) {
                e.preventDefault();
                if (firstInvalid) {
                    firstInvalid.focus();
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });

        // Clear error styling on input
        form.querySelectorAll('input, textarea, select').forEach(function(field) {
            field.addEventListener('input', function() {
                field.classList.remove('border-red-500');
            });
        });
    });

    // ============================================================
    // SMOOTH SCROLL for anchor links
    // ============================================================
    document.querySelectorAll('a[href^="#"]').forEach(function(link) {
        link.addEventListener('click', function(e) {
            var targetId = link.getAttribute('href');
            if (targetId === '#') return;
            var target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // ============================================================
    // PWA INSTALL PROMPT (Professional)
    // ============================================================
    var deferredPrompt = null;
    var pwaInstallBtn = document.getElementById('pwa-install-btn');
    var pwaPrompt = document.getElementById('pwa-install-prompt');
    var pwaSheet = document.getElementById('pwa-install-sheet');
    var pwaBackdrop = document.getElementById('pwa-install-backdrop');
    var pwaConfirm = document.getElementById('pwa-install-confirm');
    var pwaDismiss = document.getElementById('pwa-install-dismiss');
    var pwaIosPrompt = document.getElementById('pwa-ios-prompt');
    var pwaIosSheet = document.getElementById('pwa-ios-sheet');
    var pwaIosBackdrop = document.getElementById('pwa-ios-backdrop');
    var pwaIosDismiss = document.getElementById('pwa-ios-dismiss');

    function showPwaSheet(promptEl, sheetEl) {
        if (!promptEl || !sheetEl) return;
        promptEl.style.display = 'flex';
        requestAnimationFrame(function() {
            promptEl.classList.add('opacity-100');
            promptEl.classList.remove('opacity-0', 'pointer-events-none');
            sheetEl.classList.remove('translate-y-full');
            sheetEl.classList.add('translate-y-0');
        });
    }

    function hidePwaSheet(promptEl, sheetEl) {
        if (!promptEl || !sheetEl) return;
        sheetEl.classList.add('translate-y-full');
        sheetEl.classList.remove('translate-y-0');
        promptEl.classList.remove('opacity-100');
        promptEl.classList.add('opacity-0', 'pointer-events-none');
        setTimeout(function() { promptEl.style.display = 'none'; }, 300);
    }

    // Check if already installed (standalone mode)
    var isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone;
    var isIos = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;

    // Don't show install prompt if already installed
    if (!isStandalone) {
        window.addEventListener('beforeinstallprompt', function(e) {
            e.preventDefault();
            deferredPrompt = e;
            // Show install button in navbar
            if (pwaInstallBtn) pwaInstallBtn.style.display = 'inline-flex';
            // Auto-show install sheet after 60s if not dismissed before
            if (!sessionStorage.getItem('pwa-dismissed')) {
                setTimeout(function() {
                    if (deferredPrompt) showPwaSheet(pwaPrompt, pwaSheet);
                }, 60000);
            }
        });

        // iOS: show custom instructions
        if (isIos && !sessionStorage.getItem('pwa-dismissed')) {
            if (pwaInstallBtn) {
                pwaInstallBtn.style.display = 'inline-flex';
            }
        }
    }

    // Nav button click
    if (pwaInstallBtn) {
        pwaInstallBtn.addEventListener('click', function() {
            if (deferredPrompt) {
                showPwaSheet(pwaPrompt, pwaSheet);
            } else if (isIos) {
                showPwaSheet(pwaIosPrompt, pwaIosSheet);
            }
        });
    }

    // Confirm install
    if (pwaConfirm) {
        pwaConfirm.addEventListener('click', function() {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then(function(choice) {
                    deferredPrompt = null;
                    hidePwaSheet(pwaPrompt, pwaSheet);
                    if (choice.outcome === 'accepted') {
                        if (pwaInstallBtn) pwaInstallBtn.style.display = 'none';
                    }
                });
            }
        });
    }

    // Dismiss
    if (pwaDismiss) {
        pwaDismiss.addEventListener('click', function() {
            hidePwaSheet(pwaPrompt, pwaSheet);
            sessionStorage.setItem('pwa-dismissed', '1');
        });
    }
    if (pwaBackdrop) {
        pwaBackdrop.addEventListener('click', function() {
            hidePwaSheet(pwaPrompt, pwaSheet);
            sessionStorage.setItem('pwa-dismissed', '1');
        });
    }

    // iOS dismiss
    if (pwaIosDismiss) {
        pwaIosDismiss.addEventListener('click', function() {
            hidePwaSheet(pwaIosPrompt, pwaIosSheet);
            sessionStorage.setItem('pwa-dismissed', '1');
        });
    }
    if (pwaIosBackdrop) {
        pwaIosBackdrop.addEventListener('click', function() {
            hidePwaSheet(pwaIosPrompt, pwaIosSheet);
            sessionStorage.setItem('pwa-dismissed', '1');
        });
    }

    // App installed event
    window.addEventListener('appinstalled', function() {
        deferredPrompt = null;
        if (pwaInstallBtn) pwaInstallBtn.style.display = 'none';
        hidePwaSheet(pwaPrompt, pwaSheet);
        showToast('App installed successfully!', 'success');
    });

    // ============================================================
    // LANGUAGE TOGGLE
    // ============================================================
    document.querySelectorAll('[data-lang-toggle]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var currentLang = document.cookie.replace(/(?:(?:^|.*;\s*)lang\s*=\s*([^;]*).*$)|^.*$/, '$1') || 'en';
            var newLang = currentLang === 'en' ? 'am' : 'en';
            document.cookie = 'lang=' + newLang + ';path=/;max-age=' + (365 * 24 * 60 * 60);
            window.location.reload();
        });
    });

    // ============================================================
    // BACK TO TOP BUTTON
    // ============================================================
    var backToTop = document.getElementById('back-to-top');
    if (backToTop) {
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 500) {
                backToTop.classList.add('show');
            } else {
                backToTop.classList.remove('show');
            }
        }, { passive: true });

        backToTop.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // ============================================================
    // LAZY LOAD IMAGES
    // ============================================================
    if ('IntersectionObserver' in window) {
        var imgObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    var img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                    }
                    img.classList.add('loaded');
                    imgObserver.unobserve(img);
                }
            });
        }, { rootMargin: '100px' });

        document.querySelectorAll('img[data-src]').forEach(function(img) {
            imgObserver.observe(img);
        });
    }

    // ============================================================
    // SEARCH TOGGLE (mobile)
    // ============================================================
    var searchToggle = document.getElementById('search-toggle');
    var searchBar = document.getElementById('search-bar');
    if (searchToggle && searchBar) {
        searchToggle.addEventListener('click', function() {
            searchBar.classList.toggle('hidden');
            var input = searchBar.querySelector('input');
            if (input && !searchBar.classList.contains('hidden')) {
                input.focus();
            }
        });
    }

    // ============================================================
    // PORTFOLIO FILTERS
    // ============================================================
    document.querySelectorAll('[data-filter-group]').forEach(function(group) {
        var buttons = group.querySelectorAll('[data-filter]');
        buttons.forEach(function(btn) {
            btn.addEventListener('click', function() {
                var filter = btn.getAttribute('data-filter');
                var targetGroup = group.getAttribute('data-filter-group');

                // Update active button
                buttons.forEach(function(b) {
                    b.classList.remove('bg-primary', 'text-white');
                    b.classList.add('bg-slate-100', 'dark:bg-white/5', 'text-slate-600', 'dark:text-gray-300');
                });
                btn.classList.add('bg-primary', 'text-white');
                btn.classList.remove('bg-slate-100', 'dark:bg-white/5', 'text-slate-600', 'dark:text-gray-300');

                // Filter items
                document.querySelectorAll('[data-filter-item="' + targetGroup + '"]').forEach(function(item) {
                    if (filter === 'all' || item.getAttribute('data-category') === filter) {
                        item.style.display = '';
                        item.style.opacity = '0';
                        item.style.transform = 'translateY(10px)';
                        setTimeout(function() {
                            item.style.opacity = '1';
                            item.style.transform = 'translateY(0)';
                            item.style.transition = 'opacity 0.3s, transform 0.3s';
                        }, 50);
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    });

    // ============================================================
    // CHARACTER COUNTER for textareas
    // ============================================================
    document.querySelectorAll('textarea[maxlength]').forEach(function(ta) {
        var max = parseInt(ta.getAttribute('maxlength'));
        var counter = document.createElement('span');
        counter.className = 'text-xs text-slate-400 dark:text-gray-500 mt-1 block text-right';
        counter.textContent = '0 / ' + max;
        ta.parentElement.appendChild(counter);

        ta.addEventListener('input', function() {
            counter.textContent = ta.value.length + ' / ' + max;
            if (ta.value.length > max * 0.9) {
                counter.classList.add('text-orange-500');
            } else {
                counter.classList.remove('text-orange-500');
            }
        });
    });

    // ============================================================
    // FILE UPLOAD PREVIEW (Admin)
    // ============================================================
    window.previewImage = function(input, previewId) {
        var preview = document.getElementById(previewId);
        if (!preview || !input.files || !input.files[0]) return;

        var reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    };

    // ============================================================
    // SLUG GENERATOR (Admin)
    // ============================================================
    window.generateSlug = function(sourceId, targetId) {
        var source = document.getElementById(sourceId);
        var target = document.getElementById(targetId);
        if (!source || !target) return;

        source.addEventListener('input', function() {
            if (!target.dataset.manual) {
                target.value = source.value
                    .toLowerCase()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .replace(/^-|-$/g, '');
            }
        });

        target.addEventListener('input', function() {
            target.dataset.manual = 'true';
        });
    };

    // ============================================================
    // RICH TEXT TOOLBAR (Basic - Admin)
    // ============================================================
    window.insertTag = function(textareaId, openTag, closeTag) {
        var ta = document.getElementById(textareaId);
        if (!ta) return;
        var start = ta.selectionStart;
        var end = ta.selectionEnd;
        var selected = ta.value.substring(start, end);
        ta.value = ta.value.substring(0, start) + openTag + selected + closeTag + ta.value.substring(end);
        ta.focus();
        ta.setSelectionRange(start + openTag.length, start + openTag.length + selected.length);
    };

    // ============================================================
    // DATA TABLE SORT (Admin)
    // ============================================================
    document.querySelectorAll('th[data-sort]').forEach(function(th) {
        th.style.cursor = 'pointer';
        th.addEventListener('click', function() {
            var table = th.closest('table');
            var tbody = table.querySelector('tbody');
            var rows = Array.from(tbody.querySelectorAll('tr'));
            var col = th.cellIndex;
            var dir = th.dataset.dir === 'asc' ? 'desc' : 'asc';

            // Reset all
            table.querySelectorAll('th[data-sort]').forEach(function(h) { h.dataset.dir = ''; });
            th.dataset.dir = dir;

            rows.sort(function(a, b) {
                var aVal = a.cells[col].textContent.trim();
                var bVal = b.cells[col].textContent.trim();
                var aNum = parseFloat(aVal);
                var bNum = parseFloat(bVal);

                if (!isNaN(aNum) && !isNaN(bNum)) {
                    return dir === 'asc' ? aNum - bNum : bNum - aNum;
                }
                return dir === 'asc' ? aVal.localeCompare(bVal) : bVal.localeCompare(aVal);
            });

            rows.forEach(function(row) { tbody.appendChild(row); });
        });
    });

    // ============================================================
    // NEWSLETTER SUBSCRIBE (footer form)
    // ============================================================
    window.handleNewsletter = function(e) {
        e.preventDefault();
        var form  = document.getElementById('newsletterForm');
        var email = form.querySelector('input[name="email"]').value.trim();
        var btn   = form.querySelector('button[type="submit"]');
        if (!email) return false;

        var originalHTML = btn.innerHTML;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>';
        btn.disabled = true;

        var body = new FormData();
        body.append('email', email);
        var csrf = form.querySelector('input[name="csrf_token"]');
        if (csrf) body.append('csrf_token', csrf.value);

        fetch('/api/newsletter', { method: 'POST', body: body })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    showToast(data.message || 'Subscribed successfully!', 'success', 'Subscribed!');
                    form.querySelector('input[name="email"]').value = '';
                } else {
                    showToast(data.message || 'Something went wrong.', 'error', 'Error');
                }
            })
            .catch(function() { showToast('Network error. Please try again.', 'error'); })
            .finally(function() { btn.innerHTML = originalHTML; btn.disabled = false; });

        return false;
    };

})();
