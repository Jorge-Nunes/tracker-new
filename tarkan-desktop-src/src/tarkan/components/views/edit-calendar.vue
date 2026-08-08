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
      <el-tab-pane :label="KT('calendar.calendar')" name="first">
        <el-form label-width="120px" label-position="top">

          <el-form-item :label="KT('calendar.name')" >
            <el-input v-model="formData.name" ></el-input>
          </el-form-item>

          <div style="display: flex;justify-content: flex-end;padding-bottom: 5px;">
            <el-button @click="add()" type="primary">Adicionar Agendamento</el-button>
          </div>

          <div style="height: calc(70vh - 200px);border: silver 1px solid; border-radius: 5px;margin-bottom: 20px;overflow: auto;">
            <div  v-for="(r,kk) in formCalendars" :key="k+'-'+kk" style="display: flex;justify-content: space-between;padding: 10px;border-bottom: silver 1px dotted;">
              <div v-if="r.rules">
                <template v-if="r.rules.frequency==='DAILY'">
                  Todos os dias
                </template>
                <template v-else-if="r.rules.frequency==='WEEKLY'">
                  Semanalmente <span class="day" v-for="d in r.rules.days" style="margin-left: 4px;margin-top: 0px;" :key="k+'-'+kk+'-'+d">{{KT('weekDays.'+d)}}</span>
                </template>
                <template v-else-if="r.rules.frequency==='MONTHLY'">
                  Todo dia {{new Date(r.startDateTime).getDate()}}
                </template>
                <template v-else-if="r.rules.frequency==='YEARLY'">
                  Anualmente
                </template>

                <template v-if="r.rules.frequency">
                  das {{new Date(r.startDateTime).toLocaleTimeString()}} até {{new Date(r.endDateTime).toLocaleTimeString()}}
                </template>

                <template v-if="r.rules && r.rules.expires && r.rules.expires===2">
                  Expira em {{new Date(r.rules.until).toLocaleString()}}
                </template>

                <template v-if="r.rules && r.rules.expires && r.rules.expires===1">
                  Expira após {{r.rules.count}} ocorrencias.
                </template>
              </div>
              <div v-else>
                {{new Date(r.startDateTime).toLocaleString()}} até {{new Date(r.endDateTime).toLocaleString()}}
              </div>


              <div style="padding-right: 10px;font-size: 18px;" @click="remove(k)">
                <i class="fas fa-minus-square"></i>
              </div>
          </div>
          </div>

        </el-form>
      </el-tab-pane>


      <el-tab-pane :label="KT('attribute.attributes')" name="fourth">
        <tab-attributes v-model="formData.attributes" :type="'calendar'"></tab-attributes>
      </el-tab-pane>


      <el-tab-pane v-if="!isEdit" :label="KT('user.users')"  name="users">
        <tab-users ref="userList"></tab-users>
      </el-tab-pane>
    </el-tabs>
  </el-dialog>

  <el-dialog :lock-scroll="true" :width="'25%'" v-model="submodal">

    <template v-slot:title>
      <div  style="border-bottom: #e0e0e0 1px solid;padding: 20px;">
        <div class="modal-title" >Adicionar Agendamento</div>
      </div>
    </template>
    <template v-slot:footer>
      <div  style="border-top: #e0e0e0 1px solid;padding: 20px;display: flex;justify-content: space-between;">

        <el-button type="danger" plain @click="submodal=false">{{KT('cancel')}}</el-button>
        <el-button type="primary" @click="confirmAdd()">{{KT('calendar.confirmAdd')}}</el-button>
      </div>
    </template>

    <div style="padding: 10px;">
      <el-form label-position="top">
        <el-form-item :label="KT('calendar.startDate')" >
          <el-date-picker
              v-model="formAdd.startDateTime"
              type="datetime"
              size="large"
              :placeholder="KT('calendar.startDate')"
          >
          </el-date-picker>
        </el-form-item>

        <el-form-item :label="KT('calendar.endDate')" >
          <el-date-picker
              v-model="formAdd.endDateTime"
              type="datetime"
              size="large"
              :placeholder="KT('calendar.endDate')"
          >
          </el-date-picker>
        </el-form-item>

        <el-form-item :label="KT('calendar.repeat')" >
          <el-switch
              @change="formAdd.rules = ($event===true)?{frquency: 'DAILY',expires: 0,days: []}:false"
              :model-value = "(formAdd.rules && formAdd.rules!==false)"
              :inactive-text="KT('no')"
              :active-text="KT('yes')"
          >
          </el-switch>
        </el-form-item>


        <template v-if="formAdd.rules">
        <el-form-item :label="KT('calendar.frequency')" >
          <el-select v-model="formAdd.rules.frequency" filterable :size="'large'" :placeholder="KT('calendar.frequency')" :no-match-text="KT('NO_MATCH_TEXT')" :no-data-text="KT('NO_DATA_TEXT')">
            <el-option
                :label="KT('calendar.frequencyes.daily')"
                value="DAILY"
            >
            </el-option>
            <el-option
                :label="KT('calendar.frequencyes.weekly')"
                value="WEEKLY"
            >
            </el-option>
            <el-option
                :label="KT('calendar.frequencyes.monthly')"
                value="MONTHLY"
            >
            </el-option>
            <el-option
                :label="KT('calendar.frequencyes.yearly')"
                value="YEARLY"
            >
            </el-option>
          </el-select>
        </el-form-item>


          <template v-if="formAdd.rules && formAdd.rules.frequency==='DAILY'">
            <div style="padding: 10px;font-weight: bold;">Diariamente das {{new Date(formAdd.startDateTime).toLocaleTimeString()}} até {{new Date(formAdd.endDateTime).toLocaleTimeString()}}</div>
          </template>
          <template v-else-if="formAdd.rules && formAdd.rules.frequency==='WEEKLY'">

            <div style="display: flex;">
              <el-checkbox  @change="chk('SU',$event)" ></el-checkbox>
              <div style="padding: 12px;">Domingo</div>
            </div>
            <div style="display: flex;">
              <el-checkbox @change="chk('MO',$event)"></el-checkbox>
              <div style="padding: 12px;">Segunda-Feira</div>
            </div>
            <div style="display: flex;">
              <el-checkbox  @change="chk('TU',$event)"></el-checkbox>
              <div style="padding: 12px;">Terça-Feira</div>
            </div>
            <div style="display: flex;">
              <el-checkbox @change="chk('WE',$event)"></el-checkbox>
              <div style="padding: 12px;">Quarta-Feira</div>
            </div>
            <div style="display: flex;">
              <el-checkbox @change="chk('TH',$event)"></el-checkbox>
              <div style="padding: 12px;">Quinta-Feira</div>
            </div>
            <div style="display: flex;">
              <el-checkbox @change="chk('FR',$event)"></el-checkbox>
              <div style="padding: 12px;">Sexta-Feira</div>
            </div>
            <div style="display: flex;">
              <el-checkbox @change="chk('ST',$event)"></el-checkbox>
              <div style="padding: 12px;">Sábado</div>
            </div>
          </template>

          <el-form-item :label="KT('calendar.expires')" >
            <el-select v-model="formAdd.rules.expires" filterable :size="'large'" :placeholder="KT('calendar.expires')" :no-match-text="KT('NO_MATCH_TEXT')" :no-data-text="KT('NO_DATA_TEXT')">
              <el-option
                  :label="KT('calendar.expirations.never')"
                  :value="0"
              >
              </el-option>
              <el-option
                  :label="KT('calendar.expirations.count')"
                  :value="1"
              >
              </el-option>
              <el-option
                  :label="KT('calendar.expirations.until')"
                  :value="2"
              >
              </el-option>
            </el-select>
          </el-form-item>

          <template v-if="formAdd.rules && formAdd.rules.expires===1">
            <el-form-item :label="KT('calendar.occurrencies')">
              <el-input v-model="formAdd.rules.count"></el-input>
            </el-form-item>
          </template>

          <template v-else-if="formAdd.rules && formAdd.rules.expires===2">
            <el-form-item :label="KT('calendar.expiration')">
                <el-date-picker v-model="formAdd.rules.until" size="large"></el-date-picker>
            </el-form-item>

          </template>



        </template>

      </el-form>
    </div>
  </el-dialog>

