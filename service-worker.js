const CACHE_NAME = "members-dashboard-v1";

const urlsToCache = [
  "/",
  "/member/dashboard.php",
  "/css/bootstrap.min.css",
  "/js/jquery.min.js",
  "/js/bootstrap.min.js",
  "/assets/icons/your_logo.png",
  "/assets/icons/your_logo.png"
];

self.addEventListener("install", event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => cache.addAll(urlsToCache))
  );
});     

self.addEventListener("fetch", event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => response || fetch(event.request))
  );
});
