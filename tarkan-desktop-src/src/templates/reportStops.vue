<template>
  <report-common @change="onChange($event)"></report-common>

  <div style="display: flex;justify-content: flex-end">

    <div style="margin-right: 10px;margin-top: 10px;">
      <el-switch v-model="showRouteMarkers" :active-text="$t('report.showMarkers')"></el-switch>
    </div>
    <el-button @click="loadResume(true)">{{$t('report.export')}}</el-button>
    <el-button type="primary" @click="loadResume()">{{KT('report.load')}}</el-button>

  </div>

  <div style="margin-top: 20px;">

    <div v-if="loading===1" class="reportBlock" style="padding: 10px;">{{$t('LOADING')}}</div>
    <div v-if="loading===2 && data.length===0" class="reportBlock" style="padding: 10px;">{{$t('NO_DATA_TEXT')}}</div>

      <div class="reportBlock" v-for="(b,bk) in data" :key="bk" :set="device = store.getters['devices/getDevice'](b.deviceId)" @click="loadRoute(b)">
        <div style="color: var(--el-text-color-primary);font-weight: bold;font-size: 16px;padding: 10px;">{{device.name}}</div>


        <div style="border-top: var(--el-border-color-light) 1px dotted;display: flex;justify-content: space-between;min-height: 53px;">
          <div style="text-align: right;padding: 10px;font-size: 11px; color: #5b5b5b;position: relative;width: 70px;">
            <div style="position: absolute;width: 20px;height: 20px;font-size: 16px;text-align: center;right: -10px;top: 50%;border-radius: 50%;transform: translateY(-50%)">
              <i class="fas fa-flag"></i>

            </div>
            <div style="position: absolute;right: 15px;top: 50%;border-radius: 50%;transform: translateY(-50%)">
              {{new Date(b.startTime).toLocaleDateString()}}<br>
              {{new Date(b.startTime).toLocaleTimeString()}}
            </div>
          </div>
          <div style="padding: 10px;font-size: 14px;flex: 1;">{{b.address}}</div>
        </div>

        <div style="border-top: var(--el-border-color-light) 1px dotted;display: flex;justify-content: space-between;">

          <div style="flex: 1;text-align: center;border-right: var(--el-border-color-light) 1px dotted;">
            <div style="text-transform: uppercase;margin-top: 10px;font-size: 11px;color: var(--el-text-color-regular);">{{$t('device.startOdometer')}}</div>
            <div style="margin-top: 5px;margin-bottom: 10px;font-size: 20px;color: var(--el-color-primary)">{{$t('units.'+store.getters['server/getAttribute']('distanceUnit','distanceUnit'),{distance: b.startOdometer})}}</div>
          </div>
          <div style="flex: 1;text-align: center;">
            <div style="text-transform: uppercase;margin-top: 10px;font-size: 11px;color: var(--el-text-color-regular);">{{$t('device.endOdometer')}}</div>
            <div style="margin-top: 5px;margin-bottom: 10px;font-size: 20px;color: var(--el-color-primary)">{{$t('units.'+store.getters['server/getAttribute']('distanceUnit','distanceUnit'),{distance: b.endOdometer})}}</div>
          </div>
        </div>


        <div style="border-top: var(--el-border-color-light) 1px dotted;display: flex;justify-content: space-between;">
          <div style="flex: 1;text-align: center;border-right: var(--el-border-color-light) 1px dotted;">
            <div style="text-transform: uppercase;margin-top: 10px;font-size: 11px;color: var(--el-text-color-regular);">{{$t('device.averageSpeed')}}</div>
            <div style="margin-top: 5px;margin-bottom: 10px;font-size: 20px;color: var(--el-color-primary)">{{$t('units.'+store.getters['server/getAttribute']('speedUnit','speedUnit'),{speed: b.averageSpeed})}}</div>
          </div>
          <div style="flex: 1;text-align: center;border-right: var(--el-border-color-light) 1px dotted;">
            <div style="text-transform: uppercase;margin-top: 10px;font-size: 11px;color: var(--el-text-color-regular);">{{$t('device.maxSpeed')}}</div>
            <div style="margin-top: 5px;margin-bottom: 10px;font-size: 20px;color: var(--el-color-primary)">{{$t('units.'+store.getters['server/getAttribute']('speedUnit','speedUnit'),{speed: b.averageSpeed})}}</div>
          </div>
        </div>


        <div style="border-top: var(--el-border-color-light) 1px dotted;display: flex;justify-content: space-between;">
          <div style="flex: 1;text-align: center;border-right: var(--el-border-color-light) 1px dotted;">
            <div style="text-transform: uppercase;margin-top: 10px;font-size: 11px;color: var(--el-text-color-regular);">{{$t('device.duration')}}</div>
            <div style="margin-top: 5px;margin-bottom: 10px;font-size: 20px;color: var(--el-color-primary)">{{Math.round(((b.duration/60)/60)/1000)}} hs</div>
          </div>
          <div style="flex: 1;text-align: center;border-right: var(--el-border-color-light) 1px dotted;">
            <div style="text-transform: uppercase;margin-top: 10px;font-size: 11px;color: var(--el-text-color-regular);">{{$t('device.distance')}}</div>
            <div style="margin-top: 5px;margin-bottom: 10px;font-size: 20px;color: var(--el-color-primary)">{{$t('units.'+store.getters['server/getAttribute']('distanceUnit','distanceUnit'),{distance: b.distance})}}</div>
          </div>
          <div style="flex: 1;text-align: center;border-right: var(--el-border-color-light) 1px dotted;">
            <div style="text-transform: uppercase;margin-top: 10px;font-size: 11px;color: var(--el-text-color-regular);">{{$t('device.spentFuel')}}</div>
            <div style="margin-top: 5px;margin-bottom: 10px;font-size: 20px;color: var(--el-color-primary)">{{b.spentFuel}} L</div>
          </div>
        </div>

      </div>



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
import 'element-plus/es/components/date-picker/style/css'
import 'element-plus/es/components/switch/style/css'

