<template>
  <div
    style="
      display: flex;
      justify-content: space-between;
      align-content: space-between;
    "
  >
    <div style="width: 30%; font-size: 12px; font-weight: 800; padding-top: 8px">
      {{ filteredDevices.length }} {{ KT('device.devices') }}
    </div>

    <el-input
      v-model="query"
      :placeholder="KT('device.search')"
      style="--el-input-border-radius: 5px; margin-right: 5px"
    ></el-input>

    <el-button
      v-if="store.getters.advancedPermissions(49)"
      @mouseleave="hideTip"
      @mouseenter.stop="showTip($event, KT('group.add'))"
      type="primary"
      @click="editGroupRef.newGroup()"
      plain
    >
      <i class="fas fa-folder-plus"></i>
    </el-button>

    <el-button
      @mouseleave="hideTip"
      @mouseenter.stop="showTip($event, KT('device.add'))"
      :disabled="!store.getters['checkDeviceLimit']"
      v-if="
        store.getters.advancedPermissions(13) &&
        (store.state.auth.deviceLimit === -1 ||
          store.state.auth.deviceLimit > 0)
      "
      type="primary"
      @click="
        store.getters['checkDeviceLimit']
          ? editDeviceRef.newDevice()
          : deviceLimitExceded()
      "
      ><i class="fas fa-plus"></i
    ></el-button>
  </div>

  <div
    style="
      border: silver 1px solid;
      border-radius: 5px;
      margin-top: 20px;
      height: calc(100vh - 200px);
    "
  >
    <div class="deviceHead">
      <div
        @click="store.dispatch('devices/setSorting', 'name')"
        class="name"
        style="
          font-size: 12px;
          box-sizing: border-box;
          font-weight: 100;
          padding: 5px;
          flex: 1;
        "
      >
        {{ KT("device.name") }}
        <span v-if="store.getters['devices/sorting'] === 'name-asc'">
          <i class="fas fa-sort-alpha-down"></i>
        </span>
        <span v-else-if="store.getters['devices/sorting'] === 'name-desc'">
          <i class="fas fa-sort-alpha-up"></i>
        </span>
        <span v-else>
          <i style="color: silver" class="fas fa-sort-alpha-down"></i>
        </span>
      </div>
      <div
        @click="store.dispatch('devices/setSorting', 'status')"
        class="name"
        style="
          font-size: 12px;
          box-sizing: border-box;
          font-weight: 100;
          padding: 5px;
          width: 32px;
        "
      >
        <span v-if="store.getters['devices/sorting'] === 'status-asc'">
          <i class="fas fa-sort-alpha-down"></i>
        </span>
        <span v-else-if="store.getters['devices/sorting'] === 'status-desc'">
          <i class="fas fa-sort-alpha-up"></i>
        </span>
        <span v-else>
          <i style="color: silver" class="fas fa-sort-alpha-down"></i>
        </span>
      </div>
      <div
        @click="store.dispatch('devices/setSorting', 'lastUpdate')"
        class="name"
        style="
          font-size: 12px;
          box-sizing: border-box;
          font-weight: 100;
          padding: 5px;
          width: 90px;
          text-align: center;
        "
      >
        {{ KT("device.updated") }}

        <span v-if="store.getters['devices/sorting'] === 'lastUpdate-asc'">
          <i class="fas fa-sort-alpha-down"></i>
        </span>
        <span
          v-else-if="store.getters['devices/sorting'] === 'lastUpdate-desc'"
        >
          <i class="fas fa-sort-alpha-up"></i>
        </span>
        <span v-else>
          <i style="color: silver" class="fas fa-sort-alpha-down"></i>
        </span>
      </div>
      <div
        class="icons"
        @click="setSortingByState()"
        style="
          flex: none;
          width: 183px !important;
          font-size: 12px;
          box-sizing: border-box;
          font-weight: 100;
          padding: 5px;
          text-align: center;
        "
      >
        {{ KT("sorting." + store.getters["devices/sorting"]) }}

        <span><i class="fas fa-sort"></i></span>
      </div>
    </div>
    <div
      style="
        overflow-x: hidden;
        overflow-y: scroll;
        height: calc(100vh - 230px);
      "
    >
        <!-- Pagination -->

        <div v-for="group in groupedDevices" :key="group.id">
          <div
            v-if="group.id !== -1"
            style="
              background: #f7f7f7;
              padding: 5px;
              padding-left: 8px;
              font-size: 13px;
              display: flex;
            "
          >
            <div style="flex: 1">{{ group.name }}</div>
            <div
              v-if="group.id !== -1 && group.id !== 0"
              @click="doAnchorGroup(group)"
              style="cursor: pointer; margin-right: 10px; font-size: 10px"
            >
              <i
                class="fas fa-anchor"
                :style="{
                  color: store.getters['geofences/isAnchoredGroup'](group.id)
                    ? 'var(--el-color-success)'
                    : 'var(--el-color-danger)',
                }"
              ></i>
            </div>

            <div
              @click="store.dispatch('setGroupPref', group.id)"
              style="cursor: pointer; margin-right: 10px"
            >
              <span v-if="store.getters.groupPref(group.id)">
                <i class="fas fa-angle-up"></i>
              </span>
              <span style="cursor: pointer" v-else>
                <i class="fas fa-angle-down"></i>
              </span>
            </div>
          </div>

          <div v-if="group.id == -1 || store.getters.groupPref(group.id)">
            <paginate
              :items="group.devices"
              :per-page="15"
              v-model="currentPage"
            >
              <template v-slot="{ item }">
                <div class="device" :class="{'isDisabled': item.disabled}" @click="markerClick(item.id)" @contextmenu.prevent="markerContext($event,item.id)" :set="position = store.getters['devices/getPosition'](item.id)">
                  <div class="name" style="flex: 1">{{ item.name }}</div>
                  <div
                    class="name"
                    style="
                      width: 32px;
                      overflow: hidden;
                      text-align: center;
                      font-size: 18px;
                      box-sizing: border-box;
                      overflow: hidden;
                    "
                  >
                    <div
                      @mouseleave="hideTip"
                      @mouseenter.stop="
                        showTip(
                          $event,
                          item.disabled
                            ? KT('disabled')
                            : item.lastUpdate === null
                            ? KT('new')
                            : item.status === 'online'
                            ? KT('online')
                            : item.status === 'offline'
                            ? KT('offline')
                            : KT('unknown')
                        )
                      "
                    >
                      <span v-if="item.lastUpdate === null"
                        ><i
                          style="color: var(--el-color-info)"
                          class="fas fa-question-circle"
                        ></i
                      ></span>
                      <span v-else-if="item.status === 'online'"
                        ><i
                          style="color: var(--el-color-success)"
                          class="fas fa-check-circle"
                        ></i
                      ></span>
                      <span v-else-if="item.status === 'offline'"
                        ><i
                          style="color: var(--el-color-danger)"
                          class="fas fa-exclamation-circle"
                        ></i
                      ></span>
                      <span v-else
                        ><i
                          class="fas fa-question-circle"
                          style="color: var(--el-color-warning)"
                        ></i
                      ></span>
                    </div>
                  </div>
                  <div
                    class="name"
                    style="
                      width: 90px;
                      box-sizing: border-box;
                      overflow: hidden;
                      white-space: nowrap;
                      text-align: center;
                    "
                  >
                    {{ getLastUpdated(item.lastUpdate, now) }}
                  </div>
                  <div
                    v-if="position"
                    class="icons"
                    style="width: 178px !important; flex: none"
                  >
                    <div
                      v-if="position.attributes.alarm"
                      @mouseleave="hideTip"
                      @mouseenter.stop="showAlarmTip($event, item.id)"
                      :style="{ color: 'var(--el-color-danger)' }"
                    >
                      <i class="fas fa-exclamation-triangle"></i>
                    </div>
  
                    <div
                      v-else
                      @mouseleave="hideTip"
                      @mouseenter.stop="showTip($event, KT('alarms.none'))"
                      :style="{ color: 'var(--el-color-info)' }"
                    >
                      <i class="fas fa-exclamation-triangle"></i>
                    </div>
  
                    <div
                      v-if="position.attributes['driverUniqueId']"
                      @mouseleave="hideTip"
                      @mouseenter.stop="showDriverTip($event, item.id)"
                      :style="{ color: 'var(--el-color-success)' }"
                    >
                      <i class="far fa-id-card"></i>
                    </div>
  
                    <div
                      v-else-if="
                        position.attributes['isQrLocked'] &&
                        position.attributes['isQrLocked'] == true
                      "
                      @mouseleave="hideTip"
                      @mouseenter.stop="
                        showTip($event, KT('device.noDriverLocked'))
                      "
                      :style="{ color: 'var(--el-color-danger)' }"
                    >
                      <i class="far fa-id-card"></i>
                    </div>
  
                    <div
                      v-else
                      @mouseleave="hideTip"
                      @mouseenter.stop="showTip($event, KT('device.noDriver'))"
                      :style="{ color: 'var(--el-color-info)' }"
                    >
                      <i class="far fa-id-card"></i>
                    </div>
  
                    <div
                      @mouseleave="hideTip"
                      @mouseenter.stop="showTip($event, KT('device.ignitionOn'))"
                      v-if="position.attributes.ignition === true"
                      :style="{ color: 'var(--el-color-success)' }"
                    >
                      <i class="fas fa-key"></i>
                    </div>
  
                    <div
                      @mouseleave="hideTip"
                      @mouseenter.stop="showTip($event, KT('device.ignitionOff'))"
                      v-else-if="position.attributes.ignition === false"
                      :style="{ color: 'var(--el-color-danger)' }"
                    >
                      <i class="fas fa-key"></i>
                    </div>
  
                    <div
                      @mouseleave="hideTip"
                      @mouseenter.stop="showTip($event, KT('unknown'))"
                      v-else
                      :style="{ color: 'var(--el-color-info)' }"
                    >
                      <i class="fas fa-key"></i>
                    </div>
  
                    <div
                      @mouseleave="hideTip"
                      @mouseenter.stop="showTip($event, KT('device.blocked'))"
                      v-if="position.attributes.blocked === true"
                      :style="{ color: 'var(--el-color-danger)' }"
                    >
                      <i class="fas fa-lock"></i>
                    </div>
  
                    <div
                      @mouseleave="hideTip"
                      @mouseenter.stop="showTip($event, KT('device.unblocked'))"
                      v-else-if="position.attributes.blocked === false"
                      :style="{ color: 'var(--el-color-success)' }"
                    >
                      <i class="fas fa-lock-open"></i>
                    </div>
  
                    <div
                      v-else
                      @mouseleave="hideTip"
                      @mouseenter.stop="showTip($event, KT('unknown'))"
                      :style="{ color: 'var(--el-color-info)' }"
                    >
                      <i class="fas fa-lock-open"></i>
                    </div>
  
                    <template
                      v-if="
                        store.state.server.isPlus &&
                        store.getters.advancedPermissions(9)
                      "
                    >
                      <div
                        v-if="store.getters['geofences/isAnchored'](item.id)"
                        @mouseleave="hideTip"
                        @mouseenter.stop="
                          showTip($event, KT('device.anchorEnabled'))
                        "
                        :style="{ color: 'var(--el-color-warning)' }"
                      >
                        <i class="fas fa-anchor"></i>
                      </div>
  
                      <div
                        v-else
                        @mouseleave="hideTip"
                        @mouseenter.stop="
                          showTip($event, KT('device.anchorDisabled'))
                        "
                        :style="{ color: 'var(--el-color-info)' }"
                      >
                        <i class="fas fa-anchor"></i>
                      </div>
                    </template>
  
                    <div
                      v-if="position.attributes.motion"
                      @mouseleave="hideTip"
                      @mouseenter.stop="showTip($event, KT('device.moving'))"
                      :style="{ color: 'var(--el-color-primary)' }"
                    >
                      <i class="fas fa-angle-double-right"></i>
                    </div>
  
                    <div
                      v-else-if="position.attributes.stoppedTime"
                      @mouseleave="hideTip"
                      @mouseenter.stop="showStopped($event, item.id)"
                      :style="{ color: 'var(--el-color-info)' }"
                    >
                      <i class="fas fa-angle-double-right"></i>
                    </div>
                    <div
                      v-else
                      @mouseleave="hideTip"
                      @mouseenter.stop="showTip($event, KT('device.stoped'))"
                      :style="{ color: 'var(--el-color-info)' }"
                    >
                      <i class="fas fa-angle-double-right"></i>
                    </div>
                  </div>
                  <div
                    v-else
                    class="icons"
                    style="flex: none; width: 178px !important"
                  >
                    <div style="color: var(--el-text-color-disabled-base)">
                      <i>
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          style="opacity: 0.3"
                          height="15px"
                          viewBox="0 0 640 512"
                        >
                          <path
                            d="M154 95.42C187.3 38.35 249.2 0 320 0C426 0 512 85.96 512 192C512 230.7 489 282.8 459 334.5L630.8 469.1C641.2 477.3 643.1 492.4 634.9 502.8C626.7 513.2 611.6 515.1 601.2 506.9L9.196 42.89C-1.236 34.71-3.065 19.63 5.112 9.196C13.29-1.236 28.37-3.065 38.81 5.112L154 95.42zM257.8 176.8L349.6 248.7C370.1 238 384 216.7 384 192C384 156.7 355.3 128 320 128C289.9 128 264.7 148.8 257.8 176.8zM296.3 499.2C245.9 436.2 132.3 285.2 128.1 196.9L406.2 416.1C382.7 449.5 359.9 478.9 343.7 499.2C331.4 514.5 308.6 514.5 296.3 499.2V499.2z"
                          />
                        </svg>
                      </i>
                      <span>{{ KT("device.noPosition") }}</span>
                    </div>
                  </div>
                </div>
              </template>
            </paginate>

          </div>
        </div>
      </div>
    </div>
