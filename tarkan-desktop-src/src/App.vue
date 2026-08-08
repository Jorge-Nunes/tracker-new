<template>


  <div v-if="!store.getters['server/isReady']" style="width: 100%;position: absolute;left: 0px; top: 0px;z-index: 999999999 ">
    <el-progress
        :percentage="100"
        status="primary"
        :indeterminate="true"
        :duration="2"
        :show-text="false"
    />
  </div>



  <context-menu ref="contextMenuRef"></context-menu>
  <showtip></showtip>


  <div v-if="store.state.server.labelConf.whatsapp && store.state.server.labelConf.whatsapp!=''" style="position: absolute;right: 0px; bottom: 12px;z-index: 9999999999;"><a :href="'https://wa.me/'+store.state.server.labelConf.whatsapp" style="text-decoration: none;" target="_blank"><img src="img/whatsapp.png" border="0"></a></div>


  <div v-if="store.state.auth">

    /* IFTRUE_myFlag */
      <edit-calendars ref="editCalendarsRef"></edit-calendars>
      <link-objects ref="linkObjectsRef"></link-objects>
      <log-objects ref="logObjectsRef"></log-objects>

      <edit-share v-if="store.state.server.isPlus" ref="editShareRef"></edit-share>
      <edit-shares v-if="store.state.server.isPlus" ref="editSharesRef"></edit-shares>

      <edit-group ref="editGroupRef"></edit-group>
      <edit-users ref="editUsersRef"></edit-users>
      <edit-server ref="editServerRef"></edit-server>
      <edit-drivers ref="editDriversRef"></edit-drivers>
      <edit-maintenances ref="editMaintenancesRef"></edit-maintenances>
     <edit-theme  v-if="store.state.server.isPlus" ref="editThemeRef"></edit-theme>

    /* FITRUE_myFlag */


    <edit-user ref="editUserRef"></edit-user>
    <edit-notifications ref="editNotificationsRef"></edit-notifications>
    <edit-device ref="editDeviceRef"></edit-device>
    <qr-device ref="qrDeviceRef"></qr-device>
    <show-graphic ref="showGraphicsRef"></show-graphic>






  <div id="head">
    <div id="btnmenu" @click="menuShown = !menuShown">
      <i class="fas fa-bars"></i>
    </div>
    <div id="logo" >
      <img v-if="store.state.server.labelConf.headLogo.image" onclick="window.location.href = '/'" src="/tarkan/assets/custom/logo.png" style="width: 11rem;" >
      <div v-else style="font-weight: bold;text-transform: uppercase;font-family: montserrat, roboto;">
        <a onclick="window.location.href = '/'" style="color: var(--el-text-color-primary); text-decoration: none;">{{store.state.server.labelConf.headLogo.text}}</a></div>
    </div>
    <div style="display: flex;">



      <el-tooltip :content="(store.state.events.mute)?'Ouvir Notificações':'Silenciar Notificações'">
        <div id="mute" @click="store.dispatch('events/toggleMute')" style="cursor: pointer;font-size: 1.2rem;margin: 0.3rem 0.5rem;">
          <span v-if="store.state.events.mute"><i  class="fas fa-volume-mute" style="color: silver;"></i></span>
          <span v-else><i class="fas fa-volume-up"></i></span>
        </div>
      </el-tooltip>

      <push-notification-btn v-if="store.state.auth"></push-notification-btn>


      <div id="user" @click="userMenu($event)" style="cursor: pointer;">
        <div class="uname" v-if="store.state.auth && !store.state.auth.attributes['isShared']" style="font-size: 0.8rem;margin: 0.5rem 0.5rem;">{{store.state.auth.name}}</div>
        <div class="uname" v-else style="text-align: right;font-size: 1.4rem;margin: 0.3rem 0.5rem;">
          <div style="font-size: 0.3rem;">Expira em:</div>
          <div style="font-size: 0.5rem">{{store.getters.expiresCountDown}}</div>
        </div>
        <i style="font-size: 1.4rem;margin: 0.3rem 0.5rem;" class="fas fa-user-circle"></i>
      </div>
    </div>
  </div>
  <div id="content">


    <template v-if="store.getters['isDriver']">
      <router-view></router-view>

    </template>
    <template v-else>
  <div id="menu" :class="{isopen: menuShown}" v-if="store.state.auth && !store.state.auth.attributes['isShared']">
      <ul>
        <router-link v-if="store.getters.advancedPermissions(8)" to="/devices" custom v-slot="{ href,  navigate, isActive, isExactActive }">
          <li :class="{active: isActive || isExactActive,'exact-active': isExactActive}">
            <a :href="href" @click="navigate">
              <el-icon>
                <i class="fas fa-location-arrow"></i>
              </el-icon>
              <span class="text" >{{$t('menu.devices')}}</span>
            </a>
          </li>
        </router-link>




        <router-link  v-if="store.getters.advancedPermissions(72)" to="/reports" custom v-slot="{ href,  navigate, isActive, isExactActive }">
          <li :class="{active: isActive,'exact-active': isExactActive}">
            <a :href="href" @click="navigate">
              <el-icon>
                <i class="fas fa-chart-bar"></i>
              </el-icon>
              <span class="text" >{{$t('menu.reports')}}</span>
            </a>
          </li>
        </router-link>


        /* IFTRUE_myFlag */
        <router-link  v-if="store.getters.advancedPermissions(40)" to="/geofence" custom v-slot="{ href,  navigate, isActive, isExactActive }">
          <li :class="{active: isActive,'exact-active': isExactActive}">
            <a :href="href" @click="navigate">
              <el-icon>
                <i class="fas fa-draw-polygon"></i>
              </el-icon>
              <span class="text" >{{$t('menu.geofence')}}</span>
            </a>
          </li>
        </router-link>


        <router-link  v-if="store.getters.advancedPermissions(56)" to="/commands" custom v-slot="{ href,  navigate, isActive, isExactActive }">
          <li :class="{active: isActive,'exact-active': isExactActive}">
            <a :href="href" @click="navigate">
              <el-icon>
                <i class="far fa-keyboard"></i>
              </el-icon>
              <span class="text" >{{$t('menu.commands')}}</span>
            </a>
          </li>
        </router-link>



        <router-link  v-if="store.getters.advancedPermissions(48)" to="/groups" custom v-slot="{ href,  navigate, isActive, isExactActive }">
          <li :class="{active: isActive,'exact-active': isExactActive}">
            <a :href="href" @click="navigate">
              <el-icon>
                <i class="far fa-object-group"></i>
              </el-icon>
              <span class="text" >{{$t('menu.groups')}}</span>
            </a>
          </li>
        </router-link>

        /* FITRUE_myFlag */

        <router-link to="/notifications" custom v-slot="{ href,  navigate, isActive, isExactActive }">
          <li :class="{active: isActive,'exact-active': isExactActive}">
            <a :href="href" @click="navigate">
              <el-icon>
                <i class="fas fa-bell"></i>
              </el-icon>
              <span class="text" >{{$t('menu.notifications')}}</span>
            </a>
          </li>
        </router-link>



        <div class="indicator"></div>


      </ul>


      <div id="version" >
        {{$t('version')}}
        <template v-if="store.state.server.serverInfo.version">
          @ {{store.state.server.serverInfo.version}}
        </template>
      </div>
  </div>
  <div id="open" :class="{minimized: minimized,bottom: ($route.meta.mobileBottom),mobileExpanded: mobileExpand,shown: ($route.meta.shown),editing: store.state.geofences.mapEditing,allowExpand: $route.meta.allowExpand,expanded: ($route.meta.allowExpand && $route.query.expand==='true') }">
    <div style="width: calc(100%); " :style="{display: (store.state.geofences.mapEditing)?'none':'' }">
      <div id="heading">
        <span @click="$route.meta.backBtn?$router.push($route.meta.backBtn):$router.back()"><i class="fas fa-angle-double-left"></i></span>
        {{KT($route.meta.title || 'page')}}
        <span @click="$router.push('/home')"><i class="fas fa-times-circle"></i></span></div>

      <div v-if="($route.meta.mobileBottom)" @click="minimized = !minimized" class="showOnMobile" style="position: absolute;right: 35px;top: 5px;font-size: 18px;"><i class="fas fa-window-minimize"></i></div>
      <div v-if="($route.meta.mobileBottom)" @click="$router.push('/home')" class="showOnMobile" style="position: absolute;right: 5px;top: 5px;font-size: 18px;"><i class="fas fa-times-circle"></i></div>
      <div v-if="($route.meta.mobileBottom)" id="expander" @click="mobileExpand = !mobileExpand">
        <span v-if="!mobileExpand"><i  class="fas fa-angle-double-up"></i></span>
        <span v-else><i  class="fas fa-angle-double-down"></i></span>
      </div>


      <div id="rv"><router-view ></router-view></div>

    </div>
    <div v-if="store.state.geofences.mapEditing">
      <div style="padding: 10px;"><el-button @click="store.dispatch('geofences/disableEditing')" type="primary">Concluir</el-button></div>
      <div style="padding: 10px;"><el-button type="warning" plain>{{KT('reset')}}</el-button></div>
      <div style="padding: 10px;"><el-button type="danger" plain>{{KT('cancel')}}</el-button></div>
    </div>

    <div v-if="$route.meta.allowExpand" class="expandBtn" @click="$router.push({query: {expand: $route.query.expand==='true'?'false':'true'}})"><i class="fas fa-angle-double-right"></i></div>
  </div>

  <div id="main"
       :class="{menuShown: menuShown,editing: store.state.geofences.mapEditing,minimized: minimized,bottom: ($route.meta.mobileBottom),shown: ($route.meta.shown)}" :style="{width: (store.state.auth.attributes['isShared'])?'100vw':''}">

    /* IFTRUE_myFlag */
    <street-view v-if="store.state.devices.streetview" ></street-view>

    /* FITRUE_myFlag */
    <kore-map></kore-map>


  </div>
    </template>
  </div>
  </div>
  <div v-else>
    <router-view></router-view>
  </div>
