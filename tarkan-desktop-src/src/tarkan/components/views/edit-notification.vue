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

        <el-button type="info"  @click="doPlay()">{{KT('notification.listen')}}</el-button>

        <el-button type="primary" @click="doSave()">{{KT('save')}}</el-button>
      </div>
    </template>

    <el-tabs v-model="tab">
      <el-tab-pane :label="KT('notification.notification')" name="first">
        <el-form label-width="120px" label-position="top">
          <el-form-item :label="KT('notification.type')" >
            <el-select v-model="formData.type" filterable :size="'large'" :placeholder="KT('notification.type')" :no-match-text="KT('NO_MATCH_TEXT')" :no-data-text="KT('NO_DATA_TEXT')">
              <el-option
                  v-for="item in optionType"
                  :key="item.type"
                  :label="KT('notification.types.'+item.type)"
                  :value="item.type"
              >
              </el-option>
            </el-select>
          </el-form-item>


          <el-form-item v-if="formData.type==='alarm'" :label="KT('notification.alarms')" >
            <el-select v-model="formData.attributes['alarms']" filterable multiple :size="'large'"  :placeholder="KT('notification.alarms')" :no-match-text="KT('NO_MATCH_TEXT')" :no-data-text="KT('NO_DATA_TEXT')">
              <el-option
                  v-for="item in alarmType"
                  :key="item"
                  :label="KT('alarms.'+item)"
                  :value="item"
              >
              </el-option>
            </el-select>
          </el-form-item>

          <el-form-item :label="KT('notification.all')" >
            <el-switch
                v-model="formData.always"
                :inactive-text="KT('no')"
                :active-text="KT('yes')"
            >
            </el-switch>
          </el-form-item>


          <el-form-item :label="KT('notification.autoAdd')" >
            <el-switch
                v-model="formData.attributes['tarkan.autoadd']"
                :inactive-text="KT('no')"
                :active-text="KT('yes')"
            >
            </el-switch>
          </el-form-item>

          <el-form-item :label="KT('notification.channel')" >
            <el-select v-model="formData.notificators" filterable multiple :size="'large'"  :placeholder="KT('notification.channel')" :no-match-text="KT('NO_MATCH_TEXT')" :no-data-text="KT('NO_DATA_TEXT')">
              <el-option
                  v-for="item in options"
                  :key="item.type"
                  :label="KT('notification.channels.'+item.type)"
                  :value="item.type"
              >
              </el-option>
            </el-select>
          </el-form-item>

        </el-form>


      </el-tab-pane>


      /* IFTRUE_myFlag */
      <el-tab-pane :label="KT('notification.customize')" name="customize">
        <el-form label-width="120px" label-position="top">

          <el-form-item :label="KT('notification.pin')" >
            <el-switch
                v-model="customizeData.pin"
                :inactive-text="KT('no')"
                :active-text="KT('yes')"
            >
            </el-switch>
          </el-form-item>

          <el-form-item :label="KT('notification.position')" >
            <el-select v-model="customizeData.position" filterable :size="'large'" :placeholder="KT('notification.position')" :no-match-text="KT('NO_MATCH_TEXT')" :no-data-text="KT('NO_DATA_TEXT')">
              <el-option
                  v-for="item in customizePosition"
                  :key="item.value"
                  :label="KT('notification.positions.'+item.value)"
                  :value="item.value"
              >
              </el-option>
            </el-select>
          </el-form-item>


          <el-form-item :label="KT('notification.color')" >
            <el-select v-model="customizeData.color" filterable :size="'large'" :placeholder="KT('notification.color')" :no-match-text="KT('NO_MATCH_TEXT')" :no-data-text="KT('NO_DATA_TEXT')">
              <el-option
                  v-for="item in customizeColors"
                  :key="item.value"
                  :label="KT('notification.colors.'+item.value)"
                  :value="item.value"
              >

                <span style="float: left;margin-top: 7px;display: block;width: 20px;height: 20px;" :style="{'background': item.color}"></span>
                <span style="margin-left: 10px;">{{ KT('notification.colors.'+item.value) }}</span>
              </el-option>
            </el-select>
          </el-form-item>



          <el-form-item :label="KT('notification.sound')" >
            <el-select v-model="customizeData.sound" filterable :size="'large'" :placeholder="KT('notification.sound')" :no-match-text="KT('NO_MATCH_TEXT')" :no-data-text="KT('NO_DATA_TEXT')">
              <el-option
                  v-for="item in customizeSounds"
                  :key="item.value"
                  :label="KT('notification.sounds.'+item.value)"
                  :value="item.value"
              >
              </el-option>
            </el-select>
          </el-form-item>


          <el-form-item v-if="customizeData.sound==='custom'" :label="KT('notification.soundURL')" >
            <el-input v-model="customizeData.soundURL"></el-input>
          </el-form-item>



        </el-form>


      </el-tab-pane>

      /* FITRUE_myFlag */
      <el-tab-pane :label="KT('attribute.attributes')" name="fourth">
        <tab-attributes v-model="formData.attributes" :type="'notification'"></tab-attributes>
      </el-tab-pane>


      <el-tab-pane  :label="KT('device.devices')" :disabled="formData.always" name="devices">
        <tab-devices ref="deviceList"></tab-devices>
      </el-tab-pane>

      <el-tab-pane  :label="KT('group.groups')" :disabled="formData.always" name="groups">
        <tab-groups ref="groupList"></tab-groups>
      </el-tab-pane>


      <el-tab-pane  :label="KT('user.users')"  name="users">
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

