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
      <el-tab-pane :label="KT('driver.driver')" name="first">
        <el-form label-width="120px" label-position="top">
          <el-form-item :label="KT('driver.name')" >
            <el-input v-model="formData.name" ></el-input>
          </el-form-item>

          <el-form-item :label="KT('driver.uniqueId')" >
            <el-input v-model="formData.uniqueId" ></el-input>
          </el-form-item>


          <div v-if="store.getters['server/getAttribute']('tarkan.enableQrDriverId',false)" style="margin-top: 15px;display: flex;margin-bottom: 3px;padding: 7px;border-radius: 3px;background: var(--el-color-info-light); align-content: space-between;justify-content: space-between">

            <div style="font-weight: bold;font-size: 16px;">{{$t('driver.qrDriver.enable')}}</div>
            <el-switch
                v-model="formData['attributes']['tarkan.enableQrCode']"
                :inactive-text="$t('disabled')"
                :active-text="$t('enabled')"
                :active-value="true"
                :inactive-value="false"
            >
            </el-switch>
          </div>
          <div style="border: 1px solid;margin-top: -4px;margin-bottom: 10px;padding: 7px;border-radius: 0px 0px 5px 5px; border-color: var(--el-color-info-light);" v-if="formData['attributes']['tarkan.enableQrCode'] && formData['attributes']['tarkan.enableQrCode']===true">

            <el-form-item :label="KT('driver.qrDriver.username')" >
              <el-input v-model="formData['attributes']['tarkan.driverUsername']" ></el-input>
            </el-form-item>

            <el-form-item :label="KT('driver.qrDriver.password')" >
              <el-input v-model="formData['attributes']['tarkan.driverPassword']" type="password"></el-input>
            </el-form-item>


            <div style="margin-top: 15px;font-weight: bold;border-bottom: silver 1px dotted;padding-bottom: 5px;">
              {{$t('driver.qrDriver.behaviorRules')}}
            </div>
            <div  style="display: flex;margin-top: 5px;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
              <div>
                {{$t('driver.qrDriver.unlockDevice')}}
              </div>
              <el-switch
                  v-model="formData['attributes']['tarkan.driverUnlockDevice']"
                  :inactive-text="$t('no')"
                  :active-text="$t('yes')"
                  :active-value="1"
                  :inactive-value="0"
              >
              </el-switch>
            </div>

            <div  style="display: flex;margin-top: 5px;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
              <div>
                {{$t('driver.qrDriver.autoLogout')}}
              </div>
              <el-switch
                  v-model="formData['attributes']['tarkan.driverAutoLogout']"
                  :inactive-text="$t('no')"
                  :active-text="$t('yes')"
                  :active-value="1"
                  :inactive-value="0"
              >
              </el-switch>
            </div>

            <el-form-item :label="KT('driver.qrDriver.autoLogoutTimeout')" prop="name">
              <el-select size="large" v-model="formData['attributes']['tarkan.autoLogoutTimeout']">
                <el-option :value="1" :label="'1 '+KT('minute')">1 {{KT('minute')}}</el-option>
                <el-option :value="2" :label="'2 '+KT('minutes')">2 {{KT('minutes')}}</el-option>
                <el-option :value="3" :label="'3 '+KT('minutes')">3 {{KT('minutes')}}</el-option>
                <el-option :value="4" :label="'4 '+KT('minutes')">4 {{KT('minutes')}}</el-option>
                <el-option :value="5" :label="'5 '+KT('minutes')">5 {{KT('minutes')}}</el-option>
              </el-select>
            </el-form-item>

          </div>



        </el-form>


      </el-tab-pane>


      <el-tab-pane :label="KT('device.devices')" name="devices">
          <tab-devices></tab-devices>

      </el-tab-pane>

      <el-tab-pane :label="KT('attribute.attributes')" name="fourth">
        <tab-attributes v-model="formData.attributes" :type="'driver'"></tab-attributes>
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

import {ElDialog,ElSelect,ElOption,ElMessage,ElTabs,ElTabPane,ElForm,ElSwitch,ElFormItem,ElButton,ElInput,ElNotification} from "element-plus";


import {ref,defineExpose} from 'vue';
import {useStore} from 'vuex'

import TabDevices from './tab-devices'

const store = useStore();



//import {ElDialog, ElForm, ElFormItem, ElInput, ElMessage, ElNotification} from 'element-plus'
import TabAttributes from "./tab-attributes";

import KT from '../../func/kt';

const title = ref('');

const show = ref(false);
const tab = ref('first');


const defaultDriverData = {
  "id":0,
  "attributes":{},
  "name":"",
  "uniqueId":""
}



// eslint-disable-next-line no-undef
const formData = ref(defaultDriverData);




const newDriver = ()=>{
  tab.value = 'first';
  title.value = KT('driver.newDriver');
  // eslint-disable-next-line no-undef
    formData.value = JSON.parse(JSON.stringify(defaultDriverData));
    show.value = true;
}

const editDriver = (id)=>{

  if(id===0){
    ElMessage.error(KT('driver.selectError'));
    return false;
  }


  title.value = KT('driver.editDriver');
  tab.value = 'first';
  // eslint-disable-next-line no-undef
  formData.value = JSON.parse(JSON.stringify(defaultDriverData));
  let driver = store.getters["drivers/getDriver"](id);


  // eslint-disable-next-line no-undef
  for(let k of Object.keys(defaultDriverData)){
    formData.value[k] = driver[k];
  }

  show.value = true;
}

defineExpose({
  newDriver,
  editDriver
});


const doCancel = ()=>{
  show.value = false;
}




const doSave = ()=>{



  ElNotification({
    title: KT('info'),
    message: KT('driver.saving'),
    type: 'info',
  });

  store.dispatch("drivers/save",formData.value).then(()=>{

    ElNotification({
      title: KT('success'),
      message: KT('driver.saved'),
      type: 'info',
    });
      show.value = false;
  }).catch(()=>{

    ElMessage.error(KT('driver.saveError'));
  });
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