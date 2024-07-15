self.addEventListener('install', function(event) {
    event.waitUntil(
        caches.open('v1').then(function(cache) {
            return cache.addAll([
                './',
                './index.php',
                './assets/css/style.css',
                './assets/js/colorpicker.js',
                './assets/js/customizer.js',
                './assets/js/customizermenu.js',
                './assets/js/drawer.js',
                './assets/js/hamburger.js',
                './assets/js/likes.js',
                './assets/js/snow.js',
                './assets/js/spotlight.js',
                './assets/js/theme.js',
                './assets/img/logo.png'
            ]);
        })
    );
});

self.addEventListener('fetch', function(event) {
    event.respondWith(
        caches.match(event.request).then(function(response) {
            return response || fetch(event.request);
        })
    );
});