import {ElDialog,ElMessage,ElNotification,ElTabs,ElTabPane,ElForm,ElSwitch,ElFormItem,ElSelect,ElOption,ElButton,ElInput} from "element-plus";


import {ref,defineExpose,nextTick} from 'vue';
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

const options = ref([]);
const optionType = ref([]);
const alarmType = ref("general,sos,vibration,movement,lowspeed,overspeed,fallDown,lowPower,lowBattery,fault,powerOff,powerOn,door,lock,unlock,geofence,geofenceEnter,geofenceExit,gpsAntennaCut,accident,tow,idle,highRpm,hardAcceleration,hardBraking,hardCornering,laneChange,fatigueDriving,powerCut,powerRestored,jamming,temperature,parking,shock,bonnet,footBrake,fuelLeak,tampering,removing".split(","));

const customizeColors = ref([
  {value: 'soft-red',label: 'Vermelho Claro',color: '#ffdddd'},
  {value: 'red',label: 'Vermelho',color: '#f44336'},
  {value: 'soft-green',label: 'Verde Claro',color: '#ddffdd'},
  {value: 'green',label: 'Verde',color: '#4CAF50'},
  {value: 'soft-yellow',label: 'Amarelo Claro',color: '#ffffcc'},
  {value: 'yellow',label: 'Amarelo',color: '#ffeb3b'},
  {value: 'soft-info',label: 'Azul Claro',color: '#ddffff'},
  {value: 'info',label: 'Azul',color: '#2196F3'},
]);

const customizePosition = ref([
  {value: 'top-right',name: 'Superior Direito'},
  {value: 'top-left',name: 'Superior Esquerdo'},
  {value: 'bottom-right',name: 'Inferior Direito'},
  {value: 'bottom-left',name: 'Inferior Esquerdo'}
]);

const customizeSounds = ref([
  {value: 'mute'},
  {value: 'custom'},
  {value: 'audio1'},
  {value: 'audio2'},
  {value: 'audio3'},
  {value: 'audio4'},
  {value: 'audio5'},
  {value: 'audio6'},
  {value: 'audio7'},
  {value: 'audio8'},
  {value: 'audio9'},
  {value: 'audio10'},
  {value: 'audio11'},
  {value: 'audio12'},
  {value: 'audio13'},
  {value: 'audio14'},
  {value: 'audio15'},
  {value: 'audio16'},
  {value: 'audio17'},
  {value: 'audio18'},
  {value: 'audio19'},
  {value: 'audio20'},
  {value: 'audio21'},
  {value: 'audio22'},
  {value: 'audio23'},
  {value: 'audio24'}
]);

const title = ref('');

const show = ref(false);
const tab = ref('first');

const customizeData = ref({
  position: '',
  color: '',
  sound: '',
  soundURL: '',
  pin: false
});

const doPlay = ()=>{
  if(customizeData.value.sound!=='') {
    store.dispatch("events/playSound",customizeData.value.sound);
  }
}

const onShow = ()=>{
  window.$traccar.getNotificators().then(({data})=>{
    options.value = data;
  });

  window.$traccar.getNotificationTypes().then(({data})=>{
    optionType.value = data;
  });
}

