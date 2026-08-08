<template>
  <div>


  <div id="kr-actions" style="display: flex;justify-content: flex-end;align-content: space-between;">






    <el-tooltip
        effect="dark"
        :content="KT('device.showQrCode')"
        placement="bottom"
        v-if="store.getters['server/getAttribute']('tarkan.enableQrDriverId',false) && store.getters['isAdmin']"
    >
      <el-button  plain @click="openQrCode();">
        <i class="fas fa-qrcode"></i>
      </el-button>
    </el-tooltip>


    <el-tooltip
        effect="dark"
        :content="KT('device.openExternal')"
        placement="bottom"
    >
      <el-button  plain @click="showExternal($event)">
        <i class="fas fa-map-marked-alt"></i>
      </el-button>
    </el-tooltip>

    /* IFTRUE_myFlag */
    <el-tooltip
        effect="dark"
        :content="KT('device.streetview')"
        placement="bottom"
    >
      <el-button  plain @click="store.dispatch('devices/toggleStreet')">
        <i class="fas fa-street-view"></i>
      </el-button>
    </el-tooltip>

    /* FITRUE_myFlag */

    <el-tooltip
        effect="dark"
        :content="KT('device.routes')"
        placement="bottom"
        v-if="!store.state.auth.attributes['isShared'] && (store.getters.advancedPermissions(72) && store.getters.advancedPermissions(73))"
    >
      <el-button type="primary" @click="$router.push('/reports/history?deviceId='+device.id)" plain><i class="fas fa-route"></i></el-button>
    </el-tooltip>


    <template
        v-if="device">

      /* IFTRUE_myFlag */
    <el-tooltip v-if="store.state.server.isPlus && store.getters.advancedPermissions(9)"
                effect="dark"
                :content="KT((!store.getters['geofences/isAnchored'](device.id))?'actions.anchorEnable':'actions.anchorDisable')"
                placement="bottom"
    >
      <el-button type="success" @click="actAnchor(device.id)" :plain="!store.getters['geofences/isAnchored'](device.id)"><i class="fas fa-anchor"></i></el-button>
    </el-tooltip>
      /* FITRUE_myFlag */


    <el-tooltip v-if="position && (position.attributes.blocked && store.getters.advancedPermissions(11)) || store.getters['server/getAttribute']('tarkan.enableLockUnlock') && store.getters.advancedPermissions(11)"
                effect="dark"
                :content="KT('actions.engineResume')"
                placement="bottom"
    >
      <el-button type="success" @click="actUnlock()"
                 :disabled="device.status ==='offline'" @contextmenu.prevent="actContext($event)" plain><i class="fas fa-unlock"></i></el-button>
    </el-tooltip>

    <el-tooltip  v-if="position && (!position.attributes.blocked && store.getters.advancedPermissions(10)) || store.getters['server/getAttribute']('tarkan.enableLockUnlock') && store.getters.advancedPermissions(10)"
                effect="dark"
                :content="KT('actions.engineStop')"
                placement="bottom"
    >
      <el-button type="warning" @click="actBlock()"
                 :disabled="device.status ==='offline'" @contextmenu.prevent="actContext($event)" plain><i class="fas fa-lock"></i></el-button>
    </el-tooltip>
    </template>

    <el-tooltip
        effect="dark"
        :content="KT('device.edit')"
        placement="bottom"
        v-if="store.getters.advancedPermissions(14)"
    >
      <el-button type="primary" @click="openEdit()" plain><i class="fas fa-edit"></i></el-button>
    </el-tooltip>




    <el-tooltip
        effect="dark"
        :content="KT('device.remove')"
        placement="bottom"
        v-if="store.getters.advancedPermissions(15)"
    >
      <el-button type="danger" @click="doDelete()" plain><i class="fas fa-trash"></i></el-button>
    </el-tooltip>

  </div>


  <div v-if="device"  class="device"  :class="{isAttrComplete: attrComplete}">
    <div class="name" >{{device.name}}</div>
    <div class="resume">
      <div v-if="position" class="icons" >
        <el-tooltip v-if="position.attributes.ignition===true" :content="KT('device.ignitionOn')">
          <div :style="{color: 'var(--el-color-success)','font-size': '1rem'}"><i class="fas fa-key"></i></div>
        </el-tooltip>
        <el-tooltip v-else-if="position.attributes.ignition===false" :content="KT('device.ignitionOff')">
          <div :style="{color: 'var(--el-color-danger)','font-size': '1rem'}"><i class="fas fa-key"></i></div>
        </el-tooltip>
        <el-tooltip v-else :content="'Desconhecido'">
          <div :style="{color: 'var(--el-color-info)','font-size': '1rem'}"><i class="fas fa-key"></i></div>
        </el-tooltip>

        <el-tooltip v-if="position.attributes.blocked===true" :content="KT('device.blocked')">
          <div :style="{color: 'var(--el-color-danger)','font-size': '1rem'}"><i class="fas fa-lock"></i></div>
        </el-tooltip>
        <el-tooltip v-else-if="position.attributes.blocked===false"  :content="KT('device.unblocked')">
          <div :style="{color: 'var(--el-color-success)','font-size': '1rem'}"><i class="fas fa-lock-open"></i></div>
        </el-tooltip>
        <el-tooltip v-else :content="KT('unknown')">
          <div :style="{color: 'var(--el-color-info)','font-size': '1rem'}"><i class="fas fa-lock-open"></i></div>
        </el-tooltip>

          <template v-if="store.state.server.isPlus && store.getters.advancedPermissions(9)">
            <el-tooltip v-if="(store.getters['geofences/isAnchored'](device.id))" :content="KT('device.anchorEnabled')">
              <div :style="{color: 'var(--el-color-warning)','font-size': '1rem'}"><i class="fas fa-anchor"></i></div>
            </el-tooltip>
            <el-tooltip v-else :content="KT('device.anchorDisabled')">
              <div :style="{color: 'var(--el-color-success)','font-size': '1rem'}"><i class="fas fa-anchor"></i></div>
            </el-tooltip>
          </template>

        <el-tooltip v-if="position.attributes.rssi" :content="KT('device.rssi')">
          <div >
            <i style="font-size: 1rem;" class="fas fa-signal"></i>
            <span>
              <template v-if="position.network && position.network.networkType">{{position.network.networkType}} | </template>
              {{position.attributes.rssi}}
            </span>
          </div>
        </el-tooltip>
        <el-tooltip v-else-if="position.network && position.network.networkType" :content="KT('device.network')">
        <div >
          <i style="font-size: 1rem;" class="fas fa-signal"></i>
          <span>
              {{position.network.gsm}}
          </span>
        </div>
      </el-tooltip>

        <el-tooltip v-if="position.attributes.sat" :content="KT('device.sattelites')">
          <div v-if="position.attributes.sat"><i style="font-size: 1rem;" class="fas fa-satellite"></i> <span>{{position.attributes.sat}}</span></div>
        </el-tooltip>


        <el-tooltip v-if="position.attributes.power" :content="KT('device.power')">
          <div v-if="position.attributes.power"><i style="font-size: 1rem;" class="fas fa-car-battery"></i> <span>{{position.attributes.power}}v</span></div>
        </el-tooltip>

        <el-tooltip v-else-if="position.attributes.batteryLevel" :content="KT('device.batteryLevel')">
          <div>
            <span v-if="position.attributes.batteryLevel>80"><i style="color: var(--el-color-success);font-size: 1rem;" class="fas fa-battery-full"></i></span>
            <span v-else-if="position.attributes.batteryLevel>60"><i style="font-size: 1rem;" class="fas fa-battery-three-quarters"></i></span>
            <span v-else-if="position.attributes.batteryLevel>40"><i style="font-size: 1rem;"  class="fas fa-battery-half"></i></span>
            <span v-else-if="position.attributes.batteryLevel>30"><i style="font-size: 1rem;" class="fas fa-battery-quarter"></i></span>
            <span v-else-if="position.attributes.batteryLevel>20"><i  style="color: var(--el-color-warning);font-size: 1rem;" class="fas fa-battery-empty"></i></span>
            <span v-else><i style="color: var(--el-color-danger);font-size: 1rem;" class="fas fa-battery-empty"></i></span>
            <span v-if="position.attributes.battery">{{position.attributes.battery}}v</span>
            <span v-else>{{position.attributes.batteryLevel}}%</span>
          </div>
        </el-tooltip>



        <el-tooltip v-if="position.attributes.alarm" :content="KT('alarms.'+position.attributes.alarm)">
        <div >
            <span>
            <i style="color: var(--el-color-danger);font-size: 1rem;" class="fas fa-exclamation-triangle"></i>
              </span>
        </div>
        </el-tooltip>


        <el-tooltip :content="device.disabled ? KT('disabled') : device.lastUpdate===null?KT('new'): device.status === 'online' ? KT('online'): (device.status==='offline') ? KT('offline'):KT('unknown')">
          <div>

            <span v-if="device.lastUpdate===null"><i style="color: var(--el-color-info);font-size: 1rem;" class="fas fa-question-circle"></i></span>
            <span v-else-if="device.status==='online'"><i  style="color: var(--el-color-success);font-size: 1rem;" class="fas fa-check-circle"></i></span>
            <span v-else-if="device.status==='offline'"><i  style="color: var(--el-color-danger);font-size: 1rem;" class="fas fa-exclamation-circle"></i></span>
            <span v-else><i  class="fas fa-question-circle" style="color: var(--el-color-warning);font-size: 1rem;"></i></span>
          </div>
        </el-tooltip>



      </div>



      <div class="info" style="display: flex;border-bottom: var(--el-border-color-light) 1px dotted;">

        /* IFTRUE_myFlag */
      <div style="flex: 1;border-right: var(--el-border-color-light) 1px dotted;justify-content: center;align-content: center;display: flex;align-items: center;">
          <div  class="carImage" style="background-image: url('/img/upload.png');border: var(--el-border-color-light) 1px solid;background-size: cover;background-position: center;width: 170px;height: 140px;border-radius: 5px; vertical-align: middle;">
            <div :style="{'background-image': 'url(/tarkan/assets/images/'+device.id+'.jpg'+((uncache!==0)?'?uncache='+uncache:'')+')'}" style="background-size: cover;background-position: center;width: 170px;height: 140px;border-radius: 5px; vertical-align: middle;">
              <div v-if="store.getters.advancedPermissions(14)" @click="changeImage()" id="changeImage">{{KT('changeImage')}}</div>
              <div v-if="uploading" id="uploading">{{KT('uploadingImage')}}</div>
            </div>
          </div>
      </div>

        /* FITRUE_myFlag */

        /* IFTRUE_myFlag2 */
        <div style="flex: 1;border-right: var(--el-border-color-light) 1px dotted;justify-content: center;align-content: center;align-items: center;">

          <div >
            <div style="text-transform: uppercase;margin-top: 10px;font-size: 11px;color: var(--el-text-color-regular);"><i class="fas fa-address-card"></i> {{KT('device.plate')}}</div>
            <div style="margin-top: 10px;margin-bottom: 10px;font-size: 20px;color: var(--el-color-primary)">{{device.attributes['placa'] || 'N/A'}}</div>
          </div>

        </div>
        /* FITRUE_myFlag2 */
      <div style="flex: 1;">


        <div style="border-bottom: var(--el-border-color-light) 1px dotted;">
          <div style="text-transform: uppercase;margin-top: 10px;font-size: 11px;color: var(--el-text-color-regular);"><i class="fas fa-tachometer-alt"></i> {{KT('device.positionSpeed')}}</div>
          <div style="margin-top: 10px;margin-bottom: 10px;font-size: 20px;color: var(--el-color-primary)">{{$t('units.'+store.getters['server/getAttribute']('speedUnit','speedUnit'),{speed: (position)?position.speed:0})}}</div>
        </div>


        /* IFTRUE_myFlag */
        <div >
          <div style="text-transform: uppercase;margin-top: 10px;font-size: 11px;color: var(--el-text-color-regular);"><i class="fas fa-address-card"></i> {{KT('device.plate')}}</div>
          <div style="margin-top: 10px;margin-bottom: 10px;font-size: 20px;color: var(--el-color-primary)">{{device.attributes['placa'] || 'N/A'}}</div>
        </div>

        <div  style="border-top: var(--el-border-color-light) 1px dotted;">
          <div style="text-transform: uppercase;margin-top: 10px;font-size: 11px;color: var(--el-text-color-regular);"><i class="fas fa-car"></i> {{KT('device.model')}}</div>
          <div style="margin-top: 10px;margin-bottom: 10px;font-size: 20px;color: var(--el-color-primary)">{{device.attributes['brand'] + " " +device.attributes['model'] || 'N/A'}}</div>
        </div>

        /* FITRUE_myFlag */
      </div>

    </div>

      <div class="stats" v-if="position">

        <div v-if="historyInfo.length>0" style="border-bottom: var(--el-border-color-light) 1px dotted;">
            <div class="subtitle"><i class="fas fa-history"></i> {{KT('device.historyPosition')}}</div>


            <div style="background: var(--el-border-color-light); height: 20px;border-radius: 5px;margin: 5px 15px 15px;overflow: hidden;position: relative;">
                <div v-for="(s,sk) in historyInfo" :key="sk" @mouseleave="hideTip" @mouseenter.stop="showTip($event,KT((s.motion)?'device.motion':'device.stopped')+' '+Math.round(s.duration/60000)+' mins')" style="cursor: pointer;position: absolute;height: 20px;" :style="{background: (s.motion)?'var(--el-color-primary)':'',width: s.width+'%',left: s.position+'%'}">&nbsp;</div>
            </div>

        </div>


        <div class="subtitle" style="position: relative;">
        <i class="fas fa-map-marker-alt"></i> {{KT('device.lastPosition')}}


        <div style="position: absolute; right: 0px; top: 0px;display: flex;margin-right: 10px;">
          <div v-if="store.state.server.isPlus && store.getters.advancedPermissions(25)" @mouseleave="hideTip" @mouseenter.stop="showTip($event,KT('device.shareLink'))" @click="editShareRef.newShare(device.id)" style="text-align: center;padding: 7px;border-radius: 5px;cursor: pointer;border: var(--el-border-color-light) 1px solid;margin-right: 2px;">
            <i class="fas fa-share-alt" style="margin-right: 2px;margin-left: 2px;"></i>
          </div>
          <div @mouseleave="hideTip" @mouseenter.stop="showTip($event,KT('device.shareMaps'))" @click="openMapsShare()" style="text-align: center;padding: 7px;border-radius: 5px;cursor: pointer;border: var(--el-border-color-light) 1px solid;margin-right: 2px;">
            <i class="fas fa-map-marked" style="margin-right: 1px;margin-left: 1px;"></i>
          </div>
          <div @mouseleave="hideTip" @mouseenter.stop="showTip($event,KT('device.shareStreet'))" @click="openStreetShare()" style="text-align: center;padding: 7px;border-radius: 5px;cursor: pointer;border: var(--el-border-color-light) 1px solid;">
            <i class="fas fa-street-view" style="margin-right: 1px;margin-left: 1px;"></i>
          </div>
        </div>

      </div>
        <div class="updated">
        <span @mouseleave="hideTip" @mouseenter.stop="showTip($event,KT('device.lastPositionTime'))">
        <i class="far fa-clock"></i> {{new Date(position.fixTime).toLocaleString()}}
        </span>

        <span @mouseleave="hideTip" @mouseenter.stop="showTip($event,KT('device.lastStoppedTime'))" style="margin-left: 15px;" v-if="position.attributes.stoppedTime && position.attributes.stoppedTime!=-1 && now">
          <i class="far fa-hand-paper"></i> {{getLastUpdated(position.attributes.stoppedTime,now)}}
        </span>
      </div>
        <div v-if="position.address" class="address">{{position.address}}</div>
        <div v-else class="address">{{position.latitude}}, {{position.longitude}}</div>

        <div v-if="eventsInfo.length>0" style="border-top: var(--el-border-color-light) 1px dotted;">
          <div class="subtitle"><i class="fas fa-calendar-check"></i> {{KT('device.historyEvents')}}</div>

          <div style="margin: 10px;border: var(--el-border-color-light) 1px solid;border-radius: 5px;overflow: hidden">
            <div v-for="(e,sk) in eventsInfo" :key="sk" style="border-top: var(--el-border-color-light) 1px dotted;display: flex;justify-content: space-between;">
              <div style="text-align: right;padding: 10px;font-size: 10px; color: #5b5b5b;position: relative;width: 70px;">
                <div style="position: absolute;width: 20px;height: 16px;font-size: 14px;text-align: center;right: -10px;top: 50%;border-radius: 50%;transform: translateY(-50%)">
                  <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div style="position: absolute;right: 15px;top: 50%;border-radius: 50%;transform: translateY(-50%)">
                  {{new Date(e.eventTime).toLocaleDateString()}}<br>
                  {{new Date(e.eventTime).toLocaleTimeString()}}
                </div>
              </div>
              <div v-if="e.type==='alarm'" style="padding: 10px;font-size: 11px;flex: 1;text-align: left;padding-left: 20px;">
                {{KT("alarms."+e.attributes['alarm'])}}
              </div>
              <div v-else style="padding: 10px;font-size: 11px;flex: 1;text-align: left;padding-left: 20px;">
                {{KT("notification.types."+e.type)}}
              </div>
            </div>
          </div>

        </div>

      </div>
      <div class="stats" v-else>
        <div class="updated" style="padding: 10px;">{{KT('device.noPosition')}}</div>
      </div>

      <div v-if="position" class="info" style="display: flex;border-top: var(--el-border-color-light) 1px dotted;">


      <div v-if="position && position.attributes['driverUniqueId']" style="flex: 1;border-right: var(--el-border-color-light) 1px dotted;">


        <div style="text-transform: uppercase;margin-top: 20px;font-size: 11px;color: var(--el-text-color-regular);"><i class="far fa-id-card"></i> {{KT('device.driver')}}</div>


        <div style="margin-top: 10px;margin-bottom: 20px;font-size: 20px;color: var(--el-color-primary)" :set="driver = store.getters['drivers/getDriverByUniqueId'](position.attributes['driverUniqueId'])">
          <template v-if="driver">
            {{driver.name}}
          </template>
          <template v-else>
            {{position.attributes['driverUniqueId']}}
          </template>
        </div>

      </div>
      <div style="flex: 1;">
        <div style="text-transform: uppercase;margin-top: 20px;font-size: 11px;color: var(--el-text-color-regular);">
          <i class="fas fa-ruler"></i>

          {{KT('device.distance')}}
        </div>
        <div style="margin-top: 10px;margin-bottom: 20px;font-size: 20px;color: var(--el-color-primary)">{{$t('units.'+store.getters['server/getAttribute']('distanceUnit','distanceUnit'),{distance: position.attributes.totalDistance})}}</div>

      </div>

    </div>

    </div>


    <div v-if="position" class="favorites" style="border-top: var(--el-border-color-light) 1px dotted;">
      <div v-for="(a,b) in getFavorites(position)" :key="b" :class="{tr1: (b%2),tr2: !(b%2)}" style="display: flex;">
        <div style="flex: 1;border-right: var(--el-border-color-light) 1px dotted;padding: 6px;font-size: 12px;text-align: right;">{{KT('attribute.'+a.name)}}</div>
        <div style="flex: 1;padding: 6px;max-width: 40%;font-size: 12px;text-align: left;">
          {{TT('units'+a.name,{value: a.value})}}
        </div>
        <div v-if="attrComplete && !(a.type==='server' && !store.state.auth.administrator )" class="favBtn"   @click="actFav($event,a.name,false)" style="text-align: right;box-sizing: border-box;padding: 7px;font-size: 13px;width: 50px;">
          <i class="fas fa-thumbtack"></i>
        </div>
        <div v-else style="width: 50px;text-align: center;box-sizing: border-box;">

        </div>
      </div>


      <div class="complete" :set="ll = getFavorites(position).length" >


        <div v-for="(a,b) in getAttributes(position)" :key="b" :class="{tr1: ((b+ll)%2),tr2: !((b+ll)%2)}" style="display: flex;">
          <div style="flex: 1;border-right: var(--el-border-color-light) 1px dotted;padding: 6px;font-size: 12px;text-align: right;">{{KT('attribute.'+a.name)}}</div>
          <div style="flex: 1;padding: 6px;font-size: 12px;text-align: left;max-width: 40%;">
            {{TT('units.'+a.name,{value: a.value})}}
          </div>
          <div class="favBtn"  style="padding: 7px;font-size: 13px;color: var(--el-color-info);width: 50px;text-align: right;box-sizing: border-box;" @click="actFav($event,a.name,true)">
            <i class="fas fa-thumbtack"></i>
          </div>
        </div>
      </div>

      <div @click="attrComplete = !attrComplete" style="padding: 5px;background: var(--el-color-info-light);">
        <i class="allBtn fas fa-angle-double-down"></i>
      </div>
    </div>


  </div>
    <div v-else style="text-align: center;">{{$t('device.loading')}}</div>
  </div>
