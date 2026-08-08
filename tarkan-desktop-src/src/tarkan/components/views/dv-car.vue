<template>
  <div
      :class="{car: true,isCar: props.selected}"
      style="">
    <img class="base" :src="'/img/cars/'+props.img+'_base.png'">
    <img v-if="props.color1" class="color" :style="'filter:'+props.filter1" :src="'/img/cars/'+props.img+'_color1.png'">
    <img v-if="props.color2" class="color" :style="'filter:'+props.filter2" :src="'/img/cars/'+props.img+'_color2.png'">
  </div>
</template>

<script setup>

import {defineProps} from 'vue';
const props = defineProps(['selected','img','color1','color2','filter1','filter2']);



</script>

<style scoped>
.car {
  cursor: pointer;
  margin: 5px;
  border: silver 1px dotted;
  border-radius: 10px;
  padding: 10px;
  width: calc(13.7% - 10px);
  height: 80px;
  display: flex;
  align-content: center;
  justify-content: center;
  text-align: center;
  position: relative;
}

.car .base{
  height: 80px;
  position: absolute;
}


.car .color{
  height: 80px;
  position: absolute;
}


.isCar{
  border: #05a7e3 1px solid !important;
}


</style>