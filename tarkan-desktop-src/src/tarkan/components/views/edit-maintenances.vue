<template>
  <edit-maintenance ref="editMaintenanceRef"></edit-maintenance>
  <el-dialog :lock-scroll="true" :top="'50px'" v-model="show">

    <template v-slot:title>
      <div  style="border-bottom: #e0e0e0 1px solid;padding: 20px;">
        <div class="modal-title" >{{KT('maintenance.title')}}</div>
      </div>
    </template>
    <template v-slot:footer>
      <div  style="border-top: #e0e0e0 1px solid;padding: 20px;display: flex;justify-content: flex-start;">

          <el-button
              v-if="store.getters.advancedPermissions(97)"
              @mouseleave="hideTip" @mouseenter.stop="showTip($event,KT('maintenance.add'))"
              type="primary" @click="editMaintenanceRef.newMaintenance()"><i class="fas fa-user-plus"></i></el-button>


          <el-button

              v-if="store.getters.advancedPermissions(99)"
              @mouseleave="hideTip" @mouseenter.stop="showTip($event,KT('maintenance.remove'))"
              type="danger" :plain="selected===0" @click="doDelete()">
            <i class="fas fa-user-minus"></i>
          </el-button>


          <el-button
              v-if="store.getters.advancedPermissions(98)"
              @mouseleave="hideTip" @mouseenter.stop="showTip($event,KT('maintenance.edit'))"
              type="warning" :plain="selected===0"  @click="editMaintenanceRef.editMaintenance(selected);">
            <i class="fas fa-user-edit"></i>
          </el-button>

      </div>
    </template>
    <div class="itm" style="display: flex;background: #eeeeee;">
      <div style="width: 140px;padding: 10px;font-size: 12px;">{{KT('maintenance.name')}}</div>
      <div style="width: 100px;padding: 10px;font-size: 12px;text-align: center;">{{KT('maintenance.type')}}</div>
      <div style="flex: 1;padding: 10px;font-size: 12px;text-align: center;">{{KT('maintenance.start')}}</div>
      <div style="flex: 1;padding: 10px;font-size: 12px;text-align: center;">{{KT('maintenance.cicle')}}</div>
    </div>
    <div style="height: calc(100vh - 300px); overflow: hidden; overflow-y: scroll;">



      <div class="itm" v-for="(u,k) in store.state.maintenance.list" :key="k" @click="selected = (selected!==u.id)?u.id:0" :class="{tr1: (k%2),tr2: !(k%2),selected: (selected === u.id)}" style="display: flex;">

        <div style="width: 140px;padding: 10px;font-size: 14px;">{{u.name}}</div>
        <div style="width: 100px;padding: 10px;font-size: 14px;text-align: center;">{{KT('maintenance.types.'+u.type)}}</div>
        <div style="flex: 1;padding: 10px;font-size: 14px;text-align: center;">
          {{u.start}}
        </div>
        <div style="flex: 1;padding: 10px;font-size: 14px;text-align: center;">
          {{u.period}}
        </div>
      </div>

    </div>

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

import {ElDialog,ElMessage,ElMessageBox,ElNotification,ElButton} from "element-plus";


import {ref,defineExpose} from 'vue';
import {useStore} from 'vuex'
import EditMaintenance from "./edit-maintenance";
import KT from "../../func/kt";

const store = useStore();
const selected = ref(0);
const show = ref(false);

const editMaintenanceRef = ref(null);



const showTip = (evt,text)=>{
  window.$showTip(evt,text);
}

const hideTip = (evt,text)=>{
  window.$hideTip(evt,text);
}

const doDelete = () =>{

  if(selected.value===0){
    ElMessage.error(KT('maintenance.selectError'));
    return false;
  }


  const maintenance = store.getters["maintenance/getMaintenanceById"](selected.value);

  ElMessageBox.confirm(
      KT('maintenance.confirmDelete',maintenance),
      KT('danger'),
      {
        confirmButtonText: KT('delete'),
        confirmButtonClass: 'danger',
        cancelButtonText: KT('cancel'),
        type: 'warning',
      }
  ).then(()=>{

    store.dispatch("maintenance/delete",selected.value).then(()=> {

      ElNotification({
        title: KT('success'),
        message: KT('maintenance.deleted'),
        type: 'success',
      });
      selected.value = 0;
    }).catch((e)=>{

      ElNotification({
        title: KT('error'),
        message: e.response.data,
        type: 'danger',
      });
    });

  }).catch(()=>{
    ElMessage.error(KT('userCancel'));
  })
}


const showMaintenances = ()=>{
    show.value = true;
}


defineExpose({
  showMaintenances
});





</script>

<style>

.itm{
  border-bottom: silver 1px dotted;
}

.itm div{
  border-right: silver 1px dotted;
}


.tr1{
  background: #f3f3f3;
}

.tr2{
  background: #f8f8f8;
}

.tblItem:after{
  content:", "
}

.tblItem:last-child:after{
  content:"";
}

.selected{
  background: rgba(5, 167, 227, 0.05) !important;
}

.itm div:last-child{
  border-right: none;
}

.el-select.el-select--large{
  width: 100%;
}

.el-dialog__header,.el-dialog__body,.el-dialog__footer{
  padding: 0px !important;
}

.el-dialog__footer{
  margin-top: 0px;
}

.el-tabs__nav-wrap{
  padding-left: 20px;
  padding-right: 20px;
}

.el-tabs__content{
  padding-left: 20px;
  padding-right: 20px;
}



.danger{
  --el-button-text-color: var(--el-color-danger) !important;
  --el-button-bg-color: #fef0f0 !important;
  --el-button-border-color: #fbc4c4 !important;
  --el-button-hover-text-color: var(--el-color-white) !important;
  --el-button-hover-bg-color: var(--el-color-danger) !important;
  --el-button-hover-border-color: var(--el-color-danger) !important;
  --el-button-active-text-color: var(--el-color-white) !important;
  --el-button-active-border-color: var(--el-color-danger) !important;
}

</style>