</template>

<script setup>



import 'element-plus/es/components/button/style/css'
import 'element-plus/es/components/tooltip/style/css'
import 'element-plus/es/components/message/style/css'
import 'element-plus/es/components/message-box/style/css'
import 'element-plus/es/components/notification/style/css'

import {ElButton,ElTooltip} from "element-plus";

import {useRoute,useRouter} from 'vue-router';

import {ref,computed,watch,inject,onMounted} from 'vue';
import {useStore} from "vuex"

import i18n from "../lang/";

import {ElMessage} from "element-plus/es/components/message";
import {ElMessageBox} from "element-plus/es/components/message-box";
import {ElNotification} from "element-plus/es/components/notification";


const historyInfo = ref([]);
const eventsInfo = ref([]);

const store = useStore();
const route = useRoute();
const router = useRouter();
const uncache = ref(new Date().getTime());
const uploading = ref(false);

const flyToDevice = inject('flyToDevice');
const contextOpenRef = inject('contextMenu');

const editShareRef = inject("edit-share");
const editDeviceRef = inject('edit-device');
const qrDeviceRef = inject('qr-device');
const attrComplete = ref(false);
const now = ref(false);

const TT = (k,v)=>{
  const R = i18n.global.t(k,v);

  return (R===k)?v.value:R;
}

