<template>
  <edit-driver ref="editDriverRef"></edit-driver>
  <el-dialog :lock-scroll="true" :top="'50px'" v-model="show">
    <template v-slot:title>
      <div style="border-bottom: #e0e0e0 1px solid; padding: 20px">
        <div
          class="modal-title"
          style="display: flex; width: calc(100% - 50px)"
        >
          <el-input
            v-model="query"
            :placeholder="KT('driver.search')"
            style="--el-input-border-radius: 5px; margin-right: 5px"
          ></el-input>

          <el-button
            v-if="store.getters.advancedPermissions(81)"
            @mouseleave="hideTip"
            @mouseenter.stop="showTip($event, KT('driver.add'))"
            type="primary"
            @click="editDriverRef.newDriver()"
            ><i class="fas fa-user-plus"></i
          ></el-button>
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
          v-if="store.getters.advancedPermissions(83)"
          @mouseleave="hideTip"
          @mouseenter.stop="showTip($event, KT('driver.remove'))"
          type="danger"
          :plain="selected === 0"
          @click="doDelete()"
        >
          <i class="fas fa-user-minus"></i>
        </el-button>

        <el-button
          v-if="store.getters.advancedPermissions(82)"
          @mouseleave="hideTip"
          @mouseenter.stop="showTip($event, KT('driver.edit'))"
          type="warning"
          :plain="selected === 0"
          @click="editDriverRef.editDriver(selected)"
        >
          <i class="fas fa-user-edit"></i>
        </el-button>
      </div>
    </template>
    <div class="itm" style="display: flex; background: #eeeeee">
      <div style="flex: 1; padding: 10px; font-size: 12px">
        {{ KT("driver.name") }}
      </div>
      <div style="flex: 1; padding: 10px; font-size: 12px; text-align: center">
        {{ KT("driver.uniqueId") }}
      </div>
    </div>
    <div
      style="height: calc(100vh - 300px); overflow: hidden; overflow-y: scroll"
    >
      <Paginate
        :items="store.state.drivers.driverList"
        :per-page="15"
        v-model="currentPage"
      >
        <slot v-bind="{ item, index }">
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
            <div style="flex: 1; padding: 10px; font-size: 14px">
              {{ item.name }}
            </div>
            <div
              style="
                flex: 1;
                padding: 10px;
                font-size: 14px;
                text-align: center;
              "
            >
              {{ item.uniqueId }}
            </div>
          </div>
        </slot>
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

import { ElDialog, ElMessage, ElButton, ElInput } from "element-plus";

import { ref, defineExpose } from "vue";
import { useStore } from "vuex";
//import {ElMessage, ElMessageBox, ElNotification} from "element-plus";
import EditDriver from "./edit-driver";

import { ElMessageBox } from "element-plus/es/components/message-box";
import { ElNotification } from "element-plus/es/components/notification";

const store = useStore();
const query = ref("");
const selected = ref(0);
const currentPage = ref(1);
const show = ref(false);

const editDriverRef = ref(null);

import KT from "../../func/kt";

const showTip = (evt, text) => {
  window.$showTip(evt, text);
};

const hideTip = (evt, text) => {
  window.$hideTip(evt, text);
};

const doDelete = () => {
  if (selected.value === 0) {
    ElMessage.error(KT("driver.selectError"));
    return false;
  }

  const driver = store.getters["drivers/getDriver"](selected.value);

  ElMessageBox.confirm(KT("driver.confirmDelete", driver), KT("danger"), {
    confirmButtonText: KT("delete"),
    confirmButtonClass: "danger",
    cancelButtonText: KT("cancel"),
    type: "warning",
  })
    .then(() => {
      store
        .dispatch("drivers/deleteDriver", selected.value)
        .then(() => {
          ElNotification({
            title: KT("success"),
            message: KT("driver.deleteSuccess"),
            type: "success",
          });
          selected.value = 0;
        })
        .catch((e) => {
          ElNotification({
            title: "Erro",
            message: e.response.data,
            type: "danger",
          });
        });
    })
    .catch(() => {
      ElMessage.error(KT("userCancel"));
    });
};

const showDrivers = () => {
  show.value = true;
};

defineExpose({
  showDrivers,
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
