import KT from "./kt";
import {inject} from 'vue';


import {ElMessage} from "element-plus/es/components/message";
import {ElMessageBox} from "element-plus/es/components/message-box";
import {ElNotification} from "element-plus/es/components/notification";

import router from "../../routes";
import actAnchor from "./actAnchor";
import store from '../../store/'


export default {
    setup() {

        console.log("SETUP?");

        const flyToDevice = inject("flyToDevice");
        const linkObjectsRef = inject("link-objects");
        const editDeviceRef = inject("edit-device");
        const logObjectsRef = inject("log-objects");
        const contextMenuRef = inject("contextMenu");
        const editShareRef = inject("edit-share");



        return {markerContext};
    }
}
