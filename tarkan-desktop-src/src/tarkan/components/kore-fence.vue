<template>


  <l-polyline v-if="fenceType==='LINESTRING'" :interactive="false" :lat-lngs="fenceArea" :color="props.geofence.attributes['color'] || props.color ||'#05a7e3'"></l-polyline>
  <l-polygon v-else-if="fenceType==='POLYGON'" :interactive="false" :lat-lngs="fenceArea" :fill-opacity="0.15"  :fill="true" :fill-color="props.geofence.attributes['color'] || props.color || '#05a7e3'" :color="props.geofence.attributes['color'] || props.color || '#05a7e3'"></l-polygon>
  <l-circle v-else-if="fenceType==='CIRCLE'" :interactive="false" :lat-lng="[fenceArea[1],fenceArea[2]]" :radius="parseFloat(fenceArea[3])" :fill="true" :fill-opacity="0.15" :fill-color="props.geofence.attributes['color'] || props.color || '#05a7e3'"   :color="props.geofence.attributes['color'] || props.color || '#05a7e3'"></l-circle>


</template>

<script setup>


import {LPolyline,LCircle,LPolygon} from "@vue-leaflet/vue-leaflet";

import {defineProps,computed} from 'vue';

const props = defineProps(['geofence','color']);

var fenceTypeReg = /(CIRCLE|POLYGON|LINESTRING)/gi;
var fenceAreaCircle = /\((.*?) (.*?),(.*?)\)/gi;
var fenceAreaPolygon = /(\s?([-\d.]*)\s([-\d.]*),?)/gm;
var fenceAreaLinestring = /(\s?([-\d.]*)\s([-\d.]*),?)/gm;

const fenceType = computed(()=>{
  return fenceTypeReg.exec(props.geofence.area)[0];
})

const fenceArea = computed(()=>{
  if(fenceType.value === 'LINESTRING'){

      const linestring = props.geofence.area.match(fenceAreaLinestring);
      let tmp = [];
      linestring.forEach((L)=>{
          const S = L.trim().replace(",","").split(" ");
          if(S.length===2) {
            tmp.push(S);
          }
      })


      return tmp;
  }else if(fenceType.value === 'POLYGON'){

    const polygon = props.geofence.area.match(fenceAreaPolygon);
    let tmp = [];
    polygon.forEach((L)=>{
      const S = L.trim().replace(",","").split(" ");
      if(S.length===2) {
        tmp.push(S);
      }
    })


    return tmp;

  }else if(fenceType.value === 'CIRCLE'){
    return fenceAreaCircle.exec(props.geofence.area);
  }

  return [];

})



</script>