// Developed by Yisak A. Alemayehu (yisak.dev)
/**
 * Authopic Technologies PLC - Service Worker v2.0
 * Professional caching strategies, offline fallback, background sync
 */

const CACHE_VERSION = 'Authopic-v2.0';
const STATIC_CACHE = CACHE_VERSION + '-static';
const DYNAMIC_CACHE = CACHE_VERSION + '-dynamic';
const IMAGE_CACHE = CACHE_VERSION + '-images';
const FONT_CACHE = CACHE_VERSION + '-fonts';

// Files to cache immediately on install
const STATIC_ASSETS = [
    '/',
    '/offline',
    '/assets/css/tailwind.css',
    '/assets/css/style.css',
    '/assets/js/app.js',
    '/manifest.json',
    '/assets/images/icons/icon-192x192.png',
    '/assets/images/icons/icon-512x512.png'
];

// Cache size limits
const DYNAMIC_CACHE_LIMIT = 80;
const IMAGE_CACHE_LIMIT = 150;
const FONT_CACHE_LIMIT = 30;

// Trim cache to limit
function trimCache(cacheName, maxItems) {
    caches.open(cacheName).then(function(cache) {
        cache.keys().then(function(keys) {
            if (keys.length > maxItems) {
                cache.delete(keys[0]).then(function() {
                    trimCache(cacheName, maxItems);
                });
            }
        });
    });
}

// ---- INSTALL ----
self.addEventListener('install', function(event) {
    event.waitUntil(
        caches.open(STATIC_CACHE).then(function(cache) {
            return cache.addAll(STATIC_ASSETS);
        }).then(function() {
            return self.skipWaiting();
        })
    );
});

// ---- ACTIVATE ----
self.addEventListener('activate', function(event) {
    event.waitUntil(
        caches.keys().then(function(cacheNames) {
            return Promise.all(
                cacheNames
                    .filter(function(name) {
                        return name.startsWith('Authopic-') && 
                               name !== STATIC_CACHE && 
                               name !== DYNAMIC_CACHE && 
                               name !== IMAGE_CACHE &&
                               name !== FONT_CACHE;
                    })
                    .map(function(name) {
                        return caches.delete(name);
                    })
            );
        }).then(function() {
            return self.clients.claim();
        })
    );
});

// ---- FETCH ----
self.addEventListener('fetch', function(event) {
    var request = event.request;

    // Skip non-GET requests
    if (request.method !== 'GET') return;

    // Skip admin and API routes (they need fresh data)
    var url = new URL(request.url);
    if (url.pathname.startsWith('/admin') || url.pathname.startsWith('/api')) return;

    // Google Fonts: Cache-first (long-lived)
    if (url.hostname === 'fonts.googleapis.com' || url.hostname === 'fonts.gstatic.com') {
        event.respondWith(
            caches.match(request).then(function(cached) {
                if (cached) return cached;
                return fetch(request).then(function(response) {
                    if (response.ok) {
                        var clone = response.clone();
                        caches.open(FONT_CACHE).then(function(cache) {
                            cache.put(request, clone);
                            trimCache(FONT_CACHE, FONT_CACHE_LIMIT);
                        });
                    }
                    return response;
                });
            })
        );
        return;
    }

    // Strategy based on request type
    if (request.destination === 'image') {
        // Images: Cache-first
        event.respondWith(
            caches.match(request).then(function(cached) {
                if (cached) return cached;
                return fetch(request).then(function(response) {
                    if (response.ok) {
                        var clone = response.clone();
                        caches.open(IMAGE_CACHE).then(function(cache) {
                            cache.put(request, clone);
                            trimCache(IMAGE_CACHE, IMAGE_CACHE_LIMIT);
                        });
                    }
                    return response;
                }).catch(function() {
                    // Return a transparent 1px image on failure
                    return new Response(
                        '<svg xmlns="http://www.w3.org/2000/svg" width="300" height="200"><rect fill="#e2e8f0" width="300" height="200"/><text fill="#94a3b8" font-family="sans-serif" font-size="14" x="50%" y="50%" text-anchor="middle" dy=".3em">Image Unavailable</text></svg>',
                        { headers: { 'Content-Type': 'image/svg+xml' } }
                    );
                });
            })
        );
    } else if (STATIC_ASSETS.indexOf(url.pathname) !== -1) {
        // Static assets: Stale-while-revalidate (serve cache, update in background)
        event.respondWith(
            caches.open(STATIC_CACHE).then(function(cache) {
                return cache.match(request).then(function(cached) {
                    var fetchPromise = fetch(request).then(function(response) {
                        if (response.ok) cache.put(request, response.clone());
                        return response;
                    }).catch(function() { return cached; });
                    return cached || fetchPromise;
                });
            })
        );
    } else if (request.destination === 'style' || request.destination === 'script') {
        // Other CSS/JS: Cache-first with network fallback
        event.respondWith(
            caches.match(request).then(function(cached) {
                return cached || fetch(request).then(function(response) {
                    if (response.ok) {
                        var clone = response.clone();
                        caches.open(STATIC_CACHE).then(function(cache) {
                            cache.put(request, clone);
                        });
                    }
                    return response;
                });
            })
        );
    } else {
        // HTML pages: Network-first, cache fallback
        event.respondWith(
            fetch(request).then(function(response) {
                if (response.ok && response.headers.get('content-type') && response.headers.get('content-type').indexOf('text/html') !== -1) {
                    var clone = response.clone();
                    caches.open(DYNAMIC_CACHE).then(function(cache) {
                        cache.put(request, clone);
                        trimCache(DYNAMIC_CACHE, DYNAMIC_CACHE_LIMIT);
                    });
                }
                return response;
            }).catch(function() {
                return caches.match(request).then(function(cached) {
                    return cached || caches.match('/offline');
                });
            })
        );
    }
});

// ---- PUSH NOTIFICATIONS (placeholder) ----
self.addEventListener('push', function(event) {
    var data = event.data ? event.data.json() : {};
    var title = data.title || 'Authopic Technologies PLC';
    var options = {
        body: data.body || 'You have a new notification',
        icon: '/assets/images/icons/icon-192x192.png',
        badge: '/assets/images/icons/icon-72x72.png',
        vibrate: [100, 50, 100],
        data: { url: data.url || '/' }
    };
    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    var url = event.notification.data.url || '/';
    event.waitUntil(
        clients.matchAll({ type: 'window' }).then(function(clientList) {
            for (var i = 0; i < clientList.length; i++) {
                if (clientList[i].url.indexOf(url) !== -1 && 'focus' in clientList[i]) {
                    return clientList[i].focus();
                }
            }
            return clients.openWindow(url);
        })
    );
});
