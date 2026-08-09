<template>
  <edit-calendar ref="editCalendarRef"></edit-calendar>
  <el-dialog :lock-scroll="true" :top="'50px'" v-model="show">
    <template v-slot:title>
      <div style="border-bottom: #e0e0e0 1px solid; padding: 20px">
        <div class="modal-title">{{ KT("calendar.title") }}</div>
      </div>
    </template>
    <template v-slot:footer>
      <div
        style="
          border-top: #e0e0e0 1px solid;
          padding: 20px;
          display: flex;
          justify-content: flex-start;
        "
      >
        <el-button
          v-if="store.getters.advancedPermissions(89)"
          @mouseleave="hideTip"
          @mouseenter.stop="showTip($event, KT('calendar.add'))"
          type="primary"
          @click="editCalendarRef.newCalendar()"
          ><i class="fas fa-user-plus"></i
        ></el-button>

        <el-button
          v-if="store.getters.advancedPermissions(91)"
          @mouseleave="hideTip"
          @mouseenter.stop="showTip($event, KT('calendar.remove'))"
          type="danger"
          :plain="selected === 0"
          @click="doDelete()"
        >
          <i class="fas fa-user-minus"></i>
        </el-button>

        <el-button
          v-if="store.getters.advancedPermissions(90)"
          @mouseleave="hideTip"
          @mouseenter.stop="showTip($event, KT('calendar.edit'))"
          type="warning"
          :plain="selected === 0"
          @click="editCalendarRef.editCalendar(selected)"
        >
          <i class="fas fa-user-edit"></i>
        </el-button>
      </div>
    </template>
    <div class="itm" style="display: flex; background: #eeeeee">
      <div style="width: 250px; padding: 10px; font-size: 12px">
        {{ KT("calendar.name") }}
      </div>
      <div style="flex: 1; padding: 10px; font-size: 12px; text-align: center">
        {{ KT("calendar.data") }}
      </div>
    </div>
    <div
      style="height: calc(100vh - 300px); overflow: hidden; overflow-y: scroll"
    >
    <Paginate :items="store.state.calendars.calendarList" :per-page="15" v-model="currentPage">
      <template #default="{ item, index }">
        <div
          class="itm"
          @click="selected = item.id"
          :class="{ tr1: index % 2, tr2: !(index % 2), selected: selected === item.id }"
          style="display: flex"
        >
          <div style="width: 250px; padding: 10px; font-size: 14px">
            {{ item.name }}
          </div>
          <div
            style="
              flex: 1;
              padding: 10px;
              font-size: 14px;
              text-align: center;
              overflow: hidden;
              white-space: nowrap;
            "
          >
            <span
              class="calendars"
              v-for="(r, kk) in parseCalendar(item.data)"
              :key="index + '-' + kk"
            >
              <template v-if="r.rules.frequency === 'DAILY'">
                Todos os dias
              </template>
              <template v-else-if="r.rules.frequency === 'WEEKLY'">
                Semanalmente
                <span
                  class="day"
                  v-for="d in r.rules.days"
                  :key="index + '-' + kk + '-' + d"
                  >{{ KT("weekDays." + d) }}</span
                >
              </template>
              <template v-else-if="r.rules.frequency === 'MONTHLY'">
                Todo dia {{ r.rules.monthDay }}
              </template>
              <template v-else-if="r.rules.frequency === 'YEARLY'">
                Anualmente
              </template>
              <template v-else>
                {{ new Date(r.startDateTime).toLocaleString() }} até
                {{ new Date(r.endDateTime).toLocaleString() }}
              </template>
  
              <template v-if="r.rules.frequency">
                ás {{ new Date(r.startDateTime).toLocaleTimeString() }} até
                {{ new Date(r.endDateTime).toLocaleTimeString() }}
              </template>
            </span>
          </div>
        </div>

      </template>
    </Paginate>
    </div>
  </el-dialog>
</template>

<script setup>
import { ref, defineExpose } from "vue";
import { useStore } from "vuex";
//import {ElMessage, ElMessageBox, ElNotification} from "element-plus";

import "element-plus/es/components/input/style/css";
import "element-plus/es/components/button/style/css";
import "element-plus/es/components/switch/style/css";
import "element-plus/es/components/select/style/css";
import "element-plus/es/components/option/style/css";
import "element-plus/es/components/dialog/style/css";
import "element-plus/es/components/tab-pane/style/css";
import "element-plus/es/components/tabs/style/css";
import Paginate from "../../../components/base/Paginate.vue";

import { ElDialog, ElButton } from "element-plus";

import { ElMessage } from "element-plus/es/components/message";
import { ElMessageBox } from "element-plus/es/components/message-box";
import { ElNotification } from "element-plus/es/components/notification";

import EditCalendar from "./edit-calendar";
import KT from "../../func/kt";
import { parseIcs } from "../../func/ics";

const store = useStore();
const selected = ref(0);
const show = ref(false);

const editCalendarRef = ref(null);

const showTip = (evt, text) => {
  window.$showTip(evt, text);
};

const hideTip = (evt, text) => {
  window.$hideTip(evt, text);
};

const parseCalendar = (data) => {
  return parseIcs(atob(data));
};

const doDelete = () => {
  if (selected.value === 0) {
    ElMessage.error(KT("calendar.selectError"));
    return false;
  }

  const calendar = store.getters["calendars/getCalendarById"](selected.value);

  ElMessageBox.confirm(KT("calendar.confirmDelete", calendar), KT("danger"), {
    confirmButtonText: KT("delete"),
    confirmButtonClass: "danger",
    cancelButtonText: KT("cancel"),
    type: "warning",
  })
    .then(() => {
      store
        .dispatch("calendars/deleteCalendar", selected.value)
        .then(() => {
          ElNotification({
            title: KT("success"),
            message: KT("calendar.deleted"),
            type: "success",
          });
          selected.value = 0;
        })
        .catch((e) => {
          ElNotification({
            title: KT("error"),
            message: e.response.data,
            type: "danger",
          });
        });
    })
    .catch(() => {
      ElMessage.error(KT("userCancel"));
    });
};

const showCalendars = () => {
  show.value = true;
};

defineExpose({
  showCalendars,
});
</script>

<style>
.day {
  margin-right: 3px;
  background: silver;
  padding: 5px;
  border-radius: 5px;
  font-size: 11px;
}

.calendars:after {
  content: " | ";
}

.calendars:last-child:after {
  content: "";
}

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

.tblItem:after {
  content: ", ";
}

.tblItem:last-child:after {
  content: "";
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

.el-dialog__header,
.el-dialog__body,
.el-dialog__footer {
  padding: 0px !important;
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
</style>