</template>

<script setup>

import {defineAsyncComponent,ref,onMounted,provide} from 'vue'
import { useStore } from 'vuex'



import 'element-plus/es/components/button/style/css'
import 'element-plus/es/components/icon/style/css'
import 'element-plus/es/components/tooltip/style/css'
import 'element-plus/es/components/progress/style/css'
import {ElProgress} from "element-plus/es/components/progress";

import {ElButton} from "element-plus/es/components/button";
import {ElIcon} from "element-plus/es/components/icon";
import {ElTooltip} from "element-plus/es/components/tooltip";

import router from "./routes";

/* IFTRUE_myFlag */
const StreetView = defineAsyncComponent(()=> import("./tarkan/components/street-view") );
/* FITRUE_myFlag */

const KoreMap = defineAsyncComponent(()=> import("./tarkan/components/kore-map") );

import KT from './tarkan/func/kt'
//import scRecorder from './tarkan/func/recorder'
import actAnchor from './tarkan/func/actAnchor'


//window.SCREEN_RECORDER = new scRecorder();


import "leaflet/dist/leaflet.css"

const ContextMenu = defineAsyncComponent(()=> import("./tarkan/components/context-menu") );


const EditUser = defineAsyncComponent(()=> import("./tarkan/components/views/edit-user") );
/* IFTRUE_myFlag */
const EditShare  = defineAsyncComponent(()=> import("./tarkan/components/views/edit-share") );
const EditShares  = defineAsyncComponent(()=> import("./tarkan/components/views/edit-shares") );
const EditGroup  = defineAsyncComponent(()=> import("./tarkan/components/views/edit-group") );
const EditUsers  = defineAsyncComponent(()=> import("./tarkan/components/views/edit-users") );
const EditServer = defineAsyncComponent(()=> import("./tarkan/components/views/edit-server") );

