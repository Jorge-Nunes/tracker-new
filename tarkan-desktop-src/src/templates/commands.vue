<template>

  <edit-command ref="editCommandRef"></edit-command>

  <div style="display: flex;justify-content: space-between;align-content: space-between;">
    <div style="width: 30%;"></div>

    <el-input v-model="query" :placeholder="KT('commands.search')" style="--el-input-border-radius: 5px;margin-right: 5px;"></el-input>

    <el-tooltip
        effect="dark"
        :content="KT('commands.new')"
        placement="bottom"
        v-if="store.getters.advancedPermissions(57)"
    >
      <el-button type="primary" @click="editCommandRef.newCommand()"><i class="fas fa-plus"></i></el-button>
    </el-tooltip>

  </div>

  <div style="border: silver 1px solid; border-radius: 5px;margin-top: 20px;height: calc(100vh - 200px);">
    <div class="deviceHead">
      <div class="name" style="font-size: 12px;font-weight: 100;padding: 5px;width: calc(60% + 3.5px)">{{KT('commands.title')}}</div>
      <div class="name" style="font-size: 12px;font-weight: 100;padding: 5px;flex: 2;">{{KT('commands.type')}}</div>
    </div>
    <div style="overflow-x: hidden;overflow-y: scroll;height: calc(100vh - 230px);">
      <template v-if="store.getters.advancedPermissions(58)">
      <div v-for="(device) in filteredDevices" :key="device.id" @click="editCommandRef.editCommand(device)" class="device">

        <div class="name" >{{device.description}}</div>
        <div  class="name" style="flex: 2;">
          {{device.type}}
        </div>
      </div>

      </template>
      <template v-else>
        <div v-for="(device) in filteredDevices" :key="device.id"  class="device">

          <div class="name" >{{device.description}}</div>
          <div  class="name" style="flex: 2;">
            {{device.type}}
          </div>
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
import EditCommand from "../tarkan/components/views/edit-command";

const store = useStore();

const query = ref('');
const editCommandRef = ref(null);


import KT from '../tarkan/func/kt.js';


const filteredDevices = computed(()=>{
  if(query.value === ''){
    return store.state.commands.commandList;
  }else {
    return store.state.commands.commandList.filter((d) => {
      return d.description.toLowerCase().match(query.value.toLowerCase());
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