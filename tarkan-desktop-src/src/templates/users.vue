<template>

  <edit-geofence ref="editGeofenceRef"></edit-geofence>

  <div style="display: flex;justify-content: space-between;align-content: space-between;">
    <div style="width: 30%;"></div>

    <el-input v-model="query" placeholder="Buscar Geocerca" style="--el-input-border-radius: 5px;margin-right: 5px;"></el-input>
    <el-tooltip
        effect="dark"
        content="Cadastrar Grupo"
        placement="bottom"
    >
      <el-button type="primary" plain><i class="fas fa-folder-plus"></i></el-button>
    </el-tooltip>
    <el-tooltip
        effect="dark"
        content="Cadastrar Geocerca"
        placement="bottom"
    >
      <el-button type="primary" @click="editGeofenceRef.newGeofence()"><i class="fas fa-plus"></i></el-button>
    </el-tooltip>

  </div>

  <div style="border: silver 1px solid; border-radius: 5px;margin-top: 20px;height: calc(100vh - 200px);">
    <div class="device">
      <div class="name" style="font-size: 12px;font-weight: 100;padding: 5px;width: calc(60% + 3.5px)">Titulo</div>
      <div class="icons" style="font-size: 12px;font-weight: 100;padding: 5px;">Estado</div>
    </div>
    <div style="overflow-x: hidden;overflow-y: scroll;height: calc(100vh - 230px);">

      <div v-for="(device) in filteredDevices" :key="device.id" class="device" @click="markerClick(device)" :set="position = store.getters['devices/getPosition'](device.positionId)">

        <div class="name" >{{device.name}}</div>
        <div v-if="position" class="icons">
          <div :style="{color: (position.attributes.ignition)?'var(--text-blue)':''}"><i class="fas fa-key"></i></div>
          <div :style="{color: (position.attributes.blocked)?'red':''}"><i class="fas fa-lock"></i></div>

          <el-tooltip :content="(device.status==='offline')?'Desconectado':(position.attributes.rssi)?'RSSI'+position.attributes.rssi:'Conectado'">
            <div :style="{color: (device.status==='offline')?'silver':''}"><i class="fas fa-signal"></i></div>
          </el-tooltip>
        </div>
      </div></div>
  </div>

</template>

<script setup>

import {ref,computed} from 'vue';
import {useStore} from "vuex"
import EditGeofence from "../tarkan/components/views/edit-geofence";

const store = useStore();

const query = ref('');
const editGeofenceRef = ref(null);


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
  border-bottom: silver 1px solid;
  display: flex;
  flex-direction: row;
  text-align: center;
  cursor: pointer;
  margin-right: -1px;
}
.device:first-child{
  background: rgba(0,0,0,0.03)
}

.device:hover{
  background: rgba(0,0,0,0.03);
}



.device .name{
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

.updated{
  font-size: 12px;
  margin-top: 5px;
  color: var(--text-silver);
}

.address{
  color: var(--text-blue);
  font-size: 15px;
  margin-top: 10px;
  margin-bottom: 20px;
  padding: 10px;
  line-height: 20px;
}

</style>