</template>

<script setup>
import "element-plus/es/components/button/style/css";
import "element-plus/es/components/input/style/css";
import "element-plus/es/components/message/style/css";
import "element-plus/es/components/message-box/style/css";
import "element-plus/es/components/notification/style/css";
import Paginate from "../components/base/Paginate.vue";

import { ElButton, ElInput } from "element-plus";

import { ElMessage } from "element-plus/es/components/message";
import { ElMessageBox } from "element-plus/es/components/message-box";

import { ref, computed, inject, onMounted, onUnmounted, watch } from "vue";
import { useStore } from "vuex";

import KT from "../tarkan/func/kt.js";

const store = useStore();

const markerContext = inject("markerContext");
const markerClick = inject("markerClick");

const filteredDevices = ref([]);

const query = ref(window.localStorage.getItem("query") || "");
const editDeviceRef = inject("edit-device");
const editGroupRef = inject("edit-group");
const currentPage = ref(1);

const now = ref(0);

const deviceLimitExceded = () => {};

const showTip = (evt, text) => {
  window.$showTip(evt, text);
};

const hideTip = (evt, text) => {
  window.$hideTip(evt, text);
};

const showAlarmTip = ($event, deviceId) => {
  const position = store.getters["devices/getPosition"](deviceId);
  showTip($event, KT("alarms." + position.attributes.alarm));
};

