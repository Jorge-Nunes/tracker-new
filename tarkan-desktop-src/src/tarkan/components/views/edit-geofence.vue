<template>
  <el-dialog :lock-scroll="true" v-model="show">

    <template v-slot:title>
      <div  style="border-bottom: #e0e0e0 1px solid;padding: 20px;">
        <div class="modal-title" >{{title}}</div>
      </div>
    </template>
    <template v-slot:footer>
      <div  style="border-top: #e0e0e0 1px solid;padding: 20px;display: flex;justify-content: space-between">
        <div><el-button  type="danger" @click="doDelete()">{{KT('delete')}}</el-button></div>
        <div><el-button type="danger" plain @click="doCancel()">{{KT('cancel')}}</el-button>
          <el-button type="primary" @click="doSave()">{{KT('save')}}</el-button></div>
      </div>
    </template>

    <el-tabs v-model="tab">
      <el-tab-pane :label="KT('geofence.geofences')" name="first">
        <el-form label-width="120px" label-position="top">
          <el-form-item :label="KT('geofence.name')" >
            <el-input v-model="formData.name"></el-input>
          </el-form-item>
          <el-form-item :label="KT('geofence.type')" >
            <el-button-group size="large">
              <el-button type="primary" :plain="!(formData.type==='LINESTRING')" @click="formData.type='LINESTRING'">{{KT('geofence.line')}}</el-button>
              <el-button type="primary" :plain="!(formData.type==='CIRCLE')" @click="formData.type='CIRCLE'">{{KT('geofence.circle')}}</el-button>
              <el-button type="primary" :plain="!(formData.type==='POLYGON')" @click="formData.type='POLYGON'">{{KT('geofence.polygon')}}</el-button>
            </el-button-group>

          </el-form-item>


          <div style="display: flex;flex-direction: row;">
              <div style="flex: 1;">

                <el-form-item :label="KT('geofence.totalArea')" >
                  <el-input disabled :value="store.getters['geofences/getTotalArea']"></el-input>
                </el-form-item>

              </div>

            <div style="padding-top: 35px;margin-left: 10px;">
              <el-button type="primary" @click="doEditArea()">{{KT('geofence.editArea')}}</el-button>
            </div>

          </div>



        </el-form>


      </el-tab-pane>

      <el-tab-pane :label="KT('geofence.attributes')" name="attributes">
        <tab-attributes v-model="formData.attributes"></tab-attributes>
      </el-tab-pane>
    </el-tabs>
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
import 'element-plus/es/components/message-box/style/css'
import 'element-plus/es/components/dialog/style/css'
import 'element-plus/es/components/notification/style/css'

import {ElDialog,ElTabs,ElMessageBox,ElNotification,ElTabPane,ElForm,ElFormItem,ElButton,ElInput} from "element-plus";


import TabAttributes from "./tab-attributes";

const defaultGeofenceData = {
  name: '',
  type: 'LINESTRING',
  attributes: []
}

const defaultTraccarGeofenceData = {
  "id": 0,
  "name": "",
  "description": "",
  "area": "",
  "calendarId": 0,
  "attributes": { }
}

import {ref,defineExpose} from 'vue';
import {useStore} from 'vuex'

const store = useStore();



const show = ref(false);
const tab = ref('first');


const title = ref('');

// eslint-disable-next-line no-undef
const formData = ref(defaultGeofenceData);


import KT from '../../func/kt.js';

const doDelete = ()=>{
  ElMessageBox.confirm(KT('geofence.deleteConfirm'),KT('sure')).then(()=>{
    store.dispatch("geofences/delete",formData.value.id).then(()=>{
      show.value = false;
      ElNotification({
        title: 'Info',
        message: KT('success'),
        type: 'info',
      });
    })
  });
}

const newGeofence = ()=>{
  title.value = KT('geofence.new');

  // eslint-disable-next-line no-undef
    formData.value = JSON.parse(JSON.stringify(defaultGeofenceData));
    show.value = true;
}

