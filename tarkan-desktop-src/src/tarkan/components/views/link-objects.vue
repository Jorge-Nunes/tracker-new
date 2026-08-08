<template>
  <el-dialog :lock-scroll="true" :top="'50px'" v-model="show" title="Teste">
      <template v-slot:title>
          <div style="border-bottom: #e0e0e0 1px solid; padding: 20px">
              <div class="modal-title" style="display: flex; width: calc(100% - 50px)">
                  <el-input v-model="query" :placeholder="KT(search)" style="--el-input-border-radius: 5px; margin-right: 5px"></el-input>
              </div>
          </div>
      </template>

      <template v-slot:footer>
          <div style="border-top: #e0e0e0 1px solid; padding: 20px; display: flex; gap: 10px">
              <el-button type="success" :disabled="checkedIds.length === 0" @click="bulkLink(true)">
                  <i class="fas fa-link"></i> Vincular selecionados ({{ checkedIds.length }})
              </el-button>
              <el-button type="danger" :disabled="checkedIds.length === 0" @click="bulkLink(false)">
                  <i class="fas fa-unlink"></i> Desvincular selecionados
              </el-button>
          </div>
      </template>

      <div class="itm" style="display: flex; background: #eeeeee">
          <div style="width: 50px; padding: 10px; font-size: 12px">
              <el-checkbox :model-value="allChecked" :indeterminate="someChecked" @change="(v) => toggleSelectAll(v)"></el-checkbox>
          </div>
          <div @click="toggleSorting('check')" style="width: 50px; padding: 10px; font-size: 12px">
              <span v-if="orderFlag === 'check-asc'">
                  <i class="fas fa-sort-alpha-down"></i>
              </span>
              <span v-else-if="orderFlag === 'check-desc'">
                  <i class="fas fa-sort-alpha-up"></i>
              </span>
              <span v-else>
                  <i style="color: silver" class="fas fa-sort-alpha-down"></i>
              </span>
          </div>
          <div @click="toggleSorting('id')" style="width: 30px; text-align: center; padding: 10px; font-size: 12px">
              Id

              <span v-if="orderFlag === 'id-asc'">
                  <i class="fas fa-sort-alpha-down"></i>
              </span>
              <span v-else-if="orderFlag === 'id-desc'">
                  <i class="fas fa-sort-alpha-up"></i>
              </span>
              <span v-else>
                  <i style="color: silver" class="fas fa-sort-alpha-down"></i>
              </span>
          </div>
          <div @click="toggleSorting('name')" style="flex: 1; padding: 10px; font-size: 12px; text-align: center">
              Nome

              <span v-if="orderFlag === 'name-asc'">
                  <i class="fas fa-sort-alpha-down"></i>
              </span>
              <span v-else-if="orderFlag === 'name-desc'">
                  <i class="fas fa-sort-alpha-up"></i>
              </span>
              <span v-else>
                  <i style="color: silver" class="fas fa-sort-alpha-down"></i>
              </span>
          </div>
      </div>
      <div style="height: calc(100vh - 300px); overflow: hidden; overflow-y: scroll">
        <paginate :items="filteredObjects" :per-page="10" v-model="currentPage">
              <template v-slot="{item, index}">
                <div class="itm" :class="{ tr1: index % 2, tr2: !(index % 2) }" style="display: flex">
                  <div style="width: 50px; padding: 10px; font-size: 14px">
                      <el-checkbox :model-value="isChecked(item.id)" @change="(v) => toggleChecked(item.id, v)"></el-checkbox>
                  </div>
                  <div style="width: 50px; padding: 10px; font-size: 14px">
                      <el-switch :model-value="isLinked(item.id)" @change="(v) => changeLink(item, v)" :size="'large'"></el-switch>
                  </div>
                  <div style="width: 30px; text-align: center; padding: 10px; font-size: 14px">
                      {{ item.id }}
                  </div>
                  <template v-if="objectType === 'notifications' && item.attributes && !item.attributes['description']">
                      <div style="flex: 1; padding: 10px; font-size: 14px; text-align: center">{{ KT('notification.types.' + item.type) }}</div>
                      <div style="flex: 1; padding: 10px; font-size: 14px; text-align: center">
                          <template v-if="item.notificators">
                              <span class="tblItem" v-for="(a, b) in item.notificators.split(',')" :key="b">{{ KT('notification.channels.' + a, a) }}</span>
                          </template>
                      </div>
                      <div style="flex: 1; padding: 10px; font-size: 14px; text-align: center">
                          <template v-if="item.attributes && item.attributes['alarms']">
                              <span class="tblItem" v-for="(a, b) in item.attributes['alarms'].split(',')" :key="b">{{ KT('alarms.' + a, a) }}</span>
                          </template>
                      </div>
                  </template>
                  <div v-else-if="objectType === 'devices'" style="flex: 1; padding: 10px; font-size: 14px; text-align: center"
                      >{{ item.name || item.description || (item.attributes && item.attributes['description']) }} | {{ (item.attributes && item.attributes['placa']) || '' }}</div
                  >
                  <div v-else style="flex: 1; padding: 10px; font-size: 14px; text-align: center">{{
                      item.name || item.description || (item.attributes && item.attributes['description'])
                  }}</div>
              </div>
              </template>
          </paginate>
      </div>
  </el-dialog>
