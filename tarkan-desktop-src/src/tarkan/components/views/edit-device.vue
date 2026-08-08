<template>
  <el-dialog :lock-scroll="true" v-model="show">
    <template v-slot:title>
      <div style="border-bottom: #e0e0e0 1px solid; padding: 20px">
        <div class="modal-title">{{ title }}</div>
      </div>
    </template>
    <template v-slot:footer>
      <div
        style="
          border-top: #e0e0e0 1px solid;
          padding: 20px;
          display: flex;
          justify-content: space-between;
        "
      >
        <el-button type="danger" plain @click="doCancel()">{{
          KT("cancel")
        }}</el-button>
        <el-button type="primary" @click="doSave()">{{ KT("save") }}</el-button>
      </div>
    </template>

    <el-form
      ref="formRef"
      :model="formData"
      :rules="rules"
      label-width="120px"
      label-position="top"
    >
      <el-tabs v-model="tab">
        <el-tab-pane :label="KT('device.device')" name="first">
          <el-form-item :label="KT('device.imei')" prop="uniqueId">
            <el-input v-model="formData.uniqueId"></el-input>
          </el-form-item>
          <el-form-item :label="KT('device.name')" prop="name">
            <el-input v-model="formData.name"></el-input>
          </el-form-item>

          <el-form-item :label="KT('group.group')">
            <el-select
              v-model="formData.groupId"
              :value-key="'id'"
              filterable
              :placeholder="KT('group.group')"
              :size="'large'"
              :no-match-text="KT('NO_MATCH_TEXT')"
              :no-data-text="KT('NO_DATA_TEXT')"
            >
              <el-option :label="KT('no')" :value="0"> </el-option>
              <el-option
                v-for="item in store.state.groups.groupList"
                :key="item.id"
                :label="item.name"
                :value="item.id"
              >
              </el-option>
            </el-select>
          </el-form-item>

          <el-form-item
            v-if="store.state.auth.administrator"
            :label="KT('device.status')"
          >
            <el-switch
              v-model="formData.disabled"
              :inactive-text="KT('disabled')"
              :active-text="KT('enabled')"
              :active-value="false"
              :inactive-value="true"
            >
            </el-switch>
          </el-form-item>
        </el-tab-pane>

        <el-tab-pane :label="KT('device.chip')" name="second-one">
          <el-form-item :label="KT('device.iccid')">
            <el-input v-model="formData.attributes['iccid']"></el-input>
          </el-form-item>
          <el-form-item :label="KT('device.phone')">
            <el-input v-model="formData.phone"></el-input>
          </el-form-item>
          <el-form-item :label="KT('device.operator')">
            <el-input v-model="formData.attributes['operator']"></el-input>
          </el-form-item>
        </el-tab-pane>

        <el-tab-pane :label="KT('user.user')" name="second-two">
          <el-form-item :label="KT('user.name')">
            <el-input v-model="formData.attributes['tarkan.name']"></el-input>
          </el-form-item>
          <el-form-item :label="KT('user.phone')">
            <el-input v-model="formData.contact"></el-input>
          </el-form-item>
          <el-form-item :label="KT('user.email')">
            <el-input v-model="formData.attributes['tarkan.email']"></el-input>
          </el-form-item>
        </el-tab-pane>

        <el-tab-pane :label="KT('device.details')" name="second">
          <template v-if="true">
            <div style="display: flex">
              <el-form-item
                :label="KT('device.carbrand')"
                style="flex: 1; margin-right: 5px"
              >
                <el-select
                  v-model="formData.attributes['brand']"
                  :allow-create="true"
                  :value-key="'id'"
                  filterable
                  :placeholder="KT('device.carbrand')"
                  :size="'large'"
                  :no-match-text="KT('NO_MATCH_TEXT')"
                  :no-data-text="KT('NO_DATA_TEXT')"
                >
                  <el-option
                    v-for="item in availableBrands"
                    :key="item.key"
                    :label="item.name"
                    :value="item.key"
                  >
                  </el-option>
                </el-select>
              </el-form-item>

              <el-form-item
                :label="KT('device.carmodel')"
                style="flex: 1; margin-right: 5px"
              >
                <el-select
                  v-model="formData.attributes['model']"
                  :allow-create="true"
                  :value-key="'id'"
                  filterable
                  :placeholder="KT('device.carmodel')"
                  :size="'large'"
                  :no-match-text="KT('NO_MATCH_TEXT')"
                  :no-data-text="KT('NO_DATA_TEXT')"
                >
                  <el-option
                    v-for="item in availableModels"
                    :key="item"
                    :label="item"
                    :value="item"
                  >
                  </el-option>
                </el-select>
              </el-form-item>

              <el-form-item :label="KT('device.carcolor')" style="flex: 1">
                <el-select
                  v-model="formData.attributes['color']"
                  :allow-create="true"
                  :value-key="'id'"
                  filterable
                  :placeholder="KT('device.carcolor')"
                  :size="'large'"
                  :no-match-text="KT('NO_MATCH_TEXT')"
                  :no-data-text="KT('NO_DATA_TEXT')"
                >
                  <el-option
                    v-for="item in availableModelColors"
                    :key="item"
                    :label="item"
                    :value="item"
                  >
                  </el-option>
                </el-select>
              </el-form-item>
            </div>
          </template>
          <template v-else>
            <el-form-item :label="KT('device.model')">
              <el-input v-model="formData.model"></el-input>
            </el-form-item>
          </template>
          <el-form-item :label="KT('device.plate')">
            <el-input v-model="formData.attributes['placa']"></el-input>
          </el-form-item>

          <el-form-item :label="KT('device.odometer')">
            <el-input v-model="odometerData"></el-input>
          </el-form-item>

          <div class="el-form-item">
            <label class="el-form-item__label">{{ KT("device.icon") }}</label>
            <div
              class="el-form-item__content"
              style="
                display: flex;
                border: silver 1px solid;
                border-radius: 3px;
                flex-wrap: wrap;
                margin-right: -10px;
                overflow: auto;
                max-height: 350px;
              "
            >
              <dv-car
                v-for="(cc, ck) in availableCars"
                :key="ck"
                :img="cc.img"
                :selected="formData.category === cc.key"
                @click="formData.category = cc.key"
                @mouseleave="hideTip"
                @mouseenter.stop="showTip($event, KT('map.devices.' + cc.key))"
                :color1="cc.color1"
                :color2="cc.color2"
                :filter1="
                  'hue-rotate(' +
                  hue +
                  'deg) saturate(' +
                  (saturation / 100).toFixed(2) +
                  ') brightness(' +
                  (brightnes / 100).toFixed(2) +
                  ')'
                "
                :filter2="
                  'hue-rotate(' +
                  hue2 +
                  'deg) saturate(' +
                  (saturation2 / 100).toFixed(2) +
                  ') brightness(' +
                  (brightnes2 / 100).toFixed(2) +
                  ')'
                "
              ></dv-car>
            </div>
          </div>

          /* IFTRUE_myFlag */
          <div style="display: flex">
            <div style="flex: 1; margin-right: 30px">
              <label
                class="el-form-item__label"
                style="
                  margin-bottom: -15px !important;
                  font-weight: bold;
                  display: flex;
                  justify-content: space-between;
                "
              >
                {{ KT("device.color1") }}
                <div style="margin-top: 0px">
                  <el-switch
                    v-model="useCustomColor1"
                    :active-text="KT('customize')"
                  ></el-switch>
                </div>
              </label>
              <div
                v-if="useCustomColor1"
                style="display: flex; flex-direction: column; padding: 10px"
              >
                <div class="el-form-item" style="flex: 1; margin-right: 5px">
                  <label
                    class="el-form-item__label"
                    style="margin-bottom: -15px !important"
                    >{{ KT("device.hue") }}</label
                  >
                  <div class="el-form-item__content">
                    <el-slider v-model="hue" :max="360"></el-slider>
                  </div>
                </div>

                <div class="el-form-item" style="flex: 1; margin-right: 5px">
                  <label
                    class="el-form-item__label"
                    style="margin-bottom: -15px !important"
                    >{{ KT("device.saturate") }}</label
                  >
                  <div class="el-form-item__content">
                    <el-slider v-model="saturation" :max="300"></el-slider>
                  </div>
                </div>

                <div class="el-form-item" style="flex: 1">
                  <label
                    class="el-form-item__label"
                    style="margin-bottom: -15px !important"
                    >{{ KT("device.brightness") }}</label
                  >
                  <div>
                    <el-slider v-model="brightnes" :max="200"></el-slider>
                  </div>
                </div>
              </div>
              <div
                v-else
                style="margin-top: 15px; display: flex; flex-wrap: wrap"
              >
                <div
                  v-for="(c, ck) in availableColors"
                  :key="'color1' + ck"
                  style="
                    margin-right: 2px;
                    margin-bottom: 2px;
                    border: silver 1px solid;
                    border-radius: 3px;
                    cursor: pointer;
                  "
                  @click="setColor1(c)"
                  :style="{
                    filter:
                      'hue-rotate(' +
                      c.hue +
                      'deg) saturate(' +
                      c.saturation +
                      ') brightness(' +
                      c.brightness +
                      ')',
                  }"
                >
                  <div
                    style="background: #3e8db9; width: 30px; height: 30px"
                  ></div>
                </div>
              </div>
            </div>
            <div style="flex: 1; margin-left: 30px">
              <label
                class="el-form-item__label"
                style="
                  margin-bottom: -15px !important;
                  font-weight: bold;
                  display: flex;
                  justify-content: space-between;
                "
              >
                {{ KT("device.color2") }}
                <div style="margin-top: 0px">
                  <el-switch
                    v-model="useCustomColor2"
                    :active-text="KT('customize')"
                  ></el-switch>
                </div>
              </label>
              <div
                v-if="useCustomColor2"
                style="display: flex; flex-direction: column; padding: 10px"
              >
                <div class="el-form-item" style="flex: 1; margin-right: 5px">
                  <label
                    class="el-form-item__label"
                    style="margin-bottom: -15px !important"
                    >{{ KT("device.hue") }}</label
                  >
                  <div class="el-form-item__content">
                    <el-slider v-model="hue2" :max="360"></el-slider>
                  </div>
                </div>

                <div class="el-form-item" style="flex: 1; margin-right: 5px">
                  <label
                    class="el-form-item__label"
                    style="margin-bottom: -15px !important"
                    >{{ KT("device.saturate") }}</label
                  >
                  <div class="el-form-item__content">
                    <el-slider v-model="saturation2" :max="300"></el-slider>
                  </div>
                </div>

                <div class="el-form-item" style="flex: 1">
                  <label
                    class="el-form-item__label"
                    style="margin-bottom: -15px !important"
                    >{{ KT("device.brightness") }}</label
                  >
                  <div>
                    <el-slider v-model="brightnes2" :max="200"></el-slider>
                  </div>
                </div>
              </div>
              <div
                v-else
                style="
                  margin-top: 15px;
                  display: flex;
                  margin-bottom: 10px;
                  flex-wrap: wrap;
                "
              >
                <div
                  v-for="(c, ck) in availableColors"
                  :key="'color2' + ck"
                  style="
                    margin-right: 2px;
                    margin-bottom: 2px;
                    border: silver 1px solid;
                    border-radius: 3px;
                    cursor: pointer;
                  "
                  @click="setColor2(c)"
                  :style="{
                    filter:
                      'hue-rotate(' +
                      c.hue +
                      'deg) saturate(' +
                      c.saturation +
                      ') brightness(' +
                      c.brightness +
                      ')',
                  }"
                >
                  <div
                    style="background: #3e8db9; width: 30px; height: 30px"
                  ></div>
                </div>
              </div>
            </div>
          </div>

          /* FITRUE_myFlag */
        </el-tab-pane>

        <el-tab-pane :label="KT('attribute.attributes')" name="attributes">
          <tab-attributes
            v-model="formData.attributes"
            :type="'device'"
          ></tab-attributes>
        </el-tab-pane>
      </el-tabs>
    </el-form>
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
import "element-plus/es/components/slider/style/css";

