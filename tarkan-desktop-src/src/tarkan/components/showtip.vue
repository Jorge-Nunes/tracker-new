<template>
  <div v-if="show" class="tooltip" :style="{left: position.left,top: position.top}">
      {{text}}
  </div>
</template>

<script setup>

import {onMounted,ref} from "vue";

const show = ref(false);
const text = ref('');
const position = ref({left: 0,top: 0});


const showTip = (evt,txt,e=false)=>{




  if(e){
    position.value = evt;
  }else {
    const pos = evt.target.getBoundingClientRect();

    position.value = {left: (pos.left + (pos.width / 2)) + 'px', top: (pos.top - 5) + 'px'};
  }

  show.value = true;
  text.value = txt;
}

const hideTip = ()=>{
  show.value = false;
}


onMounted(()=>{
  window.$showTip = showTip;
  window.$hideTip = hideTip;
})

</script>


<style scoped>
.tooltip{
  background: rgba(0,0,0,0.8);
  border-radius: 3px;
  color: white;
  font-size: 11px;
  padding: 7px;
  position: absolute;
  z-index: 99999999999;
  transform: translate(-50%,-101%);
}

</style>