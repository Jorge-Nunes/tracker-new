<template>
  <el-form>
  <el-form-item >
    <el-select v-model="formData.deviceId" :value-key="'id'" filterable :placeholder="$t('device.device')" :size="'large'" :no-data-text="$t('NO_DATA_TEXT')" :no-match-text="$t('NO_MATCH_TEXT')">
      <el-option
          v-for="item in store.state.devices.deviceList"
          :key="item.id"
          :label="item.name"
          :value="item.id"
      >
      </el-option>
    </el-select>
  </el-form-item>
   <div style="display: flex;">
     <div style="width: 100px"></div>

     <el-input v-model="formData.date[0]" type="datetime-local"></el-input>
     <el-input v-model="formData.date[1]" type="datetime-local"></el-input>






   </div>

    <div style="display: flex;justify-content: flex-end;margin-top: 3px;">


    <div style="margin-right: 10px;margin-top: 10px;">
      <el-switch v-model="showRouteMarkers"  :active-text="$t('report.showMarkers')"></el-switch>
    </div>

      <el-button type="info" @click="loadRoute(false,true)">{{$t('report.export')}}</el-button>

      <el-button
          @mouseleave="hideTip" @mouseenter.stop="showTip($event,$t('device.routes'))"
          type="primary" @click="loadRoute()"><i class="fas fa-route"></i></el-button>


      <el-button
          @mouseleave="hideTip" @mouseenter.stop="showTip($event,$t('device.graphic'))"
          type="info" @click="loadGraph()"><i class="fas fa-chart-bar"></i></el-button>

    </div>
    <br><br><br>
  </el-form>


  <div  style="display: flex;flex-direction: column;border: var(--el-border-color-light) 1px solid;margin-top: 20px;border-radius: 10px;">
    <template v-if="loading">

      <div style="text-align: center;padding: 40px;">{{$t('LOADING')}}</div>
    </template>
    <template v-else-if="routePoints.length===0">
      <div style="text-align: center;padding: 40px;">{{$t('route.empty')}}</div>
    </template>
    <template v-else>
      <div v-if="routePoints.length>0" style="border-bottom: silver 1px dotted;display: flex;">
        <div style="text-align: right;padding: 10px;font-size: 11px; color: #5b5b5b;position: relative;width: 70px;">
          <div style="position: absolute;border-right: #cbcbcb 1px dashed;width: 1px;height: 55%;right: 3px;bottom: 0%;transform: translate(-55%,0)"></div>
          <div style="position: absolute;width: 20px;height: 20px;font-size: 16px;text-align: center;right: -10px;top: 50%;border-radius: 50%;transform: translateY(-50%)">
            <i class="fas fa-flag"></i>

          </div> <div style="position: absolute;right: 15px;top: 50%;border-radius: 50%;transform: translateY(-50%)">
            {{new Date(routePoints[0].fixTime).toLocaleDateString()}}<br>
            {{new Date(routePoints[0].fixTime).toLocaleTimeString()}}
          </div>
        </div>
        <div style="padding: 10px;font-size: 14px;flex: 1;">{{routePoints[0].address}}</div>

        <div style="padding: 10px;width: 70px;text-align: right;font-size: 12px;margin-right: 1px;">
          <i class="fas fa-tachometer-alt"></i>
          {{$t('units.'+store.getters['server/getAttribute']('speedUnit','speedUnit'),{speed: routePoints[0].speed})}}
        </div>
      </div>
      <div v-if="routePoints.length>2" style="overflow: auto;height: calc(100vh - 300px)">
        <template v-for="(p,k) in routePoints" :key="p.id">
            <div v-if="k>0 && k<routePoints.length-1" style="display: flex;border-bottom: silver 1px dotted;">

              <div style="text-align: right;padding: 10px;font-size: 11px; color: #5b5b5b;position: relative;width: 70px;">
                <div style="position: absolute;border-right: #cbcbcb 1px dashed;width: 1px;height: 100%;right: 3px;bottom: 0%;transform: translate(-55%,0)"></div>
                <div style="position: absolute;background: #05a7e3;width: 10px;height: 10px;right: 0px;top: 50%;border-radius: 50%;transform: translateY(-50%)"></div>
                <div style="position: absolute;right: 15px;top: 50%;border-radius: 50%;transform: translateY(-50%)">
                  {{new Date(p.fixTime).toLocaleDateString()}}<br>
                  {{new Date(p.fixTime).toLocaleTimeString()}}
                </div>
              </div>
              <div style="padding: 10px;font-size: 12px;flex: 1;"><a target="_blank" style="text-decoration: none;color: var(--el-text-color-primary)" :href="'https://maps.google.com/?q='+p.latitude+','+p.longitude">{{p.address || p.latitude+','+p.longitude }} <i class="fas fa-external-link-alt"></i></a></div>
              <div style="padding: 10px;font-size: 12px;width: 70px;text-align: right">
                <i class="fas fa-tachometer-alt"></i>
                {{$t('units.'+store.getters['server/getAttribute']('speedUnit','speedUnit'),{speed: p.speed})}}
              </div>

            </div>
        </template>
      </div>
    <div v-if="routePoints.length>0" style="border-top: silver 1px dotted;display: flex;">
      <div style="text-align: right;padding: 10px;font-size: 11px; color: #5b5b5b;position: relative;width: 70px;">
        <div style="position: absolute;border-right: #cbcbcb 1px dashed;width: 1px;height: 55%;right: 3px;top: 0%;transform: translate(-55%,0)"></div>
        <div style="position: absolute;width: 20px;height: 20px;font-size: 16px;text-align: center;right: -10px;top: 50%;border-radius: 50%;transform: translateY(-50%)">
          <i class="fas fa-flag"></i>

        </div>
        <div style="position: absolute;right: 15px;top: 50%;border-radius: 50%;transform: translateY(-50%)">
          {{new Date(routePoints[routePoints.length-1].fixTime).toLocaleDateString()}}<br>
          {{new Date(routePoints[routePoints.length-1].fixTime).toLocaleTimeString()}}
        </div>
      </div>
      <div style="padding: 10px;font-size: 14px;flex: 1;">{{routePoints[routePoints.length-1].address}}</div>
      <div style="padding: 10px;font-size: 12px;width: 70px;text-align: right;margin-right: 1px;">
        <i class="fas fa-tachometer-alt"></i>
        {{$t('units.'+store.getters['server/getAttribute']('speedUnit','speedUnit'),{speed: routePoints[routePoints.length-1].speed})}}
      </div>
    </div>
    </template>
  </div>