const showStopped = ($event, deviceId) => {
  const position = store.getters["devices/getPosition"](deviceId);

  showTip(
    $event,
    "Parado há " + getLastUpdated(position.attributes.stoppedTime, now.value)
  );
};

const showDriverTip = ($event, deviceId) => {
  const position = store.getters["devices/getPosition"](deviceId);

  if (position.attributes["driverUniqueId"]) {
    const driver = store.getters["drivers/getDriverByUniqueId"](
      position.attributes["driverUniqueId"]
    );
    if (driver) {
      showTip($event, driver.name);
    } else {
      showTip($event, position.attributes["driverUniqueId"]);
    }
  }
};

const validStates = [
  "motion",
  "anchor",
  "locked",
  "ignition",
  "driver",
  "alert",
];

const setSortingByState = () => {
  const tmp = store.getters["devices/sorting"].split("-");

  if (tmp[0] === "state") {
    let idx = validStates.findIndex((i) => i === tmp[1]) + 1;
    if (idx > validStates.length - 1) {
      store.dispatch("devices/setSorting", "name");
    } else {
      store.dispatch("devices/setSortingState", "state-" + validStates[idx]);
    }
  } else {
    store.dispatch("devices/setSortingState", "state-motion");
  }
};

onMounted(() => {
  setInterval(() => {
    now.value = new Date();
  }, 3000);

  filteredDevices.value = recalcDevices();
});

