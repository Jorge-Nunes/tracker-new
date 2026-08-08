

// eslint-disable-next-line no-undef
importScripts('https://www.gstatic.com/firebasejs/8.4.3/firebase-app.js');
// eslint-disable-next-line no-undef
importScripts('https://www.gstatic.com/firebasejs/8.4.3/firebase-messaging.js');


// ====== config lida do CONFIG global (/tarkan/assets/custom/config.js) em runtime ======
// eslint-disable-next-line no-undef
importScripts('/tarkan/assets/custom/config.js');

// eslint-disable-next-line no-undef
const firebaseConfig = (typeof CONFIG !== 'undefined' && CONFIG.firebase) ? CONFIG.firebase : null;

if (firebaseConfig) {

    // eslint-disable-next-line no-undef
    firebase.initializeApp(firebaseConfig)

    // eslint-disable-next-line no-undef
    const messaging  = firebase.messaging()
    messaging.onBackgroundMessage((msg) => {
        console.log("testing service worker",msg)
        // Customize notification here
    });
}

