<template>
  <div class="login">
      <div id="logo"><img :style="{width: store.getters['server/getLogoWidth']}" src="/tarkan/assets/custom/logoWhite.png"></div>

      <div id="login-form">
        <div style="text-align: center;" v-if="$route.name==='Share'">
          Acessando compartilhamento, por favor aguarde...<br><br>
            <div style="margin-left: 10%;">
            <el-progress :percentage="50" status="warning" :indeterminate="true" />
            </div>
        </div>
        <template v-else>
          <div>
            <div class="label">{{KT('login.username')}}</div>
            <el-input ref="userinput" v-model="username" @keydown.enter="passinput.focus()" :placeholder="KT('login.your_email')" />
          </div>
          <div>
            <div class="label">{{KT('login.password')}}</div>
            <el-input ref="passinput" v-model="password" @keydown.enter="doLogin()" type="password" :placeholder="KT('login.your_password')" />
          </div>
          <div style="margin-top: 10px; float: left;">
            <el-switch
                v-model="rememberme"
                :inactive-text="''"
                :active-text="KT('rememberme')"
                :active-value="true"
                :inactive-value="false"
            >
            </el-switch>
            <div v-if="false" @click="doPasswordRecovery()" style="cursor: pointer;text-decoration: none;font-size: 12px;margin-top: 7px;color: var(--el-color-primary);">
              {{ KT('login.lost_password') }}
            </div>
            <div v-if="false && store.getters['server/getRegistrationEnabled']" @click="push('/register')" style="cursor: pointer;text-decoration: none;font-size: 12px;margin-top: 7px;color: var(--el-color-primary);">
              {{ KT('login.register') }}
            </div>
          </div>

          <div style="margin-top: 25px;float: right;">
            <el-button type="primary" @click="doLogin()">{{KT('login.signin')}}</el-button>
          </div>
        </template>


      </div>

    /* IFTRUE_myFlag2 */
      <div style="background: rgba(255,255,255,0.8);padding: 5px;color: #101010;z-index: 999999999;position: fixed; bottom: 0px;right: 0px;width: 100%; text-align: center;font-size: 11px;">Esta empresa de Rastreamento faz uso do <a style="color: #111111;text-decoration: none;" href="https://tarkan.mobi" target="_blank">Tarkan Starter</a></div>
    /* FITRUE_myFlag2 */

  </div>
</template>



<script setup>

import 'element-plus/es/components/input/style/css'
import 'element-plus/es/components/button/style/css'
import 'element-plus/es/components/switch/style/css'
import 'element-plus/es/components/progress/style/css'
import 'element-plus/es/components/message-box/style/css'

import {ElInput} from "element-plus/es/components/input";

import {ElProgress} from "element-plus/es/components/progress";
import {ElSwitch} from "element-plus/es/components/switch";
import {ElButton} from "element-plus/es/components/button";

import {ElMessageBox} from "element-plus/es/components/message-box";

import KT from '../tarkan/func/kt'

import {useRouter,useRoute} from 'vue-router'
import {useStore} from 'vuex';



const store = useStore();

const {push} = useRouter();
const route = useRoute();


import {ref,onMounted,inject} from 'vue';

const traccar = inject('traccar');
//const flyTo = inject('markerClick');

const userinput = ref(null);
const passinput = ref(null);

const rememberme = ref(false);
const username = ref(route.query.user || '');
const password = ref(route.query.pass || '');

onMounted(()=>{
  if(route.name==='Share'){
    doLoginWithToken();
  }else {
      userinput.value.focus();
  }
})

const doLoginWithToken = ()=>{
  traccar.login(route.params.token,route.params.token).then((r)=>{
    store.commit('setAuth',r);
    store.dispatch("loadUserData").then(()=> {
      push('/devices/' + r.attributes.deviceId);

      store.commit("devices/setTrail",parseInt(r.attributes.deviceId));
      store.commit("devices/setFollow",parseInt(r.attributes.deviceId));
    });
  }).catch((err)=>{
    ElMessageBox.confirm(KT('INVALID_SHARE') || err)
        .then(() => {
        })
        .catch(() => {
          // catch error
        })
  });
}

const doPasswordRecovery = ()=>{
  let user = username.value;

  if(user == ''){
    ElMessageBox.confirm(KT('login.invalid_username'))
        .then(() => {
        })
        .catch(() => {
          // catch error
        })
  }

  if(route.meta.isDriver){
    user = 'qrcode-'+user;
  }

  store.commit("server/setPage",false);

  traccar.recoverPassword(user).then((r)=>{
    console.log(r);

    ElMessageBox.confirm(KT('login.lost_send'))
      .then(() => {
      })
      .catch(() => {
        // catch error
      })

    store.commit("server/setPage",true);
  }).catch((err)=>{


    store.commit("server/setPage",true);

    console.log(err);
    //loadingInstance1.close();
    ElMessageBox.confirm(KT(err) || err)
      .then(() => {
      })
      .catch(() => {
        // catch error
      })
  });
}

const doLogin = ()=>{

  //const loadingInstance1 = ElLoading.service({ fullscreen: true })

  store.commit("server/setPage",false);

  let user = username.value;

  if(route.meta.isDriver){
    user = 'qrcode-'+user;
  }

  traccar.login(user,password.value).then((r)=>{
    console.log(r);

      var regex = /(iPhone|iPad|iPod);[^OS]*OS (\d)/;
      var matches = navigator.userAgent.match(regex);

      if (rememberme.value) {
        window.localStorage.setItem('rememberme', btoa(username.value + "|" + password.value));
      }

      if(route.query.to){
        window.location.href = window.location.protocol + '//' + window.location.host + ''+route.query.to+'?time=' + new Date().getTime();
      }else if(matches){
        if(route.meta.isDriver) {
          window.location.href = window.location.protocol + '//' + window.location.host + '/qr-driver?time=' + new Date().getTime();
        }else {
          window.location.href = window.location.protocol + '//' + window.location.host + '/home?time=' + new Date().getTime();
        }
      }else {

        store.commit('setAuth', r);

        store.dispatch("loadUserData");




        if(route.meta.isDriver) {
          window.location.href = window.location.protocol + '//' + window.location.host + '/qr-driver?time=' + new Date().getTime();
        }else {
          push('/home')
        }
      }
      //loadingInstance1.close();
  }).catch((err)=>{


    store.commit("server/setPage",true);

    console.log(err);
    //loadingInstance1.close();
    ElMessageBox.confirm(KT(err) || err)
        .then(() => {
        })
        .catch(() => {
          // catch error
        })
  });
}

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