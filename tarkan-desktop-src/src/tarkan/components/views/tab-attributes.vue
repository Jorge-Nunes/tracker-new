<template>
  <div style="display: flex">
    <div style="flex: 1">
      <el-select
        v-model="tmpAttribute.name"
        :value-key="'id'"
        allow-create
        filterable
        :placeholder="KT('attribute.attribute')"
        :size="'large'"
        :no-match-text="KT('NO_MATCH_TEXT')"
        :no-data-text="KT('NO_DATA_TEXT')"
      >
        <el-option
          v-for="item in availableAttributes"
          :key="item"
          :label="$t('attribute.' + item)"
          :value="item"
        >
        </el-option>
      </el-select>
    </div>
    <div style="flex: 1; margin-left: 5px">
      <el-input
        v-model="tmpAttribute.value"
        :placeholder="KT('attribute.value')"
      ></el-input>
    </div>

    <div style="padding: 4px">
      <el-button type="primary" @click="addAttribute()" size="small">{{
        KT("attribute.add")
      }}</el-button>
    </div>
  </div>

  <div
    style="
      border: silver 1px solid;
      border-radius: 5px;
      margin-top: 5px;
      margin-bottom: 15px;
      height: calc(70vh - 200px);
    "
  >
    <Paginate :items="attributesFiltered" :per-page="15" v-model="currentPage">
      <template v-slot="{ item }">
        <div style="display: flex; border-bottom: silver 1px dotted">
          <div style="padding: 7px; flex: 1">
            {{ $t("attribute." + item.key) }}
          </div>
          <div style="padding: 2px; flex: 1">
            <input
              style="
                width: 100%;
                border: silver 1px solid;
                height: 23px;
                border-radius: 5px;
                padding: 2px;
              "
              @change="changeAttribute(item.key, $event)"
              :value="item.value"
            />
          </div>
          <div
            style="padding: 5px; padding-right: 10px; font-size: 18px"
            @click="remove(item.key)"
          >
            <i class="fas fa-minus-square"></i>
          </div>
        </div>
      </template>
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

import { ElSelect, ElOption, ElButton, ElInput } from "element-plus";

const defaultAvailableAttributes = {
  device: ["speedLimit", "lockOnExit"],
  user: ["telegramChatId"],
};

import { defineProps, computed, defineEmits, ref } from "vue";

const tmpAttribute = ref({ name: "", value: "" });
const currentPage = ref(1);

const props = defineProps(["modelValue", "type"]);

const emit = defineEmits(["update:modelValue"]);

// eslint-disable-next-line no-undef
const _availableAttributes = ref(defaultAvailableAttributes);

const availableAttributes = computed(() => {
  return _availableAttributes.value[props.type] || [];
});

const changeAttribute = (a, b) => {
  let tmp = props.modelValue;
  tmp[a] = b.srcElement.value;
  emit("update:modelValue", tmp);
};

const attributesFiltered = computed(() => {
  const tmp = [];

  let test = props.modelValue ? Object.keys(props.modelValue) : [];

  for (var a of test) {
    if (!a.match("tarkan.")) {
      tmp.push({ key: a, value: props.modelValue[a] });
    }
  }

  return tmp;
});

const addAttribute = () => {
  let tmp = props.modelValue;

  tmp[tmpAttribute.value.name] = tmpAttribute.value.value;

  emit("update:modelValue", tmp);

  tmpAttribute.value = { name: "", value: "" };
};

const remove = (key) => {
  delete props.modelValue[key];

  emit("update:modelValue", props.modelValue);
};
</script>
