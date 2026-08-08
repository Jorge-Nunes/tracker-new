<template>
  <el-dialog :lock-scroll="true" :top="'50px'" width="80%" v-model="show" title="Teste">

    <template v-slot:title>
      <div  style="border-bottom: #e0e0e0 1px solid;padding: 20px;">
        <div class="modal-title" style="display: flex;width: calc(100% - 50px)">

          <el-input v-model="query" :placeholder="KT(search)" style="--el-input-border-radius: 5px;margin-right: 5px;"></el-input>




        </div>
      </div>
    </template>
    <template v-slot:footer>

    </template>
    <div class="itm" style="display: flex;background: #eeeeee;">
      <div @click="toggleSorting('created_at')" style="width: 70px;padding: 10px;font-size: 12px;text-align: center;">

        Data

        <span v-if="sorting==='created_at-asc'">
          <i class="fas fa-sort-alpha-down"></i>
        </span>
        <span v-else-if="sorting==='created_at-desc'">
          <i  class="fas fa-sort-alpha-up"></i>
        </span>
        <span v-else>
          <i style="color: silver;" class="fas fa-sort-alpha-down"></i>
        </span>

      </div>
      <div @click="toggleSorting('username')" style="width: 100px;padding: 10px;font-size: 12px;text-align: center;">
        Nome

        <span v-if="sorting==='username-asc'">
          <i class="fas fa-sort-alpha-down"></i>
        </span>
        <span v-else-if="sorting==='username-desc'">
          <i  class="fas fa-sort-alpha-up"></i>
        </span>
        <span v-else>
          <i style="color: silver;" class="fas fa-sort-alpha-down"></i>
        </span>
      </div>
      <div @click="toggleSorting('userIp')" style="width: 110px;padding: 10px;font-size: 12px;text-align: center;">
        IP

        <span v-if="sorting==='userIp-asc'">
          <i class="fas fa-sort-alpha-down"></i>
        </span>
        <span v-else-if="sorting==='userIp-desc'">
          <i  class="fas fa-sort-alpha-up"></i>
        </span>
        <span v-else>
          <i style="color: silver;" class="fas fa-sort-alpha-down"></i>
        </span>
      </div>
      <div style="width: 100px;padding: 10px;font-size: 12px;text-align: center;">Dispositivo de Acesso</div>
      <div style="flex: 1;padding: 10px;font-size: 12px;text-align: center;">Log</div>
    </div>
    <div v-if="loading"  style="height: calc(100vh - 300px); overflow: hidden; overflow-y: scroll;">
      <div style="flex: 1;padding: 10px;font-size: 14px;text-align: center;">Carregando registros, por favor aguarde...</div>
    </div>
    <div v-else style="height: calc(100vh - 300px); overflow: hidden; overflow-y: scroll;">



      <div class="itm" v-for="(u,k) in orderedObjects" :key="k" :class="{tr1: (k%2),tr2: !(k%2)}" style="display: flex;">
        <div style="width: 70px;padding: 10px;font-size: 12px;text-align: center;">{{new Date(u.created_at).toLocaleString()}}</div>
        <div style="width: 100px;padding: 10px;font-size: 14px;text-align: center;">{{u.username}}</div>
        <div style="width: 110px;padding: 10px;font-size: 14px;text-align: center;"><a style="text-decoration: none;color: var(--el-text-color-primary)" :href="'https://ip-api.com/#'+u.userIp" target="_blank">{{u.userIp}} <i class="fas fa-external-link-alt"></i></a></div>
        <div style="width: 100px;padding: 10px;font-size: 14px;text-align: center;">{{parseDevice(u.userAgent)}}</div>
        <div style="flex: 1;padding: 10px;font-size: 14px;text-align: center;" v-html="parseLog(u.log)"></div>
      </div>

    </div>

  </el-dialog>
</template>


<script setup>
import {ref,defineExpose,provide,computed} from 'vue';
import {useStore} from 'vuex';




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

import {ElDialog,ElInput} from "element-plus";

const store = useStore();

const query = ref('');
const show = ref(false);
const search = ref('search');
const loading = ref(true);
const sorting = ref('');

const toggleSorting = (s)=>{
  const p = sorting.value.split("-");

  if(p[0]===s){
    sorting.value = s+'-'+((p[1]==='asc')?'desc':'asc');
  }else{
    sorting.value = s+'-asc';
  }


}


const availableObjects = ref([]);

const IP2Int = (IP)=>{
    const tmp = IP.split(".");

  const p1 = parseInt(tmp[0]).toString(16);
  const p2 = parseInt(tmp[1]).toString(16);
  const p3 = parseInt(tmp[2]).toString(16);
  const p4 = parseInt(tmp[3]).toString(16);

    return parseInt(p1+p2+p3+p4,16);
}