const EditDrivers  = defineAsyncComponent(()=> import("./tarkan/components/views/edit-drivers") );
const LinkObjects  = defineAsyncComponent(()=> import("./tarkan/components/views/link-objects") );
const LogObjects  = defineAsyncComponent(()=> import("./tarkan/components/views/log-objects") );
const EditCalendars  = defineAsyncComponent(()=> import("./tarkan/components/views/edit-calendars") );
const EditMaintenances  = defineAsyncComponent(()=> import("./tarkan/components/views/edit-maintenances") );
const EditTheme  = defineAsyncComponent(()=> import("./tarkan/components/views/edit-theme") );
/* FITRUE_myFlag */


const EditNotifications  = defineAsyncComponent(()=> import("./tarkan/components/views/edit-notifications") );
const EditDevice  = defineAsyncComponent(()=> import("./tarkan/components/views/edit-device") );
const QrDevice  = defineAsyncComponent(()=> import("./tarkan/components/views/qr-device") );

const Showtip  = defineAsyncComponent(()=> import("./tarkan/components/showtip") );


const ShowGraphic  = defineAsyncComponent(()=> import("./tarkan/components/views/show-graphic") );

const PushNotificationBtn  = defineAsyncComponent(()=> import("./tarkan/components/push-notification-btn") );


