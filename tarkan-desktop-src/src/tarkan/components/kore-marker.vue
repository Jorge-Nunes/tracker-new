<template>
  <div  v-if="props.position">
    <l-marker @click="$emit('click',$event)"  :lat-lng="{lat: props.position[0],lon: props.position[1]}">
      <l-icon :icon-size="[50,50]" :icon-anchor="[25,50]">
        <div class="car" style="position: relative;width:50px;height: 50px;">
          <div class="info" v-if="props.type!=='point'" :class="{right: (props.type==='end')}">
            {{KT('route.'+props.name)}}
          </div>

          <div v-if="props.type!=='point'" class="bolinha">
            <svg v-if="props.type==='start'" aria-hidden="true" style="margin-left: 2.5px;" height="13px" focusable="false" data-prefix="fas" data-icon="play" class="svg-inline--fa fa-play fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M424.4 214.7L72.4 6.6C43.8-10.3 0 6.1 0 47.9V464c0 37.5 40.7 60.1 72.4 41.3l352-208c31.4-18.5 31.5-64.1 0-82.6z"></path></svg>
            <svg v-if="props.type==='end'" aria-hidden="true" height="13px" focusable="false" data-prefix="fas" data-icon="flag-checkered" class="svg-inline--fa fa-flag-checkered fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M243.2 189.9V258c26.1 5.9 49.3 15.6 73.6 22.3v-68.2c-26-5.8-49.4-15.5-73.6-22.2zm223.3-123c-34.3 15.9-76.5 31.9-117 31.9C296 98.8 251.7 64 184.3 64c-25 0-47.3 4.4-68 12 2.8-7.3 4.1-15.2 3.6-23.6C118.1 24 94.8 1.2 66.3 0 34.3-1.3 8 24.3 8 56c0 19 9.5 35.8 24 45.9V488c0 13.3 10.7 24 24 24h16c13.3 0 24-10.7 24-24v-94.4c28.3-12.1 63.6-22.1 114.4-22.1 53.6 0 97.8 34.8 165.2 34.8 48.2 0 86.7-16.3 122.5-40.9 8.7-6 13.8-15.8 13.8-26.4V95.9c.1-23.3-24.2-38.8-45.4-29zM169.6 325.5c-25.8 2.7-50 8.2-73.6 16.6v-70.5c26.2-9.3 47.5-15 73.6-17.4zM464 191c-23.6 9.8-46.3 19.5-73.6 23.9V286c24.8-3.4 51.4-11.8 73.6-26v70.5c-25.1 16.1-48.5 24.7-73.6 27.1V286c-27 3.7-47.9 1.5-73.6-5.6v67.4c-23.9-7.4-47.3-16.7-73.6-21.3V258c-19.7-4.4-40.8-6.8-73.6-3.8v-70c-22.4 3.1-44.6 10.2-73.6 20.9v-70.5c33.2-12.2 50.1-19.8 73.6-22v71.6c27-3.7 48.4-1.3 73.6 5.7v-67.4c23.7 7.4 47.2 16.7 73.6 21.3v68.4c23.7 5.3 47.6 6.9 73.6 2.7V143c27-4.8 52.3-13.6 73.6-22.5z"></path></svg>
          </div>

          <div v-if="props.type!=='point'" class="icon">
              <img src="/img/marker.png" style="width: 50px;height: 50px;">
          </div>
          <div v-else style="width: 13px;height: 13px;left: 50%;bottom: 0px;position: absolute;color: #05a7e3;" :style="'transform: translate(-50%,50%)'">
            <img src="/img/pointer.png" :style="'rotate('+position[3]+'deg)'" >
          </div>
        </div>
      </l-icon>
    </l-marker>
  </div>
</template>



<script setup>

import {LIcon,LMarker} from "@vue-leaflet/vue-leaflet";

import {defineProps} from 'vue'
const props = defineProps(['name','type','position','zoom']);


</script>






<style scoped>
.info{
  position: absolute;
  left: 15px;
  top: 5px;
  padding: 3px;
  background: #007fff;
  border-radius: 5px;
  color: white;
  padding-left: 15px;
  padding-right: 15px;
  transform: translate(-100%,0);
  z-index: 1;
}

.right{

  left: auto;
  right: 15px;
  transform: translate(100%,0);
}

.car:hover .info{
  display: block;
}

.icon{
  width: 60px;
  height: 60px;
  position: absolute;
  left: 0px;
  top: 0px;
  z-index: 5;
}

.bolinha{
  background: white;
  width: 27px;
  height: 27px;
  position: absolute;
  border-radius: 50%;
  left: 11.5px;
  top: 6px;
  z-index: 3;
  text-align: center;
  padding: 6px;
  box-sizing: border-box;
}

</style>