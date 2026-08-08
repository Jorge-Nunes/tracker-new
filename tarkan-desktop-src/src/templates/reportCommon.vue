<template>

  <el-form label-position="top">


    <el-form-item v-if="!isOnMobile">
      <el-date-picker
          v-model="formData.date"
          type="datetimerange"
          :shortcuts="shortcuts"
          :range-separator="$t('to')"
          format="DD/MM/YYYY HH:mm"
          :start-placeholder="$t('startDate')"
          :end-placeholder="$t('endDate')"

          @change="onChange"

      >
      </el-date-picker>
    </el-form-item>
    <el-form-item v-else>
      <el-input v-model="formData.date[0]" type="datetime-local" @change="onChange"></el-input>
      <el-input v-model="formData.date[1]" type="datetime-local" @change="onChange"></el-input>
    </el-form-item>

    <div style="display: flex;">
        <div style="flex: 1;margin-right: 10px;">
            <el-form-item>
              <el-select
                  @change="onChange" v-model="formData.deviceId" multiple :value-key="'id'" filterable :placeholder="$t('device.devices')" :size="'large'" :no-data-text="$t('NO_DATA_TEXT')" :no-match-text="$t('NO_MATCH_TEXT')">
                <el-option
                    v-for="item in store.state.devices.deviceList"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id"
                >
                </el-option>
              </el-select>
            </el-form-item>
        </div>
        <div style="flex: 1;">
            <el-form-item>
              <el-select
                  @change="onChange" v-model="formData.groupId" multiple :value-key="'id'" filterable :placeholder="$t('group.groups')" :size="'large'" :no-data-text="$t('NO_DATA_TEXT')" :no-match-text="$t('NO_MATCH_TEXT')">
                <el-option
                    v-for="item in store.state.groups.groupList"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id"
                >
                </el-option>
              </el-select>
            </el-form-item>
        </div>
    </div>

  </el-form>

</template>

<script setup>



import 'element-plus/es/components/date-picker/style/css'
import 'element-plus/es/components/form/style/css'
import 'element-plus/es/components/form-item/style/css'
import 'element-plus/es/components/select/style/css'
import 'element-plus/es/components/option/style/css'

import {ElDatePicker,ElForm,ElSelect,ElOption,ElFormItem,ElInput} from "element-plus";

import {ref,onMounted,defineEmits} from 'vue';
import {useStore} from 'vuex';

import t from '@/tarkan/func/kt';



const emit = defineEmits(['change']);

const store = useStore();

const isOnMobile = ref(true);

const date1 = new Date();
const date2 = new Date();

date1.setHours(0);
date1.setMinutes(0);

const formData = ref({
  date: [date1,date2],
  deviceId: [],
  showMarkers: true,
  groupId: []
});

onMounted(()=>{
    emit("change",formData.value);

})

const onChange = ()=>{
  emit("change",formData.value);
}


const shortcuts = ref([
  {
    text: t('today'),
    value: () => {
      const end = new Date()
      const start = new Date()
      start.setHours(0);
      start.setMinutes(0);
      start.setSeconds(0);

      return [start, end]
    },
  },
  {
    text: t('yesterday'),
    value: () => {
      const end = new Date()
      const start = new Date()
      start.setHours(0);
      start.setMinutes(0);
      start.setSeconds(0);
      start.setDate(start.getDate()-1);

      end.setHours(0);
      end.setMinutes(0);
      end.setSeconds(0);



      return [start, end]
    },
  },
  {
    text: t('thisweek'),
    value: () => {
      const end = new Date()
      const start = new Date()
      start.setTime(start.getTime() - 3600 * 1000 * 24 * 7)
      return [start, end]
    },
  },
  {
    text: t('thismonth'),
    value: () => {
      const end = new Date()
      const start = new Date()
      start.setTime(start.getTime() - 3600 * 1000 * 24 * 30)
      return [start, end]
    },
  },
  {
    text: t('theeemonths'),
    value: () => {
      const end = new Date()
      const start = new Date()
      start.setTime(start.getTime() - 3600 * 1000 * 24 * 90)
      return [start, end]
    },
  },
]);

</script>

<style>
.el-range-editor.el-input__inner{
    width: 100% !important;
}
</style>