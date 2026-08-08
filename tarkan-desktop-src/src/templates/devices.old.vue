<template>

  <edit-device ref="editDeviceRef"></edit-device>

  <div style="display: flex;justify-content: space-between;align-content: space-between;">
  <div style="width: 30%;"></div>

    <el-input v-model="query" placeholder="Buscar veiculo" style="--el-input-border-radius: 5px;margin-right: 5px;"></el-input>
    <el-tooltip
        effect="dark"
        content="Cadastrar Grupo"
        placement="bottom"
    >
      <el-button type="primary" plain><i class="fas fa-folder-plus"></i></el-button>
    </el-tooltip>
    <el-tooltip
        effect="dark"
        content="Cadastrar Dispositivo"
        placement="bottom"
    >
      <el-button type="primary" @click="editDeviceRef.newDevice()"><i class="fas fa-plus"></i></el-button>
    </el-tooltip>

  </div>


  <div v-for="(device) in filteredDevices" :key="device.id" class="device" @click="markerClick(device)" :set="position = store.getters['devices/getPosition'](device.positionId)">
    <div class="name" >{{device.name}}</div>
    <div class="stats" v-if="position">
      <div class="subtitle"><i class="fas fa-map-marker-alt"></i> Localização Atual</div>
      <div class="updated">Atualizado há alguns segundos</div>
      <div v-if="position.address" class="address">{{position.address}}</div>
      <div v-else class="address">{{position.latitude}}, {{position.longitude}}</div>
    </div>
    <div class="stats" v-else>
      <div class="updated" style="padding: 10px;">Não há informação de posição ainda</div>
    </div>
    <div v-if="position" class="icons">
      <div :style="{color: (position.attributes.ignition)?'var(--text-blue)':''}"><i class="fas fa-key"></i></div>
      <div :style="{color: (position.attributes.blocked)?'red':''}"><i class="fas fa-lock"></i></div>
      <div v-if="position.attributes.rssi"><i class="fas fa-signal"></i></div>
      <div v-if="position.attributes.sat"><i class="fas fa-satellite"></i> <span>{{position.attributes.sat}}</span></div>
      <div v-if="position.attributes.power"><i class="fas fa-car-battery"></i> <span>{{position.attributes.power}}v</span></div>

    </div>
  </div>

</template>

<script setup>

import {ref,computed,inject} from 'vue';
import {useStore} from "vuex"
import EditDevice from "../tarkan/components/views/edit-device";

const store = useStore();

const markerClick = inject('markerClick');


const query = ref('');
const editDeviceRef = ref(null);


const filteredDevices = computed(()=>{
  if(query.value === ''){
    return store.state.devices.deviceList;
  }else {
    return store.state.devices.deviceList.filter((d) => {
        return d.name.toLowerCase().match(query.value.toLowerCase()) ||
            d.placa.toLowerCase().match(query.value.toLowerCase()) ||
            d.uniqueId.toLowerCase().match(query.value.toLowerCase());
    });
  }

})



</script>

<style>
  .device{
    border: silver 1px solid;
    border-radius: 10px;
    margin-top: 10px;
    display: flex;
    flex-direction: column;
    text-align: center;
    cursor: pointer;
  }

  .device .name{
    border-bottom: silver 1px dotted;
    font-size: 16px;
    padding: 5px;
    font-weight: bold;

  }

  .icons{
    display: flex;
    justify-content: center;
  }

  .icons div{
    display: flex;
    justify-content: center;
    flex: 1;
    border-top: silver 1px solid;
    border-right: silver 1px solid;
    padding: 7px;
    font-size: 11px;
  }
  .icons div i{
    font-size: 16px;
  }

  .icons div:last-child{
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