/* declare html object component refs */
const contextMenuRef = ref(null);
const radialMenuRef = ref(null);
const editDeviceRef = ref(null);
const qrDeviceRef = ref(null);
const editUserRef = ref(null);
const editUsersRef = ref(null);
const editShareRef = ref(null);
const editSharesRef = ref(null);
const editGroupRef = ref(null);
const editNotificationsRef = ref(null);
const editServerRef = ref(null);
const editDriversRef = ref(null);
const linkObjectsRef = ref(null);
const logObjectsRef = ref(null);
const editCalendarsRef = ref(null);
const editMaintenancesRef = ref(null);
const editThemeRef = ref(null);
const showGraphicsRef = ref(null);

const mobileExpand = ref(false);
const menuShown = ref(false);
const minimized = ref(false);
/* declare variable object refs */



const store = useStore();



function generateRandomToken(){
  const letters = "TKZYxLSOPERT123965U".split("")
  let tmp = [];
  let i = 0;
  while(i<20){

    const rand = Math.round(Math.random()*(letters.length-1));

    tmp.push(letters[rand]);

    i++;
  }
  return tmp.join("");
}

router.afterEach(()=>{
  minimized.value = false;
})

onMounted(()=> {




  window.localStorage.setItem('query','');

  if (!window.localStorage.getItem('TKSESSIONTOKEN')) {
    const token = generateRandomToken();
    window.localStorage.setItem('TKSESSIONTOKEN', token);
  }


  //store.dispatch("server/loadConfig");



})







