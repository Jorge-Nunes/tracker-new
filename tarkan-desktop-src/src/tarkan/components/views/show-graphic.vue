<template>
  <el-dialog :lock-scroll="true" :top="'50px'" :width="'60%'" v-model="show" title="Teste">

    <template v-slot:title>
      <div  style="border-bottom: #e0e0e0 1px solid;padding: 20px;">
        <div class="modal-title" style="display: flex;width: calc(100% - 50px);justify-content: space-between;">
            <div>Graphic</div>
            <div>
              <el-select v-model="formData.type" filterable multiple :size="'large'"  :placeholder="KT('graph.graphicType')" :no-match-text="KT('NO_MATCH_TEXT')" :no-data-text="KT('NO_DATA_TEXT')">
                <el-option
                    v-for="item in graphicType"
                    :key="item"
                    :label="KT('graphs.'+item)"
                    :value="item"
                >
                </el-option>
              </el-select>
            </div>
        </div>
      </div>
    </template>

    <div style="margin: 20px;height: calc(70vh + 20px);">
      <LineChart style="height: 70vh;" :chartData="testData"></LineChart>
    </div>
  </el-dialog>
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

import {ElDialog,ElSelect,ElOption} from "element-plus";

import {ref,defineExpose,computed} from 'vue';

const show = ref(false);

const data = ref([]);
const formData = ref({type: ['speed']});

const graphicType = computed(()=>{
    if(data.value.length>0) {

      let tmp = Object.keys(data.value[0]);

      Object.keys(data.value[0].attributes).forEach((t)=>{
        tmp.push('attributes.'+t);
      });

      return tmp;
    }else{
      return [];
    }
});



import { LineChart } from 'vue-chart-3';
import { Chart, registerables } from "chart.js";

Chart.register(...registerables);


const testData = computed(()=>{

  let labels = [];
  let sets = [];
  let tmpSets = {};


  console.log(formData.value.type);

  data.value.forEach((d)=>{
      labels.push(new Date(d.deviceTime).toLocaleString());

      formData.value.type.forEach((t)=>{

          if(!tmpSets[t]){
            tmpSets[t] = [];
          }

          tmpSets[t].push(parseFloat(d[t]));

      })
  })

  formData.value.type.forEach((t)=>{

    const color = Math.floor(Math.random()*16777215).toString(16);

    const colorAlpha = 'rgba('+parseInt(color.substring(0,2),16)+','+parseInt(color.substring(2,4),16)+','+parseInt(color.substring(4,6),16)+',0.4)';

    sets.push({
      label: t,
      data: tmpSets[t],
      borderColor: '#'+color,
      backgroundColor: colorAlpha,
    });
  })


  return {
            labels: labels,
            datasets: sets
          }
});

const showGraphic = (d)=>{

    data.value = d;
    show.value = true;
}


defineExpose({
  showGraphic
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