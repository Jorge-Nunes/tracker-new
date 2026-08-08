<template>

  <div style="margin-top: 20px;height: calc(100vh - 200px);">
    <div style="overflow-x: hidden;overflow-y: scroll;height: calc(100vh - 200px);">
      <div v-for="(event) in store.state.events.notificationList" :key="event.id" class="device" style="background: white;flex-direction: column;border: silver 1px solid; border-radius: 5px;width: calc(100% - 2px);margin-bottom: 10px;" :set="device = store.getters['devices/getDevice'](event.deviceId)">

        <div style="font-size: 16px;padding: 3px;font-weight: bold;border-bottom: silver 1px dotted;">{{device.name}}</div>
        <div style="font-size: 12px;padding: 3px;padding-top: 6px;padding-bottom: 6px;position: relative;">
          <div v-if="event.type==='alarm'">{{KT('alarms.' + event.attributes['alarm'])}}</div>
          <div v-else-if="event.geofenceId!=0" :set="geofence = store.getters['geofences/getGeofenceById'](event.geofenceId)">

            {{KT('notification.messages.' + event.type+((geofence)?'Name':''),geofence)}}
          </div>
          <div v-else>{{KT('notification.messages.' + event.type,event.attributes)}}</div>

          <div style="position: absolute; right: 7px;bottom: 3px;color: #505050; font-size: 11px;">{{new Date(event.eventTime).toLocaleTimeString()}}</div>
        </div>


      </div></div>
  </div>

</template>

<script setup>

import {useStore} from "vuex"

const store = useStore();








</script>

<style scoped>


.device{
  border-bottom: var(--el-border-color-light) 1px solid;
  display: flex;
  flex-direction: row;
  text-align: center;
  cursor: pointer;
  margin-right: -1px;
}

.deviceHead{
  border-bottom: var(--el-border-color-light) 1px solid;
  display: flex;
  flex-direction: row;
  text-align: center;
  cursor: pointer;
  margin-right: -1px;
  background: var(--el-color-info-light);
}

.device:hover{
  background: var(--el-color-primary-light-9);
}



.device .name,.deviceHead .name{
  font-size: 12px;
  padding: 7px;
  text-align: left;
  line-height: 14px;
  font-weight: 800;
  border-right: silver 1px dotted;
  width: 60%;
}

.icons{
  display: flex;
  justify-content: center;
  flex-direction: row-reverse;
  flex: 1;
  font-size: 11px;
}

.icons div{
  display: flex;
  justify-content: center;
  flex: 1;
  border-right: silver 1px dotted;
  padding: 7px;
  font-size: 11px;
}
.icons div i{
  font-size: 14px;
}

.icons div:first-child{
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
  color: var(--text-black);
}

.subtitle i{
  font-size: 12px;
  margin-right: 3px;
}



</style>