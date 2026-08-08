<template>
  <el-dialog :lock-scroll="true" :top="'50px'" :width="'60%'" v-model="show" title="Teste">

    <template v-slot:title>
      <div  style="border-bottom: #e0e0e0 1px solid;padding: 20px;">
        <div class="modal-title" style="display: flex;width: calc(100% - 50px)">

          <el-input v-model="query" :placeholder="KT('user.search')" style="--el-input-border-radius: 5px;margin-right: 5px;"></el-input>


            <el-button
                v-if="store.getters.advancedPermissions(25)"
                @mouseleave="hideTip" @mouseenter.stop="showTip($event,KT('share.add'))"
                type="primary" @click="editShareRef.newShare()"><i class="fas fa-user-plus"></i></el-button>


        </div>
      </div>
    </template>
    <template v-slot:footer>
      <div  style="border-top: #e0e0e0 1px solid;padding: 20px;display: flex;justify-content: flex-start;">


          <el-button
              v-if="store.getters.advancedPermissions(27)"
              @mouseleave="hideTip" @mouseenter.stop="showTip($event,KT('share.remove'))"
              type="danger" :plain="selected===0" @click="doDelete()">
            <i class="fas fa-user-minus"></i>
          </el-button>


          <el-button
              v-if="store.getters.advancedPermissions(26)"
              @mouseleave="hideTip" @mouseenter.stop="showTip($event,KT('share.edit'))"
              type="warning" :plain="selected===0"  @click="editShareRef.editShare(selected);">
            <i class="fas fa-user-edit"></i>
          </el-button>




      </div>
    </template>
    <div class="itm" style="display: flex;background: #eeeeee;">
      <div style="flex: 1;padding: 10px;font-size: 12px;">{{KT('user.name')}}</div>
      <div style="flex: 1;padding: 10px;font-size: 12px;text-align: center;">{{KT('user.device')}}</div>
      <div style="width: 90px;padding: 10px;font-size: 12px;text-align: center;">{{KT('user.limitCommands')}}</div>
      <div style="width: 182px;padding: 10px;font-size: 12px;text-align: center;">{{KT('user.expirationTime')}}</div>
      <div style="width: 202px;padding: 10px;font-size: 12px;text-align: center;">{{KT('user.shareLink')}}</div>
    </div>
    <div style="height: calc(100vh - 300px); overflow: hidden; overflow-y: scroll;">



      <div class="itm" v-for="(u,k) in store.state.shares.list" :key="k" @click="selected = (selected!==u.id)?u.id:0" :class="{tr1: (k%2),tr2: !(k%2),selected: (selected === u.id)}" style="display: flex;">

        <div style="flex: 1;padding: 10px;font-size: 14px;">{{u.name}}</div>
        <div style="flex: 1;padding: 10px;font-size: 14px;text-align: center;" :set="device = store.getters['devices/getDevice'](u.deviceId)">
          <template v-if="device">
            {{device.name}}
          </template>
          <template v-else>
            Device #{{u.deviceId}}
          </template>
        </div>
        <div style="width: 90px;padding: 10px;font-size: 14px;text-align: center;">{{(!u.limitCommands)?'Sim':'Não'}}</div>
        <div style="width: 180px;padding: 10px;font-size: 14px;text-align: center;">{{new Date(u.expirationTime).toLocaleString()}}</div>
        <div style="width: 220px;font-size: 14px;text-align: center;">
          <input :value="location+''+u.token" style="width: 100%; height: 32px;padding: 3px;border-radius: 5px;border: none;" @focus="doSelect($event)" @click="doSelect($event)">
        </div>
      </div>

    </div>

  </el-dialog>
</template>


<script setup>
import {ref,defineExpose,inject} from 'vue';
import {useStore} from 'vuex'


import {ElMessageBox} from "element-plus/es/components/message-box";
import {ElNotification} from "element-plus/es/components/notification";

import A from '../../../license'




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

import {ElDialog,ElMessage,ElButton,ElInput} from "element-plus";

const showTip = (evt,text)=>{
  window.$showTip(evt,text);
}

const hideTip = (evt,text)=>{
  window.$hideTip(evt,text);
}

const store = useStore();
const query = ref('');
const selected = ref(0);
const show = ref(false);

const editShareRef = inject('edit-share');

const location = ref(window.location.protocol+'//'+A[1]+'/share/');

const doSelect = (e)=>{

  console.log(e);

  e.target.select();
  document.execCommand('copy');
}

const doDelete = () =>{

  if(selected.value===0){
    ElMessage.error('Você precisa selecionar um usuário');
    return false;
  }

  if(selected.value===store.state.auth.id){
    ElMessage.error('Você não pode se deletar!');
    return false;
  }


  const user = store.getters["shares/getShare"](selected.value);

  if(user.id < store.state.auth.id){
    ElMessage.error('Você não pode deletar um admin superior a você!');
    return false;
  }

  ElMessageBox.confirm(
      'Você está excluindo o compartilhamento "'+user.name+'", deseja continuar?',
      'Perigo!',
      {
        confirmButtonText: 'Excluir',
        confirmButtonClass: 'danger',
        cancelButtonText: 'Cancelar',
        type: 'warning',
      }
  ).then(()=>{

    store.dispatch("shares/delete",selected.value).then(()=> {

      ElNotification({
        title: 'Successo',
        message: 'Compartilhamento deletado com sucesso.',
        type: 'success',
      });
      selected.value = 0;
    }).catch((e)=>{

      ElNotification({
        title: 'Erro',
        message: e.response.data,
        type: 'danger',
      });
    });

  }).catch(()=>{

    ElMessage.error('Ação cancelada pelo usuário');
  })
}


const showShares = ()=>{
    show.value = true;
}


defineExpose({
  showShares
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