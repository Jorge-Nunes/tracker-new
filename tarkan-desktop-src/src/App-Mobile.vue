<template>
  <context-menu ref="contextMenuRef"></context-menu>

  <edit-calendars ref="editCalendarsRef"></edit-calendars>
  <link-objects ref="linkObjectsRef"></link-objects>

  <edit-user ref="editUserRef"></edit-user>
  <edit-notifications ref="editNotificationsRef"></edit-notifications>
  <edit-group ref="editGroupRef"></edit-group>
  <edit-users ref="editUsersRef"></edit-users>
  <edit-device ref="editDeviceRef"></edit-device>
  <edit-server ref="editServerRef"></edit-server>
  <edit-drivers ref="editDriversRef"></edit-drivers>
  <edit-maintenances ref="editMaintenancesRef"></edit-maintenances>



  <div v-if="store.state.auth">

  <div id="head">
    <div style="cursor: pointer;" @click="deviceMenu = !deviceMenu">
      <i style="font-size: 20px;margin: 12px 12px 0px 12px;" class="fas fa-bars"></i>
    </div>
    <div id="logo">
      <img src="img/logo.png" height="15" >
    </div>
    <div style="display: flex;">
      <el-tooltip :content="(store.state.events.mute)?'Ouvir Notificações':'Silenciar Notificações'">
        <div id="mute" @click="store.dispatch('events/toggleMute')" style="cursor: pointer;font-size: 17px;margin: 10.5px 12px 12px 0px;">
          <i v-if="store.state.events.mute" class="fas fa-volume-mute" style="color: silver;"></i>
          <i v-else class="fas fa-volume-up"></i>
        </div>
      </el-tooltip>


      <div id="user" @click="userMenu($event)" style="cursor: pointer;">
        <i style="font-size: 20px;margin: 12px 12px 12px 0px;" class="fas fa-user-circle"></i>
      </div>
    </div>
  </div>
  <div id="content">

  <div id="menu" :class="{show: deviceMenu}">
      <ul>
        <router-link to="/devices" custom v-slot="{ href,  navigate, isActive, isExactActive }">
          <li :class="{active: isActive || isExactActive,'exact-active': isExactActive}">
            <a :href="href" @click="navigate">
              <el-icon>
                <i class="fas fa-car"></i>
              </el-icon>
              <span class="text">{{KT('menu.devices')}}</span>
            </a>
          </li>
        </router-link>


        <router-link to="/history" custom v-slot="{ href,  navigate, isActive, isExactActive }">
          <li :class="{active: isActive,'exact-active': isExactActive}">
            <a :href="href" @click="navigate">
              <el-icon>
                <i class="fas fa-route"></i>
              </el-icon>
              <span class="text">{{KT('menu.history')}}</span>
            </a>
          </li>
        </router-link>


        <router-link to="/geofence" custom v-slot="{ href,  navigate, isActive, isExactActive }">
          <li :class="{active: isActive,'exact-active': isExactActive}">
            <a :href="href" @click="navigate">
              <el-icon>
                <i class="fas fa-draw-polygon"></i>
              </el-icon>
              <span class="text">{{KT('menu.geofence')}}</span>
            </a>
          </li>
        </router-link>


        <router-link to="/commands" custom v-slot="{ href,  navigate, isActive, isExactActive }">
          <li :class="{active: isActive,'exact-active': isExactActive}">
            <a :href="href" @click="navigate()">
              <el-icon>
                <i class="far fa-keyboard"></i>
              </el-icon>
              <span class="text">{{KT('menu.commands')}}</span>
            </a>
          </li>
        </router-link>



        <router-link to="/groups" custom v-slot="{ href,  navigate, isActive, isExactActive }">
          <li :class="{active: isActive,'exact-active': isExactActive}">
            <a :href="href" @click="navigate()">
              <el-icon>
                <i class="far fa-object-group"></i>
              </el-icon>
              <span class="text">{{KT('menu.groups')}}</span>
            </a>
          </li>
        </router-link>

        <router-link to="/notifications" custom v-slot="{ href,  navigate, isActive, isExactActive }">
          <li :class="{active: isActive,'exact-active': isExactActive}">
            <a :href="href" @click="navigate()">
              <el-icon>
                <i class="fas fa-bell"></i>
              </el-icon>
              <span class="text">{{KT('menu.notifications')}}</span>
            </a>
          </li>
        </router-link>



        <div class="indicator"></div>


      </ul>


      <div id="version">0.1.33-dev</div>
  </div>
  <div id="open" :class="{bottom: ($route.meta.mobileBottom),mobileExpanded: mobileExpand,shown: ($route.meta.shown),editing: store.state.geofences.mapEditing,allowExpand: $route.meta.allowExpand,expanded: ($route.meta.allowExpand && $route.query.expand==='true') }">
    <div style="width: calc(100%); " :style="{display: (store.state.geofences.mapEditing)?'none':'' }">
      <div id="heading">
        <span @click="$route.meta.backBtn?$router.push($route.meta.backBtn):$router.back()"><i class="fas fa-angle-double-left"></i></span>
        {{$route.meta.title || 'Route'}}
        <span @click="$router.push('/home')"><i class="fas fa-times-circle"></i></span>
      </div>


        <div v-if="($route.meta.mobileBottom)" @click="$router.push('/home')" style="position: absolute;right: 5px;top: 5px;font-size: 18px;"><i class="fas fa-times-circle"></i></div>
        <div v-if="($route.meta.mobileBottom)" id="expander" @click="mobileExpand = !mobileExpand">
          <i v-if="!mobileExpand" class="fas fa-angle-double-up"></i>
          <i v-else class="fas fa-angle-double-down"></i>
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
       :class="{bottom: ($route.meta.mobileBottom),editing: store.state.geofences.mapEditing}">

    <street-view v-if="store.state.devices.streetview" ></street-view>

    <LMap

        :options="{preferCanvas: true}"

        :use-global-leaflet="true"
        ref="map"
        :zoom="zoomForce"
        :min-zoom="4"
        :max-zoom="19"
        :zoom-animation="true"
        :fade-animation="true"
        :marker-zoom-animation="true"

        @click="mapClick"
        @mousemove="mapMove"


        :center="center" @update:zoom="zoomUpdated($event)"  style="width: 100%;height: 100%;">

      <l-control position="topright" >
          <el-dropdown size="large" style="margin-right: 5px;" max-height="50%" :hide-timeout="300" :hide-on-click="false"  trigger="click">
            <el-button><i class="fas fa-filter"></i></el-button>
            <template #dropdown>
              <el-dropdown-menu>
                <div style="overflow: auto;max-height: 50vh;">
                <el-dropdown-item v-for="(t,tk) in availableTypes" :key="tk" @click="store.dispatch('devices/toggleHiddenFilter',t.key)">
                  <el-switch :key="'chk'+t.key" size="small" :model-value="store.getters['devices/isHiddenFilter'](t.key)"></el-switch> <span style="margin-left: 10px;">{{t.name}}</span>
                </el-dropdown-item>
                </div>
              </el-dropdown-menu>
            </template>
          </el-dropdown>

          <el-dropdown size="large"  trigger="click">
            <el-button><i class="fas fa-layer-group"></i></el-button>
            <template #dropdown>
              <el-dropdown-menu>
                <el-dropdown-item v-for="(m,mk) in availableMaps" :key="'map-'+mk" @click="selectedMap=m.id">
                  <el-radio v-model="selectedMap" :label="m.id"> {{m.name}}</el-radio>
                </el-dropdown-item>
              </el-dropdown-menu>
            </template>
          </el-dropdown>
      </l-control>




      <l-control position="topleft" >
        <div>
          <el-tooltip placement="right-start" content="Todos"><div class="customFilter all" @click="store.dispatch('devices/setStatusFilter','all')"      :class="{active: store.state.devices.applyFilters.statusFilter==='all'}">{{deviceCount.all}}</div></el-tooltip>
          <el-tooltip placement="right-start" content="Online"><div class="customFilter online" @click="store.dispatch('devices/setStatusFilter','online')"   :class="{active: store.state.devices.applyFilters.statusFilter==='online'}">{{deviceCount.online}}</div></el-tooltip>
          <el-tooltip placement="right-start" content="Offline"><div class="customFilter offline" @click="store.dispatch('devices/setStatusFilter','offline')"  :class="{active: store.state.devices.applyFilters.statusFilter==='offline'}">{{deviceCount.offline}}</div></el-tooltip>
          <el-tooltip placement="right-start" content="Desconhecido"><div class="customFilter unknown" @click="store.dispatch('devices/setStatusFilter','unknown')"  :class="{active: store.state.devices.applyFilters.statusFilter==='unknown'}">{{deviceCount.unknown}}</div></el-tooltip>
          <el-tooltip placement="right-start" content="Em movimento"><div class="customFilter motion" @click="store.dispatch('devices/toggleMotionFilter')"  :class="{active: store.state.devices.applyFilters.motionFilter}">{{deviceCount.motion}}</div></el-tooltip>
        </div>
      </l-control>


      <l-tile-layer v-for="(m,mk) in availableMaps"
                    :key="'tsmap'+mk"
                    :name="m.name"
                    layer-type="base"
                    :visible="m.id === selectedMap" :subdomains="m.subdomains"
                    :url="m.url"
      ></l-tile-layer>


      <l-layer-group
          layer-type="overlay"
          name="Carros"
          ref="carLayer"
      >

        <l-polyline v-if="store.state.devices.trail!==false"
                    :lat-lngs="store.getters['devices/getTrails']"
                    :color="'#05a7e3'"></l-polyline>

        <kore-canva-marker :map="map" :zoom="zoom" @click="markerClick" @contextmenu="markerContext"></kore-canva-marker>



      </l-layer-group>




      <l-layer-group
          v-if="routePoints.length>0"
          layer-type="overlay"
          name="Rota"
      >
        <l-polyline :lat-lngs="cptPoints" :color="'#05a7e3'"></l-polyline>

        <template v-for="(p,k) in routePoints" :key="p[3]">
          <kore-marker v-if="k===0 || k===routePoints.length-1" :name="(k===0)?'start':(k===routePoints.length-1)?'end':'point'" :position="p" :type="(k===0)?'start':(k===routePoints.length-1)?'end':'point'"></kore-marker>
        </template>
      </l-layer-group>

      <l-layer-group

          layer-type="overlay"
          name="Geocercas"
      >

        <kore-fence v-for="(f) in store.state.geofences.fenceList" :key="f.id" :geofence="f"></kore-fence>


        <template v-if="store.state.geofences.mapEditing!==0">
          <l-polyline :key="'polyline-'+store.getters['geofences/getLatLngs'].length" v-if="store.state.geofences.mapPointEditingType==='LINESTRING' && store.state.geofences.mapPointEditingParams.length>0"
                      :lat-lngs="store.getters['geofences/getLatLngs']"
                      :color="'#05a7e3'"></l-polyline>
          <l-polygon :key="'polyline-'+store.getters['geofences/getLatLngs'].length" :no-clip="true" v-else-if="store.state.geofences.mapPointEditingType==='POLYGON' && store.state.geofences.mapPointEditingParams.length>0"
                     :lat-lngs="store.getters['geofences/getLatLngs']"
                     :fill-opacity="0.15"
                     :fill="true"
                     :fill-color="'#05a7e3'"
                     :color="'#05a7e3'"></l-polygon>
          <l-circle v-else-if="store.state.geofences.mapPointEditingType==='CIRCLE' && store.state.geofences.mapPointEditingParams.length===3"
                    :lat-lng="[store.state.geofences.mapPointEditingParams[0],store.state.geofences.mapPointEditingParams[1]]"
                    :radius="parseFloat(store.state.geofences.mapPointEditingParams[2])"
                    :fill="true" :fill-opacity="0.15"
                    :fill-color="'#05a7e3'"
                    :color="'#05a7e3'"></l-circle>

        </template>

      </l-layer-group>


    </LMap>


  </div>
  </div>
  </div>
  <div v-else>
    <router-view></router-view>
  </div>