import {
  ElDialog,
  ElSlider,
  ElMessage,
  ElMessageBox,
  ElNotification,
  ElTabs,
  ElTabPane,
  ElForm,
  ElSwitch,
  ElFormItem,
  ElSelect,
  ElOption,
  ElButton,
  ElInput,
} from "element-plus";

import TabAttributes from "./tab-attributes";

import { ref, defineExpose, reactive, computed } from "vue";
import { useStore } from "vuex";

const store = useStore();

//import {ElDialog, ElForm, ElFormItem, ElInput, ElMessage, ElMessageBox, ElNotification} from 'element-plus'

import DvCar from "./dv-car";
import KT from "../../func/kt";

const show = ref(false);
const tab = ref("first");
const title = ref("");

const formRef = ref(null);
const odometerData = ref(0);

const rules = reactive({
  name: [
    {
      required: true,
      message: KT("device.form.nameEmpty"),
      trigger: "blur",
    },
  ],
  uniqueId: [
    {
      required: true,
      message: KT("device.form.uniqueIdEmpty"),
      trigger: "blur",
    },
  ],
});

const showTip = (evt, text) => {
  window.$showTip(evt, text);
};

const hideTip = (evt, text) => {
  window.$hideTip(evt, text);
};

const hue = ref(0);
const saturation = ref(100);
const brightnes = ref(100);