watch(query, () => {
  scheduleRecalc();
});

watch(
  () => store.getters["devices/getOrderedDevices"].length,
  () => {
    scheduleRecalc();
  }
);

watch(
  () => store.getters["devices/sorting"],
  () => {
    scheduleRecalc();
  }
);

watch(
  () => filteredDevices.value.length,
  () => {
    const totalPages = Math.ceil(filteredDevices.value.length / 15);
    if (currentPage.value > totalPages) {
      currentPage.value = 1;
    }
  }
);

const getLastUpdated = (t, tt) => {
  tt = new Date();

  if (t === null) {
    return KT("new");
  }

  const diff = Math.round(
    (new Date(tt).getTime() - new Date(t).getTime()) / 1000
  );

  if (diff < 0) {
    return KT("now");
  } else if (diff > 86400) {
    const dias = Math.round(diff / 86400);

    return dias + " " + KT("days");
  } else if (diff > 3600) {
    const horas = Math.round(diff / 3600);

    return horas + " " + KT("hours");
  } else if (diff > 60) {
    const minutos = Math.round(diff / 60);

    return minutos + " " + KT("minutes");
  } else {
    return KT("lessMinute");
  }
};

const doAnchorGroup = (group) => {
  const devices = store.getters["devices/byGroupId"](group.id);

  if (!store.getters["geofences/isAnchoredGroup"](group.id)) {
    ElMessageBox.confirm(
      "Deseja realmente criar uma ancora para os " +
        devices.length +
        ' dispositivos do grupo "' +
        group.name +
        '"?',
      "Warning",
      {
        confirmButtonText: "OK",
        cancelButtonText: "Cancelar",
        type: "warning",
      }
    ).then(() => {
      devices.forEach(async (d) => {
        await store.dispatch("geofences/setupAnchor", {
          id: d,
          groupId: group.id,
        });
      });

      ElMessage.success("As ancoras foram definidas com sucesso.");
    });
  } else {
    ElMessageBox.confirm(
      "Deseja realmente removera a ancora para os " +
        devices.length +
        ' dispositivos do grupo "' +
        group.name +
        '"?',
      "Warning",
      {
        confirmButtonText: "OK",
        cancelButtonText: "Cancelar",
        type: "warning",
      }
    ).then(() => {
      const anchors = store.getters["geofences/anchorListByGroup"](group.id);

      anchors.forEach(async (a) => {
        store.dispatch("geofences/delete", a.id).then(() => {});
      });

      ElMessage.success("As acoras foram removidas com sucesso.");
    });
  }
};