</template>

<script setup>

import {ref,onMounted,provide,computed} from 'vue'
import { useStore } from 'vuex'


import router from "./routes";
import StreetView from "./tarkan/components/street-view"

import KT from './tarkan/func/kt'


import "leaflet/dist/leaflet.css"
import { LMap,LTileLayer,LControl,LLayerGroup,LPolyline,LCircle,LPolygon } from "@vue-leaflet/vue-leaflet";
import KoreMarker from "./tarkan/components/kore-marker";
import KoreFence from "./tarkan/components/kore-fence";
import KoreCanvaMarker from "./tarkan/test/CanvaMarker";
import ContextMenu from "./tarkan/components/context-menu";
import EditDevice from "./tarkan/components/views/edit-device";
import EditUser from "./tarkan/components/views/edit-user";
import EditGroup from "./tarkan/components/views/edit-group";
import {ElLoading,ElMessageBox, ElMessage, ElNotification} from "element-plus";
import EditUsers from "./tarkan/components/views/edit-users";
import EditNotifications from "./tarkan/components/views/edit-notifications";
import EditServer from "./tarkan/components/views/edit-server";
import EditDrivers from "./tarkan/components/views/edit-drivers";
import LinkObjects from "./tarkan/components/views/link-objects";
import EditCalendars from "./tarkan/components/views/edit-calendars"
import EditMaintenances from "./tarkan/components/views/edit-maintenances";



