<template>
  <el-dialog :lock-scroll="true" v-model="show" title="Teste">

    <template v-slot:title>
      <div  style="border-bottom: #e0e0e0 1px solid;padding: 20px;">
        <div class="modal-title" >Cadastrar Atributo</div>
      </div>
    </template>
    <template v-slot:footer>
      <div  style="border-top: #e0e0e0 1px solid;padding: 20px;display: flex;justify-content: space-between">
        <div><el-button  type="danger" @click="doDelete()">Excluir</el-button></div>
        <div><el-button type="danger" plain @click="doCancel()">Cancelar</el-button>
        <el-button type="primary" @click="doSave()">Salvar</el-button></div>
      </div>
    </template>

    <el-tabs v-model="tab">
      <el-tab-pane label="Comando" name="first">
        <el-form label-width="120px" label-position="top">
          <el-form-item label="Descrição" >
            <el-input v-model="formData.description"></el-input>
          </el-form-item>

          <el-form-item label="Atributo" >
            <el-input v-model="formData.attribute"></el-input>
          </el-form-item>

          <el-form-item label="Tipo de Saída">
            <el-select v-model="formData.type" @change="testExp()" filterable placeholder="Tipo de Saída" :size="'large'" :no-data-text="'Nenhum tipo disponível'">

              <el-option
                  :key="1"
                  :label="'Texto'"
                  :value="'string'"

              >
              </el-option>


              <el-option
                  :key="2"
                  :label="'Numérico'"
                  :value="'number'"

              >
              </el-option>


              <el-option
                  :key="3"
                  :label="'Lógico'"
                  :value="'boolean'"

              >
              </el-option>
            </el-select>
          </el-form-item>

          <div style="display: flex;">
            <div style="flex: 1;margin-right: 5px;">
            <el-form-item label="Expressão">
              <el-input
                  v-model="formData.expression"
                  :autosize="{minRows: 4}"
                  type="textarea"
                  placeholder="Please input"
                  @keyup="expChanged()"
              />
            </el-form-item>
              </div>

            <div style="flex: 1;margin-left: 5px;">

              <el-form-item label="Testar Atributo em">
              <el-select v-model="deviceId" @change="testExp()" :value-key="'id'" filterable placeholder="Grupo" :size="'large'" :no-data-text="'Nenhum tipo disponível'">
                <el-option
                    label="Não Testar"
                    :value="0"

                >
                </el-option>
                <el-option
                    v-for="item in store.state.devices.deviceList"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id"

                >
                </el-option>
              </el-select>
              </el-form-item>
            <el-form-item label="Resultado">
              <el-input
                  disabled
                  v-model="resultExp"
                  :autosize="{minRows: 4}"
                  type="textarea"
                  placeholder="Please input"
              />
            </el-form-item>
              </div>
          </div>


          <br>
          <el-switch
              v-model="changeNative"
              active-text="Aplicar este atributo aos meus favoritos"
          >
          </el-switch>






        </el-form>


      </el-tab-pane>

      <el-tab-pane label="Dispositivos" :name="devices">
        <tab-devices ref="deviceList"></tab-devices>
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

import {ElDialog,ElTabs,ElTabPane,ElForm,ElSwitch,ElFormItem,ElSelect,ElOption,ElButton,ElInput} from "element-plus";


import TabDevices from "./tab-devices";

const defaultGeofenceData = {
  "id":0,
  "description":"",
  "attribute":"",
  "expression":"",
  "type":"string"}


import {ElNotification} from 'element-plus/es/components/notification';
import {ElMessageBox} from 'element-plus/es/components/message-box';
import {ref, defineExpose} from 'vue';
import {useStore} from 'vuex'





const store = useStore();


const deviceList = ref(null);
const show = ref(false);
const tab = ref('first');

const changeNative = ref(false);



// eslint-disable-next-line no-undef
const formData = ref(defaultGeofenceData);
const deviceId = ref(0);
const resultExp = ref('');

let timer = null;

const testExp = ()=>{
  window.$traccar.attributeTest(deviceId.value,formData.value).then(({data})=>{
    resultExp.value = data;
  }).catch((r)=>{
    resultExp.value = r.response.data;
  })
}

const expChanged = ()=>{
    if(timer){
      clearInterval(timer);
    }

    if(deviceId.value!==0) {

      timer = setTimeout(() => {
         testExp();
      }, 1000)
    }
}

const newAttribute = ()=>{
  // eslint-disable-next-line no-undef
    formData.value = JSON.parse(JSON.stringify(defaultGeofenceData));
    show.value = true;
}

function decodeEntity(inputStr) {
  var textarea = document.createElement("textarea");
  textarea.innerHTML = inputStr;
  return textarea.value;
}

const editAttribute = (attribute)=>{

  // eslint-disable-next-line no-undef
  formData.value = JSON.parse(JSON.stringify(defaultGeofenceData));



  for(let k of Object.keys(defaultGeofenceData)){
    if(k==='expression'){

      formData.value[k] = decodeEntity(attribute[k]);
    }else {
      formData.value[k] = attribute[k];
    }
  }
  //Object.assign(formData.value,JSON.parse(JSON.stringify(attribute)));

  show.value = true;
}

defineExpose({
  newAttribute,
  editAttribute
});




const doCancel = ()=>{
  show.value = false;
}

const doDelete = ()=>{
  ElMessageBox.confirm('Deseja realmente excluir este atributo computado?','Tem certeza?').then(()=>{
    store.dispatch("attributes/delete",formData.value.id).then(()=>{
      show.value = false;
      ElNotification({
        title: 'Info',
        message: 'Atributo computado deletado com suceso.',
        type: 'info',
      });
    })
  });
}


const doSave = ()=>{

  ElNotification({
    title: 'Info',
    message: 'Cadastrando seu atributo...',
    type: 'info',
  });

  const tmp = JSON.parse(JSON.stringify(defaultGeofenceData));

  Object.assign(tmp,formData.value);



  store.dispatch("attributes/save",tmp).then(async (data)=>{



    const selectedDevices = deviceList.value.selected;

    if(selectedDevices.length>0) {
      for(let device of selectedDevices){

        console.log(device);

        ElNotification({
          title: 'Info',
          message: 'Vinculando atributo ao dispositivo '+device.name,
          type: 'info',
        });

        await window.$traccar.linkObjects({deviceId: device.id,attributeId: data.id});

      }
    }


    ElNotification({
      title: 'Successo',
      message: 'Atributo computado cadastrado com sucesso.',
      type: 'success',
    });

    show.value = false;

  })




  /*

  tmp.name = formData.value.name;
  tmp.area = getParsedArea();

  store.dispatch("geofences/save",tmp).then(()=>{
      show.value = false;
  })*/
}


</script>

<style>

.el-button-group{
  display: flex !important;
}

.el-button-group .el-button--large{
  flex: 1 !important;
}

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



.isCar{
  border: #05a7e3 1px solid !important;
}
</style>