</template>

<script setup>



import 'element-plus/es/components/button/style/css'
import 'element-plus/es/components/icon/style/css'
import 'element-plus/es/components/tooltip/style/css'
import 'element-plus/es/components/form/style/css'
import 'element-plus/es/components/form-item/style/css'
import 'element-plus/es/components/select/style/css'
import 'element-plus/es/components/option/style/css'
import 'element-plus/es/components/switch/style/css'
import 'element-plus/es/components/input/style/css'

import {ElButton,ElForm,ElSelect,ElOption,ElFormItem,ElSwitch,ElInput} from "element-plus";

import {ref,inject,onMounted,watch,onBeforeUnmount,nextTick} from 'vue';
import {useStore} from 'vuex';
import {useRoute} from 'vue-router'

const showGraphicsRef = inject("show-graphics");

const loading = ref(false);

const date1 = new Date();
const date2 = new Date();

date1.setHours(0);
date1.setMinutes(0);

const showRouteMarkers = inject("showRouteMarkers");


const showTip = (evt,text)=>{
  window.$showTip(evt,text);
}

const hideTip = (evt,text)=>{
  window.$hideTip(evt,text);
}



const formData = ref({deviceId: '',date: [date1,date2]})
const store = useStore();
const route = useRoute();

onMounted(()=>{
  if(route.query.deviceId){
    formData.value.deviceId = parseInt(route.query.deviceId);

    loadRoute();
  }
})

onBeforeUnmount(()=>{
  //resetDevices();
  //updateRoute([]);
})

watch(()=> route.query.deviceId,()=>{
  if(route.query.deviceId){
    formData.value.deviceId = parseInt(route.query.deviceId);

    loadRoute();
  }
})

const updateRoute = inject('updateRoute');


const routePoints = ref([]);

const loadGraph = ()=>{
    if(routePoints.value.length===0){
      loadRoute(true);
    }else{
      showGraphicsRef.value.showGraphic(routePoints.value);
    }
}
const hideDevices = (deviceId=0)=>{
  store.dispatch("devices/setDeviceFilter",deviceId);
}

/*
const resetDevices = ()=>{
  store.dispatch("devices/setDeviceFilter",0);
}*/


import {saveAs} from "file-saver";

const loadRoute = (g=false,e=false)=>{
  const $traccar = window.$traccar;

  loading.value = true;

  $traccar.loadRoute(formData.value.deviceId,formData.value.date[0],formData.value.date[1],e).then((r)=>{
    if(e){

      loading.value = 0;



      if(r['headers']['content-type']==='application/pdf'){
        saveAs(new Blob([r.data], {type: 'application/pdf'}), 'resume.pdf');

      }else {
        saveAs(new Blob([r.data], {type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'}), 'resume.xlsx');
      }
    }else {

      const data = r.data;

      routePoints.value = data;


      let tmp = [];
      data.forEach((p) => {
        tmp.push([p.latitude, p.longitude, p.id, p.course]);
      });

      updateRoute(tmp);
      nextTick(() => {
        hideDevices(formData.value.deviceId);
      });

      loading.value = false;
      if (g && data.length > 0) {
        loadGraph();
      }
    }
  })
}

</script>

<style scoped>
.el-select.el-select--large{
  width: 100%;
}


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