const showTip = (evt,text)=>{
  window.$showTip(evt,text);
}

const hideTip = (evt,text)=>{
  window.$hideTip(evt,text);
}


const getLastUpdated = (t,tt)=>{
  tt = new Date();

  if(t===null){
    return KT('new');
  }

  const diff = Math.round((new Date(tt).getTime() - new Date(t).getTime())/1000);

  if(diff<0){
    return KT('now');
  }else if(diff>86400){
    const dias = Math.round(diff/86400);

    return dias+' '+KT('days');
  }else if(diff>3600){
    const horas = Math.round(diff/3600);

    return horas+' '+KT('hours');
  }else if(diff>60){
    const minutos = Math.round(diff/60);

    return minutos+' '+KT('minutes');
  }else{
    return KT('lessMinute');
  }

}

onMounted(()=>{


  loadHistoryInfo(parseInt(route.params.deviceId));

  setInterval(()=>{
    now.value = new Date();
  },1000);

  if(route.query.edit){
    openEdit();
  }
})


const openMapsShare = () => {

  const link = 'http://maps.google.com/maps?q=loc:'+position.value.latitude+","+position.value.longitude;

  if (navigator.share) {
    navigator.share({
      title: device.value.name,
      url: link
    }).then(() => {
      console.log('Thanks for sharing!');
    }).catch(console.error);
  } else {
    const elm = document.createElement("input");
    elm.value = link;
    document.body.appendChild(elm);
    elm.select();
    document.execCommand("copy");
    document.body.removeChild(elm);

    ElMessage.success('Copiado para área de transferência');
  }

};



