const CACHE_NAME = 'jjwc-hris-v2-pwa-v2';
const CORE_ASSETS = [
    '/',
    '/login',
    '/home',
    '/manifest.webmanifest',
    '/images/logo.png',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => cache.addAll(CORE_ASSETS))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(keys.map((key) => key === CACHE_NAME ? null : caches.delete(key))))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') {
        return;
    }

    const url = new URL(event.request.url);
    const shouldBypassCache = url.pathname.startsWith('/build/')
        || url.pathname.startsWith('/livewire/')
        || url.pathname.includes('/employee-management/wfh-monitoring')
        || url.pathname.includes('/home');

    if (shouldBypassCache) {
        event.respondWith(fetch(event.request));
        return;
    }

    event.respondWith(
        fetch(event.request)
            .then((response) => {
                const copy = response.clone();
                caches.open(CACHE_NAME).then((cache) => cache.put(event.request, copy));
                return response;
            })
            .catch(() => caches.match(event.request).then((cached) => cached || caches.match('/login')))
    );
});
