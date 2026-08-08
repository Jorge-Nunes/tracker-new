<template>
  <el-dialog :lock-scroll="true" v-model="show">

    <template v-slot:title>
      <div  style="border-bottom: #e0e0e0 1px solid;padding: 20px;">
        <div class="modal-title" >{{title}}</div>
      </div>
    </template>
    <template v-slot:footer>
      <div  style="border-top: #e0e0e0 1px solid;padding: 20px;display: flex;justify-content: space-between;">

        <el-button type="danger" plain @click="doCancel()">{{KT('cancel')}}</el-button>
        <el-button type="primary" @click="doSave()">{{KT('save')}}</el-button>
      </div>
    </template>

    <el-tabs v-model="tab">
      <el-tab-pane :label="KT('notification.notification')" name="first">
        <el-form label-width="120px" label-position="top">


          <el-form-item :label="KT('maintenance.name')" >
            <el-input v-model="formData.name" ></el-input>
          </el-form-item>


          <el-form-item :label="KT('maintenance.type')" >
            <el-select v-model="formData.type" filterable :size="'large'" :placeholder="KT('maintenance.type')" :no-match-text="KT('NO_MATCH_TEXT')" :no-data-text="KT('NO_DATA_TEXT')">
              <el-option
                  v-for="item in optionTypes"
                  :key="item.type"
                  :label="KT('maintenance.types.'+item.type)"
                  :value="item.type"
              >
              </el-option>
            </el-select>
          </el-form-item>


          <el-form-item :label="KT('maintenance.start')" >
            <el-input v-model="formData.start" ></el-input>
          </el-form-item>

          <el-form-item :label="KT('maintenance.cicle')" >
            <el-input v-model="formData.period" ></el-input>
          </el-form-item>

        </el-form>


      </el-tab-pane>


      <el-tab-pane :label="KT('attribute.attributes')" name="fourth">
        <tab-attributes v-model="formData.attributes" :type="'notification'"></tab-attributes>
      </el-tab-pane>


      <el-tab-pane v-if="!isEdit" :label="KT('device.devices')" :disabled="formData.always" name="devices">
        <tab-devices ref="deviceList"></tab-devices>
      </el-tab-pane>

      <el-tab-pane v-if="!isEdit" :label="KT('group.groups')" :disabled="formData.always" name="groups">
        <tab-groups ref="groupList"></tab-groups>
      </el-tab-pane>


      <el-tab-pane v-if="!isEdit" :label="KT('user.users')"  name="users">
        <tab-users ref="userList"></tab-users>
      </el-tab-pane>
    </el-tabs>
  </el-dialog>
</template>


<script setup>


import 'element-plus/es/components/input/style/css'
import 'element-plus/es/components/button/style/css'
import 'element-plus/es/components/switch/style/css'
import 'element-plus/es/components/select/style/css'
import 'element-plus/es/components/option/style/css'
import 'element-plus/es/components/dialog/style/css'
import 'element-plus/es/components/tab-pane/style/css'
import 'element-plus/es/components/tabs/style/css'
import 'element-plus/es/components/message/style/css'
import 'element-plus/es/components/checkbox/style/css'

import {ElDialog,ElMessage,ElNotification,ElTabs,ElTabPane,ElForm,ElFormItem,ElSelect,ElOption,ElButton,ElInput} from "element-plus";



import {ref,defineExpose} from 'vue';
import {useStore} from 'vuex'

const store = useStore();
const isEdit = ref(false);

import KT from '../../../tarkan/func/kt'

import TabAttributes from "./tab-attributes";
import TabGroups from './tab-groups';
import TabDevices from './tab-devices'
import TabUsers from './tab-users'

const deviceList = ref(null);
const userList = ref(null);
const groupList = ref(null);

const optionTypes = ref([
  {type: 'totalDistance'},
  {type: 'hours'}]);

const title = ref('');

const show = ref(false);
const tab = ref('first');



const defaultMaintenanceData = {
  "id":0,
  "attributes":{},
  "name":"",
  "type":"",
  "start":0,
  "period":0}


// eslint-disable-next-line no-undef
const formData = ref(defaultMaintenanceData);




const newMaintenance = ()=>{
  tab.value = 'first';

  isEdit.value = false;
  title.value = KT('maintenance.newMaintenance');
  // eslint-disable-next-line no-undef
    formData.value = JSON.parse(JSON.stringify(defaultMaintenanceData));

    show.value = true;
}

const editMaintenance = (id)=>{

  if(id===0){
    ElMessage.error(KT('maintenance.selectError'));
    return false;
  }

  isEdit.value = true;

  title.value = KT('maintenance.editMaintenance');
  tab.value = 'first';
  // eslint-disable-next-line no-undef
  formData.value = JSON.parse(JSON.stringify(defaultMaintenanceData));
  const maintenance = JSON.parse(JSON.stringify(store.getters["maintenance/getMaintenanceById"](id)));

  for(var k of Object.keys(defaultMaintenanceData)){
    formData.value[k] = maintenance[k];
  }

  show.value = true;
}

defineExpose({
  newMaintenance,
  editMaintenance
});


const doCancel = ()=>{
  show.value = false;
}




const doSave = async ()=>{


  ElNotification({
    title: KT('info'),
    message: KT('maintenance.saving'),
    type: 'info',
  });

  if(formData.value.type === 'totalDistance'){
    formData.value.start = formData.value.start * 1000;
    formData.value.period = formData.value.period * 1000;
  }


  store.dispatch("maintenance/save",formData.value).then(async (data)=>{

    ElNotification({
      title: KT('success'),
      message: KT('notification.saved'),
      type: 'info',
    });

    if(!isEdit.value) {
      if(deviceList.value.selected.length>0) {
        for(let device of deviceList.value.selected){

          ElNotification({
            title: 'Info',
            message: KT('notification.linkToDevice',device),
            type: 'info',
          });

          await window.$traccar.linkObjects({deviceId: device.id,maintenanceId: data.id});

        }
      }

      if(groupList.value.selected.length>0) {
        for(let device of groupList.value.selected){

          ElNotification({
            title: 'Info',
            message: KT('notification.linkToGroup',device),
            type: 'info',
          });

          await window.$traccar.linkObjects({groupId: device.id,maintenanceId: data.id});

        }
      }

      if(userList.value.selected.length>0) {
        for(let device of userList.value.selected){

          ElNotification({
            title: 'Info',
            message: KT('notification.linkToUser',device),
            type: 'info',
          });

          await window.$traccar.linkObjects({userId: device.id,maintenanceId: data.id});

        }
      }


      ElNotification({
        title: KT('success'),
        message: KT('ALL_TASK_READY'),
        type: 'success',
      });

    }


    show.value = false;

  }).catch(()=>{
    ElMessage.error(KT('maintenance.saveError'));
  });

  show.value = false;
}


</script>

<style>

.el-select.el-select--large{
  width: 100%;
}

.el-dialog__header,.el-dialog__body,.el-dialog__footer{
  padding: 0px !important;
}

.el-dialog__footer{
  margin-top: 20px;
}

.el-tabs__nav-wrap{
  padding-left: 20px;
  padding-right: 20px;
}

.el-tabs__content{
  padding-left: 20px;
  padding-right: 20px;
}



</style>