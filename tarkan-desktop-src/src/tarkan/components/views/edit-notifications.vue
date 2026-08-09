<template>
  <edit-notification ref="editNotificationRef"></edit-notification>
  <el-dialog :lock-scroll="true" :top="'50px'" v-model="show">
    <template v-slot:title>
      <div style="border-bottom: #e0e0e0 1px solid; padding: 20px">
        <div class="modal-title">
          {{ KT("notification.title", "Notificações") }}
        </div>
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
          v-if="store.getters.advancedPermissions(33)"
          @mouseleave="hideTip"
          @mouseenter.stop="showTip($event, KT('notification.add'))"
          type="primary"
          @click="editNotificationRef.newNotification()"
          ><i class="fas fa-user-plus"></i
        ></el-button>

        <el-button
          v-if="store.getters.advancedPermissions(35)"
          @mouseleave="hideTip"
          @mouseenter.stop="showTip($event, KT('notification.remove'))"
          type="danger"
          :plain="selected === 0"
          @click="doDelete()"
        >
          <i class="fas fa-user-minus"></i>
        </el-button>

        <el-button
          v-if="store.getters.advancedPermissions(34)"
          @mouseleave="hideTip"
          @mouseenter.stop="showTip($event, KT('notification.edit'))"
          type="warning"
          :plain="selected === 0"
          @click="editNotificationRef.editNotification(selected)"
        >
          <i class="fas fa-user-edit"></i>
        </el-button>
      </div>
    </template>
    <div class="itm" style="display: flex; background: #eeeeee">
      <div style="width: 140px; padding: 10px; font-size: 12px">
        {{ KT("notification.type") }}
      </div>
      <div
        style="width: 100px; padding: 10px; font-size: 12px; text-align: center"
      >
        {{ KT("notification.all") }}
      </div>
      <div style="flex: 1; padding: 10px; font-size: 12px; text-align: center">
        {{ KT("notification.alarms") }}
      </div>
      <div style="flex: 1; padding: 10px; font-size: 12px; text-align: center">
        {{ KT("notification.channel") }}
      </div>
    </div>
    <div
      style="height: calc(100vh - 300px); overflow: hidden; overflow-y: scroll"
    >
      <Paginate
        :items="store.state.events.eventsList"
        :per-page="15"
        v-model="currentPage"
      >
        <template #default="{ item, index }">
          <div
            class="itm"
            @click="selected = item.id"
            :class="{
              tr1: index % 2,
              tr2: !(index % 2),
              selected: selected === item.id,
            }"
            style="display: flex"
          >
            <div style="width: 140px; padding: 10px; font-size: 14px">
              {{ KT("notification.types." + item.type) }}
            </div>
            <div
              style="
                width: 100px;
                padding: 10px;
                font-size: 14px;
                text-align: center;
              "
            >
              {{ KT(item.always ? "yes" : "no") }}
            </div>
            <div
              style="
                flex: 1;
                padding: 10px;
                font-size: 14px;
                text-align: center;
              "
            >
              <template v-if="item.attributes['alarms']">
                <span
                  class="tblItem"
                  v-for="(a, b) in item.attributes['alarms'].split(',')"
                  :key="b"
                  >{{ KT("alarms." + a, a) }}</span
                >
              </template>
            </div>
            <div
              style="
                flex: 1;
                padding: 10px;
                font-size: 14px;
                text-align: center;
              "
            >
              <template v-if="item.notificators.split(',').length">
                <span
                  class="tblItem"
                  v-for="(a, b) in item.notificators.split(',')"
                  :key="b"
                  >{{ KT("notification.channels." + a, a) }}</span
                >
              </template>
            </div>
          </div>
        </template>
      </Paginate>
    </div>
  </el-dialog>
</template>

<script setup>
import "element-plus/es/components/input/style/css";
import "element-plus/es/components/button/style/css";
import "element-plus/es/components/switch/style/css";
import "element-plus/es/components/select/style/css";
import "element-plus/es/components/option/style/css";
import "element-plus/es/components/dialog/style/css";
import "element-plus/es/components/tab-pane/style/css";
import "element-plus/es/components/tabs/style/css";
import "element-plus/es/components/message/style/css";
import "element-plus/es/components/checkbox/style/css";
import Paginate from "../../../components/base/Paginate.vue";

import {
  ElDialog,
  ElMessage,
  ElMessageBox,
  ElNotification,
  ElButton,
} from "element-plus";

import { ref, defineExpose } from "vue";
import { useStore } from "vuex";

import EditNotification from "./edit-notification";
import KT from "../../func/kt";

const store = useStore();
const selected = ref(0);
const show = ref(false);
const currentPage = ref(1);
const editNotificationRef = ref(null);

const showTip = (evt, text) => {
  window.$showTip(evt, text);
};

const hideTip = (evt, text) => {
  window.$hideTip(evt, text);
};

const doDelete = () => {
  if (selected.value === 0) {
    ElMessage.error(KT("notification.selectError"));
    return false;
  }

  const notification = store.getters["events/getNotificationById"](
    selected.value
  );

  ElMessageBox.confirm(
    KT("notification.confirmDelete", {
      type: KT("notification.types." + notification.type),
    }),
    KT("danger"),
    {
      confirmButtonText: KT("delete"),
      confirmButtonClass: "danger",
      cancelButtonText: KT("cancel"),
      type: "warning",
    }
  )
    .then(() => {
      store
        .dispatch("events/delete", selected.value)
        .then(() => {
          ElNotification({
            title: KT("success"),
            message: KT("notification.deleted"),
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

const showNotifications = () => {
  show.value = true;
};

defineExpose({
  showNotifications,
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