/* declare html object component refs */
const contextMenuRef = ref(null);
const editDeviceRef = ref(null);
const editUserRef = ref(null);
const editUsersRef = ref(null);
const editGroupRef = ref(null);
const editNotificationsRef = ref(null);
const editServerRef = ref(null);
const editDriversRef = ref(null);
const linkObjectsRef = ref(null);
const editCalendarsRef = ref(null);
const editMaintenancesRef = ref(null);

/* declare variable object refs */
const center = ref([-29.942484, -50.990526]);
const zoom = ref(10);
const zoomForce = ref(3);
const map = ref(null);
const routePoints = ref([]);

const selectedMap = ref(1);

const availableMaps = ref([
  {id: 1, name: 'OpenStreetMap',url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'},
  {id: 2, name: 'Google Maps Sat',subdomains: ['mt0','mt1','mt2','mt3'],url: 'https://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}&hl=pt-BR'},
  {id: 3, name: 'Google Maps Trafego',subdomains: ['mt0','mt1','mt2','mt3'],url: 'https://{s}.google.com/vt/lyrs=m@221097413,traffic&x={x}&y={y}&z={z}&hl=pt-BR'},
  {id: 4, name: 'Google Maps',subdomains: ['mt0','mt1','mt2','mt3'],url: 'https://{s}.google.com/vt/lyrs=m@221097413&x={x}&y={y}&z={z}&hl=pt-BR'},
]);

const availableTypes = ref([
  {key: 'arrow',name: 'Seta'},
  {key: 'person',name: 'Pessoas'},
  {key: 'animal',name: 'Animais'},
  {key: 'bicycle',name: 'Bicicletas'},
  {key: 'motorcycle',name: 'Motos'},
  {key: 'scooter',name: 'Scooters'},
  {key: 'car',name: 'Carros'},
  {key: 'pickup',name: 'Pick-Up'},
  {key: 'van',name: 'Van'},
  {key: 'truck',name: 'Caminhão'},
  {key: 'truck1',name: 'Caminhão Cavalo Mecânico'},
  {key: 'truck2',name: 'Caminhão Carreta'},

  {key: 'tractor',name: 'Tratores'},
  {key: 'boat',name: 'Barcos'},
  {key: 'ship',name: 'Lanchas'},
  {key: 'bus',name: 'Õnibus'},
  {key: 'train',name: 'Trêm'},
  {key: 'trolleybus',name: 'Ônibus Elétrico'},
  {key: 'tram',name: 'Trêm Elétrico'},
  {key: 'crane',name: 'Guindastes'},
  {key: 'plane',name: 'Aviões'},
  {key: 'helicopter',name: 'Helicópteros'},
  {key: 'offroad',name: 'Off-Road'}
]);

const carLayer = ref(null);
const mobileExpand = ref(false);

const deviceMenu = ref(false);

const deviceCount = computed(()=>{

  let tmp ={};

  const all = store.state.devices.deviceList;

  tmp.all = all.length;
  tmp.online = all.filter((f)=> f.status==='online').length;
  tmp.offline = all.filter((f)=> f.status==='offline').length;
  tmp.unknown =all.filter((f)=> f.status==='unknown').length;

  tmp.motion = all.filter((f)=>{
    const pos = store.getters['devices/getPosition'](f.id);
    if(pos){
      if(pos.attributes.motion){
        return true;
      }else{
        return false;
      }
    }else{
      return false;
    }
  }).length;


  return tmp;

})

const store = useStore();



window.addEventListener("keydown",(e)=>{
  if(e.code==='ControlLeft'){
    carLayer.value.leafletObject.eachLayer((layer)=>{
      layer.setPressed(true);
    });
  }
});


window.addEventListener("keyup",(e)=>{
  if(e.code==='ControlLeft'){
    carLayer.value.leafletObject.eachLayer((layer)=>{
      layer.setPressed(false);
    });
  }
});





onMounted(()=>{
/*
  var l = document.createElement("script");
      l.src = "js/chunk-a156cd36.js";
      l.onload = ()=>{
          // eslint-disable-next-line no-undef
          chunk_a156cd36();
      }
      document.head.appendChild(l);*/
  const loadingInstance1 = ElLoading.service({ fullscreen: true })


  store.dispatch("server/load");
  store.dispatch("checkSession").then(()=>{
    router.push("/home");
    store.dispatch("loadUserData").then(()=> {
      loadingInstance1.close();
    });
  }).catch(()=>{
    router.push("/logins")
    loadingInstance1.close();
  });
  setInterval(()=>{
    if(map.value) {
      map.value.leafletObject.invalidateSize();
    }
  },100);
})



const zoomUpdated = (z) => {
  zoom.value = z;
/*
  const minZoom = 4;
  const maxZoom = 19;

  const minSize = 0.45;
  const maxSize = 0.8;

  const rSize = minSize + ((z - minZoom) * ((maxSize-minSize)/(maxZoom-minZoom)));

  // eslint-disable-next-line no-unused-vars
  carLayer.value.leafletObject.eachLayer((layer)=>{
    layer.options.img.rSize = rSize;
      layer.redraw();
  });*/
}
// z = 4 -->> 0.5x
// z = 19 --> 0.8x

const flyToDevice = (device) =>{
  const position = store.getters["devices/getPosition"](device.id);
  if(position){
    setTimeout(()=> {
      //map.value.leafletObject.setZoom(17)
      setTimeout(() => {
        map.value.leafletObject.flyTo([position.latitude, position.longitude],14,{animate: true,duration: 1.5});
      }, 100);
    },100);
  }
}

const markerClick = (e) =>{


  const deviceId = (e.target)?e.target.options.id:e;

  router.push('/devices/'+deviceId);
  const device = store.state.devices.deviceList.find((d)=> d.id === deviceId);
  flyToDevice(device);

}


const markerContext = async (evt,e)=>{

  const deviceId = (evt.target && evt.target.options && evt.target.options.id)?evt.target.options.id:e;


  const user =store.state.auth;
  const device = store.getters["devices/getDevice"](deviceId);
  const position = store.getters["devices/getPosition"](deviceId)


  const response = await window.$traccar.getAvailableCommands(deviceId);
  const availableSaved = response.data;

  let tmp = [];

  tmp.push({
    text: KT('device.details'), cb: () => {
      router.push('/devices/'+deviceId);
    }
  });

  tmp.push({
    text: KT('device.zoom'), cb: () => {
      flyToDevice(device);
    }
  });

  if(store.state.devices.isFollowingId===deviceId){
    tmp.push({
      text: KT('device.unfollow'), cb: () => {
        store.commit("devices/setFollow",0)
      }
    });
  }else{
    tmp.push({
      text: KT('device.follow'), cb: () => {
        store.commit("devices/setFollow",deviceId);
        flyToDevice(device);
      }
    });
  }

  if(store.state.devices.trail===deviceId){
    tmp.push({
      text: KT('device.untrail'), cb: () => {
        store.commit("devices/setTrail",false)
      }
    });
  }else{
    tmp.push({
      text: KT('device.trail'), cb: () => {
        store.commit("devices/setTrail",deviceId);
        flyToDevice(device);
      }
    });
  }


  if(!user.readonly) {
    tmp.push({text: 'separator'});

    if (position.attributes.blocked) {

      tmp.push({
        text: KT('device.unlock'), cb: () => {
          ElMessageBox.confirm(
              KT('device.confirm_unlock',device),
              'Warning',
              {
                confirmButtonText: KT('ok'),
                cancelButtonText: KT('cancel'),
                type: 'warning',
              }
          ).then(() => {
            const changeNative = availableSaved.find((a) => a.attributes['tarkan.changeNative'] && a.attributes['tarkan.changeNative'] === 'engineResume');
            if (changeNative) {
              window.$traccar.sendCommand({...changeNative, ...{deviceId: deviceId}});
            } else {
              window.$traccar.sendCommand({deviceId: deviceId, type: "engineResume"});
            }


            ElNotification({
              title: KT('success'),
              message: KT('device.command_sent'),
              type: 'success',
            });
          }).catch(() => {
            ElMessage.error(KT('userCancel'));
          })
        }
      });
    } else {
      tmp.push({
        text: KT('device.lock'), cb: () => {
          ElMessageBox.confirm(
              KT('device.confirm_lock',device),
              'Warning',
              {
                confirmButtonText: KT('ok'),
                cancelButtonText: KT('cancel'),
                type: 'warning',
              }
          ).then(() => {
            const changeNative = availableSaved.find((a) => a.attributes['tarkan.changeNative'] && a.attributes['tarkan.changeNative'] === 'engineStop');
            if (changeNative) {
              window.$traccar.sendCommand({...changeNative, ...{deviceId: deviceId}});
            } else {
              window.$traccar.sendCommand({deviceId: deviceId, type: "engineStop"});
            }


            ElNotification({
              title: KT('success'),
              message: KT('device.command_sent'),
              type: 'success',
            });
          }).catch(() => {
            ElMessage.error(KT('userCancel'));
          })

        }
      });
    }


    let commands = [];
    availableSaved.forEach((c) => {
      commands.push({
        text: c.description, cb: () => {
          ElMessageBox.confirm(
              KT('device.confirm_command',device),
              'Warning',
              {
                confirmButtonText: KT('OK'),
                cancelButtonText: KT('Cancel'),
                type: 'warning',
              }
          ).then(() => {
            window.$traccar.sendCommand({...c, ...{deviceId: deviceId}});

            ElNotification({
              title: KT('success'),
              message: KT('device.command_sent'),
              type: 'success',
            });
          }).catch(() => {
            ElMessage.error(KT('userCancel'));
          })
        }
      });
    });


    tmp.push({
      text: KT('device.send_command'),
      submenu: commands
    });


    tmp.push({text: 'separator'});

    tmp.push({
      text: KT('device.edit'), cb: () => {
        editDeviceRef.value.editDevice(deviceId);
      }
    });

  }



  contextMenuRef.value.openMenu({evt: evt.originalEvent || evt, menus: tmp})
}

const userMenu = (e)=>{
  let tmp = [];


  tmp.push({
    text: KT('usermenu.account'), cb: () => {
      editUserRef.value.editUser();
    }
  });

  if(store.state.auth.userLimit>0 || store.state.auth.userLimit===-1) {
    tmp.push({
      text: KT('usermenu.users'), cb: () => {
        editUsersRef.value.showUsers();
      }
    });
  }


  if(store.state.auth.administrator) {
    tmp.push({
      text: KT('usermenu.computedAttributes'), cb: () => {
        router.push("/computed")
      }
    });


    tmp.push({
      text: KT('usermenu.server'), cb: () => {
        editServerRef.value.showServer();
      }
    });
  }

  tmp.push({
    text: KT('usermenu.notifications'), cb: () => {
      editNotificationsRef.value.showNotifications();
    }
  });


  tmp.push({
    text: KT('usermenu.drivers'), cb: () => {
      editDriversRef.value.showDrivers();
    }
  });



  tmp.push({
    text: KT('usermenu.calendars'), cb: () => {
      editCalendarsRef.value.showCalendars();
    }
  });



  tmp.push({
    text: KT('usermenu.maintenance'), cb: () => {
      editMaintenancesRef.value.showMaintenances();
    }
  });




  tmp.push({
    text: KT('usermenu.logout'), cb: () => {
      store.dispatch("logout").then(()=>{
        router.push('/login');
      })

    }
  });




  contextMenuRef.value.openMenu({evt: e, menus: tmp})
}

const cptPoints = computed(()=>{
  let tmp = [];
  routePoints.value.forEach((p)=>{
    tmp.push([p[0],p[1]]);
  })

  return tmp;
})

const updateRoute = (points) =>{

  console.log(points);
  routePoints.value = points;

  if(points.length>0) {



    setTimeout(() => {
      map.value.leafletObject.flyTo([points[0][0],points[0][1]], 12, {animate: true, duration: 1.5});
    }, 300);
  }
}

const setMapCenter = (pos)=>{
  map.value.leafletObject.panTo(pos);
}
window.$setMapCenter = setMapCenter;



const mapClick = (e)=>{
  // eslint-disable-next-line valid-typeof
  if(typeof e === 'PointerEvent'){
    return false;
  }

  if(e.latlng && store.state.geofences.mapEditing!==0){

    if(store.state.geofences.mapPointEditingType==='CIRCLE'){
        if(store.state.geofences.mapPointEditing!==2){
          store.dispatch("geofences/setupCircle",[e.latlng.lat, e.latlng.lng, 10])
        }else {
          store.dispatch("geofences/completeCircle")
        }
    }else if(store.state.geofences.mapPointEditingType==='LINESTRING'){
      store.dispatch("geofences/setupLine",[e.latlng.lat, e.latlng.lng])
    }else if(store.state.geofences.mapPointEditingType==='POLYGON'){
      store.dispatch("geofences/setupPolygon",[e.latlng.lat, e.latlng.lng])
    }
  }
}

const mapMove = (e)=>{
  if(e.latlng && store.state.geofences.mapPointEditing === 2 && store.state.geofences.mapPointEditingType === 'CIRCLE' && store.state.geofences.mapPointEditingParams.length===3){
    // eslint-disable-next-line no-undef
    store.dispatch("geofences/setCircleRadius",L.latLng(store.getters["geofences/getCirclePosition"]).distanceTo(e.latlng));
  }
}


/* provide references for global use */
provide('markerClick', markerClick);
provide('flyToDevice',flyToDevice)
provide('updateRoute',updateRoute)
provide('markerContext',markerContext);


provide('contextMenu',contextMenuRef);
provide('edit-device',editDeviceRef);
provide('edit-user',editUserRef);
provide('edit-users',editUsersRef);
provide('edit-group',editGroupRef);
provide('link-objects',linkObjectsRef);

</script>

<style>

.editing .leaflet-container {
  cursor:crosshair !important;
}

body.el-popup-parent--hidden{
  padding-right: 0px !important;
}



body{
  overflow: hidden;
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
}

#head{
  height: 40px;
  border-bottom: var(--soft-black) 1px solid;
  display: flex;
  align-content: space-between;
  justify-content: space-between;
}

