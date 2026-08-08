<template>
  <report-common @change="onChange($event)"></report-common>

  <div style="display: flex;justify-content: flex-end">

    <div style="margin-right: 10px;margin-top: 10px;">
      <el-switch v-model="showRouteMarkers" active-text="Mostrar Marcadores"></el-switch>
    </div>
    <el-button @click="loadResume(true)">Baixar</el-button>
    <el-button type="primary" @click="loadResume()">Buscar</el-button>

  </div>

  <div style="margin-top: 20px;">

    <div v-if="loading===1" class="reportBlock" style="padding: 10px;">CARREGANDO DADOS...</div>
    <div v-if="loading===2 && data.length===0" class="reportBlock" style="padding: 10px;">--NÃO HÁ DADOS PARA MOSTRAR--</div>

      <div class="reportBlock" v-for="(b,bk) in events" :key="bk"  @click="loadRoute(b)" :set="device = store.getters['devices/getDevice'](parseInt(bk))">
        <div style="color: var(--el-text-color-primary);font-weight: bold;font-size: 16px;padding: 10px;" >{{device.name}}</div>


        <div v-for="(e,ek) in b" :key="'evt-'+bk+'-'+ek" style="border-top: var(--el-border-color-light) 1px dotted;display: flex;justify-content: space-between;">
          <div style="text-align: right;padding: 10px;font-size: 11px; color: #5b5b5b;position: relative;width: 70px;">
            <div style="position: absolute;width: 20px;height: 20px;font-size: 16px;text-align: center;right: -10px;top: 50%;border-radius: 50%;transform: translateY(-50%)">
              <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div style="position: absolute;right: 15px;top: 50%;border-radius: 50%;transform: translateY(-50%)">
              {{new Date(e.eventTime).toLocaleDateString()}}<br>
              {{new Date(e.eventTime).toLocaleTimeString()}}
            </div>
          </div>
          <div style="padding: 10px;font-size: 14px;flex: 1;">{{KT("notification.types."+e.type)}}</div>
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

import {inject, ref, computed, onBeforeUnmount} from 'vue';
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



const events = computed(()=>{
    let tmp = {};
    data.value.forEach((e)=>{
          if(!tmp[e.deviceId]){
            tmp[e.deviceId] = [];
          }

          tmp[e.deviceId].push(e);
    });

    return tmp;
});

const $traccar = window.$traccar;

const onChange = (e)=>{
  filter.value = e;
}

const loadRoute = (b)=>{
  $traccar.loadRoute(b.deviceId,b.startTime,b.endTime).then(({data})=>{


    let tmp = [];
    data.forEach((p)=>{
      tmp.push([p.latitude,p.longitude,p.id,p.course]);
    });

    updateRoute(tmp);
    loading.value = false;
  })
}


import {saveAs} from "file-saver";

onBeforeUnmount(()=>{
  //updateRoute([]);
})

const loadResume = (exp=false)=>{
  loading.value = 1;

  $traccar.getReportEvents(filter.value.deviceId,filter.value.groupId,new Date(filter.value.date[0]).toISOString(),new Date(filter.value.date[1]).toISOString(),exp).then((r)=>{
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
        if(p.positionId && !tmp.includes(p.positionId)) {
          tmp.push(p.positionId);
        }
      });


      $traccar.getPositions(tmp).then((pdata)=>{

        tmp = [];

        pdata.forEach((p)=>{
          tmp.push([p.latitude,p.longitude,p.id,p.course]);
        });

        updateRoute(tmp,false);
      })


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