<template>

  <div style="font-weight: bold;text-align: center;margin-bottom: 20px;">
    {{$t('report.selectType')}}
  </div>


  <div class="reportType"  @click="$router.push('/reports/resume')">
    {{$t('report.resume')}}
  </div>
  <div class="reportType" v-if="store.getters.advancedPermissions(73)" @click="$router.push('/reports/history')">
    {{$t('report.history')}}
  </div>
  <div class="reportType" v-if="store.getters.advancedPermissions(75)" @click="$router.push('/reports/travels')">
      {{$t('report.travels')}}
  </div>
  <div class="reportType" v-if="store.getters.advancedPermissions(76)" @click="$router.push('/reports/events')">
      {{$t('report.events')}}
  </div>
  <div class="reportType" v-if="store.getters.advancedPermissions(74)" @click="$router.push('/reports/stops')">
      {{$t('report.stops')}}
  </div>
</template>

<script setup>

import {useStore} from "vuex";

const store = useStore();
</script>


<style scoped>
  .reportType{
      border: var(--el-border-color-light) 1px solid;
      border-radius: 10px;
      padding: 20px;
      text-align: center;
      margin-bottom: 5px;
      cursor: pointer;
  }

  .reportType:hover{
    background: var(--el-color-primary-light-9);
  }
</style>