const orderedObjects = computed(()=>{

  const p = sorting.value.split("-");

  return [...availableObjects.value].sort((a,b)=>{

    if(!a[p[0]] || !b[p[0]]){
      return (p[1]==='desc')?1:-1;
    }else if(p[0]==='userIp'){
      const IP1 = IP2Int(a[p[0]]);
      const IP2 = IP2Int(b[p[0]]);

      if(IP1>IP2){
        return (p[1]==='asc')?1:-1;
      }else if(IP1<IP2){
        return (p[1]==='desc')?1:-1;
      }else{
        return 0;
      }

    }else if(p[0]==='created_at'){
      if(new Date(a[p[0]])<new Date(b[p[0]])){
        console.log(a[p[0]],b[p[0]],(p[1]==='asc'));
        return (p[1]==='asc')?1:-1;
      }else if(new Date(a[p[0]])>new Date(b[p[0]])){
        console.log(a[p[0]],b[p[0]],(p[1]==='desc'));
        return (p[1]==='desc')?1:-1;
      }else{
        return 0;
      }
    }else if(a[p[0]]>b[p[0]]){
      console.log(a[p[0]],b[p[0]],(p[1]==='asc'));
      return (p[1]==='asc')?1:-1;
    }else if(a[p[0]]<b[p[0]]){
      console.log(a[p[0]],b[p[0]],(p[1]==='desc'));
      return (p[1]==='desc')?1:-1;
    }else{
      return 0;
    }


  })

});

import KT from '../../func/kt';

var parser = require('ua-parser-js');

const parseDevice = (userAgent)=>{
    const ua = parser(userAgent);

    return (ua.os.name+' '+ua.os.version+((ua.device && ua.device.model)?' '+ua.device.model:''));
}

const parseObject = (log,kk=0)=>{

  const objectType = Object.keys(log.object)[kk];

  if(objectType==='userId'){
    return 'Usuário';
  }else if(objectType==='deviceId'){

    const device = store.getters['devices/getDevice'](log.object.deviceId);

    return 'Dispositivo '+((device)?device.name:'n/a')
  }else if(objectType==='geofenceId'){

    const device = store.getters['geofences/getGeofenceById'](log.object.geofenceId);

    return 'Geocerca '+((device)?device.name:(log.old && log.old.name)?log.old.name:'n/a')
  }else{
    return log.object[objectType];
  }
}

