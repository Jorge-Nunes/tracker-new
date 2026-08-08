<template>
  <div class="login">
      <div id="logo"><img :style="{width: store.getters['server/getLogoWidth']}" src="/tarkan/assets/custom/logoWhite.png"></div>

      <div id="login-form">
        
        <div>
          <div>
            <div class="label">{{KT('user.email')}}</div>
            <el-input ref="ruserinput" v-model="rusername" disabled :placeholder="KT('user.email')" />
          </div>
          <div>
            <div class="label">{{KT('user.password')}}</div>
            <el-input ref="rpassinput" v-model="rpassword" @keydown.enter="passcinput.focus()" type="password" :placeholder="KT('user.password')" />
          </div>
          
          <div>
            <div class="label">{{KT('user.passwordConfirm')}}</div>
            <el-input ref="passcinput" v-model="passwordConfirm" @keydown.enter="doLogin()" type="password" :placeholder="KT('user.passwordConfirm')" />
          </div>
          <div style="margin-top: 25px; float: left;">            
            <el-button type="danger" @click="push('/login')">{{KT('cancel')}}</el-button>      
          </div>

          <div style="margin-top: 25px;float: right;">
            <el-button type="primary" @click="doRegister()">{{KT('save')}}</el-button>
          </div>

        </div>
      </div>

  </div>
</template>



<script setup>

import 'element-plus/es/components/input/style/css'
import 'element-plus/es/components/button/style/css'
import 'element-plus/es/components/switch/style/css'
import 'element-plus/es/components/progress/style/css'
import 'element-plus/es/components/message-box/style/css'

import {ElInput} from "element-plus/es/components/input";

import {ElButton} from "element-plus/es/components/button";


import KT from '../tarkan/func/kt'

import {ref,inject} from 'vue';
import {useRouter,useRoute} from 'vue-router'
import {useStore} from 'vuex';



const store = useStore();

const {push} = useRouter();
const route = useRoute();



const traccar = inject('traccar');
//const flyTo = inject('markerClick');

console.log(traccar);

const ruserinput = ref(null);
const rpassinput = ref(null);
const passcinput = ref(null);

const rusername = ref(route.query.user || '');

const rpassword = ref('');
const passwordConfirm = ref('');




</script>

<style scoped>
.login{
  background: url('/tarkan/assets/custom/bg.jpg');
  background-size: cover;

  width: var(--vw,100vw);
  height: var(--vh,100vh);
}

.login:after {
  content: " ";
  position: absolute;
  left: 0;
  top: 0;
  width: var(--vw,100vw);
  height: var(--vh,100vh);
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