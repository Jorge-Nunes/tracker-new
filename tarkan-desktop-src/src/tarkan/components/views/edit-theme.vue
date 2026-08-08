<template>
  <el-dialog :lock-scroll="true" width="750px" v-model="show">

    <template v-slot:title>
      <div  style="border-bottom: #e0e0e0 1px solid;padding: 20px;">
        <div class="modal-title" >{{title}}</div>
      </div>
    </template>
    <template v-slot:footer>
      <div  style="border-top: #e0e0e0 1px solid;padding: 20px;display: flex;justify-content: space-between;">

        <el-button type="danger" plain @click="doCancel()">{{KT('cancel')}}</el-button>
        <el-button type="primary" @click="doSave()">{{KT('save')}}</el-button>
      </div>
    </template>

    <el-tabs v-model="tab">

      <el-tab-pane :label="KT('theme.general')" name="general">

        <el-form label-width="150px" label-position="left">


          <div style="display: flex;justify-content: space-between">
            <div style="position: relative;background: var(--el-bg-color);width: 300px;height: 150px;">
              <img style="width: 100px;position: absolute;top: 50%;left: calc(50% - 100px);transform: translateY(-50%);" :src="'/tarkan/assets/custom/icons/android-chrome-192x192.png?uncache='+uncache">
            </div>

            <div>



              <el-form-item :label="KT('theme.icon')">
                <el-upload
                    class="upload-demo"
                    action="/tarkan/theme/upload?type=fav-icon"
                    :on-success = "onSuccess"
                >
                  <el-button type="primary">{{KT('select')}}</el-button>
                </el-upload>
              </el-form-item>
            </div>

          </div>

          <el-form-item :label="KT('theme.appName')">
            <el-input v-model="labelConf.title"></el-input>
          </el-form-item>


          <el-form-item :label="KT('theme.whatsapp')">
            <el-input v-model="labelConf.whatsapp"></el-input>
          </el-form-item>

          <el-form-item :label="KT('theme.internalLogo')">
            <el-switch v-model="labelConf.headLogo.image" :active-value="false" :inactive-value="true" :inactive-text="KT('theme.logoAsImage')" :active-text="KT('theme.logoAsText')"></el-switch>
            <div style="height: 10px;"></div>
            <el-input v-if="!labelConf.headLogo.image" v-model="labelConf.headLogo.text"></el-input>
          </el-form-item>
        </el-form>
      </el-tab-pane>





      <el-tab-pane :label="KT('theme.login')" name="first">
        <el-form label-width="150px" label-position="left">

          <div style="display: flex;justify-content: space-between">
            <div style="position: relative;">
              <div class="loginfake" :style="'background-image: url(/tarkan/assets/custom/bg.jpg?uncache='+uncache+');'"></div>
            </div>

            <div>
              <el-form-item  :label="KT('theme.filterColor')" >
                <el-color-picker @active-change="updateGlobal()" :show-alpha="true" @change="updateGlobal()" v-model="formData['--tk-login-filter']" ></el-color-picker>
              </el-form-item>

              <el-form-item :label="KT('theme.backgroundImage')">
                <el-upload
                    class="upload-demo"
                    action="/tarkan/theme/upload?type=bg-login"

                    :on-success = "onSuccess"
                >
                  <el-button type="primary">{{KT('select')}}</el-button>

                </el-upload>
              </el-form-item>



            </div>

          </div>

        </el-form>


      </el-tab-pane>

      <el-tab-pane :label="KT('theme.logos')" name="first3">
        <el-form label-width="150px" label-position="left">

          <div style="display: flex;justify-content: space-between">
            <div class="loginfake" style="position: relative;width: 300px;height: 150px;" :style="'background-image: url(/tarkan/assets/custom/bg.jpg?uncache='+uncache+');'">
              <img style="z-index: 9999999;width: 200px;position: absolute;top: 50%;left: calc(50% - 100px);transform: translateY(-50%);" :src="'/tarkan/assets/custom/logoWhite.png?uncache='+uncache">
            </div>

            <div>



              <el-form-item :label="KT('theme.logoOnLogin')">
                <el-upload
                    class="upload-demo"
                    action="/tarkan/theme/upload?type=logo-login"

                    :on-success = "onSuccess"
                >
                  <el-button type="primary">{{KT('select')}}</el-button>

                </el-upload>
              </el-form-item>

              <el-form-item :label="KT('theme.logoSize')">
                <el-slider v-model="sizeLogo" @change="changeLogo($event)" :min="10" :max="100"></el-slider>
              </el-form-item>
            </div>

          </div>





          <div style="display: flex;justify-content: space-between">
            <div style="position: relative;background: var(--el-bg-color);width: 300px;height: 150px;">
              <img style="width: 200px;position: absolute;top: 50%;left: calc(50% - 100px);transform: translateY(-50%);" :src="'/tarkan/assets/custom/logo.png?uncache='+uncache">
            </div>

            <div>



              <el-form-item :label="KT('theme.logoOnHead')">
                <el-upload
                    class="upload-demo"
                    action="/tarkan/theme/upload?type=logo-interno"

                    :on-success = "onSuccess"
                >
                  <el-button type="primary">{{KT('select')}}</el-button>

                </el-upload>
              </el-form-item>
            </div>

          </div>
        </el-form>


      </el-tab-pane>

      <el-tab-pane :label="KT('theme.generalColors')" name="first2">
        <el-form label-width="150px" label-position="left">
          <div style="display: flex;justify-content: space-between">
            <el-form-item  :label="KT('theme.backgroundColor')" >
              <el-color-picker @active-change="updateGlobal()" @change="updateGlobal()" v-model="formData['--el-bg-color']" ></el-color-picker>
            </el-form-item>

            <el-form-item  :label="KT('theme.textColor')" >
              <el-color-picker @active-change="updateGlobal()" @change="updateGlobal()" v-model="formData['--el-text-color-primary']" ></el-color-picker>
            </el-form-item>
          </div>

          <div style="display: flex;justify-content: space-between">
            <el-form-item  :label="KT('theme.generalWhite')" >
              <el-color-picker @active-change="updateGlobal()" @change="updateGlobal()" v-model="formData['--el-color-white']" ></el-color-picker>
            </el-form-item>

            <el-form-item  :label="KT('theme.generalBlack')" >
              <el-color-picker @active-change="updateGlobal()" @change="updateGlobal()" v-model="formData['--el-color-black']" ></el-color-picker>
            </el-form-item>
          </div>


          <div style="display: flex;justify-content: space-between">
            <el-form-item  :label="KT('theme.primary')" >
              <el-color-picker @active-change="updateGlobal()" @change="updateGlobal()" v-model="formData['--el-color-primary']" ></el-color-picker>
            </el-form-item>

            <el-form-item  :label="KT('theme.variant1')" >
              <el-color-picker @active-change="updateGlobal()" @change="updateGlobal()" v-model="formData['--el-color-primary-light-1']" ></el-color-picker>
            </el-form-item>
          </div>



          <div style="display: flex;justify-content: space-between">
            <el-form-item  :label="KT('theme.variant2')"  >
              <el-color-picker @active-change="updateGlobal()" @change="updateGlobal()" v-model="formData['--el-color-primary-light-2']" ></el-color-picker>
            </el-form-item>

            <el-form-item  :label="KT('theme.variant3')"  >
              <el-color-picker @active-change="updateGlobal()" @change="updateGlobal()" v-model="formData['--el-color-primary-light-3']" ></el-color-picker>
            </el-form-item>
          </div>



          <div style="display: flex;justify-content: space-between">
            <el-form-item  :label="KT('theme.variant4')"  >
              <el-color-picker @active-change="updateGlobal()" @change="updateGlobal()" v-model="formData['--el-color-primary-light-4']" ></el-color-picker>
            </el-form-item>

            <el-form-item  :label="KT('theme.variant5')"  >
              <el-color-picker @active-change="updateGlobal()" @change="updateGlobal()" v-model="formData['--el-color-primary-light-5']" ></el-color-picker>
            </el-form-item>
          </div>


          <div style="display: flex;justify-content: space-between">
            <el-form-item  :label="KT('theme.variant6')"  >
              <el-color-picker @active-change="updateGlobal()" @change="updateGlobal()" v-model="formData['--el-color-primary-light-6']" ></el-color-picker>
            </el-form-item>

            <el-form-item  :label="KT('theme.variant7')"  >
              <el-color-picker @active-change="updateGlobal()" @change="updateGlobal()" v-model="formData['--el-color-primary-light-7']" ></el-color-picker>
            </el-form-item>
          </div>


          <div style="display: flex;justify-content: space-between">
            <el-form-item :label="KT('theme.variant8')" >
              <el-color-picker @active-change="updateGlobal()" @change="updateGlobal()" v-model="formData['--el-color-primary-light-8']" ></el-color-picker>
            </el-form-item>

            <el-form-item :label="KT('theme.variant9')"  >
              <el-color-picker @active-change="updateGlobal()" @change="updateGlobal()" v-model="formData['--el-color-primary-light-9']" ></el-color-picker>
            </el-form-item>
          </div>




        </el-form>


      </el-tab-pane>


      <el-tab-pane :label="KT('theme.textColors')" name="second">
        <el-form label-width="170px" label-position="left">
          <div style="display: flex;justify-content: space-between">
            <el-form-item  :label="KT('theme.textPrimary')" >
              <el-color-picker @active-change="updateGlobal()" @change="updateGlobal()" v-model="formData['--el-text-color-primary']" ></el-color-picker>
            </el-form-item>

            <el-form-item   :label="KT('theme.textRegular')"  >
              <el-color-picker @active-change="updateGlobal()" @change="updateGlobal()" v-model="formData['--el-text-color-regular']" ></el-color-picker>
            </el-form-item>
          </div>

          <div style="display: flex;justify-content: space-between">
            <el-form-item  :label="KT('theme.textSecondary')"  >
              <el-color-picker @active-change="updateGlobal()" @change="updateGlobal()" v-model="formData['--el-text-color-secondary']" ></el-color-picker>
            </el-form-item>

            <el-form-item   :label="KT('theme.textWhite')"  >
              <el-color-picker @active-change="updateGlobal()" @change="updateGlobal()" v-model="formData['--el-text-color-placeholder']" ></el-color-picker>
            </el-form-item>
          </div>

        </el-form>
      </el-tab-pane>


      <el-tab-pane :label="KT('theme.otherColors')" name="third">
        <el-form label-width="170px" label-position="left">
          <div style="display: flex;justify-content: space-between">
            <el-form-item  :label="KT('theme.success')" >
              <el-color-picker @active-change="updateGlobal()" @change="updateGlobal()" v-model="formData['--el-color-success']" ></el-color-picker>
            </el-form-item>

            <el-form-item  :label="KT('theme.successVariant1')" >
              <el-color-picker @active-change="updateGlobal()" @change="updateGlobal()" v-model="formData['--el-color-success-light']" ></el-color-picker>
            </el-form-item>


            <el-form-item  :label="KT('theme.successVariant2')" >
              <el-color-picker @active-change="updateGlobal()" @change="updateGlobal()" v-model="formData['--el-color-success-lighter']" ></el-color-picker>
            </el-form-item>
          </div>

          <div style="display: flex;justify-content: space-between">
            <el-form-item  :label="KT('theme.alert')" >
              <el-color-picker @active-change="updateGlobal()" @change="updateGlobal()" v-model="formData['--el-color-warning']" ></el-color-picker>
            </el-form-item>

            <el-form-item  :label="KT('theme.alertVariant1')" >
              <el-color-picker @active-change="updateGlobal()" @change="updateGlobal()" v-model="formData['--el-color-warning-light']" ></el-color-picker>
            </el-form-item>


            <el-form-item  :label="KT('theme.alertVariant2')">
              <el-color-picker @active-change="updateGlobal()" @change="updateGlobal()" v-model="formData['--el-color-warning-lighter']" ></el-color-picker>
            </el-form-item>
          </div>


          <div style="display: flex;justify-content: space-between">
            <el-form-item  :label="KT('theme.danger')" >
              <el-color-picker @active-change="updateGlobal()" @change="updateGlobal()" v-model="formData['--el-color-danger']" ></el-color-picker>
            </el-form-item>

            <el-form-item  :label="KT('theme.dangerVariant1')" >
              <el-color-picker @active-change="updateGlobal()" @change="updateGlobal()" v-model="formData['--el-color-danger-light']" ></el-color-picker>
            </el-form-item>


            <el-form-item  :label="KT('theme.dangerVariant2')" >
              <el-color-picker @active-change="updateGlobal()" @change="updateGlobal()" v-model="formData['--el-color-danger-lighter']" ></el-color-picker>
            </el-form-item>
          </div>



          <div style="display: flex;justify-content: space-between">
            <el-form-item  :label="KT('theme.info')" >
              <el-color-picker @active-change="updateGlobal()" @change="updateGlobal()" v-model="formData['--el-color-info']" ></el-color-picker>
            </el-form-item>

            <el-form-item :label="KT('theme.infoVariant1')" >
              <el-color-picker @active-change="updateGlobal()" @change="updateGlobal()" v-model="formData['--el-color-info-light']" ></el-color-picker>
            </el-form-item>


            <el-form-item  :label="KT('theme.infoVariant2')" >
              <el-color-picker @active-change="updateGlobal()" @change="updateGlobal()" v-model="formData['--el-color-info-lighter']" ></el-color-picker>
            </el-form-item>
          </div>



        </el-form>
      </el-tab-pane>
    </el-tabs>
  </el-dialog>
