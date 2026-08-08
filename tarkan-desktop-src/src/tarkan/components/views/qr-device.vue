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

    <el-form  ref="formRef" :model="formData" :rules="rules" label-width="120px" label-position="top">
    <el-tabs v-model="tab">
      <el-tab-pane :label="KT('device.device')" name="first">
          <div style="display: flex;">
            <div style="flex: 1;margin-right: 5px;">
              <el-form-item :label="KT('device.imei')" prop="uniqueId">
                <el-input disabled v-model="formData.uniqueId"></el-input>
              </el-form-item>
              <el-form-item :label="KT('device.name')" prop="name">
                <el-input disabled v-model="formData.name"></el-input>
              </el-form-item>



              <div style="margin-top: 15px;font-weight: bold;border-bottom: silver 1px dotted;padding-bottom: 5px;">
                {{$t('driver.qrDriver.behaviorRules')}}
              </div>
              <div  style="display: flex;margin-top: 5px;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('driver.qrDriver.lockDevice')}}
                </div>
                <el-switch
                    v-model="formData['attributes']['tarkan.driverLockDevice']"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>

              <el-form-item :label="KT('driver.qrDriver.lockDeviceTimeout')" prop="name">
                <el-select size="large" v-model="formData['attributes']['tarkan.lockDeviceTimeout']">
                  <el-option :value="1" :label="'1 Minuto'">1 Minuto</el-option>
                  <el-option :value="2" :label="'2 Minutos'">2 Minutos</el-option>
                  <el-option :value="3" :label="'3 Minutos'">3 Minutos</el-option>
                  <el-option :value="4" :label="'4 Minutos'">4 Minutos</el-option>
                  <el-option :value="5" :label="'5 Minutos'">5 Minutos</el-option>
                </el-select>
              </el-form-item>

            </div>
            <div style="flex: 1;margin-left: 5px;text-align: center;">

              <img v-if="qrDataURL" :src="qrDataURL"><br>

              <el-button @click="doDownload()" type="primary">Download</el-button><br>
            </div>
          </div>
      </el-tab-pane>


    </el-tabs>

    </el-form>
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

import {ElSelect,ElOption,ElDialog,ElMessage,ElMessageBox,ElNotification,ElTabs,ElTabPane,ElForm,ElFormItem,ElButton,ElInput,ElSwitch} from "element-plus";



import {ref, defineExpose, reactive} from 'vue';
import {useStore} from 'vuex'

const store = useStore();

//import {ElDialog, ElForm, ElFormItem, ElInput, ElMessage, ElMessageBox, ElNotification} from 'element-plus'



import KT from "../../func/kt";

const show = ref(false);
const tab = ref('first');
const title = ref('');

const formRef = ref(null);
const odometerData = ref(0);
const qrDataURL = ref(false);


const rules = reactive({
  name: [
    {
      required: true,
      message: KT('device.form.nameEmpty'),
      trigger: 'blur',
    }
  ],
  uniqueId: [
    {
      required: true,
      message: KT('device.form.uniqueIdEmpty'),
      trigger: 'blur',
    }
  ],
  });




const hue = ref(0);
const saturation = ref(100);
const brightnes = ref( 100);

const hue2 = ref(0);
const saturation2 = ref(100);
const brightnes2 = ref( 100);



// eslint-disable-next-line no-undef
const formData = ref(defaultDeviceData);





const getColorsFromAttribute = ()=>{
  const attrColor = formData.value.attributes['tarkan.color'];
  const attrColorExtra = formData.value.attributes['tarkan.color_extra'];

  if(attrColor){


    const tmp = formData.value.attributes['tarkan.color'].split("|");

    hue.value = parseInt((tmp[0])?tmp[0]:0);
    saturation.value = parseInt((tmp[1])?(tmp[1]*100):100);
    brightnes.value = parseInt((tmp[2])?(tmp[2]*100):100);
  }else{
    hue.value = 0;
    saturation.value = 0;
    brightnes.value = 180;
  }


  if(attrColorExtra){

    const tmp = formData.value.attributes['tarkan.color_extra'].split("|");


    hue2.value = parseInt((tmp[0])?tmp[0]:0);
    saturation2.value = parseInt((tmp[1])?(tmp[1]*100):100);
    brightnes2.value = parseInt((tmp[2])?(tmp[2]*100):100);
  }else{
    hue2.value = 0;
    saturation2.value = 0;
    brightnes2.value = 180;
  }


}


