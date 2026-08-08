<template>

  <qrcode-stream @decode="onDecode" @init="onInit"></qrcode-stream>

  <div style="background: white; padding: 20px; border-radius: 5px; position: absolute; bottom: 10%;left: 10%;width: 80%;box-sizing: border-box;text-align: center;">

    <template v-if="state===1 || store.getters['isDriverOnDevice']">
      Você fez o check-in no veiculo

      <template v-if="deviceInfo">
        <b>{{deviceInfo.name}} - {{deviceInfo['attributes']['placa']}}</b>
      </template>
      <el-button @click="doCheckOut()" type="warning">Check-out</el-button>
    </template>
    <template v-else-if="state===0">
     Por favor, Aponte a camera do Smartphone para o QRCode para liberar o acesso ao veículo.
    </template>
    <template v-else-if="state===-1">
      O QRCode não é válido, tente novamente...
    </template>
    <template v-else-if="state===-5">
      Realizando check-out, por favor aguarde...
    </template>
    <template v-else>
      Verificando seu acesso...por favor aguarde!
    </template>
  </div>

</template>


<script setup>


import {QrcodeStream} from "vue3-qrcode-reader";

import {ref,computed,onMounted} from "vue";
import {useStore} from "vuex";

import {ElButton} from "element-plus";
import {useRoute} from "vue-router";

const store = useStore();
const route = useRoute();

const deviceInfo = computed(()=>{
    return store.getters["devices/getDevice"](store.state.auth.attributes['tarkan.isQrDeviceId']);
})
const state = ref(0);


onMounted(()=>{
    if(route.name==='Driver2'){
      onDecode(window.location.href);
    }
})

const onDecode = (r)=>{

  if(!store.getters['isDriverOnDevice']) {

    if (!r.match(window.location.host)) {
      state.value = -1;
    } else if (r.match(/scan\/(.*?)/gi)) {

      state.value = -2;

      const scan = r.split("scan/")[1].split("?")[0];

      window.$tarkan.checkDriver(scan).then(({data}) => {

        store.commit("setAuth", data);

        state.value = 1;
      }).catch(() => {
        state.value = -1;
      });

    }
  }
}

const doCheckOut = ()=>{

  state.value = -5;

  window.$tarkan.checkOutDriver().then(({data}) => {
    store.commit("setAuth", data);
    state.value = 0;
  }).catch(() => {
    state.value = 1;
  });
}

const onInit = (i)=>{

  console.log(i);

}


</script>