const userMenu = (e)=>{
  let tmp = [];

  if(!store.state.auth.attributes['isShared']) {

    tmp.push({
      text: KT('usermenu.account'), cb: () => {
        editUserRef.value.editUser();
      }
    });

    /*
    if (store.state.auth.administrator && store.state.server.isPlus) {
      tmp.push({
        text: KT('usermenu.reseller'), cb: () => {
          editThemeRef.value.showTheme();
        }
      });
    }*/


    if (store.state.auth.administrator && store.state.server.isPlus) {
      tmp.push({
        text: KT('usermenu.logs'), cb: () => {
          logObjectsRef.value.showLogs('all');
        }
      });
    }

    if (store.state.auth.administrator && store.state.server.isPlus) {
      tmp.push({
        text: KT('usermenu.theme'), cb: () => {
          editThemeRef.value.showTheme();
        }
      });
    }

    /* IFTRUE_myFlag */
    if (store.getters.advancedPermissions(16)
        /*store.state.auth.administrator || store.state.auth.userLimit > 0 || store.state.auth.userLimit === -1*/
    ) {
      tmp.push({
        text: KT('usermenu.users'), cb: () => {
          editUsersRef.value.showUsers();
        }
      });
    }
    /* FITRUE_myFlag */


    /* IFTRUE_myFlag */
    if (store.getters.advancedPermissions(64)) {
      tmp.push({
        text: KT('usermenu.computedAttributes'), cb: () => {
          router.push("/computed")
        }
      });
    }
    /* FITRUE_myFlag */


    /* IFTRUE_myFlag */
    if (store.getters.isAdmin) {
      tmp.push({
        text: KT('usermenu.server'), cb: () => {
          editServerRef.value.showServer();
        }
      });
    }
    /* FITRUE_myFlag */


    if (store.getters.advancedPermissions(32)) {
      tmp.push({
        text: KT('usermenu.notifications'), cb: () => {
          editNotificationsRef.value.showNotifications();
        }
      });
    }


    /* IFTRUE_myFlag */
    if (store.getters.advancedPermissions(80)) {
      tmp.push({
        text: KT('usermenu.drivers'), cb: () => {
          editDriversRef.value.showDrivers();
        }
      });
    }

    /* FITRUE_myFlag */

    /* IFTRUE_myFlag */
    if (store.getters.advancedPermissions(88)) {
      tmp.push({
        text: KT('usermenu.calendars'), cb: () => {
          editCalendarsRef.value.showCalendars();
        }
      });
    }

    /* FITRUE_myFlag */

    /* IFTRUE_myFlag */
    if (store.getters.advancedPermissions(96)) {
      tmp.push({
        text: KT('usermenu.maintenance'), cb: () => {
          editMaintenancesRef.value.showMaintenances();
        }
      });
    }
    /* FITRUE_myFlag */

  }


  tmp.push({
    text: KT('usermenu.logout'), cb: () => {
      store.dispatch("logout").then(()=>{
        router.push('/login');
      })

    }
  });

  contextMenuRef.value.openMenu({evt: e, menus: tmp})
}


const showRouteMarker = ref(false);

const setShowMarker = (b)=>{
  showRouteMarker.value = b;
}




/* provide references for global use */

provide('setRouteMarker',setShowMarker)


provide("act-anchor",actAnchor);


provide('contextMenu',contextMenuRef);
provide('radialMenu',radialMenuRef);
provide('edit-device',editDeviceRef);
provide('qr-device',qrDeviceRef);
provide('edit-user',editUserRef);
provide('edit-users',editUsersRef);
provide('edit-share',editShareRef);
provide('edit-shares',editSharesRef);
provide('edit-group',editGroupRef);
provide('link-objects',linkObjectsRef);
provide('log-objects',logObjectsRef);

provide('show-graphics',showGraphicsRef);

</script>

<style>

.showOnMobile{
  display: none;
}

.editing .leaflet-container {
  cursor:crosshair !important;
}

body.el-popup-parent--hidden{
  padding-right: 0px !important;
}



body{
  overflow: hidden;
  position: fixed;
  left: 0px;
  top: 0px;
  bottom: 0px;
  right: 0px;
}


*{
  margin: 0px;
  padding: 0px;
}


#app {
  font-family: trebuchet ms;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: left;
  color: #2c3e50;
  display: flex;
  flex-direction: column;
  overflow: hidden;

  position: fixed;
  left: 0px;
  top: 0px;
  bottom: 0px;
  right: 0px;
}

#head{
  height: 2rem;
  overflow: hidden;
  border-bottom: var(--el-text-color-primary) 1px solid;
  background: var(--el-bg-color);
  display: flex;
  align-content: space-between;
  justify-content: space-between;

}

#head #user{
  display: flex;
}

#logo{
  padding: 0.5rem;
}

#content{
  display: flex;
  height: calc(var(--vh,100vh) - 2rem);
}

#menu{
  width: 4.2rem;
  height: calc(var(--vh,100vh) - 2rem);
  background: var(--el-color-primary);
  position: relative;
  transition: all 0.3s;
}

#version{
  position: absolute;
  bottom: 0.5rem;
  left: 0.25rem;
  background: var(--el-color-primary-light-3);
  color: var(--el-text-color-primary);
  padding: 0.3rem;
  font-size: 0.55rem;
  border-radius: 0.2rem;
  width: 3.8rem;
  box-sizing: border-box;
  text-align: center;
}