#head #user{
  display: flex;
}

#head #user i{

  font-size: 14px;
}

#logo{
  padding: 12px;
}

#content{
  display: flex;
  height: calc(100vh - 50px);
}

#menu{
  width: 0px;
  overflow: hidden;
  height: calc(100vh - 40px);
  background: var(--soft-black);
  position: absolute;
  left: 0px;
  top: 40px;
  z-index: 1001;
  transition: width 0.1s;
}

#menu.show{
  width: 73px;
}

#version{
  position: absolute;
  bottom: 10px;
  left: 1px;
  background: #2d2d2d;
  color: white;
  padding: 5px;
  font-size: 11px;
  border-radius: 3px;
  width: 70px;
  box-sizing: border-box;
  text-align: center;
}

#open{
  height: calc(100vh - 0px);
  background: var(--white);
  color: var(--text-black);
  display: flex;
  align-content: center;
  justify-content: space-between;
  transition: 0.2s;
  opacity: 0;
  width: 0px;
  overflow: hidden;
  position: absolute;
  left: 0px;
  top: 0px;
  z-index: 1002;
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
  width: calc(100vw - 0px);
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
  padding: 10px;
}

::-webkit-scrollbar{
  width: 2px;
  height: 3px;
}

::-webkit-scrollbar-thumb{
  background: #e7e7e7;
  width: 5px;
  height: 5px;
}

