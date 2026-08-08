<template>

  <el-tooltip v-if="showBtn" :content="(!pushEnabled)?'Habilitar Notificações':'Desabilitar Notificações'">
    <div id="mute" @click="enablePush()" style="cursor: pointer;font-size: 1.2rem;margin: 0.3rem 0.5rem;">
      <span v-if="!pushEnabled"><i  class="fas fa-bell" style="color: silver;"></i></span>
      <span v-else ><i class="fas fa-bell"></i></span>
    </div>
  </el-tooltip>

</template>


<script setup>

import {ElTooltip} from "element-plus";

import {ref,onMounted} from 'vue';
import {useStore} from 'vuex';

// eslint-disable-next-line no-unused-vars
const store = useStore();

const showBtn = ref(false);
const pushEnabled = ref(false);

/*
function urlB64ToUint8Array(base64String) {
  const padding = '='.repeat((4 - base64String.length % 4) % 4);
  const base64 = (base64String + padding)
      .replace(/-/g, '+')
      .replace(/_/g, '/');

  const rawData = window.atob(base64);
  const outputArray = new Uint8Array(rawData.length);

  for (let i = 0; i < rawData.length; ++i) {
    outputArray[i] = rawData.charCodeAt(i);
  }
  return outputArray;
}*/



import firebaseMessaging from '@/firebase'


onMounted(()=>{
    if(firebaseMessaging!==false){

      showBtn.value = true;


	// eslint-disable-next-line no-undef
      firebaseMessaging.getToken({ vapidKey: vapidKey })
          .then((currentToken) => {
            if (currentToken) {
              console.log('client token', currentToken);
              store.dispatch("setToken",currentToken);
              pushEnabled.value = true;
            } else {
              console.log('No registration token available. Request permission to generate one.');
            }
          }).catch((err) => {
        console.log('An error occurred while retrieving token. ', err);
      })
    }
})

const enablePush = ()=>{


	// eslint-disable-next-line no-undef
    firebaseMessaging.getToken({ vapidKey: vapidKey })
      .then((currentToken) => {
        if (currentToken) {
        console.log('client token', currentToken);
          store.dispatch("setToken",currentToken);
          pushEnabled.value = true;
      } else {
        console.log('No registration token available. Request permission to generate one.');
      }
      }).catch((err) => {
        console.log('An error occurred while retrieving token. ', err);
      })


}

</script>