#open{
  height: calc(100vh - 2rem);
  background: var(--el-bg-color);
  color: var(--el-text-color-primary);
  display: flex;
  align-content: center;
  justify-content: space-between;
  transition: 0.2s;
  opacity: 0;
  width: 0px;
  overflow: hidden;
}



#open.allowExpand .expandBtn{
  position: absolute;
  left: 555px;
  top: 50%;
  z-index: 9999999999;
  border: white 1px solid;
  background: #05a7e3;
  padding: 5px;
  padding-top: 25px;
  padding-bottom: 25px;
  color: white;
  transform: translate(0,-50%);
  border-radius: 0px 5px 5px 0px;
}

#open.shown{
  opacity: 1;
  width: 700px;
}

#open.allowExpand.expanded{
  width: 1400px !important;
}


#open.allowExpand.expanded .expandBtn{
    left: 805px;
}

#open.allowExpand.expanded .expandBtn i{
  transform: rotate(180deg)
}

#open.shown.editing{
  width: 130px !important;
}

#open.shown.editing div{

  display: flex;
  flex-direction: column-reverse;
  align-content: space-between;
  justify-content: space-between;
}

#open #rv{
  overflow-y: auto;
  height: calc(100vh - 130px);
  padding: 7px;
}

#open.minimized{
  height: 35px !important;
}

::-webkit-scrollbar{
  width: 10px;
  height: 3px;
  background: #f5f5f5;
}

::-webkit-scrollbar-thumb{
  width: 10px;
  height: 5px;
  background: #cccccc;
}

::-webkit-scrollbar-thumb:hover {
  background: var(--el-color-info);
}

#menu ul{
  list-style: none;
  margin-top: 2.5rem;
}

#menu ul li{
  position: relative;
  width: 4.2rem;
  height: 4.2rem;
  z-index: 5;
}

#menu ul li a{
  color: var(--el-text-color-primary);
  position: relative;
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: row;
  width: 100%;
  height: 100%;
}


#menu ul li a .el-icon{
  position: relative;
  display: block;
  line-height: 1.7rem;
  font-size: 1.7rem;
  text-align: center;
  transition: 0.5s;
  color: var(--el-bg-color);
  width: 1.7rem;
  height: 1.7rem;
  z-index: 10;
  transform: translateX(5px);
}

#menu ul li.active a .el-icon{
  transform: translateX(2.30rem);
}

#menu ul li a .text{
  position: absolute;
  display: block;
  color: var(--el-bg-color);
  font-weight: 400;
  font-size: 0.65rem;
  letter-spacing: 0.05rem;
  transition: 0.5s;
  opacity: 1;
  left: 0px;
  width: 4.5rem;
  text-align: center;
  transform: translateX(-25px) rotate(-90deg) ;
}


#menu ul li.active a .text{
  opacity: 1;
  left: 0px;
  font-size: 0.75rem;
  transform: translateX(-12px) rotate(-90deg) ;
}

#menu ul li:hover:not(.active) a .text{
  transform: translateX(-20px) rotate(-90deg) ;
}


#menu ul li:hover:not(.active) a .el-icon{
  transform: translateX(10px);
}

#main{
  width: calc(var(--vw,100vw) - 4.2rem);
  height: calc(var(--vh,100vh) - 2rem);
  transition: all 0.3s;
}

#main.minimized{
  height: calc(100vh - 50px) !important;
}

.indicator{
  position: absolute;
  width: 4rem;
  height: 4rem;
  background: var(--el-text-color-primary);
  box-sizing: border-box;
  left: 2.35rem;
  top: 2.7rem;
  border-radius: 50%;
  border: 6px solid var(--el-bg-color);
  transition: 0.5s;
  opacity: 0;
  z-index: 2;
  box-shadow: 3px 2px 10px rgba(0,0,0,0.20)
}

.indicator::before{
  content: '';
  position: absolute;
  box-sizing: border-box;
  background: transparent;
  left: calc(50% - 23px);
  top: -21px;
  width: 20px;
  height: 20px;
  border-bottom-right-radius: 20px;
  box-shadow: 10px 0px 0 0 var(--el-bg-color)
}