::-webkit-scrollbar-thumb:hover {
  background: #555;
}

#menu ul{
  list-style: none;
  margin-top: 45px;
}

#menu ul li{
  position: relative;
  width: 75px;
  height: 75px;
  z-index: 5;
}

#menu ul li a{
  color: var(--soft-black);
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
  line-height: 1em;
  font-size: 1.7em;
  text-align: center;
  transition: 0.5s;
  color: var(--soft-white);
  width: 1em;
  height: 1em;
  z-index: 10;
  transform: translateX(5px);
}

#menu ul li.active a .el-icon{
  transform: translateX(1px);
}

#menu ul li a .text{
  position: absolute;
  display: block;
  color: var(--soft-white);
  font-weight: 400;
  font-size: 0.65em;
  letter-spacing: 0.05em;
  transition: 0.5s;
  opacity: 1;
  left: 0px;
  width: 75px;
  text-align: center;
  transform: translateX(-25px) rotate(-90deg) ;
}


#menu ul li.active a .text{
  opacity: 1;
  left: 0px;
  font-size: 0.75em;
  transform: translateX(0px)  rotate(-90deg) ;
  display: none;
}

#menu ul li:hover:not(.active) a .text{
  transform: translateX(-20px) rotate(-90deg) ;
}


#menu ul li:hover:not(.active) a .el-icon{
  transform: translateX(10px);
}