const openStreetShare = () => {

  const link = 'https://www.google.com/maps/@?api=1&map_action=pano&viewpoint='+position.value.latitude+','+position.value.longitude+'&heading='+position.value.course+'&pitch=10&fov=80';

  if (navigator.share) {
    navigator.share({
      title: device.value.name,
      url: link
    }).then(() => {
      console.log('Thanks for sharing!');
    }).catch(console.error);
  } else {
    const elm = document.createElement("input");
    elm.value = link;
    document.body.appendChild(elm);
    elm.select();
    document.execCommand("copy");
    document.body.removeChild(elm);

    ElMessage.success('Copiado para área de transferência');
  }

}

const position = computed(()=>{
  return store.getters['devices/getPosition'](device.value.id);
});


const getFavorites = (position) => {
  let tmp = [];
  store.getters.getDeviceAttributes.forEach((f)=>{
      const value = findAttribute(position,f.id);

      if(value!==null){
        tmp.push({name: f.id, value: value,type: f.type});
      }
  })

  return tmp;
}

const getAttributes = (position)=>{
  let tmp = [];

  for(const k of Object.keys(position)){
    if(k==='attributes'){
      for(const kk of Object.keys(position.attributes)){


        if(position['attributes'][kk]!==null && !store.getters.getDeviceAttributes.find((a)=> a.id ==='attributes.'+kk)) {

          tmp.push({name: kk, value: position['attributes'][kk]});
        }
      }
    }else {
      if(position[k]!==null  && !store.getters.getDeviceAttributes.find((a)=> a.id === k)) {
        tmp.push({name: k, value: position[k]});
      }
    }
  }


  return tmp;
}