let prevVisible = new Map();

const recalcDevices = () => {
  window.localStorage.setItem("query", query.value);

  const r = query.value
    .toLowerCase()
    .matchAll(
      /(.*?):(?<sinal>\+|-|=)(?<tempo>\d*) (?<filtro>dias|minutos|horas|segundos)/gi
    );
  const s = r.next();

  let groupList = [];

  store.state.groups.groupList.forEach((g) => {
    if (String(g.name).toLowerCase().match(query.value.toLowerCase())) {
      groupList.push(g.id);
    }
  });

  let tmp = [];
  const nextVisible = new Map();

  store.getters["devices/getOrderedDevices"].forEach((dk) => {
    const d = store.getters["devices/getDevice"](dk);
    if (!d) return;

    let visible = false;

    if (s.value) {
      if (s.value.groups.filtro === "dias") {
        const df = parseInt(s.value.groups.tempo) * 86400;
        const diff = Math.round(
          (new Date().getTime() - new Date(d.lastUpdate).getTime()) / 1000
        );

        if (s.value.groups.sinal === "+" && diff >= df) {
          visible = true;
        } else if (s.value.groups.sinal === "-" && diff <= df) {
          visible = true;
        }
      }
    }

    for (let k of Object.keys(d)) {
      if (
        k === "status" &&
        String(d[k])
          .toLowerCase()
          .replace("unknown", "desconhecido")
          .match(query.value.toLowerCase())
      ) {
        visible = true;
      } else if (String(d[k]).toLowerCase().match(query.value.toLowerCase())) {
        visible = true;
      }
    }

    for (let k of Object.keys(d.attributes || {})) {
      if (
        d.attributes[k] &&
        d.attributes[k]
          .toString()
          .toLowerCase()
          .match(query.value.toLowerCase())
      ) {
        visible = true;
      }
    }

    if (!visible && d.groupId !== 0 && groupList.includes(d.groupId)) {
      visible = true;
    }

    nextVisible.set(d.id, visible);
    if (visible) {
      tmp.push(d);
    }
  });

  // diff: só atualiza o ícone dos devices cuja visibilidade mudou
  // (evita 5K chamadas addToMap/remove a cada tecla digitada)
  const firstRun = prevVisible.size === 0;
  const allIds = new Set([...prevVisible.keys(), ...nextVisible.keys()]);
  allIds.forEach((id) => {
    const was = firstRun ? true : !!prevVisible.get(id);
    const now = !!nextVisible.get(id);

    if (was !== now) {
      const dd = store.getters["devices/getDevice"](id);
      if (dd && dd.icon) {
        if (now) {
          dd.icon.addToMap();
        } else {
          dd.icon.remove();
        }
      }
    }
  });

  prevVisible = nextVisible;
  return tmp;
};