#main{
  width: calc(100vw);
  height: calc(100vh - 40px);
}

.indicator{
  position: absolute;
  width: 65px;
  height: 65px;
  background: var(--soft-blue);
  box-sizing: border-box;
  left: 7px;
  top:50px;
  border-radius: 50%;
  border: 6px solid var(--soft-white);
  transition: 0.5s;
  opacity: 0;
  z-index: 1;
  box-shadow: 3px 2px 15px rgba(0,0,0,0.15)
}



#menu ul li:nth-child(1).active ~ .indicator{
  transform: translateY(calc(75px * 0));
  opacity: 1;
}

#menu ul li:nth-child(2).active ~ .indicator{
  transform: translateY(calc(75px * 1));
  opacity: 1;
}

#menu ul li:nth-child(3).active ~ .indicator{
  transform: translateY(calc(75px * 2));
  opacity: 1;
}

#menu ul li:nth-child(4).active ~ .indicator{
  transform: translateY(calc(75px * 3));
  opacity: 1;
}


#menu ul li:nth-child(5).active ~ .indicator{
  transform: translateY(calc(75px * 4));
  opacity: 1;
}


#menu ul li:nth-child(6).active ~ .indicator{
  transform: translateY(calc(75px * 5));
  opacity: 1;
}


#menu ul li:nth-child(7).active ~ .indicator{
  transform: translateY(calc(75px * 6));
  opacity: 1;
}


