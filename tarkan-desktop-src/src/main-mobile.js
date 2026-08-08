import { createApp } from 'vue'
import App from './App-Mobile.vue'


import './registerServiceWorker'
import Tarkan from './tarkan/traccarConnector'

import ElementPlus from "element-plus";
import "element-plus/dist/index.css";

import routes from './routes.js'

const loc = window.location;

const serverUrl = loc.protocol+'//'+loc.host+'/api';


import i18n from "./lang/pt-BR";

import store from "./store/index"

import KT from './tarkan/func/kt'

createApp(App).mixin({
    methods: {
        KT: KT
    }
}).use(store).use(Tarkan,serverUrl).use(i18n).use(ElementPlus).use(routes).mount('#app')