const hue2 = ref(0);
const saturation2 = ref(100);
const brightnes2 = ref(100);

const useCustomColor1 = ref(false);
const useCustomColor2 = ref(false);

const availableBrands = computed(() => {
  return [
    { key: "Yamaha", name: "Yamaha" },
    { key: "Honda", name: "Honda" },
  ];
});

const brandModels = {
  Yamaha: ["Nmax", "Tracer", "Mt07", "Xmax", "Xj6"],
  Honda: ["Pcx", "Sh", "Hornet", "Forza", "Xadv"],
};

const availableModels = computed(() => {
  const brandSelected = formData.value.attributes["brand"] ?? null;
  if (!brandSelected) return [];

  return brandModels[brandSelected];
})

const availableModelColors = [
  "Black",
  "White",
  "Yellow",
  "Blue",
  "Red",
  "Grey",
  "Green",
  "Orange",
];

// eslint-disable-next-line no-undef
// const availableModelColors = ref(availableColors);

const availableColors = ref([
  { hue: 13, saturation: 0, brightness: 0.3 },
  { hue: 13, saturation: 0, brightness: 0.6 },
  { hue: 13, saturation: 0, brightness: 1.8 },

  { hue: 13, saturation: 1, brightness: 1 },
  { hue: 13, saturation: 2, brightness: 1 },
  { hue: 13, saturation: 2, brightness: 0.6 },
  { hue: 155, saturation: 1, brightness: 1 },
  { hue: 155, saturation: 2, brightness: 1 },
  { hue: 155, saturation: 2, brightness: 0.6 },
  { hue: -14, saturation: 1, brightness: 1 },
  { hue: -14, saturation: 2, brightness: 1 },
  { hue: -14, saturation: 2, brightness: 0.6 },

  { hue: -95, saturation: 1, brightness: 1 },
  { hue: -95, saturation: 2, brightness: 1 },
  { hue: -95, saturation: 2, brightness: 0.6 },

  { hue: -165, saturation: 1, brightness: 1 },
  { hue: -165, saturation: 2, brightness: 1 },
  { hue: -165, saturation: 2, brightness: 0.6 },

  { hue: 43, saturation: 1, brightness: 1 },
  { hue: 43, saturation: 2, brightness: 1 },
  { hue: 43, saturation: 2, brightness: 0.6 },

  { hue: 105, saturation: 1, brightness: 1 },
  { hue: 105, saturation: 2, brightness: 1 },
  { hue: 105, saturation: 2, brightness: 0.6 },
]);

