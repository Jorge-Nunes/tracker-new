<template>
  <el-dialog :lock-scroll="true" width="400px" v-model="show">

    <template v-slot:title>
      <div  style="border-bottom: #e0e0e0 1px solid;padding: 20px;">
        <div class="modal-title" >{{title}}</div>
      </div>
    </template>
    <template v-slot:footer>
      <div  style="border-top: #e0e0e0 1px solid;padding: 20px;display: flex;justify-content: space-between;">

        <el-button type="danger" plain @click="doCancel()">Cancelar</el-button>
        <el-button type="primary" @click="doSave()">Salvar</el-button>
      </div>
    </template>

    <el-tabs v-model="tab">
      <el-tab-pane label="Dados da Conta" name="first">
        <el-form label-width="120px" label-position="top">
          <el-form-item label="Nome" >
            <el-input v-model="formData.name" ></el-input>
          </el-form-item>

          <el-form-item label="Dispositivo" >
            <el-select v-model="formData.deviceId" :value-key="'id'" filterable :placeholder="KT('device.device')" :size="'large'"  :no-match-text="KT('NO_MATCH_TEXT')" :no-data-text="KT('NO_DATA_TEXT')">

              <el-option
                  v-for="item in store.state.devices.deviceList"
                  :key="item.id"
                  :label="item.name"
                  :value="item.id"
              >
              </el-option>
            </el-select>
          </el-form-item>

          <el-form-item v-if="store.getters['advancedPermissions'](10)" label="Permitir Bloquear" >
          <el-switch
              v-model="formData.limitCommands"
              inactive-text="Não"
              active-text="Sim"

              :active-value="false"
              :inactive-value="true"
          >
          </el-switch>
          </el-form-item>

            <el-form-item label="Duração" >
              <el-date-picker
                  size="large"
                  v-model="formData.expirationTime"
                  type="datetime"
                  placeholder="Select date and time"
                  :shortcuts="shortcuts"
                  format="DD/MM/YYYY HH:mm"
              >
              </el-date-picker>
            </el-form-item>



        </el-form>


      </el-tab-pane>
    </el-tabs>
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
import 'element-plus/es/components/date-picker/style/css'

import {ElDialog,ElDatePicker,ElMessage,ElTabs,ElTabPane,ElForm,ElSwitch,ElFormItem,ElSelect,ElOption,ElButton,ElInput} from "element-plus";

const store = useStore();

import KT from "../../func/kt";


const title = ref('');

const show = ref(false);
const tab = ref('first');

const shortcuts = [
  {
    text: '30 minutos',
    value: () => {
      const date = new Date()
      date.setTime(date.getTime() + (3600000/2))
      return date
    },
  },
  {
    text: '1 hora',
    value: () => {
      const date = new Date()
      date.setTime(date.getTime() + 3600000)
      return date
    },
  },
  {
    text: '2 horas',
    value: () => {
      const date = new Date()
      date.setTime(date.getTime() + (2*3600000))
      return date
    },
  },
  {
    text: '3 horas',
    value: () => {
      const date = new Date()
      date.setTime(date.getTime() + (3*3600000))
      return date
    },
  },
  {
    text: '1 dia',
    value: () => {
      const date = new Date()
      date.setTime(date.getTime() + (24*3600000))
      return date
    },
  },
  {
    text: '2 dias',
    value: () => {
      const date = new Date()
      date.setTime(date.getTime() + (2 * (24*3600000)))
      return date
    },
  },
]


const defaultUserData = {
    "id": 0,
    "name": "",
    "deviceId": 0,
    "expirationTime": "",
    "token": null,
    "limitCommands": true
  }


// eslint-disable-next-line no-undef
const formData = ref(defaultUserData);




const newShare = (deviceId=0)=>{
  tab.value = 'first';
  title.value = 'Criar compartilhamento';
  // eslint-disable-next-line no-undef
    formData.value = JSON.parse(JSON.stringify(defaultUserData));

    const date = new Date();
    date.setTime(date.getTime() + (3600000/2))

    formData.value.deviceId = deviceId;

    formData.value.expirationTime = date.toISOString();

    show.value = true;
}

const editShare = (id)=>{

  if(id===0){
    ElMessage.error('Você precisa selecionar um compartilhamento para editar.');
    return false;
  }


  title.value = 'Editar Compartilhamento';
  tab.value = 'first';
  // eslint-disable-next-line no-undef
  formData.value = JSON.parse(JSON.stringify(defaultUserData));
  let device = store.getters["shares/getShare"](id);


  // eslint-disable-next-line no-undef
  for(let k of Object.keys(defaultUserData)){
    formData.value[k] = device[k];
  }

  show.value = true;
}

defineExpose({
  newShare,
  editShare
});


const doCancel = ()=>{
  show.value = false;
}




const doSave = ()=>{

  if(formData.value.deviceId==='' || formData.value.deviceId<1) {
    ElMessage.error('Você precisa selecionar um dispositivo.');
  }else if(formData.value.expirationTime===''){
    ElMessage.error('Você precisa selecionar uma data de expiração.');
  }else{
    store.dispatch("shares/save", formData.value).then((r) => {

      const host = window.location.protocol+'//'+window.location.host+'/share/';
      const link = host+r.token;

      if (navigator.share) {
        navigator.share({
          title: r.name,
          url: link
        }).then(() => {
          show.value = false;
          console.log('Thanks for sharing!');
        }).catch(console.error);
      } else {
        const elm = document.createElement("input");
        elm.value = link;
        document.body.appendChild(elm);
        elm.select();
        document.execCommand("copy");
        document.body.removeChild(elm);

        ElMessage.success('Copiado para área de transferência');

        setTimeout(()=>{
          show.value = false;
        },2000)
      }


  })
  }
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