const findAttribute = (position,a)=>{

  let result = null;

  eval('result = position.attributes.'+a);

  if(result===undefined || result === null){
    eval('result = position.'+a);
  }

  return result;
}

import KT from '../tarkan/func/kt.js'

const actFav = (evt,id,add)=>{

  if(store.state.auth.administrator && !add) {
      const findAttr = store.getters.getDeviceAttributes.find((a)=> a.id === id);
      if(findAttr.type==='server'){
        store.dispatch("pinServer", [id,false]);
      }else{
        store.dispatch("pinUser", [id,false]);
      }


  }else if(store.state.auth.administrator) {
    let commands = [];
    commands.push({
      text: KT('server.server'), cb: () => {
        store.dispatch("pinServer", [id,add]);
      }
    });
    commands.push({
      text: KT('user.user'), cb: () => {
        store.dispatch("pinUser", [id,add]);

      }
    });


    contextOpenRef.value.openMenu({evt: evt, menus: commands})
  }else{
    store.dispatch("pinUser", [id,add]);
  }



}



const actAnchor = inject('act-anchor');


const actBlock = async ()=>{

  const response = await window.$traccar.getAvailableCommands(parseInt(route.params.deviceId));
  const availableSaved = response.data;

  ElMessageBox.confirm(
      'Deseja realmente bloquear o veiculo "'+device.value.name+'"?',
      'Warning',
      {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancelar',
        type: 'warning',
      }
  ).then(()=>{
    const changeNative = availableSaved.find((a)=> a.attributes['tarkan.changeNative'] && a.attributes['tarkan.changeNative'] === 'engineStop' );
    if(changeNative){
      window.$traccar.sendCommand({...changeNative,...{deviceId: device.value.id}});
    }else{
      window.$traccar.sendCommand({deviceId: device.value.id,type: "engineStop"});
    }


    ElNotification({
      title: 'Successo',
      message: 'Comando enviado para o dispositivo...',
      type: 'success',
    });
  }).catch(()=>{
    ElMessage.error('Ação cancelada pelo usuário');
  })
}