#menu ul li:nth-child(8).active ~ .indicator{
  transform: translateY(calc(75px * 7));
  opacity: 1;
}


#heading{
  text-align: center;
  font-weight: bold;
  background: var(--soft-blue);
  border-radius: 20px;
  padding: 10px;
  color: white;
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
  box-shadow: -10px 0px 0 0 var(--soft-white)
}

body.rtl #app div #content #menu ul .indicator:after{
  left: calc(50% + 7px);
  bottom: -18px;
  border-top-right-radius: 0px;
  border-top-left-radius: 20px;
  box-shadow: -10px 0px 0 0 var(--soft-white)
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
  background: #595959;
}

.online{
  background: #1eb21e;
}

.offline{
  background: #d31010;
}

.unknown{
  background: orange
}

.motion{
  background: #006fff;
}


.customFilter.active{
  border: white 1px solid;
}


.el-dialog{
  --el-dialog-width: 100% !important;
}

#open.bottom{
  position: absolute;
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

#expander{
  text-align: center;
  padding: 5px;
  background: #f3f3f3;
}

#open.bottom #expander{
  display: block !important;
}

#main.bottom{
  height: calc(55vh - 20px);
}

#pano{
  left: 0px !important;
  width: 100% !important;
  height: calc(44vh - 85px) !important;
  z-index: 1003 !important;
}

.el-dropdown-menu__item .el-checkbox{
  height: 30px;
}

</style>