</template>


<script setup>
import {ref,defineExpose} from 'vue';
import {useStore} from 'vuex'



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

import {ElDialog,ElDatePicker,ElCheckbox,ElMessage,ElTabs,ElTabPane,ElForm,ElSwitch,ElFormItem,ElSelect,ElOption,ElButton,ElInput} from "element-plus";

const store = useStore();
const isEdit = ref(false);

import KT from '../../../tarkan/func/kt'
import {parseIcs,toIcs} from '../../../tarkan/func/ics'

import {ElNotification} from "element-plus/es/components/notification";

//import {ElDialog, ElForm, ElFormItem, ElMessage, ElNotification} from 'element-plus'
import TabAttributes from "./tab-attributes";
import TabUsers from './tab-users'

const userList = ref(null);

const title = ref('');

const show = ref(false);
const submodal = ref(false);
const tab = ref('first');

const parseCalendar = (data)=>{
  return parseIcs(atob(data));
}



const defaultCalendarData = {
  "id":0,
  "attributes":{},
  "name":"",
  "data":""}


// eslint-disable-next-line no-undef
const formData = ref(defaultCalendarData);
const formCalendars = ref([]);
const formAdd = ref({});

const add = ()=>{
  formAdd.value = {};
  submodal.value = true;
}


const chk = (k,v)=>{


  if(v){
    formAdd.value.rules.days.push(k);
  }else{
    formAdd.value.rules.days.splice(formAdd.value.rules.days.findIndex((f)=> f === k),1);
  }
}