</template>

<script setup>
import { ref, defineExpose, provide, computed } from 'vue';
import Paginate from '../../../components/base/Paginate.vue';

import 'element-plus/es/components/input/style/css';
import 'element-plus/es/components/button/style/css';
import 'element-plus/es/components/switch/style/css';
import 'element-plus/es/components/checkbox/style/css';
import 'element-plus/es/components/dialog/style/css';

import { ElDialog, ElSwitch, ElInput, ElCheckbox, ElButton, ElMessage } from 'element-plus';

const currentPage = ref(1);
const query = ref('');
const show = ref(false);
const search = ref('search');

const objectType = ref(null);

const selection = ref({});
const keyType = ref(null);
const availableObjects = ref([]);
const linkedIds = ref([]);
const checkedIds = ref([]);

const orderFlag = ref('name-asc');

const toggleSorting = (s) => {
  const p = orderFlag.value.split('-');

  if (p[0] === s) {
      orderFlag.value = s + '-' + (p[1] === 'asc' ? 'desc' : 'asc');
  } else {
      orderFlag.value = s + '-asc';
  }
};

import KT from '../../func/kt';

const isLinked = (id) => {
  return linkedIds.value.includes(id);
};

const isChecked = (id) => {
  return checkedIds.value.includes(id);
};

const toggleChecked = (id, state) => {
  if (state) {
      if (!checkedIds.value.includes(id)) {
          checkedIds.value.push(id);
      }
  } else {
      checkedIds.value = checkedIds.value.filter((f) => f !== id);
  }
};

const allChecked = computed(() => {
  return filteredObjects.value.length > 0 && filteredObjects.value.every((o) => checkedIds.value.includes(o.id));
});

const someChecked = computed(() => {
  return !allChecked.value && filteredObjects.value.some((o) => checkedIds.value.includes(o.id));
});

const toggleSelectAll = (state) => {
  const ids = filteredObjects.value.map((o) => o.id);

  if (state) {
      ids.forEach((id) => {
          if (!checkedIds.value.includes(id)) {
              checkedIds.value.push(id);
          }
      });
  } else {
      checkedIds.value = checkedIds.value.filter((f) => !ids.includes(f));
  }
};

// carrega os ids já vinculados via GET /permissions (jeito Traccar 5+/6.x;
// o filtro legado /devices?userId=... foi removido na v6)
const loadLinked = () => {
  let qs = JSON.parse(JSON.stringify(selection.value));
  qs[keyType.value] = 0;

  window.$traccar.getPermissions(qs).then(({ data }) => {
      const fallbackKey = keyType.value.toLowerCase();

      linkedIds.value = (data || []).map((p) => {
          return (p[keyType.value] !== undefined) ? p[keyType.value] : p[fallbackKey];
      });
  }).catch(() => {
      linkedIds.value = [];
  });
};