const setColor1 = (c) => {
  hue.value = c.hue;
  saturation.value = c.saturation * 100;
  brightnes.value = c.brightness * 100;
};

const setColor2 = (c) => {
  hue2.value = c.hue;
  saturation2.value = c.saturation * 100;
  brightnes2.value = c.brightness * 100;
};

// eslint-disable-next-line no-undef
const formData = ref(defaultDeviceData);

const availableCars = ref([
  { key: "default", img: "default", color1: true, color2: false },
  { key: "arrow", img: "arrow", color1: true, color2: false },
  { key: "animal", img: "pet", color1: true, color2: false },
  { key: "person", img: "person", color1: true, color2: false },
  { key: "bicycle", img: "bicycle", color1: true, color2: false },
  { key: "motorcycle", img: "motorcycle", color1: true, color2: false },
  { key: "scooter", img: "scooter", color1: true, color2: false },
  { key: "car", img: "carroPasseio", color1: true, color2: false },
  { key: "pickup", img: "carroUtilitario", color1: true, color2: false },
  { key: "van", img: "vanUtilitario", color1: true, color2: false },
  { key: "truck", img: "caminhaoBau", color1: true, color2: true },
  { key: "truck1", img: "truckCavalo", color1: true, color2: false },
  { key: "truck2", img: "truckBau", color1: true, color2: true },
  { key: "bus", img: "bus", color1: true, color2: false },
  { key: "crane", img: "crane", color1: true, color2: false },

  { key: "offroad", img: "offroad", color1: true, color2: false },
  { key: "tractor", img: "tractor", color1: true, color2: false },

  { key: "plane", img: "plane", color1: true, color2: false },
  { key: "helicopter", img: "helicopter", color1: true, color2: false },
  { key: "boat", img: "boat", color1: true, color2: false },
  { key: "ship", img: "ship", color1: true, color2: false },
]);