const parseLog = (log)=>{
  if(log.code===101){
      if(log.status===200){
        return 'tentativa de login realizada com sucesso.';
      }else{
        return 'tentativa de login falhou.';
      }
  }else if(log.code===105){
    if(log.status===200){
      return parseObject(log,0)+' editado: '+(parseCompare(log.old,log.data));
    }else{
      return parseObject(log,0)+' tentativa de editar falhou';
    }
  }else if(log.code===102){
    if(log.status===200){
      return parseObject(log,0)+' deletado';
    }else{
      return 'tentativa de deletar usuário falhou';
    }
  }else if(log.code===110){
    if(log.status===200){
      return parseObject(log,0)+' Link de compartilhamento criado';
    }else{
      return 'tentativa de compartilhamento falhou';
    }
  }else if(log.code===111){
    if(log.status===200){
      return parseObject(log,0)+' Link de compartilhamento editado';
    }else{
      return 'tentativa de editar compartilhamento falhou';
    }
  }else if(log.code===112){
    if(log.status===200){
      return parseObject(log,0)+' Link de compartilhamento excluído';
    }else{
      return 'tentativa de excluir compartilhamento falhou';
    }
  }else if(log.code===120){
    if(log.status===200){
      return parseObject(log,0)+' Usuário para acesso de motorista criado.';
    }else{
      return 'tentativa de criar acesso de motorista falhou.';
    }
  }else if(log.code===201){
    if(log.status===202){
      if(log.command.attributes && log.command.attributes['tarkan.changeNative']){
        return parseObject(log,0)+' enviado o comando "'+KT('actions.'+log.command.attributes['tarkan.changeNative'])+'"';
      }else if(log.command.description){
        return parseObject(log,0)+' enviado o comando "'+log.command.description+'"';
      }else {
        return parseObject(log,0)+' enviado o comando "' + KT('actions.' + log.command.type) + '"' + ((log.command.attributes && log.command.attributes.data) ? ' -> DATA: ' + log.command.attributes.data : '');
      }
    }else if(log.status===200 && log.command){

      if(log.command.attributes && log.command.attributes['tarkan.changeNative']){
        return parseObject(log,0)+' enviado o comando "'+KT('actions.'+log.command.attributes['tarkan.changeNative'])+'"';
      }else if(log.command.description!=="Novo..."){
        return parseObject(log,0)+' enviado o comando "'+log.command.description+'"';
      }else {
        return parseObject(log,0)+' enviado o comando "' + KT('actions.' + log.command.type) + '"' + ((log.command.attributes && log.command.attributes.data) ? ' -> DATA: ' + log.command.attributes.data : '');
      }

    }else{
      return parseObject(log,0)+' envio de comando falhou.';
    }
  }else if(log.code===211){
    return 'Bloqueio realizado pela regra de motorista.'
  }else if(log.code===212){
    return 'Desbloqueio realizado pela regra de motorista.'
  }else if(log.code === 301){
    if(log.status===200){
      return parseObject(log,0)+' editado: '+(parseCompare(log.old,log.data));
    }else{
      return parseObject(log,0)+' tentativa de editar o veiculo falhou.'
    }
  }else if(log.code === 302){
    if(log.status===204){
      return 'Dispositivo "'+log.old[0].name+'" excluido';
    }else{
      return parseObject(log,0)+' tentativa de excluir o dispositivo "'+log.old[0].name+'" falhou.'
    }
  }else if(log.code === 405){
    if(log.status===200){
      return parseObject(log,0)+' Âncora Ativada';
    }else{
      return parseObject(log,0)+' tentativa de ativar âncora falhou.'
    }
  }else if(log.code === 401){
    if(log.status===200){
      return parseObject(log,0)+' Geocerca criada';
    }else{
      return parseObject(log,0)+' tentativa de criar geocerca falhou.'
    }
  }else if(log.code === 406){
    if(log.status===204){
      return parseObject(log,0)+' Âncora Desativada';
    }else{
      return parseObject(log,0)+' tentativa de desativar ancora falhou.'
    }
  }else if(log.code === 402){
    if(log.status===204){
      return parseObject(log,0)+' Geocerca removida';
    }else{
      return parseObject(log,0)+' tentativa de remover geocerca falhou.'
    }
  }else if(log.code === 901){
    if(log.status===204){
      return parseObject(log,0)+' atribuido para '+parseObject(log,1);
    }else{
      return parseObject(log,0)+' tentativa de remover geocerca falhou.'
    }
  }else if(log.code === 902){
    if(log.status===204){
      return parseObject(log,0)+' removido de '+parseObject(log,1);
    }else{
      return parseObject(log,0)+' tentativa de remover geocerca falhou.'
    }
  }else{
    return log.code;
  }
}

const parseCompare = (o,n)=>{

  if(!o || !n){
    return '';
  }

  let tmp = [];

  for(var k of Object.keys(o)){
    if(typeof o[k]==='object' && (o[k]!=null) && n[k]!=null && k!=='geofenceIds'){
      for(var a of Object.keys(o[k])){

        console.log(o[k][a],n[k][a]);

        if(o[k][a]!= n[k][a]) {
          tmp.push('<div style="display: flex;border: silver 1px solid;margin-left: -10px;margin-right: -10px;margin-top: 5px;"> <div style="flex: 1;">' + a + '</div><div style="flex: 1;">' + ((o[k][a] === '' || o[k][a] === null) ? '-Vazio-' : o[k][a]) + '</div><div style="flex: 1;">' + ((n[k][a] === '' || n[k][a] === null) ? '-Vazio-' : n[k][a]) + '</div></div>');
        }
      }
    }else if((o[k] != n[k]) && (o[k]!=null) && n[k]!=null && k!=='geofenceIds'){
      tmp.push('<div style="display: flex;border: silver 1px solid;margin-left: -10px;margin-right: -10px;margin-top: 5px;"> <div style="flex: 1;">'+k+'</div><div style="flex: 1;">'+((o[k]==='' || o[k]===null)?'-Vazio-':o[k])+'</div><div style="flex: 1;">'+((n[k]==='' || n[k]===null)?'-Vazio-':n[k])+'</div></div>');
    }
  }

    if(tmp.length===0){
      return 'sem alteração.';
    }else {
      return tmp.join("");
    }
}


const showLogs = (params)=>{

  loading.value = true;
  availableObjects.value = [];

  if(params==='all'){
    window.$tarkan.getServerLogs().then(({data})=>{
      loading.value = false;
      availableObjects.value = data;
    });
  }else if(Object.keys(params)[0]==='userId'){
    window.$tarkan.getUserLogs(params.userId).then(({data})=>{
      loading.value = false;
      availableObjects.value = data;
    });
  }else if(Object.keys(params)[0]==='deviceId'){
    window.$tarkan.getDeviceLogs(params.deviceId).then(({data})=>{
      loading.value = false;
      availableObjects.value = data;
    });
  }


  show.value = true;
}

provide("showLogs",showLogs);

defineExpose({
  showLogs
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