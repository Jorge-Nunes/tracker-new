
import {createI18n} from '../../node_modules/vue-i18n/dist/vue-i18n.esm-browser.prod';

import ptBR from './pt-BR.js';
import esES from './es-ES.js';
import enUS from './en-US.js';

const params = (new URL(document.location)).searchParams;
const bodyLocale = document.querySelector("html").getAttribute("lang");
const navigatorLocale = navigator.language;
const queryLocale = params.get('locale');

const i18n = createI18n({
    legacy: true,
    locale: (queryLocale)?queryLocale:(navigatorLocale)?navigatorLocale:(bodyLocale?bodyLocale:'pt-BR'),
    fallbackLocale: ['en-US'],
    messages: {
        "pt-BR": ptBR,
        "es-ES": esES,
        "en-US": enUS
    }
});




export default i18n;