const getColorsFromAttribute = () => {
  const attrColor = formData.value.attributes["tarkan.color"];
  const attrColorExtra = formData.value.attributes["tarkan.color_extra"];

  if (attrColor) {
    const tmp = formData.value.attributes["tarkan.color"].split("|");

    hue.value = parseInt(tmp[0] ? tmp[0] : 0);
    saturation.value = parseInt(tmp[1] ? tmp[1] * 100 : 100);
    brightnes.value = parseInt(tmp[2] ? tmp[2] * 100 : 100);
  } else {
    hue.value = 0;
    saturation.value = 0;
    brightnes.value = 180;
  }

  if (attrColorExtra) {
    const tmp = formData.value.attributes["tarkan.color_extra"].split("|");

    hue2.value = parseInt(tmp[0] ? tmp[0] : 0);
    saturation2.value = parseInt(tmp[1] ? tmp[1] * 100 : 100);
    brightnes2.value = parseInt(tmp[2] ? tmp[2] * 100 : 100);
  } else {
    hue2.value = 0;
    saturation2.value = 0;
    brightnes2.value = 180;
  }
};

const newDevice = () => {
  tab.value = "first";
  title.value = KT("device.add");
  // eslint-disable-next-line no-undef
  formData.value = JSON.parse(JSON.stringify(defaultDeviceData));
  show.value = true;
  odometerData.value = 0;
};

const editDevice = (id) => {
  tab.value = "first";
  title.value = KT("device.edit");
  // eslint-disable-next-line no-undef
  formData.value = JSON.parse(JSON.stringify(defaultDeviceData));

  const device = store.getters["devices/getDevice"](id);

  // eslint-disable-next-line no-undef
  for (let k of Object.keys(defaultDeviceData)) {
    if (k === "attributes") {
      formData.value[k] =
        device[k] === null ? {} : JSON.parse(JSON.stringify(device[k]));
    } else {
      formData.value[k] = device[k] === null ? "" : device[k];
    }
  }

  getColorsFromAttribute();
  const pos = store.getters["devices/getPosition"](device.id);

  odometerData.value = pos ? pos.attributes["totalDistance"] : 0;

  show.value = true;
};

defineExpose({
  newDevice,
  editDevice,
});

const doCancel = () => {
  show.value = false;
};

const doSave = () => {
  formRef.value.validate((valid) => {
    if (valid) {
      formData.value.attributes["tarkan.color"] =
        hue.value +
        "|" +
        (saturation.value / 100).toFixed(2) +
        "|" +
        (brightnes.value / 100).toFixed(2);
      formData.value.attributes["tarkan.color_extra"] =
        hue2.value +
        "|" +
        (saturation2.value / 100).toFixed(2) +
        "|" +
        (brightnes2.value / 100).toFixed(2);

      ElNotification({
        title: KT("info"),
        message: KT("device.saving"),
        type: "info",
      });

      formData.value.uniqueId = formData.value.uniqueId.trim();

      store
        .dispatch("devices/save", formData.value)
        .then((d) => {
          store.dispatch("devices/accumulators", {
            deviceId: d.id,
            totalDistance: odometerData.value,
          });

          ElNotification({
            title: KT("success"),
            message: KT("device.saved"),
            type: "info",
          });
          show.value = false;
        })
        .catch((r) => {
          const err = r.response.data
            .split("-")[0]
            .trim()
            .replaceAll(" ", "_")
            .toUpperCase();

          ElMessageBox.alert(
            KT("device.error." + err),
            KT("device.saveError"),
            {
              confirmButtonText: "OK",
            }
          );
        });
    } else {
      ElMessage.error(KT("device.error.checkForm"));
    }
  });
};
</script>

<style>
.el-select.el-select--large {
  width: 100%;
}

.el-dialog__header,
.el-dialog__body,
.el-dialog__footer {
  padding: 0px !important;
}

.el-dialog__footer {
  margin-top: 20px;
}

.el-tabs__nav-wrap {
  padding-left: 20px;
  padding-right: 20px;
}

.el-tabs__content {
  padding-left: 20px;
  padding-right: 20px;
}
</style>