</template>


<script setup>


import 'element-plus/es/components/input/style/css'
import 'element-plus/es/components/button/style/css'
import 'element-plus/es/components/switch/style/css'
import 'element-plus/es/components/select/style/css'
import 'element-plus/es/components/option/style/css'
import 'element-plus/es/components/dialog/style/css'
import 'element-plus/es/components/tab-pane/style/css'
import 'element-plus/es/components/tabs/style/css'
import 'element-plus/es/components/message/style/css'
import 'element-plus/es/components/checkbox/style/css'
import 'element-plus/es/components/color-picker/style/css'
import 'element-plus/es/components/upload/style/css'
import 'element-plus/es/components/slider/style/css'

import {ElDialog,ElTabs,ElTabPane,ElForm,ElSwitch,ElFormItem,ElButton,ElInput,ElSlider,ElUpload,ElColorPicker} from "element-plus";


import KT from '../../func/kt';

import {ref,defineExpose} from 'vue';
import {useStore} from 'vuex';

const store = useStore();

const title = ref('');

const show = ref(false);
const tab = ref('general');

const uncache = ref(new Date().getTime());

// eslint-disable-next-line no-undef
const labelConf = ref(CONFIG);

// eslint-disable-next-line no-undef
const formData = ref(defaultThemeData);

