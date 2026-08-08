<template>
  <el-dialog :lock-scroll="true" v-model="show">
    <template v-slot:title>
      <div  style="border-bottom: #e0e0e0 1px solid;padding: 20px;">
        <div class="modal-title" >{{title}}</div>
      </div>
    </template>
    <template v-slot:footer>
      <div  style="border-top: #e0e0e0 1px solid;padding: 20px;display: flex;justify-content: space-between">
        <div><el-button  type="danger" @click="doDelete()">{{KT('delete')}}</el-button></div>
        <div><el-button type="danger" plain @click="doCancel()">{{KT('cancel')}}</el-button>
          <el-button type="primary" @click="doSave()">{{KT('save')}}</el-button></div>
      </div>
    </template>

    <el-tabs v-model="tab">
      <el-tab-pane :label="KT('group.group')" name="first">
        <el-form label-width="120px" label-position="top">
          <el-form-item :label="KT('group.name')" >
            <el-input v-model="formData.name"></el-input>
          </el-form-item>


          <el-form-item :label="KT('group.father')" >
            <el-select v-model="formData.groupId" :value-key="'id'" filterable :placeholder="KT('group.father')" :size="'large'" :no-data-text="KT('NO_DATA_TEXT')">


              <el-option
                  :label="KT('no')"
                  :value="0"
              >
              </el-option>
              <el-option
                  v-for="item in store.state.groups.groupList"
                  :key="item.id"
                  :label="item.name"
                  :value="item.id"
              >
              </el-option>
            </el-select>
          </el-form-item>



        </el-form>


      </el-tab-pane>

      <el-tab-pane :label="KT('group.attributes')" name="attributes">
          <tab-attributes v-model="formData.attributes"></tab-attributes>
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

import {ElDialog,ElTabs,ElTabPane,ElForm,ElFormItem,ElSelect,ElOption,ElButton,ElInput} from "element-plus";


import KT from "../../func/kt";

import TabAttributes from "./tab-attributes";

const defaultGeofenceData = {
  "id":0,
  "attributes":{},
  "groupId":0,
  "name":""
}


import {ElMessageBox, ElNotification} from 'element-plus';
import {ref, defineExpose} from 'vue';
import {useStore} from 'vuex'

const title = ref('');


const store = useStore();


const show = ref(false);
const tab = ref('first');





// eslint-disable-next-line no-undef
const formData = ref(defaultGeofenceData);



const newGroup = ()=>{
  title.value = 'Cadastrar Grupo';
  tab.value = 'first';
  // eslint-disable-next-line no-undef
    formData.value = JSON.parse(JSON.stringify(defaultGeofenceData));
    show.value = true;
}

const editGroup = (command)=>{

  title.value = 'Editar Grupo';
  tab.value = 'first';
  // eslint-disable-next-line no-undef
  formData.value = JSON.parse(JSON.stringify(defaultGeofenceData));


  Object.assign(formData.value,JSON.parse(JSON.stringify(command)));

  show.value = true;
}

defineExpose({
  newGroup,
  editGroup
});


const doCancel = ()=>{
  show.value = false;
}


const doDelete = ()=>{
  ElMessageBox.confirm('Deseja realmente excluir este grupo?','Tem certeza?').then(()=>{
    store.dispatch("groups/delete",formData.value.id).then(()=>{
      show.value = false;
      ElNotification({
        title: 'Info',
        message: 'Grupo deletado com suceso.',
        type: 'info',
      });
    })
  });
}

const doSave = ()=>{

  ElNotification({
    title: 'Info',
    message: 'Cadastrando seu grupo...',
    type: 'info',
  });

  const tmp = JSON.parse(JSON.stringify(defaultGeofenceData));

  Object.assign(tmp,formData.value);



  store.dispatch("groups/save",tmp).then(async ()=>{


    ElNotification({
      title: 'Successo',
      message: 'Grupo cadastrado com sucesso.',
      type: 'success',
    });

    show.value = false;

  })


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