const newDevice = ()=>{

  tab.value = 'first';
  title.value = KT('device.add');
  // eslint-disable-next-line no-undef
    formData.value = JSON.parse(JSON.stringify(defaultDeviceData));
    show.value = true;
    odometerData.value = 0;

}

const editDevice = (id)=>{

  tab.value = 'first';
  title.value = KT('device.manageQrCode');
  // eslint-disable-next-line no-undef
  formData.value = JSON.parse(JSON.stringify(defaultDeviceData));

  const device = store.getters["devices/getDevice"](id);

  // eslint-disable-next-line no-undef
  for(let k of Object.keys(defaultDeviceData)){
    if(k==='attributes') {
      formData.value[k] = (device[k] === null) ? {} : JSON.parse(JSON.stringify(device[k]));
    }else {
      formData.value[k] = (device[k] === null) ? "" : device[k];
    }
  }

  getColorsFromAttribute();
  const pos = store.getters["devices/getPosition"](device.id);

  odometerData.value = (pos)?pos.attributes['totalDistance']:0;

  const URL = document.location.protocol+'//'+document.location.host+'/qr-driver/scan/'+formData.value.id;

  qrDataURL.value = 'https://chart.googleapis.com/chart?cht=qr&chs=300x300&chl='+URL+'&chld=H|1';

  show.value = true;
}

const toDataURL = (url)=>{
  return fetch(url).then((response) => {
    return response.blob();
  }).then(blob => {
    return URL.createObjectURL(blob);
  });
}

const doDownload = async ()=>{

  const URL = document.location.protocol+'//'+document.location.host+'/qr-driver/scan/'+formData.value.id;

  var el = document.createElement("a");
  el.setAttribute("href", await toDataURL('https://chart.googleapis.com/chart?cht=qr&chs=300x300&chl='+URL+'&chld=H|1'));
  el.setAttribute("download", formData.value.id+'-'+formData.value.name+'.png');
  document.body.appendChild(el);
  el.click();
  el.remove();
}

defineExpose({
  newDevice,
  editDevice
});


const doCancel = ()=>{
  show.value = false;
}

const doSave = ()=>{


  formRef.value.validate((valid)=> {

    if (valid) {
      formData.value.attributes['tarkan.color'] = hue.value + '|' + (saturation.value / 100).toFixed(2) + '|' + (brightnes.value / 100).toFixed(2);
      formData.value.attributes['tarkan.color_extra'] = hue2.value + '|' + (saturation2.value / 100).toFixed(2) + '|' + (brightnes2.value / 100).toFixed(2);


      ElNotification({
        title: KT('info'),
        message: KT('device.saving'),
        type: 'info',
      });

      formData.value.uniqueId = formData.value.uniqueId.trim();

      store.dispatch("devices/save", formData.value).then((d) => {


        store.dispatch("devices/accumulators",{deviceId: d.id,totalDistance: odometerData.value});


        ElNotification({
          title: KT('success'),
          message: KT('device.saved'),
          type: 'info',
        });
        show.value = false;
      }).catch((r) => {

        const err = r.response.data.split("-")[0].trim().replaceAll(" ", "_").toUpperCase();


        ElMessageBox.alert(KT('device.error.' + err), KT('device.saveError'), {
          confirmButtonText: 'OK'
        })
      });
    } else {

      ElMessage.error(KT('device.error.checkForm'));
    }
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