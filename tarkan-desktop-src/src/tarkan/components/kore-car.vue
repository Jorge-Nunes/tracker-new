<template>
  <div  v-if="props.car && latlng">
    <l-marker @click="$emit('click',$event)" :duration="1000"  :lat-lng="latlng">
      <l-icon :icon-size="[30,30]" :icon-anchor="[15,15]">
        <div class="car" style="position: relative;width: 30px;height: 30px;">
          <div class="info">
              {{ props.car.name}}
          </div>

          <div style="width: 30px;height: 30px; background: red;"></div>
          <div v-if="false" :class="'glow_'+((props.car.status!=='online')?'orange':(props.position.attributes.ignition)?'green':(props.position.attributes.blocked)?'red':'none')" :set="carType = props.car.attributes['tarkan.car'] || 'car'" :style="'transform: rotate('+( course)+'deg)'">
            <car :zoom="props.zoom" :id="'svg-'+ props.car.id" v-if="carType === 'car'"  :cabineStyle="props.car.attributes['tarkan.color'] || ''"></car>
            <truck :zoom="props.zoom" :id="'svg-'+ props.car.id" v-if="carType === 'truck'"  :bauStyle="props.car.attributes['tarkan.color_extra1'] || ''" :cabineStyle="props.car.attributes['tarkan.color'] || ''"></truck>
            <truck18 :zoom="props.zoom" :id="'svg-'+ props.car.id" v-if="carType === 'truck18'"  :bauStyle="props.car.attributes['tarkan.color_extra1'] || ''" :cabineStyle="props.car.attributes['tarkan.color'] || ''"></truck18>
            <utility :zoom="props.zoom" :id="'svg-'+ props.car.id" v-if="carType === 'utility'"  :cabineStyle="props.car.attributes['tarkan.color'] || ''"></utility>
          </div>

        </div>
      </l-icon>
    </l-marker>
  </div>
</template>



<script setup>
import truck18 from '../cars/truck'
import truck from '../cars/truck2'
import car from '../cars/car'
import utility from '../cars/utility'

import {LIcon,LMarker} from "@vue-leaflet/vue-leaflet";

import {defineProps,ref,watch,onMounted} from 'vue'

const props = defineProps(['car','position','zoom']);
const latlng = ref(false);
const course = ref(0);

//let interval = false;

onMounted(()=>{
  if(props.position) {
    latlng.value = {lat: props.position.latitude, lon: props.position.longitude};
    course.value = props.position.course;
  }
})

watch(()=>{
 return props.position
},()=>{
  if(props.position===false){ return false; }
  if(latlng.value===false) {
    latlng.value = {lat: props.position.latitude, lon: props.position.longitude};
    course.value = props.position.course;
  }else{


    latlng.value = {lat: props.position.latitude, lon: props.position.longitude};
    course.value = props.position.course;

    /*
    if(interval){
      clearInterval(interval);
    }

    const courseDiff = course.value - props.position.course;
    const latDiff = latlng.value.lat - props.position.latitude;
    const lonDiff = latlng.value.lon - props.position.longitude;

    const latAdd = latDiff / 100;
    const lonAdd = lonDiff / 100;
    const courseAdd = courseDiff / 100;

    let lp = 0;

    interval = setInterval(()=>{
      const lat = latlng.value.lat - latAdd;
      const lon = latlng.value.lon - lonAdd;

      latlng.value = {lat: lat, lon: lon};

      course.value = course.value - courseAdd;

      lp++;

      if(lp>=100){
        clearInterval(interval);
      }
    },1)*/


  }
  console.log("position changed")

})



</script>






<style scoped>
.info{
  display: none;
  position: absolute;
  left: 50%;
  bottom: 0px;
  padding: 5px;
  background: white;
  border: silver 1px solid;
  border-radius: 5px;
  transform: translate(-50%,100%);
  z-index: 2000;
  width: 200px;
}

.car:hover .info{
  display: block;
}

.icon{
  width: 60px;
  height: 60px;
  position: relative;
  background-size: cover;
}

.icon .color{
  width: 60px;
  height: 60px;
  position: absolute;
  left: 0px;
  top: 0px;
  background-size: cover;
}

#cabine2{
  box-shadow: 10px 10px 10px #05a7e3;
}

.car1{
  background-image: url('/img/cars/car1_base.png');
}

.car1 .color{
  background-image: url('/img/cars/car1_color.png');
}


.car1 .glow{
  background-image: url('/img/cars/car1_glow.png');
}



.car2{
  background-image: url('/img/cars/car2_base.png');
}

.car2 .color{
  background-image: url('/img/cars/car2_color.png');
}



.car3{
  background-image: url('/img/cars/car3_base.png');
}

.car3 .color{
  background-image: url('/img/cars/car3_color.png');
}


.car8{
  background-image: url('/img/cars/car8_base.png');
}

.car8 .color{
  background: url('/img/cars/car8_color.png');
}


.car10{
  background-image: url('/img/cars/car10_base.png');
}

.car10 .color{
  background-image: url('/img/cars/car10_color.png');
}

.color_none{
  filter: hue-rotate(0deg) saturate(0) brightness(1.6);
}

.color_white{
  filter: hue-rotate(0deg) saturate(0) brightness(2);
}


.color_black{
  filter: hue-rotate(0deg) saturate(0) brightness(0.5);
}

.color_red{
  filter: hue-rotate(160deg) saturate(5);
}

.color_blue{
  filter: hue-rotate(5deg) saturate(5);
}

.color_green{
  filter: hue-rotate(250deg) saturate(5);
}

.color_orange{
  filter: hue-rotate(211deg) saturate(5);
}

.glow_orange{
  filter: drop-shadow( 0px 0px 1px rgba(0, 0, 0, 1)) drop-shadow( 0px 0px 5px rgba(255, 180, 0, 1));
}


.glow_red{
  filter: drop-shadow( 0px 0px 1px rgba(0, 0, 0, 1)) drop-shadow( 0px 0px 5px rgba(255, 0, 0, 1));
}


.glow_green{
  filter: drop-shadow( 0px 0px 1px rgba(0, 0, 0, 1)) drop-shadow( 0px 0px 5px rgba(0, 255, 0, 1));
}
</style>