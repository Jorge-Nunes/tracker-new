import { createApp } from 'vue'
import App from './App.vue'


import A from './license.js';


import Tarkan from './tarkan/tarkanConnector'
import Traccar from './tarkan/traccarConnector'



//import ElementPlus from "element-plus";

//import "element-plus/dist/index.css";



import routes from './routes.js'

const loc = window.location;

let serverUrl = loc.protocol + '//' + A[2] + '/api';
if (loc.hostname === 'localhost' || loc.hostname === '127.0.0.1') {
  // serverUrl = 'http://5.78.103.184/api';
  serverUrl = 'http://localhost:8082/api';
  // serverUrl = 'http://localhost:8082/api';
  // serverUrl = 'https://5.161.254.156/api'
  // serverUrl = 'https://5.161.67.137/api'; // 3900 usuarios
}

let tarkanUrl = loc.protocol + '//' + A[3] + '/tarkan';
if (loc.hostname === 'localhost' || loc.hostname === '127.0.0.1') {
  tarkanUrl = 'http://localhost:8005';
}

import store from "./store/index"

import KT from './tarkan/func/kt'
import './registerServiceWorker'
import i18n from './lang/index'




window.$app = createApp(App).use(i18n).mixin({
    methods: {
        KT: KT
    }
}).use(store).use(Tarkan,tarkanUrl).use(Traccar,serverUrl).use(routes);

/*


import ElInput from "element-plus/es/components/input";
import ElButton from "element-plus/es/components/button";
import ElDialog from "element-plus/es/components/dialog";
import {ElSelect,ElOption} from "element-plus/es/components/select";
import {ElTabs,ElTabPane} from "element-plus/es/components/tabs";

import ElDatePicker from "element-plus/es/components/date-picker";
import ElSwitch from "element-plus/es/components/switch";
import ElCheckbox from "element-plus/es/components/checkbox";
import ElSlider from "element-plus/es/components/slider";
import ElColorPicker from "element-plus/es/components/color-picker";
import {ElForm,ElFormItem} from "element-plus/es/components/form";

import ElUpload from "element-plus/es/components/upload"

import {ElDropdown,ElDropdownItem,ElDropdownMenu} from "element-plus/es/components/dropdown";

import ElRadio from "element-plus/es/components/radio";
import ElTooltip from "element-plus/es/components/tooltip";




import 'element-plus/es/components/button/style/css'
import 'element-plus/es/components/input/style/css'
import 'element-plus/es/components/dialog/style/css'
import 'element-plus/es/components/message/style/css'
import 'element-plus/es/components/message-box/style/css'
import 'element-plus/es/components/notification/style/css'
import 'element-plus/es/components/tabs/style/css'
import 'element-plus/es/components/tab-pane/style/css'
import 'element-plus/es/components/select/style/css'
import 'element-plus/es/components/option/style/css'
import 'element-plus/es/components/date-picker/style/css'
import 'element-plus/es/components/switch/style/css'
import 'element-plus/es/components/checkbox/style/css'
import 'element-plus/es/components/slider/style/css'
import 'element-plus/es/components/color-picker/style/css'
import 'element-plus/es/components/form-item/style/css'
import 'element-plus/es/components/upload/style/css'

import 'element-plus/es/components/dropdown/style/css'
import 'element-plus/es/components/dropdown-item/style/css'
import 'element-plus/es/components/dropdown-menu/style/css'

import 'element-plus/es/components/radio/style/css'
import 'element-plus/es/components/tooltip/style/css'

window.$app.use(ElInput);
window.$app.use(ElButton);
window.$app.use(ElDialog);
window.$app.use(ElOption);
window.$app.use(ElSelect);
window.$app.use(ElTabPane);
window.$app.use(ElTabs);
window.$app.use(ElDatePicker);
window.$app.use(ElSwitch);
window.$app.use(ElCheckbox);
window.$app.use(ElSlider);

window.$app.use(ElColorPicker);
window.$app.use(ElForm);
window.$app.use(ElFormItem);
window.$app.use(ElUpload);

window.$app.use(ElDropdown);
window.$app.use(ElDropdownItem);
window.$app.use(ElDropdownMenu);

window.$app.use(ElRadio);
window.$app.use(ElTooltip);
*/
window.$app.mount('#app');