.indicator::after{
  content: '';
  position: absolute;
  box-sizing: border-box;
  background: transparent;
  left: calc(50% - 23px);
  bottom: -21px;
  width: 20px;
  height: 20px;
  border-top-right-radius: 20px;
  box-shadow: 10px 0px 0 0 var(--el-bg-color)
}


#menu ul li:nth-child(1).active ~ .indicator{
  transform: translateY(calc(4rem * 0));
  opacity: 1;
}

#menu ul li:nth-child(2).active ~ .indicator{
  transform: translateY(calc(4.2rem * 1));
  opacity: 1;
}

#menu ul li:nth-child(3).active ~ .indicator{
  transform: translateY(calc(4.2rem * 2));
  opacity: 1;
}

#menu ul li:nth-child(4).active ~ .indicator{
  transform: translateY(calc(4.2rem * 3));
  opacity: 1;
}


#menu ul li:nth-child(5).active ~ .indicator{
  transform: translateY(calc(4.2rem * 4));
  opacity: 1;
}


#menu ul li:nth-child(6).active ~ .indicator{
  transform: translateY(calc(4.2rem * 5));
  opacity: 1;
}


#menu ul li:nth-child(7).active ~ .indicator{
  transform: translateY(calc(4.2rem * 6));
  opacity: 1;
}


#menu ul li:nth-child(8).active ~ .indicator{
  transform: translateY(calc(4.2rem * 7));
  opacity: 1;
}


#heading{
  text-align: center;
  font-weight: bold;
  background: var(--el-color-primary);
  border-radius: 20px;
  padding: 10px;
  color: var(--el-color-white);
  position: relative;
  z-index: 0;
  margin: 10px;
}


#heading span:first-child{
  position: absolute;
  left: 0px;
  top: 0px;
  padding: 6px;
  font-size: 25px;
  cursor: pointer;
}

#heading span:last-child{
  position: absolute;
  right: 0px;
  top: 0px;
  padding: 6px;
  font-size: 25px;
  cursor: pointer;
}

body.rtl #app div #content{
  flex-direction: row-reverse !important;
}

body.rtl #app div #content #menu ul .indicator{
  left: -40px;
}
body.rtl #app div #content #menu ul .indicator:before{
  left: calc(50% + 7px);
  top: -18px;
  border-bottom-right-radius: 0px;
  border-bottom-left-radius: 20px;
  box-shadow: -10px 0px 0 0 var(--el-bg-color)
}

body.rtl #app div #content #menu ul .indicator:after{
  left: calc(50% + 7px);
  bottom: -18px;
  border-top-right-radius: 0px;
  border-top-left-radius: 20px;
  box-shadow: -10px 0px 0 0 var(--el-bg-color)
}

body.rtl #app div #content #menu ul li a .text{
  left: 45px;
}

body.rtl #app div #content #menu ul li.active a .text{
  left: 20px;
}

body.rtl #app div #content #menu ul li a .el-icon{
  transform: translateX(-7px);
}

body.rtl #app div #content #menu ul li.active a .el-icon{
    transform: translateX(-45px);
}


.notification-soft-red{
  --el-color-white: #ffdddd!important;
  --el-notification-icon-color:#181818!important;
  --el-notification-content-color: #181818!important;
}

.notification-soft-red .el-icon{
  color: #181818 !important;
}

.notification-red{
  --el-color-white: #f44336!important;
  --el-notification-icon-color: white!important;
  --el-notification-title-color:white!important;
}

.notification-red .el-icon{
  color: white !important;
}


.notification-soft-yellow{
  --el-color-white: #ffffcc!important;
  --el-notification-icon-color: #181818!important;
  --el-notification-title-color:#181818!important;
}


.notification-soft-yellow .el-icon{
  color: #181818 !important;
}

.notification-yellow{
  --el-color-white: #ffeb3b!important;
  --el-notification-icon-color: #181818!important;
  --el-notification-title-color:#181818!important;
}

