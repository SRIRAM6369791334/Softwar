/** Service Worker [#90] - Offline Support **/
const CACHE_NAME = 'sos-offline-v1';
const ASSETS = [
    '/css/hud.css',
    '/css/dark_mode.css',
    '/js/pos_utilities.js',
    '/dashboard'
];

self.addEventListener('install', (e) => {
    e.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(ASSETS))
    );
});

self.addEventListener('fetch', (e) => {
    e.respondWith(
        caches.match(e.request).then((res) => {
            return res || fetch(e.request);
        })
    );
});
