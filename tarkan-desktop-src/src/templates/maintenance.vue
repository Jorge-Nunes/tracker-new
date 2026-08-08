<template>
  <div class="login">
    <div id="logo" style="text-align: center;"><img width="25%" src="/tarkan/assets/custom/logoWhite.png"></div>

    <div id="login-form">
      <div style="text-align: center;">
        Não é possivel acessar a plataforma no momento<br><br>
        Verifique sua conexão com a internet e tente novamente.

        <br><br><br>

        <el-button @click="recheck()" :disabled="isLoading" type="primary">Tentar novamente</el-button>

      </div>
    </div>

  </div>
</template>



<script setup>

import {ref,onMounted} from "vue";
import store from "@/store";
import router from "@/routes";

import {ElButton} from "element-plus/es/components/button"


import 'element-plus/es/components/button/style/css'

let interval = null;

const isLoading = ref(false);

const recheck = ()=>{
  isLoading.value = true;
  store.dispatch("server/load").then(()=> {

    isLoading.value = false;
    if(interval) {
      clearInterval(interval);
    }

    router.push("/home");

  })
}

onMounted(()=>{
  recheck();

  interval = setInterval(()=>{
    recheck();
  },30000);
})


</script>

<style scoped>
.login{
  background: url('/tarkan/assets/custom/bg.jpg');
  background-size: cover;
  width: 100vw;
  height: 100vh;
}

.login:after {
  content: " ";
  position: absolute;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background: var(--tk-login-filter);
}

#logo{
  position: absolute;
  left: 50%;
  top: 20%;
  width: 30%;
  transform: translate(-50%,-50%);
  z-index: 10;
  transition: width 0.3s;
  text-align: center;
}

#login-form{
  position: absolute;
  left: 50%;
  top: 50%;
  background: white;
  border-radius: 10px;
  z-index: 10;
  width: 30%;
  transform: translate(-50%,-50%);
  padding: 20px;
  box-sizing: border-box;
  text-align: left;
  transition: width 0.3s;
}

.label{
  font-size: 14px;
  margin-top: 10px;
  margin-bottom: 5px;
}

.input input{
  width: 100%;
  height: 40px;
  border: silver 1px solid;
  border-radius: 5px;
  outline: none;
  padding: 10px;
}

.submit{
  margin-top: 10px;
  float: right;
}

#copy{
  position: fixed;
  bottom: 0px;
  right: 0px;
  width: 100%;
  text-align: center;
  font-size: 12px;
  padding: 10px;
  z-index: 10001;
}

@media (max-width: 1000px)
{
  #login-form,#logo
  {
    width: 80%;
  }
}
</style>