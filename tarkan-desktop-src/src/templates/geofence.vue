<template>

  <edit-geofence ref="editGeofenceRef"></edit-geofence>

  <div style="display: flex;justify-content: space-between;align-content: space-between;">
    <div style="width: 30%;"></div>

    <el-input v-model="query" :placeholder="KT('geofence.search')" style="--el-input-border-radius: 5px;margin-right: 5px;"></el-input>

    <el-tooltip
        v-if="store.getters.advancedPermissions(41)"
        effect="dark"
        :content="KT('geofence.add')"
        placement="bottom"
    >
      <el-button type="primary" @click="editGeofenceRef.newGeofence()"><i class="fas fa-plus"></i></el-button>
    </el-tooltip>

  </div>

  <div style="border: silver 1px solid; border-radius: 5px;margin-top: 20px;height: calc(100vh - 200px);">
    <div class="deviceHead">
      <div class="name" style="font-size: 12px;font-weight: 100;padding: 5px;">{{KT('geofence.name')}}</div>
    </div>
    <div style="overflow-x: hidden;overflow-y: scroll;height: calc(100vh - 230px);">
      <template
          v-if="store.getters.advancedPermissions(42)">
      <div v-for="(device) in filteredDevices" :key="device.id" class="device" @click="editGeofenceRef.editGeofence(device)">

        <div class="name" >{{device.name}}</div>

      </div>
      </template>
      <template v-else>
        <div v-for="(device) in filteredDevices" :key="device.id" class="device" >

          <div class="name" >{{device.name}}</div>

        </div>
      </template>

    </div>
  </div>

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
import 'element-plus/es/components/color-picker/style/css'
import 'element-plus/es/components/upload/style/css'

import {ElButton,ElInput} from "element-plus";

import {ref,computed} from 'vue';
import {useStore} from "vuex"
import EditGeofence from "../tarkan/components/views/edit-geofence";

const store = useStore();

const query = ref('');
const editGeofenceRef = ref(null);


import KT from '../tarkan/func/kt.js';


const filteredDevices = computed(()=>{
  if(query.value === ''){
    return store.state.geofences.fenceList;
  }else {
    return store.state.geofences.fenceList.filter((d) => {
      return d.name.toLowerCase().match(query.value.toLowerCase());
    });
  }

})



</script>

<style scoped>

.device{
  border-bottom: var(--el-border-color-light) 1px solid;
  display: flex;
  flex-direction: row;
  text-align: center;
  cursor: pointer;
  margin-right: -1px;
}

.deviceHead{
  border-bottom: var(--el-border-color-light) 1px solid;
  display: flex;
  flex-direction: row;
  text-align: center;
  cursor: pointer;
  margin-right: -1px;
  background: var(--el-color-info-light);
}

.device:hover{
  background: var(--el-color-primary-light-9);
}



.device .name,.deviceHead .name{
  font-size: 12px;
  padding: 7px;
  text-align: left;
  line-height: 14px;
  font-weight: 800;
  border-right: silver 1px dotted;
  width: 60%;
}

.icons{
  display: flex;
  justify-content: center;
  flex-direction: row-reverse;
  flex: 1;
  font-size: 11px;
}

.icons div{
  display: flex;
  justify-content: center;
  flex: 1;
  border-right: silver 1px dotted;
  padding: 7px;
  font-size: 11px;
}
.icons div i{
  font-size: 14px;
}

.icons div:first-child{
  border-right: none;
}

.icons div span{
  display: flex;
  padding: 2px;
  padding-left: 5px;
}

.subtitle{
  margin-top: 20px;
  font-weight: bold;
  font-size: 14px;
  text-transform: uppercase;
  color: var(--text-black);
}

.subtitle i{
  font-size: 12px;
  margin-right: 3px;
}



</style>