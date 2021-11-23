// 2.) install a service worker
/*
self.addEventListener('install', function(event) {
  // Perform install steps
});
*/

// 3.) Open a cache. Cache our files. Confirm whether all the required assets are cached or not.
var CACHE_NAME = 'v1';
var urlsToCache = [
  './',
  './img/01.png',
  './img/02.png',
  './img/03.png',
  './img/04.png',
  './img/05.png',
  './img/06.png',
  './img/07.png',
  './main.css',
  './pages/1.html',
  './pages/2.html',
  './pages/3.html'
];

self.addEventListener('install', function(event) {
  // Perform install steps
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(function(cache) {
        console.log('Opened cache');
        return cache.addAll(urlsToCache);
      })
  );
});
this.addEventListener('activate', function(event) {
  var cacheWhitelist = ['v1'];

  event.waitUntil(
    caches.keys().then(function(keyList) {
      return Promise.all(keyList.map(function(key) {
        if (cacheWhitelist.indexOf(key) === -1) {
          return caches.delete(key);
        }
      }));
    })
  );
});
self.addEventListener('fetch', function(event) {
  event.respondWith(
    caches.match(event.request)
      .then(function(response) {
        // Cache hit - return response
        if (response) {
          return response;
        }
        return fetch(event.request);
      }
    )
  );
});