const editGeofence = (geofence)=>{

  title.value = KT('geofence.edit');
  // eslint-disable-next-line no-undef
  formData.value = JSON.parse(JSON.stringify(defaultGeofenceData));


  Object.assign(formData.value,JSON.parse(JSON.stringify(geofence)));

  const area = getAreaParsed(geofence.area);

  formData.value.type = area.type;

  store.commit("geofences/setParams",area.params);

  show.value = true;
}

defineExpose({
  newGeofence,
  editGeofence
});


const doEditArea = ()=>{
  store.dispatch("geofences/enableEditing",formData.value.type)
}

const doCancel = ()=>{
  show.value = false;
}


var fenceAreaCircle = /\((.*?) (.*?),(.*?)\)/gi;
var fenceAreaPolygon = /(\s?([-\d.]*)\s([-\d.]*),?)/gm;
var fenceAreaLinestring = /(\s?([-\d.]*)\s([-\d.]*),?)/gm;

const getAreaParsed = (a)=>{
  const type = a.split("(")[0].trim();


  if(type === 'LINESTRING'){

    const linestring = a.match(fenceAreaLinestring);
    let tmp = [];
    linestring.forEach((L)=>{
      const S = L.trim().replace(",","").split(" ");
      if(S.length===2) {
        tmp.push(S);
      }
    })


    return {type: 'LINESTRING',params: tmp};
  }else if(type === 'POLYGON'){

    const polygon = a.match(fenceAreaPolygon);
    let tmp = [];
    polygon.forEach((L)=>{
      const S = L.trim().replace(",","").split(" ");
      if(S.length===2) {
        tmp.push(S);
      }
    })


    return {type: 'POLYGON',params: tmp};

  }else if(type === 'CIRCLE'){
    return {type: 'CIRCLE',params: fenceAreaCircle.exec(a)};
  }

  return {type};
}

const getParsedArea = ()=>{
    const type = formData.value.type;
    const params = store.state.geofences.mapPointEditingParams;
    if(type==='CIRCLE'){
      return 'CIRCLE ('+params[0]+' '+params[1]+', '+params[2]+')';
    }else if(type==='LINESTRING'){
      let tmp = 'LINESTRING ('

      params.forEach((p)=>{
        tmp+=p[0]+' '+p[1]+', ';
      })

      tmp = tmp.substring(0,tmp.length-2);

      tmp+= ')';

      return tmp;
    }else if(type==='POLYGON'){
      let tmp = 'POLYGON(('

      params.forEach((p)=>{
        tmp+=p[0]+' '+p[1]+', ';
      })

      tmp = tmp.substring(0,tmp.length-2);

      tmp+= '))';

      return tmp;
    }


}

const doSave = ()=>{

  const tmp = JSON.parse(JSON.stringify(defaultTraccarGeofenceData));

  tmp.id = formData.value.id;
  tmp.name = formData.value.name;
  tmp.area = getParsedArea();
  tmp.attributes = formData.value.attributes;

  if(tmp.name.trim()===''){
    ElMessageBox.confirm(KT('geofence.errors.FILL_NAME'),KT('ops')).then(()=>{});
  }else {

    store.dispatch("geofences/save", tmp).then(() => {
      show.value = false;
    })
  }
}


</script>

<style>

.el-button-group{
  display: flex !important;
}

.el-button-group .el-button--large{
  flex: 1 !important;
}

.el-select.el-select--large{
  width: 100%;
}

.el-dialog__header,.el-dialog__body,.el-dialog__footer{
  padding: 0px !important;
}

.el-dialog__footer{
  margin-top: 20px;
}

.el-tabs__nav-wrap{
  padding-left: 20px;
  padding-right: 20px;
}

.el-tabs__content{
  padding-left: 20px;
  padding-right: 20px;
}



.isCar{
  border: #05a7e3 1px solid !important;
}
</style>