const remove = (c)=>{
  formCalendars.value.splice(formCalendars.value.findIndex((f) => f === c),1);
}

const confirmAdd = ()=>{
  formCalendars.value.push(formAdd.value);
  submodal.value = false;
}


const newCalendar = ()=>{
  tab.value = 'first';
  isEdit.value = false;
  title.value = KT('calendar.newCalendar');
  // eslint-disable-next-line no-undef
    formData.value = JSON.parse(JSON.stringify(defaultCalendarData));
    formCalendars.value = [];

    show.value = true;
}

const editCalendar = (id)=>{

  if(id===0){
    ElMessage.error(KT('calendar.selectError'));
    return false;
  }

  isEdit.value = true;

  title.value = KT('calendar.editCalendar');
  tab.value = 'first';
  // eslint-disable-next-line no-undef
  formData.value = JSON.parse(JSON.stringify(defaultCalendarData));
  const calendar = store.getters["calendars/getCalendarById"](id);

  formData.value.id = calendar.id;
  formData.value.name = calendar.name;
  formData.value.attributes = calendar.attributes;

  formCalendars.value = parseCalendar(calendar.data);


  show.value = true;
}

defineExpose({
  newCalendar,
  editCalendar
});


const doCancel = ()=>{
  show.value = false;
}




const doSave = async ()=>{


  ElNotification({
    title: KT('info'),
    message: KT('calendar.saving'),
    type: 'info',
  });


  formData.value.data = btoa(toIcs(formCalendars.value));




  store.dispatch("calendars/save",formData.value).then(async (data)=>{

    ElNotification({
      title: KT('success'),
      message: KT('calendar.saved'),
      type: 'info',
    });

    if(!isEdit.value) {

      if(userList.value.selected.length>0) {
        for(let device of userList.value.selected){

          ElNotification({
            title: 'Info',
            message: KT('calendar.linkToUser',device),
            type: 'info',
          });

          await window.$traccar.linkObjects({userId: device.id,calendarId: data.id});

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
    ElMessage.error(KT('calendar.saveError'));
  });

  show.value = false;
}


</script>

<style>

.el-select.el-select--large,.el-input.el-input--large{
  width: 100% !important;
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