const actUnlock = async ()=>{
  const response = await window.$traccar.getAvailableCommands(parseInt(route.params.deviceId));
  const availableSaved = response.data;


  ElMessageBox.confirm(
      'Deseja realmente desbloquear o veiculo "'+device.value.name+'"?',
      'Warning',
      {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancelar',
        type: 'warning',
      }
  ).then(()=>{
    const changeNative = availableSaved.find((a)=> a.attributes['tarkan.changeNative'] && a.attributes['tarkan.changeNative'] === 'engineResume' );
    if(changeNative){
      window.$traccar.sendCommand({...changeNative,...{deviceId: device.value.id}});
    }else{
      window.$traccar.sendCommand({deviceId: device.value.id,type: "engineResume"});
    }


    ElNotification({
      title: 'Successo',
      message: 'Comando enviado para o dispositivo...',
      type: 'success',
    });
  }).catch(()=>{
    ElMessage.error('Ação cancelada pelo usuário');
  })
}

const showExternal = (e)=>{



  let shareOpen = [];

  shareOpen.push({
    text: KT('device.openMaps'),
    cb: () => {


      const elm = document.createElement("a");
      elm.target = "_blank";
      elm.href = 'http://maps.google.com/maps?q=loc:' + position.value.latitude + "," + position.value.longitude;
      document.body.appendChild(elm);
      elm.click();
      document.body.removeChild(elm);

    }
  });


  shareOpen.push({
    text: KT('device.openStreet'),
    cb: () => {

      const link = 'https://www.google.com/maps/@?api=1&map_action=pano&viewpoint=' + position.value.latitude + ',' + position.value.longitude + '&heading=' + position.value.course + '&pitch=10&fov=80';


      const elm = document.createElement("a");
      elm.target = "_blank";
      elm.href = link;
      document.body.appendChild(elm);
      elm.click();

    }


  });

  contextOpenRef.value.openMenu({evt: e, menus: shareOpen})


}

