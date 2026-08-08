self.__precacheManifest = [].concat(self.__precacheManifest || []);
// eslint-disable-next-line no-undef
workbox.precaching.precacheAndRoute(self.__precacheManifest, {});

// eslint-disable-next-line no-undef
workbox.core.setCacheNameDetails({prefix: "tarkan-basic"});
// eslint-disable-next-line no-undef
workbox.core.skipWaiting();