
// Config de push lida do CONFIG global (/tarkan/assets/custom/config.js servido em runtime).
// Chaves reais vivem em storage/app/assets (gitignored) - nunca commitadas.
const _cfg = (typeof CONFIG !== 'undefined' && CONFIG) ? CONFIG : {};

const vapidKey = _cfg.vapidKey || '';

const firebaseConfig = _cfg.firebase || null;