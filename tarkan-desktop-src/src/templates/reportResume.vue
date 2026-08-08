<template>
  <report-common @change="onChange($event)"></report-common>

  <div style="display: flex;justify-content: flex-end">


    <el-button type="info" @click="loadResume(true)">{{$t('report.export')}}</el-button>
    <el-button type="primary" @click="loadResume()">{{$t('report.load')}}</el-button>

  </div>

  <div style="margin-top: 20px;">

    <div v-if="loading===1" class="reportBlock" style="padding: 10px;">{{$t('LOADING')}}</div>
    <div v-if="loading===2 && data.length===0" class="reportBlock" style="padding: 10px;">{{$t('NO_DATA_TEXT')}}</div>

      <div class="reportBlock" v-for="(b,bk) in data" :key="bk" @click="loadRoute(b)" :set="device = store.getters['devices/getDevice'](b.deviceId)">
        <div style="color: var(--el-text-color-primary);font-weight: bold;font-size: 16px;padding: 10px;">{{device.name}}</div>
        <div v-if="b.startTime" style="border-top: var(--el-border-color-light) 1px dotted;">
          <div style="font-size: 10px;padding-top: 3px;color: var(--el-text-color-secondary)">{{$t('startDate')}}</div>
          <div style="font-size: 12px;color: var(--el-text-color-regular);margin-top: 3px; margin-bottom: 5px;">
            {{new Date(b.startTime).toLocaleDateString()}}
          </div>
        </div>
        <div style="border-top: var(--el-border-color-light) 1px dotted;display: flex;justify-content: space-between;">
          <div style="flex: 1;text-align: center;border-right: var(--el-border-color-light) 1px dotted;">
            <div style="text-transform: uppercase;margin-top: 10px;font-size: 11px;color: var(--el-text-color-regular);">{{$t('device.distance')}}</div>
            <div style="margin-top: 5px;margin-bottom: 10px;font-size: 20px;color: var(--el-color-primary)">{{$t('units.'+store.getters['server/getAttribute']('distanceUnit','distanceUnit'),{distance: b.distance})}}</div>
          </div>
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
            <div style="margin-top: 5px;margin-bottom: 10px;font-size: 20px;color: var(--el-color-primary)">{{$t('units.'+store.getters['server/getAttribute']('speedUnit','speedUnit'),{speed: b.maxSpeed})}}</div>
          </div>
        </div>


        <div style="border-top: var(--el-border-color-light) 1px dotted;display: flex;justify-content: space-between;">
          <div style="flex: 1;text-align: center;border-right: var(--el-border-color-light) 1px dotted;">
            <div style="text-transform: uppercase;margin-top: 10px;font-size: 11px;color: var(--el-text-color-regular);">{{$t('device.engineHours')}}</div>
            <div style="margin-top: 5px;margin-bottom: 10px;font-size: 20px;color: var(--el-color-primary)">{{Math.round(((b.engineHours/60)/60)/1000)}} hs</div>
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

import {ElButton} from "element-plus";


import {ref,inject} from 'vue';
import {useStore} from 'vuex';
import ReportCommon from "./reportCommon";
import {saveAs} from "file-saver";


const loading = ref(0);

const store = useStore();

const filter = ref({
  date: [0,0],
  deviceId: [],
  showMarkers: true,
  groupId: []
});

const data = ref([]);

const onChange = (e)=>{
  filter.value = e;
}

const loadResume = (exp=false)=>{
  const $traccar = window.$traccar;
  loading.value = 1;

  $traccar.getReportSummary(filter.value.deviceId,filter.value.groupId,new Date(filter.value.date[0]).toISOString(),new Date(filter.value.date[1]).toISOString(),exp).then((r)=>{

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
      }
  });
}

const hideDevices = (deviceId=0)=>{
  store.dispatch("devices/setDeviceFilter",deviceId);
}


const updateRoute = inject('updateRoute');

const loadRoute = (b)=>{


  const $traccar = window.$traccar;

  $traccar.loadRoute(b.deviceId,b.startTime,b.endTime).then(({data})=>{


    let tmp = [];
    data.forEach((p)=>{
      tmp.push([p.latitude,p.longitude,p.id,p.course]);
    });

    hideDevices(b.deviceId);
    updateRoute(tmp);
    loading.value = false;
  })
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