const changeLink = (obj, state) => {
  let tmp = JSON.parse(JSON.stringify(selection.value));
  tmp[keyType.value] = obj.id;

  if (state) {
      window.$traccar.linkObjects(tmp).then(() => {
          if (!linkedIds.value.includes(obj.id)) {
              linkedIds.value.push(obj.id);
          }
      }).catch(() => {
          ElMessage.error('Erro ao vincular');
          loadLinked();
      });
  } else {
      window.$traccar.unlinkObjects(tmp).then(() => {
          linkedIds.value = linkedIds.value.filter((id) => id !== obj.id);
      }).catch(() => {
          ElMessage.error('Erro ao desvincular');
          loadLinked();
      });
  }
};

const bulkLink = (link) => {
  if (checkedIds.value.length === 0) {
      return;
  }

  // Traccar 6.x nao deduplica em POST/DELETE /permissions/bulk:
  // envia apenas itens que realmente mudam de estado
  const ids = link
      ? checkedIds.value.filter((id) => !linkedIds.value.includes(id))
      : checkedIds.value.filter((id) => linkedIds.value.includes(id));

  if (ids.length === 0) {
      checkedIds.value = [];
      ElMessage.info('Nenhum item pendente de alteração');
      return;
  }

  const permissions = ids.map((id) => {
      let tmp = JSON.parse(JSON.stringify(selection.value));
      tmp[keyType.value] = id;
      return tmp;
  });

  const done = () => {
      if (link) {
          ids.forEach((id) => {
              if (!linkedIds.value.includes(id)) {
                  linkedIds.value.push(id);
              }
          });
      } else {
          linkedIds.value = linkedIds.value.filter((id) => !ids.includes(id));
      }
      checkedIds.value = [];
  };

  if (link) {
      window.$traccar.linkObjectsBulk(permissions).then(done).catch(() => {
          checkedIds.value = [];
          ElMessage.error('Erro ao vincular selecionados');
          loadLinked();
      });
  } else {
      window.$traccar.unlinkObjectsBulk(permissions).then(done).catch(() => {
          checkedIds.value = [];
          ElMessage.error('Erro ao desvincular selecionados');
          loadLinked();
      });
  }
};

const sortFunc = (a, b, p) => {
  if (p[0] === 'check') {
      const aa = isLinked(a.id);
      const bb = isLinked(b.id);

      if (p[1] === 'asc') {
          return aa === true && bb === false ? 1 : -1;
      } else {
          return aa === true && bb === false ? -1 : 1;
      }
  } else if (p[0] === 'name') {
      const aa = a.name || a.description || (a.attributes && a.attributes['description']) || '';
      const bb = b.name || b.description || (b.attributes && b.attributes['description']) || '';

      if (p[1] === 'asc') {
          return aa.localeCompare(bb);
      } else {
          const t = aa.localeCompare(bb);
          return t === 1 ? -1 : t === -1 ? 1 : 0;
      }
  } else if (!a[p[0]] || !b[p[0]]) {
      return p[1] === 'desc' ? 1 : -1;
  } else if (a[p[0]] > b[p[0]]) {
      return p[1] === 'asc' ? 1 : -1;
  } else if (a[p[0]] < b[p[0]]) {
      return p[1] === 'desc' ? 1 : -1;
  } else {
      return 0;
  }
};

const filteredObjects = computed(() => {
  const p = orderFlag.value.split('-');

  if (query.value.length < 3) {
      return [...availableObjects.value].sort((a, b) => {
          return sortFunc(a, b, p);
      });
  } else {
      return availableObjects.value
          .filter((f) => {
              for (let k of Object.keys(f)) {
                  if (String(f[k]).toLowerCase().match(query.value.toLowerCase())) {
                      return true;
                  }
              }

              for (let k of Object.keys(f.attributes || {})) {
                  if (String(f.attributes[k]).toLowerCase().match(query.value.toLowerCase())) {
                      return true;
                  }
              }

              return false;
          })
          .sort((a, b) => {
              return sortFunc(a, b, p);
          });
  }
});