.notification-yellow .el-icon{
  color: #181818 !important;
}


.notification-soft-green{
  --el-color-white: #ddffdd!important;
  --el-notification-icon-color: #181818!important;
  --el-notification-title-color:#181818!important;
}

.notification-soft-green .el-icon{
  color: #181818 !important;
}

.notification-green{
  --el-color-white: #4CAF50!important;
  --el-notification-icon-color: white!important;
  --el-notification-title-color:white!important;
}

.notification-green .el-icon{
  color: white !important;
}



.notification-soft-info{
  --el-color-white: #ddffff!important;
  --el-notification-icon-color: #181818!important;
  --el-notification-title-color:#181818!important;
}


.notification-soft-info .el-icon{
  color: #181818 !important;
}

.notification-info{
  --el-color-white: #2196F3!important;
  --el-notification-icon-color: white!important;
  --el-notification-title-color:white!important;
}

.notification-info .el-icon{
  color: white !important;
}

.el-notification__content{
  background: white !important;
  color: black !important;
  padding: 5px;
  border-radius: 5px;
  min-width: 255px;
}

.customFilter{
  margin-left: 1px;
  padding: 10px;
  background: white;
  text-align: center;
  margin-bottom: 4px;
  border-radius: 4px;
  color: white;
  box-shadow: 0px 0px 3px rgba(45, 45, 45, 0.5);
  cursor: pointer;
}

.all{
  background: var(--el-color-info);
}

.online{
  background: var(--el-color-success);
}

.offline{
  background: var(--el-color-danger);
}

.unknown{
  background: var(--el-color-warning);
}

.motion{
  background: var(--el-color-primary);
}


.customFilter.active{
  border: white 1px solid;
}

#btnmenu{
  display: none;
  padding: 0.5rem;
  font-size: 1rem;
  cursor: pointer;
}

#expander{
  display: none;
  text-align: center;
  padding: 5px;
  background: #f3f3f3;
}

@media (orientation: portrait) {
  #menu{
    width: 0px;
    overflow: hidden;
  }

  #menu.isopen{
    width: 73px !important;
  }

  #main{
    width: var(--vw,100vw);
    height: calc(var(--vh,100vh) - 2rem);
  }

  #main.menuShown{
    width: calc(var(--vw,100vw) - 2rem);
  }



  .uname{
    display: none !important;
  }

  #logo{

  }

  #btnmenu{
    display: block;
  }

  #open.shown{
    position: absolute;
    overflow: hidden;
    left: 0px;
    top: 0px;
    width: 100%;
    height: 100%;
    z-index: 1005;
  }


  #open.bottom{
    position: fixed;
    top: auto !important;
    bottom: 0px !important;
    height: 44vh;
    box-shadow: 0px -3px 15px rgba(0,0,0,0.15);
    border-radius: 15px 15px 0px 0px !important;
    overflow: hidden;
  }

  #open.bottom.mobileExpanded{
    height: calc(100vh - 100px) !important;
  }

  #open.bottom #heading{
    display: none !important;
  }

  #open.bottom .kr-spacer{
    display: none !Important;
  }



  #open.bottom #expander{
    display: block !important;
  }

  #main.bottom{
    height: calc(55vh - 20px);
  }


  #pano{
    position: fixed !important;
    left: 0px !important;
    bottom: 0px;
    width: 100% !important;
    height: calc(44vh - 85px) !important;
    z-index: 1005 !important;
  }

  .el-dialog{
    --el-dialog-width: 100vw !important;
  }

  .el-dialog__footer{
    overflow: auto;
    margin-right: 10px;
  }

  .indicator{
    display: none;
  }
  #menu ul li.active a .text{
    transform: translateX(-15px) rotate(-90deg)
  }

  #menu ul li.active a .el-icon{
    transform: translateX(10px)
  }


  .showOnMobile{
    display: block !Important;
  }
}

.el-form-item{
  margin-bottom: 5px !important;
}

.el-form-item__label{
  line-height: 30px !important;
}



</style>