const defaultNotificationData = {
  id: 0,
  attributes: {},
  calendarId: 0,
  always: true,
  type: "",
  notificators: ""}


// eslint-disable-next-line no-undef
const formData = ref(defaultNotificationData);




const newNotification = ()=>{
  tab.value = 'first';
  onShow();

  isEdit.value = false;
  title.value = KT('notification.newNotification');


  show.value = true;
	nextTick(()=>{
  groupList.value.clear();
  deviceList.value.clear();
  userList.value.clear();
});
  // eslint-disable-next-line no-undef
    formData.value = JSON.parse(JSON.stringify(defaultNotificationData));
    customizeData.value = {
      position: '',
      color: '',
      sound: '',
      soundURL: '',
      pin: false
    };
}

const editNotification = (id)=>{

  if(id===0){
    ElMessage.error(KT('notification.selectError'));
    return false;
  }

  onShow();
  isEdit.value = true;

  title.value = KT('notification.editNotification');
  tab.value = 'first';
  // eslint-disable-next-line no-undef
  formData.value = JSON.parse(JSON.stringify(defaultNotificationData));
  const notification = JSON.parse(JSON.stringify(store.getters["events/getNotificationById"](id)));

  formData.value.id = notification.id;
  formData.value.type = notification.type;
  formData.value.always = notification.always;
  formData.value.attributes = notification.attributes;
  formData.value.attributes['alarms'] = (notification.attributes['alarms'])?notification.attributes['alarms'].split(","):[];
  formData.value.notificators = notification.notificators.split(",");

  customizeData.value = {
    position: '',
    color: '',
    sound: '',
    soundURL: '',
    pin: false
  };

  if(formData.value.attributes['tarkan.position']){
    customizeData.value.position = formData.value.attributes['tarkan.position'];
  }

  if(formData.value.attributes['tarkan.color']){
    customizeData.value.color = formData.value.attributes['tarkan.color']
  }

  if(formData.value.attributes['tarkan.sound']){
     customizeData.value.sound = formData.value.attributes['tarkan.sound']
  }

  if( formData.value.attributes['tarkan.soundURL'] ){
    customizeData.value.soundURL =  formData.value.attributes['tarkan.soundURL'] ;
  }

  if(formData.value.attributes['tarkan.pin']){
      customizeData.value.pin = formData.value.attributes['tarkan.pin'];
  }


  show.value = true;
}

defineExpose({
  newNotification,
  editNotification
});


const doCancel = ()=>{
  show.value = false;
}




const doSave = async ()=>{


  ElNotification({
    title: KT('info'),
    message: KT('notification.saving'),
    type: 'info',
  });

  formData.value.notificators = formData.value.notificators.join(",");
  if(formData.value.attributes['alarms']) {
    formData.value.attributes['alarms'] = formData.value.attributes['alarms'].join(",");
  }

  if(customizeData.value.position!==''){
    formData.value.attributes['tarkan.position'] = customizeData.value.position;
  }

  if(customizeData.value.color!==''){
    formData.value.attributes['tarkan.color'] = customizeData.value.color;
  }

  if(customizeData.value.sound!==''){
    formData.value.attributes['tarkan.sound'] = customizeData.value.sound;
  }

  if(customizeData.value.soundURL!==''){
    formData.value.attributes['tarkan.soundURL'] = customizeData.value.soundURL;
  }

  if(customizeData.value.pin!==false){
    formData.value.attributes['tarkan.pin'] = customizeData.value.pin;
  }else{
    formData.value.attributes['tarkan.pin'] = false;
  }

  store.dispatch("events/save",formData.value).then(async (data)=>{

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

          await window.$traccar.linkObjects({deviceId: device.id,notificationId: data.id});

        }
      }

      if(groupList.value.selected.length>0) {
        for(let device of groupList.value.selected){

          ElNotification({
            title: 'Info',
            message: KT('notification.linkToGroup',device),
            type: 'info',
          });

          await window.$traccar.linkObjects({groupId: device.id,notificationId: data.id});

        }
      }

      if(userList.value.selected.length>0) {
        for(let device of userList.value.selected){

          ElNotification({
            title: 'Info',
            message: KT('notification.linkToUser',device),
            type: 'info',
          });

          await window.$traccar.linkObjects({userId: device.id,notificationId: data.id});

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
    ElMessage.error(KT('notification.saveError'));
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