import {ElButton,ElSwitch} from "element-plus";

import {inject,  ref} from 'vue';
import {useStore} from 'vuex';
import ReportCommon from "./reportCommon";



const loading = ref(0);

const store = useStore();

const updateRoute = inject('updateRoute');

const showRouteMarkers = inject("showRouteMarkers");

const filter = ref({
  date: [0,0],
  deviceId: [],
  showMarkers: true,
  groupId: []
});

const data = ref([]);
/*
onBeforeUnmount(()=>{
  updateRoute([]);
})*/


const $traccar = window.$traccar;

const onChange = (e)=>{
  filter.value = e;
}



import {saveAs} from "file-saver";

const loadResume = (exp=false)=>{
  loading.value = 1;

  $traccar.getReportStops(filter.value.deviceId,filter.value.groupId,new Date(filter.value.date[0]).toISOString(),new Date(filter.value.date[1]).toISOString(),exp).then((r)=>{
    if(exp){

      loading.value = 0;


      if(r['headers']['content-type']==='application/pdf'){
        saveAs(new Blob([r.data], {type: 'application/pdf'}), 'resume.pdf');
      }else {
        saveAs(new Blob([r.data], {type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'}), 'resume.xlsx');
      }
    }else {
      loading.value = 2;
      data.value = r.data;


      let tmp = [];

      r.data.forEach((p)=>{
        tmp.push([p.latitude,p.longitude,p.id,p.course]);
      });

      updateRoute(tmp,false);
    }
  });
}

</script>


<style scoped>
.reportBlock{
  border: var(--el-border-color-light) 1px solid;
  border-radius: 10px;
  text-align: center;
  margin-top: 5px;
  cursor: pointer;
}

.reportBlock:hover{
  background: var(--el-color-primary-light-9);
}
</style>