const sizeLogo = ref(80);

const changeLogo = (e)=>{
  const tmp = JSON.parse(JSON.stringify(store.state.server.serverInfo));
  tmp.attributes['tarkan.logoWidth'] = e;

  store.dispatch("server/save",tmp);
}

const updateGlobal = ()=>{

  let tmp = [];

  // eslint-disable-next-line no-undef
  for(var v of Object.keys(defaultThemeData)){
    tmp.push(v+':'+formData.value[v]+';');
  }

  document.querySelector(":root").style=tmp.join("");
}

const onSuccess = ()=>{
  uncache.value = new Date().getTime();
}

const showTheme = ()=>{


  title.value = KT('theme.edit');
  tab.value = 'general';
  // eslint-disable-next-line no-undef
  formData.value = JSON.parse(JSON.stringify(defaultThemeData));

  show.value = true;
  sizeLogo.value = store.state.server.serverInfo.attributes['tarkan.logoWidth'] || 80;
}

defineExpose({
  showTheme
});


const doCancel = ()=>{
  show.value = false;
}





const doSave = ()=>{
  window.$tarkan.saveTheme({config: labelConf.value,colors: formData.value}).then(()=>{
      show.value = false;

      window.location.reload();
  })
}


</script>

<style>

.el-select.el-select--large{
  width: 100%;
}

.el-dialog__header,.el-dialog__body,.el-dialog__footer{
  padding: 0px !important;
}

.el-dialog__footer{
  margin-top: 20px;
}

.el-tabs__nav-wrap{
  padding-left: 20px;
  padding-right: 20px;
}

.el-tabs__content{
  padding-left: 20px;
  padding-right: 20px;
}




.loginfake{

  background-size: cover;
  width: 300px;
  height: 150px;
}

.loginfake:after {
  content: " ";
  position: absolute;
  left: 0;
  top: 0;
  width: 100%;
  height: 170px;
  box-sizing: border-box;
  background: var(--tk-login-filter);
}

</style>