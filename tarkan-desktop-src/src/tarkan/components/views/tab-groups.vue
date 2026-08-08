<template>
  <el-select
    v-model="formData"
    @change="addCar($event)"
    :value-key="'id'"
    filterable
    :placeholder="KT('group.groups')"
    :size="'large'"
    :no-match-text="KT('NO_MATCH_TEXT')"
    :no-data-text="KT('NO_DATA_TEXT')"
  >
    <el-option
      v-for="item in store.state.groups.groupList"
      :key="item.id"
      :label="item.name"
      :value="item.id"
    >
    </el-option>
  </el-select>

  <div
    style="
      border: silver 1px solid;
      border-radius: 5px;
      margin-top: 10px;
      height: calc(70vh - 200px);
    "
  >
    <Paginate :items="selected" :per-page="15" v-model="currentPage">
      <slot v-bind="{ item, index }">
        <div style="display: flex; border-bottom: silver 1px dotted">
          <div style="padding: 7px; flex: 1">{{ item.name }}</div>
          <div
            style="padding: 5px; padding-right: 10px; font-size: 18px"
            @click="remove(index)"
          >
            <i class="fas fa-minus-square"></i>
          </div>
        </div>
      </slot>
    </Paginate>
  </div>
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

import { ElSelect, ElOption } from "element-plus";

import { ref, defineExpose } from "vue";
import { useStore } from "vuex";

const store = useStore();

const formData = ref(null);
const selected = ref([]);
const currentPage = ref(1);

const clear = () => {
  selected.value = [];
};

defineExpose({
  selected,
  clear,
});

const remove = (key) => {
  selected.value.splice(key, 1);
};

const addCar = (car) => {
  const check = selected.value.find((c) => c.id === car);
  if (!check) {
    const device = store.getters["groups/getGroup"](car);

    if (device) {
      selected.value.push(device);
    }
  }

  formData.value = null;
};
</script>