const actContext = (e)=>{

  if(!store.getters.advancedPermissions(12)){
    return false;
  }

  const deviceId = parseInt(route.params.deviceId);

  let commands = [];


  window.$traccar.getTypeCommands(deviceId).then((response)=> {

    const availableTypesCommand = response.data;

    availableTypesCommand.forEach((c) => {
      commands.push({
        text: KT('actions.'+c.type), cb: () => {
          ElMessageBox.confirm(
              KT('device.confirm_command', device),
              'Warning',
              {
                confirmButtonText: KT('OK'),
                cancelButtonText: KT('Cancel'),
                type: 'warning',
              }
          ).then(() => {

            window.$traccar.sendCommand({deviceId: deviceId, type: c.type});

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

    window.$traccar.getAvailableCommands(deviceId).then((response) => {
      const availableSaved = response.data;

      if(commands.length>0 && availableSaved.length>0){
        commands.push({text: 'separator'});
      }

      availableSaved.forEach((c) => {
        commands.push({
          text: c.description, cb: () => {
            ElMessageBox.confirm(
                KT('device.confirm_command', device),
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


      contextOpenRef.value.openMenu({evt: e, menus: commands})
    })
  });




}



const loadHistoryInfo = (deviceId)=>{

  const intervalo = (3600000 * 3);

  const now = new Date().getTime();

  const inicio = new Date(now - intervalo);
  const fim = new Date();

  historyInfo.value = [];
  eventsInfo.value = [];

  if(store.getters['server/getAttribute']('tarkan.enableStops',false)) {
    window.$traccar.loadRoute(deviceId, inicio, fim, false).then((r) => {

      let tmp = {start: null, duration: 0, position: 0, width: 0, motion: false, ignition: false, blocked: false};

      r.data.forEach((p, k) => {
        if (tmp.motion != p.attributes.motion || k >= (r.data.length - 1)) {

          if (tmp.start != null) {


            tmp.position = ((tmp.start - inicio.getTime()) * 100) / intervalo;
            tmp.width = (((((k >= (r.data.length - 1)) ? fim.getTime() : new Date(p.fixTime).getTime()) - inicio.getTime()) * 100) / intervalo) - tmp.position;

            tmp.duration = ((k >= (r.data.length - 1)) ? fim.getTime() : new Date(p.fixTime).getTime()) - tmp.start;

            historyInfo.value.push(JSON.parse(JSON.stringify(tmp)));
          }

          tmp.motion = p.attributes.motion;
          tmp.start = new Date(p.fixTime).getTime();
        }
      })
    });
  }

  if(store.getters['server/getAttribute']('tarkan.enableEvents',false)) {
    window.$traccar.getReportEvents([deviceId], [], new Date(inicio).toISOString(), new Date(fim).toISOString(), false).then((r) => {
      eventsInfo.value = r.data.reverse();
    });
  }
}

watch(()=> route.params.deviceId,()=>{


  const tmp = store.getters['devices/getDevice'](parseInt(route.params.deviceId));
  if(tmp) {

    loadHistoryInfo(parseInt(route.params.deviceId));
    flyToDevice(tmp);
  }
})


watch(()=> route.query.edit,(a)=>{
  if(a){
    openEdit();
  }
})

const device = computed(()=> {
  return store.getters['devices/getDevice'](parseInt(route.params.deviceId));
});


const openEdit = ()=>{
console.log(editDeviceRef);
console.log(editDeviceRef.value);

  editDeviceRef.value.editDevice(device.value.id);
}


const openQrCode = ()=>{


  qrDeviceRef.value.editDevice(device.value.id);
}

const doDelete = ()=>{
  ElMessageBox.confirm('Deseja realmente excluir este dispositivo?','Tem certeza?').then(()=>{
    store.dispatch("devices/delete",device.value.id).then(()=>{

      ElNotification({
        title: 'Info',
        message: 'Dispositivo deletado com suceso.',
        type: 'info',
      });

      router.push('/devices');
    }).catch(()=>{
      ElNotification({
        title: 'Erro',
        message: 'Ocorreu um erro inesperado ao excluir seu dispositivo.',
        type: 'danger',
      });
    });
  });
}

import axios from 'axios';

const changeImage = ()=>{
  const file = document.createElement("input");
        file.type = 'file';
        file.click();
        file.onchange = ()=>{

          uploading.value = true;

            var formData = new FormData();
            formData.append("deviceId", route.params.deviceId);
            formData.append("image", file.files[0]);
            axios.post('/tarkan/devices/' + route.params.deviceId + '/photo', formData, {
              headers: {
                'Content-Type': 'multipart/form-data'
              }
            }).then(() => {

              uncache.value = new Date().getTime();
              uploading.value = false;

            });

        }
}


</script>

<style scoped>

  #kr-actions .el-button{
    width: 40px;
    padding: 0px;
  }

  .device{
    border: var(--el-border-color-light) 1px solid;
    border-radius: 5px;
    margin-top: 10px;
    display: flex;
    flex-direction: column;
    text-align: center;
  }

  .device .name{
    border-bottom: var(--el-border-color-light) 1px dotted;
    font-size: 18px;
    padding: 5px;
    font-weight: bold;

  }

  .icons{
    display: flex;
    justify-content: center;
  }

  .icons div{
    display: flex;
    justify-content: center;
    flex: 1;
    border-bottom: var(--el-border-color-light) 1px dotted;
    border-right: var(--el-border-color-light) 1px dotted;
    padding: 7px;
    font-size: 11px;
  }
  .icons div i{
    font-size: 16px;
  }

  .icons div:last-child{
    border-right: none;
  }

  .icons div span{
    display: flex;
    padding: 2px;
    padding-left: 5px;
  }

  .subtitle{
    margin-top: 20px;
    font-weight: bold;
    font-size: 14px;
    text-transform: uppercase;
    color: var(--el-text-color-regular);
  }

  .subtitle i{
    font-size: 12px;
    margin-right: 3px;
  }

  .updated{
    font-size: 12px;
    margin-top: 5px;
    color: var(--el-text-color-secondary);
  }

  .address{
    color: var(--el-color-primary);
    font-size: 15px;
    margin-top: 5px;
    margin-bottom: 5px;
    padding: 10px;
    line-height: 20px;
  }

  .tr1{
    background: var(--el-color-white);
  }

  .tr2{
    background: var(--el-color-info-lighter);
  }


  .resume{
    transition: max-height 0.15s ease;
    max-height: 100vh;
    overflow: hidden;
  }

  .complete{
    max-height: 0vh;
    overflow: hidden;
    transition: max-height 0.15s ease;
  }

  .isAttrComplete .resume{
    max-height: 0vh;
  }

  .isAttrComplete .complete{
    max-height: 2000vh;
  }

  .isAttrComplete .favorites{
    border-top: none !important;
  }

  .device .allBtn{
    transition: all 0.15s;
  }

  .isAttrComplete .allBtn{
    transform: rotate(180deg);
  }

  .favBtn{
    cursor: pointer;
  }

  .favBtn:hover{
    color: var(--el-color-primary) !important;
  }

  .carImage{
    position: relative;
  }

  .carImage #changeImage{
    position: absolute;
    background: rgba(0,0,0,0.5);
    left: 50%;
    top: 50%;
    padding: 10px;
    color: white;
    transform: translate(-50%,-50%);
    opacity: 0;
    transition: all 0.3s;
    cursor: pointer;
  }

  .carImage:hover #changeImage{
    opacity: 1;
  }

  #uploading{
    position: absolute;
    background: rgba(0,0,0,0.5);
    left: 50%;
    top: 50%;
    padding: 10px;
    color: white;
    transform: translate(-50%,-50%);
    opacity: 1;
    transition: all 0.3s;
    cursor: pointer;
  }

</style>