const showObjects = (params) => {
  selection.value = {};

  objectType.value = params.type;
  checkedIds.value = [];
  linkedIds.value = [];

  selection.value[Object.keys(params)[0]] = params[Object.keys(params)[0]];

  if (params.type === 'geofences') {
      keyType.value = 'geofenceId';
      search.value = 'geofence.search';

      window.$traccar.getGeofences({ all: true }).then(({ data }) => {
          availableObjects.value = data;
      });
  } else if (params.type === 'devices') {
      keyType.value = 'deviceId';
      search.value = 'device.search';

      window.$traccar.getDevices({ all: true }).then(({ data }) => {
          let tmp = [];

          data.forEach((d) => {
              if (!(d.uniqueId.split('-').length == 3 && d.uniqueId.split('-')[0] === 'deleted')) {
                  tmp.push(d);
              }
          });

          availableObjects.value = tmp;
      });
  } else if (params.type === 'commands') {
      keyType.value = 'commandId';
      search.value = 'command.search';

      window.$traccar.getSavedCommands({ all: true }).then(({ data }) => {
          availableObjects.value = data;
      });
  } else if (params.type === 'maintence') {
      keyType.value = 'maintenanceId';
      search.value = 'maintenance.search';

      window.$traccar.getMaintenance({ all: true }).then(({ data }) => {
          availableObjects.value = data;
      });
  } else if (params.type === 'attributes') {
      keyType.value = 'attributeId';
      search.value = 'attribute.search';

      window.$traccar.getComputedAttributes({ all: true }).then(({ data }) => {
          availableObjects.value = data;
      });
  } else if (params.type === 'calendars') {
      keyType.value = 'calendarId';
      search.value = 'calendar.search';

      window.$traccar.getCalendars({ all: true }).then(({ data }) => {
          availableObjects.value = data;
      });
  } else if (params.type === 'notifications') {
      keyType.value = 'notificationId';
      search.value = 'notification.search';

      window.$traccar.getNotifications({ all: true }).then(({ data }) => {
          availableObjects.value = data;
      });
  } else if (params.type === 'users') {
      keyType.value = 'managedUserId';
      search.value = 'user.search';

      window.$traccar.getUsers({ all: true }).then(({ data }) => {
          availableObjects.value = data;
      });
  } else if (params.type === 'groups') {
      keyType.value = 'groupId';
      search.value = 'group.search';

      window.$traccar.getGroups({ all: true }).then(({ data }) => {
          availableObjects.value = data;
      });
  } else if (params.type === 'drivers') {
      keyType.value = 'driverId';
      search.value = 'driver.search';

      window.$traccar.getDrivers({ all: true }).then(({ data }) => {
          availableObjects.value = data;
      });
  }

  loadLinked();

  show.value = true;
};

provide('showObjects', showObjects);

defineExpose({
  showObjects,
});
</script>

<style>
.itm {
  border-bottom: silver 1px dotted;
}

.itm div {
  border-right: silver 1px dotted;
}

.tr1 {
  background: #f3f3f3;
}

.tr2 {
  background: #f8f8f8;
}

.selected {
  background: rgba(5, 167, 227, 0.05) !important;
}

.itm div:last-child {
  border-right: none;
}

.el-select.el-select--large {
  width: 100%;
}

.el-dialog__footer {
  margin-top: 0px;
}

.el-tabs__nav-wrap {
  padding-left: 20px;
  padding-right: 20px;
}

.el-tabs__content {
  padding-left: 20px;
  padding-right: 20px;
}

.danger {
  --el-button-text-color: var(--el-color-danger) !important;
  --el-button-bg-color: #fef0f0 !important;
  --el-button-border-color: #fbc4c4 !important;
  --el-button-hover-text-color: var(--el-color-white) !important;
  --el-button-hover-bg-color: var(--el-color-danger) !important;
  --el-button-hover-border-color: var(--el-color-danger) !important;
  --el-button-active-text-color: var(--el-color-white) !important;
  --el-button-active-border-color: var(--el-color-danger) !important;
}

.tblItem:after {
  content: ', ';
}

.tblItem:last-child:after {
  content: '';
}
</style>