let recalcTimer = null;

const scheduleRecalc = () => {
  clearTimeout(recalcTimer);
  recalcTimer = setTimeout(() => {
    filteredDevices.value = recalcDevices();
  }, 120);
};

onUnmounted(() => {
  clearTimeout(recalcTimer);
});

const groupedDevices = computed(() => {
  let showGroups = store.getters["mapPref"]("groups");

  if (showGroups) {
    let tmp = {};
    const groups = store.state.groups.groupList;

    filteredDevices.value.forEach((device) => {
      if (!groups.find((g) => g.id === device.groupId)) {
        if (!tmp[0]) {
          tmp[0] = [];
        }
        tmp[0].push(device);
      } else {
        if (!tmp[device.groupId]) {
          tmp[device.groupId] = [];
        }
        tmp[device.groupId].push(device);
      }
    });

    let list = [];

    list.push({ id: 0, name: "Sem Grupo", devices: tmp[0] || [], open: true });
    groups.forEach((g) => {
      if (tmp[g.id] && tmp[g.id].length > 0) {
        list.push({ id: g.id, name: g.name, devices: tmp[g.id], open: false });
      }
    });

    return list;
  } else {
    return [{ id: -1, name: "", devices: filteredDevices.value, open: true }];
  }
});
</script>

<style scoped>
.device {
  border-bottom: var(--el-border-color-light) 1px solid;
  display: flex;
  flex-direction: row;
  text-align: center;
  cursor: pointer;
  margin-right: -1px;
}

.deviceHead {
  border-bottom: var(--el-border-color-light) 1px solid;
  display: flex;
  flex-direction: row;
  text-align: center;
  cursor: pointer;
  margin-right: -1px;
  background: var(--el-color-info-light);
}

.device:hover {
  background: var(--el-color-primary-light-9);
}

.device .name,
.deviceHead .name {
  font-size: 12px;
  padding: 7px;
  text-align: left;
  line-height: 14px;
  font-weight: 800;
  border-right: var(--el-border-color-light) 1px dotted;
  width: 60%;
}

.icons {
  display: flex;
  justify-content: center;
  flex-direction: row-reverse;
  flex: 1;
  align-items: center;
  font-size: 11px;
}

.icons div {
  display: flex;
  justify-content: center;
  align-items: center;
  flex: 1;
  border-right: var(--el-border-color-light) 1px dotted;
  font-size: 11px;
}
.icons div i {
  font-size: 14px;
}

.icons div:first-child {
  border-right: none;
}

.icons div span {
  display: flex;
  padding: 2px;
  padding-left: 5px;
}

.subtitle {
  margin-top: 20px;
  font-weight: bold;
  font-size: 14px;
  text-transform: uppercase;
  color: var(--el-text-color-primary);
}

.subtitle i {
  font-size: 12px;
  margin-right: 3px;
}

.isDisabled {
  opacity: 0.4;
}
</style>
