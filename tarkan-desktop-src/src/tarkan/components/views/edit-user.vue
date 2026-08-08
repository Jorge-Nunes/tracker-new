<template>
  <el-dialog :lock-scroll="true" v-model="show">

    <template v-slot:title>
      <div  style="border-bottom: #e0e0e0 1px solid;padding: 20px;">
        <div class="modal-title" >{{title}}</div>
      </div>
    </template>
    <template v-slot:footer>
      <div  style="margin-top: 20px;border-top: #e0e0e0 1px solid;padding: 20px;display: flex;justify-content: space-between;">

        <el-button type="danger" plain @click="doCancel()">{{$t('cancel')}}</el-button>


        <el-button v-if="formData.id === store.state.auth.id" type="primary" plain @click="testNotification()">{{$t('notification.test')}}</el-button>

        <el-button v-if="formData.id === store.state.auth.id && store.getters['server/getAttribute']('telegramBot')" type="primary" plain @click="placeTelegram()">Vincular Telegram</el-button>
        <el-button type="primary" @click="doSave()">{{$t('save')}}</el-button>
      </div>
    </template>

    <el-form ref="formRef" label-width="120px" label-position="top" :model="formData" :rules="rules">
    <el-tabs v-model="tab">
      <el-tab-pane :label="$t('user.accountInfo')" name="first">
          <el-form-item :label="$t('user.name')" prop="name">
            <el-input v-model="formData.name"  :disabled="isSupAdmin || (formData.id===store.state.auth.id && !store.getters.advancedPermissions(0))"></el-input>
          </el-form-item>

          <el-form-item :label="$t('user.phone')" >
            <el-input v-model="formData.phone" :disabled="isSupAdmin  || (formData.id===store.state.auth.id && !store.getters.advancedPermissions(0))"></el-input>
          </el-form-item>

          <el-form-item :label="$t('user.email')" prop="email">
            <el-input v-model="formData.email" :disabled="isSupAdmin || (formData.id===store.state.auth.id && !store.getters.advancedPermissions(0))"></el-input>
          </el-form-item>
          <el-form-item :label="$t('user.password')" style="margin-bottom: 22px !important;" prop="password">
            <el-input v-model="formData.password" :disabled="isSupAdmin" type="password"></el-input>
          </el-form-item>



      </el-tab-pane>

      <el-tab-pane :label="$t('user.preferences')" name="second">

          <el-form-item :label="$t('user.language')" >
            <el-select v-model="formData.attributes['tarkan.lang']" @change="updateLanguage" filterable :size="'large'"  :placeholder="KT('server.language')" :no-match-text="KT('NO_MATCH_TEXT')" :no-data-text="KT('NO_DATA_TEXT')">
              <el-option
                  :label="'Português do Brasil'"
                  :value="'pt-BR'"
              >
              </el-option>
              <el-option
                  :label="'English'"
                  :value="'en-US'"
              >
              </el-option>
              <el-option
                  :label="'Español'"
                  :value="'es-ES'"
              >
              </el-option>
            </el-select>
          </el-form-item>

        <el-form-item :label="$t('user.map')" >
          <el-select v-model="formData.map" filterable :size="'large'"  :placeholder="KT('server.map')" :no-match-text="KT('NO_MATCH_TEXT')" :no-data-text="KT('NO_DATA_TEXT')">
            <el-option
                :label="'OpenStreetMap'"
                :value="'openstreet'"
            >
            </el-option>
            <el-option
                :label="'Google Maps Sat'"
                :value="'googleST'"
            >
            </el-option>
            <el-option
                :label="'Google Maps Trafego'"
                :value="'googleTR'"
            >
            </el-option>
            <el-option
                :label="'Google Maps'"
                :value="'googleSN'"
            >
            </el-option>
          </el-select>
        </el-form-item>


        <el-form-item :label="$t('user.latitude')" >
          <el-input v-model="formData.latitude" :disabled="isSupAdmin"></el-input>
        </el-form-item>
        <el-form-item :label="$t('user.longitude')" >
          <el-input v-model="formData.longitude" :disabled="isSupAdmin"></el-input>
        </el-form-item>
        <el-form-item :label="$t('user.zoom')" >
          <el-input v-model="formData.zoom" :disabled="isSupAdmin"></el-input>
        </el-form-item>


        <el-form-item :label="$t('user.coordinateFormat')" >
          <el-input v-model="formData.coordinateFormat" :disabled="isSupAdmin"></el-input>
        </el-form-item>
      </el-tab-pane>
      <el-tab-pane v-if="store.state.auth.administrator || store.state.auth.id !== formData.id" :label="$t('user.permissions')" name="third">

        <div v-if="store.state.auth.id !== formData.id" style="display: flex;margin-bottom: 3px;padding: 7px;border-radius: 3px;background: var(--el-color-info-light); align-content: space-between;justify-content: space-between">

          <div style="font-weight: bold;font-size: 16px;">{{$t('user.userStatus')}}</div>

            <el-switch
                v-model="formData.disabled"
                :inactive-text="$t('disabled')"
                :active-text="$t('enabled')"
                :active-value="false"
                :inactive-value="true"
                :disabled="isSupAdmin"
            >
            </el-switch>
          </div>

        <div v-if="store.state.auth.id !== formData.id" style="display: flex;margin-bottom: 3px;padding: 7px;border-radius: 3px;background: var(--el-color-info-light); align-content: space-between;justify-content: space-between">

          <div style="font-weight: bold;font-size: 16px;">{{$t('user.advanced.self')}}</div>

          <el-switch
              v-model="permData[0]"
              :inactive-text="$t('no')"
              :active-text="$t('yes')"
              :disabled="isSupAdmin"
              :active-value="1"
              :inactive-value="0"
          >
          </el-switch>
        </div>

        <div v-if="store.state.auth.id !== formData.id && store.state.auth.administrator" style="display: flex;margin-bottom: 3px;padding: 7px;border-radius: 3px;background: var(--el-color-info-light); align-content: space-between;justify-content: space-between">

          <div style="font-weight: bold;font-size: 16px;">{{$t('user.admin')}}</div>

            <el-switch
                v-model="formData.administrator"
                :inactive-text="$t('no')"
                :active-text="$t('yes')"
                :disabled="isSupAdmin"
            >
            </el-switch>
        </div>

        <template v-if="false && store.state.auth.id !== formData.id && (store.state.auth.administrator || store.getters['advancedPermissions'](105))">

          <div style="display: flex;margin-bottom: 3px;padding: 7px;border-radius: 3px;background: var(--el-color-info-light); align-content: space-between;justify-content: space-between">

            <div style="font-weight: bold;font-size: 16px;">{{$t('user.advanced.reseller')}}</div>
            <el-switch
                v-model="permData[104]"
                :inactive-text="$t('disabled')"
                :active-text="$t('enabled')"
                :disabled="isSupAdmin"
                :active-value="1"
                :inactive-value="0"
            >
            </el-switch>
          </div>
          <div style="border: 1px solid;margin-top: -4px;padding: 7px;border-radius: 0px 0px 5px 5px; border-color: var(--el-color-info-light);" v-if="permData[104]===1">

            <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
              <div>
                {{$t('user.advanced.subReseller')}}
              </div>
              <el-switch
                  v-model="permData[105]"
                  :inactive-text="$t('no')"
                  :active-text="$t('yes')"
                  :disabled="isSupAdmin"
                  :active-value="1"
                  :inactive-value="0"
              >
              </el-switch>
            </div>

            <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
              <div style="padding-top: 7px;">
                {{$t('user.advanced.domainReseller')}}
              </div>
              <div style="width: 40%;"><el-input v-model="formData.attributes['tarkan.domainReseller']" size="small"></el-input></div>
            </div>


            <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
              <div style="padding-top: 7px;" >
                {{$t('user.advanced.limitReseller')}}
              </div>
              <div style="width: 40%;"><el-input v-model="formData.attributes['tarkan.limitReseller']" size="small"></el-input></div>
            </div>

   </div>


        </template>


          <template v-if="store.state.server.isPlus && store.state.server.serverInfo.attributes['tarkan.enableAdvancedPerms'] && !formData.administrator">
            <div style="display: flex;margin-bottom: 3px;padding: 7px;border-radius: 3px;background: var(--el-color-info-light); align-content: space-between;justify-content: space-between">

              <div style="font-weight: bold;font-size: 16px;">{{$t('user.advanced.devices')}}</div>
              <el-switch
                  v-model="permData[8]"
                  :inactive-text="$t('disabled')"
                  :active-text="$t('enabled')"
                  :disabled="isSupAdmin"
                  :active-value="1"
                  :inactive-value="0"
              >
              </el-switch>
            </div>
            <div style="border: 1px solid;margin-top: -4px;padding: 7px;border-radius: 0px 0px 5px 5px; border-color: var(--el-color-info-light);" v-if="permData[8]===1">

              <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('user.advanced.anchor')}}
                </div>
                <el-switch
                    v-model="permData[9]"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :disabled="isSupAdmin"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>

              <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('user.advanced.lock')}}
                </div>
                <el-switch
                    v-model="permData[10]"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :disabled="isSupAdmin"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>


              <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('user.advanced.unlock')}}
                </div>
                <el-switch
                    v-model="permData[11]"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :disabled="isSupAdmin"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>


              <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('user.advanced.command')}}
                </div>
                <el-switch
                    v-model="permData[12]"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :disabled="isSupAdmin"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>


              <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('user.advanced.create')}}
                </div>
                <el-switch
                    v-model="permData[13]"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :disabled="isSupAdmin"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>


              <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('user.advanced.edit')}}
                </div>
                <el-switch
                    v-model="permData[14]"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :disabled="isSupAdmin"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>


              <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('user.advanced.delete')}}
                </div>
                <el-switch
                    v-model="permData[15]"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :disabled="isSupAdmin"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>
            </div>

            <div style="display: flex;margin-bottom: 3px;padding: 7px;border-radius: 3px;background: var(--el-color-info-light); align-content: space-between;justify-content: space-between">

              <div style="font-weight: bold;font-size: 16px;">{{$t('user.advanced.users')}}</div>
              <el-switch
                  v-model="permData[16]"
                  :inactive-text="$t('disabled')"
                  :active-text="$t('enabled')"
                  :disabled="isSupAdmin"
                  :active-value="1"
                  :inactive-value="0"
              >
              </el-switch>
            </div>
            <div style="border: 1px solid;margin-top: -4px;padding: 7px;border-radius: 0px 0px 5px 5px; border-color: var(--el-color-info-light);" v-if="permData[16]===1">

              <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('user.advanced.create')}}
                </div>
                <el-switch
                    v-model="permData[17]"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :disabled="isSupAdmin"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>


              <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('user.advanced.edit')}}
                </div>
                <el-switch
                    v-model="permData[18]"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :disabled="isSupAdmin"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>


              <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('user.advanced.delete')}}
                </div>
                <el-switch
                    v-model="permData[19]"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :disabled="isSupAdmin"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>
            </div>

            <div style="display: flex;margin-bottom: 3px;padding: 7px;border-radius: 3px;background: var(--el-color-info-light); align-content: space-between;justify-content: space-between">

              <div style="font-weight: bold;font-size: 16px;">{{$t('user.advanced.share')}}</div>
              <el-switch
                  v-model="permData[24]"
                  :inactive-text="$t('disabled')"
                  :active-text="$t('enabled')"
                  :disabled="isSupAdmin"
                  :active-value="1"
                  :inactive-value="0"
              >
              </el-switch>
            </div>
            <div style="border: 1px solid;margin-top: -4px;padding: 7px;border-radius: 0px 0px 5px 5px; border-color: var(--el-color-info-light);" v-if="permData[24]===1">

              <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('user.advanced.create')}}
                </div>
                <el-switch
                    v-model="permData[25]"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :disabled="isSupAdmin"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>


              <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('user.advanced.edit')}}
                </div>
                <el-switch
                    v-model="permData[26]"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :disabled="isSupAdmin"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>


              <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('user.advanced.delete')}}
                </div>
                <el-switch
                    v-model="permData[27]"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :disabled="isSupAdmin"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>
            </div>

            <div style="display: flex;margin-bottom: 3px;padding: 7px;border-radius: 3px;background: var(--el-color-info-light); align-content: space-between;justify-content: space-between">

              <div style="font-weight: bold;font-size: 16px;">{{$t('user.advanced.notifications')}}</div>
              <el-switch
                  v-model="permData[32]"
                  :inactive-text="$t('disabled')"
                  :active-text="$t('enabled')"
                  :disabled="isSupAdmin"
                  :active-value="1"
                  :inactive-value="0"
              >
              </el-switch>
            </div>
            <div style="border: 1px solid;margin-top: -4px;padding: 7px;border-radius: 0px 0px 5px 5px; border-color: var(--el-color-info-light);" v-if="permData[32]===1">

              <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('user.advanced.create')}}
                </div>
                <el-switch
                    v-model="permData[33]"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :disabled="isSupAdmin"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>


              <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('user.advanced.edit')}}
                </div>
                <el-switch
                    v-model="permData[34]"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :disabled="isSupAdmin"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>


              <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('user.advanced.delete')}}
                </div>
                <el-switch
                    v-model="permData[35]"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :disabled="isSupAdmin"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>
            </div>

            <div style="display: flex;margin-bottom: 3px;padding: 7px;border-radius: 3px;background: var(--el-color-info-light); align-content: space-between;justify-content: space-between">

              <div style="font-weight: bold;font-size: 16px;">{{$t('user.advanced.geofences')}}</div>
              <el-switch
                  v-model="permData[40]"
                  :inactive-text="$t('disabled')"
                  :active-text="$t('enabled')"
                  :disabled="isSupAdmin"
                  :active-value="1"
                  :inactive-value="0"
              >
              </el-switch>
            </div>
            <div style="border: 1px solid;margin-top: -4px;padding: 7px;border-radius: 0px 0px 5px 5px; border-color: var(--el-color-info-light);" v-if="permData[40]===1">

              <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('user.advanced.create')}}
                </div>
                <el-switch
                    v-model="permData[41]"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :disabled="isSupAdmin"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>


              <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('user.advanced.edit')}}
                </div>
                <el-switch
                    v-model="permData[42]"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :disabled="isSupAdmin"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>


              <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('user.advanced.delete')}}
                </div>
                <el-switch
                    v-model="permData[43]"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :disabled="isSupAdmin"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>
            </div>


            <div style="display: flex;margin-bottom: 3px;padding: 7px;border-radius: 3px;background: var(--el-color-info-light); align-content: space-between;justify-content: space-between">

              <div style="font-weight: bold;font-size: 16px;">{{$t('user.advanced.groups')}}</div>
              <el-switch
                  v-model="permData[48]"
                  :inactive-text="$t('disabled')"
                  :active-text="$t('enabled')"
                  :disabled="isSupAdmin"
                  :active-value="1"
                  :inactive-value="0"
              >
              </el-switch>
            </div>
            <div style="border: 1px solid;margin-top: -4px;padding: 7px;border-radius: 0px 0px 5px 5px; border-color: var(--el-color-info-light);" v-if="permData[48]===1">

              <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('user.advanced.create')}}
                </div>
                <el-switch
                    v-model="permData[49]"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :disabled="isSupAdmin"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>


              <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('user.advanced.edit')}}
                </div>
                <el-switch
                    v-model="permData[50]"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :disabled="isSupAdmin"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>


              <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('user.advanced.delete')}}
                </div>
                <el-switch
                    v-model="permData[51]"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :disabled="isSupAdmin"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>
            </div>


            <div style="display: flex;margin-bottom: 3px;padding: 7px;border-radius: 3px;background: var(--el-color-info-light); align-content: space-between;justify-content: space-between">

              <div style="font-weight: bold;font-size: 16px;">{{$t('user.advanced.commands')}}</div>
              <el-switch
                  v-model="permData[56]"
                  :inactive-text="$t('disabled')"
                  :active-text="$t('enabled')"
                  :disabled="isSupAdmin"
                  :active-value="1"
                  :inactive-value="0"
              >
              </el-switch>
            </div>
            <div style="border: 1px solid;margin-top: -4px;padding: 7px;border-radius: 0px 0px 5px 5px; border-color: var(--el-color-info-light);" v-if="permData[56]===1">

              <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('user.advanced.create')}}
                </div>
                <el-switch
                    v-model="permData[57]"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :disabled="isSupAdmin"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>


              <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('user.advanced.edit')}}
                </div>
                <el-switch
                    v-model="permData[58]"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :disabled="isSupAdmin"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>


              <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('user.advanced.delete')}}
                </div>
                <el-switch
                    v-model="permData[59]"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :disabled="isSupAdmin"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>
            </div>



            <div style="display: flex;margin-bottom: 3px;padding: 7px;border-radius: 3px;background: var(--el-color-info-light); align-content: space-between;justify-content: space-between">

              <div style="font-weight: bold;font-size: 16px;">{{$t('user.advanced.attributes')}}</div>
              <el-switch
                  v-model="permData[64]"
                  :inactive-text="$t('disabled')"
                  :active-text="$t('enabled')"
                  :disabled="isSupAdmin"
                  :active-value="1"
                  :inactive-value="0"
              >
              </el-switch>
            </div>
            <div style="border: 1px solid;margin-top: -4px;padding: 7px;border-radius: 0px 0px 5px 5px; border-color: var(--el-color-info-light);" v-if="permData[64]===1">

              <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('user.advanced.create')}}
                </div>
                <el-switch
                    v-model="permData[65]"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :disabled="isSupAdmin"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>


              <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('user.advanced.edit')}}
                </div>
                <el-switch
                    v-model="permData[66]"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :disabled="isSupAdmin"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>


              <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('user.advanced.delete')}}
                </div>
                <el-switch
                    v-model="permData[67]"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :disabled="isSupAdmin"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>
            </div>



            <div style="display: flex;margin-bottom: 3px;padding: 7px;border-radius: 3px;background: var(--el-color-info-light); align-content: space-between;justify-content: space-between">

              <div style="font-weight: bold;font-size: 16px;">{{$t('user.advanced.reports')}}</div>
              <el-switch
                  v-model="permData[72]"
                  :inactive-text="$t('disabled')"
                  :active-text="$t('enabled')"
                  :disabled="isSupAdmin"
                  :active-value="1"
                  :inactive-value="0"
              >
              </el-switch>
            </div>
            <div style="border: 1px solid;margin-top: -4px;padding: 7px;border-radius: 0px 0px 5px 5px; border-color: var(--el-color-info-light);" v-if="permData[72]===1">

              <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('report.history')}}
                </div>
                <el-switch
                    v-model="permData[73]"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :disabled="isSupAdmin"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>


              <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('report.stops')}}
                </div>
                <el-switch
                    v-model="permData[74]"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :disabled="isSupAdmin"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>


              <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('report.travels')}}
                </div>
                <el-switch
                    v-model="permData[75]"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :disabled="isSupAdmin"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>


              <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('report.events')}}
                </div>
                <el-switch
                    v-model="permData[76]"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :disabled="isSupAdmin"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>
            </div>



            <div style="display: flex;margin-bottom: 3px;padding: 7px;border-radius: 3px;background: var(--el-color-info-light); align-content: space-between;justify-content: space-between">

              <div style="font-weight: bold;font-size: 16px;">{{$t('user.advanced.drivers')}}</div>
              <el-switch
                  v-model="permData[80]"
                  :inactive-text="$t('disabled')"
                  :active-text="$t('enabled')"
                  :disabled="isSupAdmin"
                  :active-value="1"
                  :inactive-value="0"
              >
              </el-switch>
            </div>
            <div style="border: 1px solid;margin-top: -4px;padding: 7px;border-radius: 0px 0px 5px 5px; border-color: var(--el-color-info-light);" v-if="permData[80]===1">

              <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('user.advanced.create')}}
                </div>
                <el-switch
                    v-model="permData[81]"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :disabled="isSupAdmin"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>


              <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('user.advanced.edit')}}
                </div>
                <el-switch
                    v-model="permData[82]"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :disabled="isSupAdmin"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>


              <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('user.advanced.delete')}}
                </div>
                <el-switch
                    v-model="permData[83]"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :disabled="isSupAdmin"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>
            </div>



            <div style="display: flex;margin-bottom: 3px;padding: 7px;border-radius: 3px;background: var(--el-color-info-light); align-content: space-between;justify-content: space-between">

              <div style="font-weight: bold;font-size: 16px;">{{$t('user.advanced.calendars')}}</div>
              <el-switch
                  v-model="permData[88]"
                  :inactive-text="$t('disabled')"
                  :active-text="$t('enabled')"
                  :disabled="isSupAdmin"
                  :active-value="1"
                  :inactive-value="0"
              >
              </el-switch>
            </div>
            <div style="border: 1px solid;margin-top: -4px;padding: 7px;border-radius: 0px 0px 5px 5px; border-color: var(--el-color-info-light);" v-if="permData[88]===1">

              <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('user.advanced.create')}}
                </div>
                <el-switch
                    v-model="permData[89]"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :disabled="isSupAdmin"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>


              <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('user.advanced.edit')}}
                </div>
                <el-switch
                    v-model="permData[90]"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :disabled="isSupAdmin"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>


              <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('user.advanced.delete')}}
                </div>
                <el-switch
                    v-model="permData[91]"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :disabled="isSupAdmin"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>
            </div>



            <div style="display: flex;margin-bottom: 3px;padding: 7px;border-radius: 3px;background: var(--el-color-info-light); align-content: space-between;justify-content: space-between">

              <div style="font-weight: bold;font-size: 16px;">{{$t('user.advanced.maintenance')}}</div>
              <el-switch
                  v-model="permData[96]"
                  :inactive-text="$t('disabled')"
                  :active-text="$t('enabled')"
                  :disabled="isSupAdmin"
                  :active-value="1"
                  :inactive-value="0"
              >
              </el-switch>
            </div>
            <div style="border: 1px solid;margin-top: -4px;padding: 7px;border-radius: 0px 0px 5px 5px; border-color: var(--el-color-info-light);" v-if="permData[96]===1">

              <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('user.advanced.create')}}
                </div>
                <el-switch
                    v-model="permData[97]"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :disabled="isSupAdmin"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>


              <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('user.advanced.edit')}}
                </div>
                <el-switch
                    v-model="permData[98]"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :disabled="isSupAdmin"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>


              <div  style="display: flex;align-content: space-between;justify-content: space-between;padding-top: 3px;padding-bottom: 3px;border-bottom: 1px dotted;border-color: var(--el-color-info-light);">
                <div>
                  {{$t('user.advanced.delete')}}
                </div>
                <el-switch
                    v-model="permData[99]"
                    :inactive-text="$t('no')"
                    :active-text="$t('yes')"
                    :disabled="isSupAdmin"
                    :active-value="1"
                    :inactive-value="0"
                >
                </el-switch>
              </div>
            </div>

          </template>


          <template v-else>
          <div  v-if="store.state.auth.id !== formData.id && store.state.auth.administrator" style="display: flex;align-content: space-between;justify-content: space-between">
              <div >
          <el-form-item v-if="!formData.administrator" :label="$t('user.readOnly')">
            <el-switch
                v-model="formData.readonly"
                :inactive-text="$t('no')"
                :active-text="$t('yes')"
                :disabled="isSupAdmin"
            >
            </el-switch>
          </el-form-item>
              </div>
            <div >


          <el-form-item  v-if="!formData.administrator && !formData.readonly" :label="$t('user.editDevices')">
            <el-switch
                v-model="formData.deviceReadonly"
                :inactive-text="$t('no')"
                :active-text="$t('yes')"

                :active-value="false"
                :inactive-value="true"
                :disabled="isSupAdmin"
            >
            </el-switch>
          </el-form-item>

            </div>
            <div >

          <el-form-item  v-if="!formData.administrator && !formData.readonly" :label="$t('user.limitCommands')">
            <el-switch
                v-model="formData.limitCommands"
                :inactive-text="$t('no')"
                :active-text="$t('yes')"

                :active-value="false"
                :inactive-value="true"
                :disabled="isSupAdmin"
            >
            </el-switch>
          </el-form-item>
            </div>
          </div>
          </template>

          <el-form-item :label="$t('user.expirationDate')" >

            <el-date-picker
                size="large"
                v-model="formData.expirationTime"
                type="datetime"
                :placeholder="$t('dateTime')"
                :shortcuts="shortcuts"
                format="DD/MM/YYYY HH:mm"
                :disabled="!store.state.auth.administrator && !(store.state.auth.id !==formData.id)"
            >
            </el-date-picker>
          </el-form-item>
          <el-form-item v-if="store.state.auth.id !== formData.id && store.state.auth.administrator" :label="$t('user.deviceLimit')" >
            <el-switch
                v-model="formDataF.deviceLimitX"
                :inactive-text="$t('set')"
                :active-text="$t('unlimited')"

                :active-value="-1"
                :inactive-value="0"

                @change="changeDeviceLimit($event)"

                :disabled="isSupAdmin"
            >
            </el-switch>
            <el-input v-if="parseInt(formData.deviceLimit)!==-1" v-model="formData.deviceLimit"></el-input>
          </el-form-item>

          <el-form-item v-if="store.state.auth.id !== formData.id && store.state.auth.administrator" :label="$t('user.userLimit')" >
            <el-switch
                v-model="formDataF.userLimitX"
                :inactive-text="$t('set')"
                :active-text="$t('unlimited')"

                :active-value="-1"
                :inactive-value="0"

                @change="changeUserLimit($event)"

                :disabled="isSupAdmin"
            >
            </el-switch>
            <el-input v-if="parseInt(formData.userLimit)!==-1" v-model="formData.userLimit" :disabled="isSupAdmin"></el-input>
          </el-form-item>

          <!-- <el-form-item :label="$t('user.token')" >
            <el-input v-model="formData.token" :disabled="isSupAdmin"></el-input>
          </el-form-item> -->

      </el-tab-pane>
      <el-tab-pane v-if="store.state.auth.administrator || store.state.auth.id !== formData.id" :label="$t('attribute.attributes')" name="fourth">

        <tab-attributes v-model="formData.attributes" :type="'user'"></tab-attributes>

      </el-tab-pane>
    </el-tabs>

    </el-form>
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

import {ElDialog,ElDatePicker,ElMessage,ElTabs,ElTabPane,ElForm,ElSwitch,ElFormItem,ElSelect,ElOption,ElButton,ElInput} from "element-plus";


import {ref,defineExpose,computed,watch} from 'vue';
import {useStore} from 'vuex'

const store = useStore();


import {ElMessageBox} from "element-plus/es/components/message-box";

import TabAttributes from "./tab-attributes";

const formRef = ref(null);
const title = ref('');

const show = ref(false);
const tab = ref('first');

import i18n from '../../../lang/'

import KT from "../../func/kt";

const rules = ref({});

const updateLanguage = (a)=>{
  i18n.global.locale = a;
}


const defaultUserData = {
    "id": 0,
    "name": "",
    "email": "",
    "phone": "",
    "readonly": false,
    "administrator": false,
    "map": "",
    "latitude": "",
    "longitude": "",
    "zoom": "",
    "password": "",
    "coordinateFormat": "",
    "disabled": false,
    "expirationTime": "",
    "deviceLimit": 0,
    "userLimit": 0,
    "deviceReadonly": true,
    "limitCommands": false,
    "poiLayer": "",
    "attributes": {}
  }


// eslint-disable-next-line no-undef
const formData = ref(defaultUserData);
const permData = ref([0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]);

const formDataF = ref({deviceLimitX: 0,userLimitX: 0});

watch(()=> formData.value.deviceLimit,()=>{
  formDataF.value.deviceLimitX = (parseInt(formData.value.deviceLimit)===-1)?-1:0;
})

watch(()=> formData.value.userLimit,()=>{
  formDataF.value.userLimitX = (parseInt(formData.value.userLimit)===-1)?-1:0;
})

const changeDeviceLimit = (e)=>{
  formData.value.deviceLimit = e;
}

const changeUserLimit = (e)=>{
  formData.value.userLimit = e;
}

const newUser = ()=>{
  tab.value = 'first';
  title.value = KT('user.add');
  // eslint-disable-next-line no-undef
    formData.value = JSON.parse(JSON.stringify(defaultUserData));

    if(store.state.auth.expirationTime){
      formData.value.expirationTime = String(store.state.auth.expirationTime);
    }

    if(store.getters['server/getAttribute']('tarkan.lang')){
      formData.value.attributes['tarkan.lang'] = store.getters['server/getAttribute']('tarkan.lang');
    }

  rules.value = {
    name: [
      {
        required: true,
        message: KT('user.form.userNameEmpty'),
        trigger: 'blur',
      },
      {
        min: 3,
        message: KT('user.form.userNameLength'),
        trigger: 'blur',
      },
    ],
    email: [
      {
        required: true,
        message: KT('user.form.emailEmpty'),
        trigger: 'blur',
      },
      {
        min: 4,
        message: KT('user.form.emailLength'),
        trigger: 'blur',
      },
    ],
    password: [
      {
        required: true,
        message: KT('user.form.passwordEmpty'),
        trigger: 'blur',
      },
      {
        min: 4,
        message: KT('user.form.passwordLength'),
        trigger: 'blur',
      },
    ]};

    show.value = true;
}

const editUser = (id)=>{

  if(id===0){
    ElMessage.error(KT('user.error.selectUser'));
    return false;
  }


  rules.value = {
    name: [
      {
        required: true,
        message: KT('user.form.userNameEmpty'),
        trigger: 'blur',
      },
      {
        min: 3,
        message: KT('user.form.userNameLength'),
        trigger: 'blur',
      },
    ],
    email: [
      {
        required: true,
        message: KT('user.form.emailEmpty'),
        trigger: 'blur',
      },
      {
        min: 4,
        message: KT('user.form.emailLength'),
        trigger: 'blur',
      },
    ]};


  title.value = KT('user.edit');
  tab.value = 'first';
  // eslint-disable-next-line no-undef
  formData.value = JSON.parse(JSON.stringify(defaultUserData));

  show.value = true;
  let device = null;
  if(id) {
    device = store.getters["users/getUser"](id);
  }else{
    device = store.state.auth;
    /*if(!device){

      ElMessage.error('Por favor, aguarde...');
      setTimeout(()=>{
        editUser();
      },10000);

      return false;
    }*/
  }



  // eslint-disable-next-line no-undef
  for(let k of Object.keys(defaultUserData)){
    formData.value[k] = device[k];
  }

  if(device.attributes['tarkan.advancedPerms']){
    const p1 = parseInt(device.attributes['tarkan.advancedPerms'].substring(0,8),16).toString(2).padStart(32,'0');
    const p2 = parseInt(device.attributes['tarkan.advancedPerms'].substring(8,16),16).toString(2).padStart(32,'0');
    const p3 = parseInt(device.attributes['tarkan.advancedPerms'].substring(16,24),16).toString(2).padStart(32,'0');
    const p4 = parseInt(device.attributes['tarkan.advancedPerms'].substring(24,32),16).toString(2).padStart(32,'0');

    const perms = (p1+p2+p3+p4).split("");
    perms.forEach((v,k)=>{
      permData.value[k] = parseInt(v);
    })
  }



}

defineExpose({
  newUser,
  editUser
});


const doCancel = ()=>{
  show.value = false;
}


const isSupAdmin = computed(()=>{

  const user = store.getters["users/getUser"](formData.value.id);


  if(user) {
    return (user.id < store.state.auth.id && user.administrator);
  }else{
    return false;
  }
})

const placeTelegram = () => {

  const d = new Date();
  const _id = String(formData.value.id).padStart(5,'0');

  const tkn = 'tel-'+_id+'-'+d.getTime();

  formData.value.token = tkn;

  store.dispatch("users/save", formData.value).then(() => {

    document.location.href="https://t.me/"+store.getters['server/getAttribute']('telegramBot')+"?start="+tkn+"_"+window.location.protocol.replace(':','')+'_'+(window.location.host.replaceAll('.','-'));

  }).catch((r) => {

    const err = r.response.data.split("-")[0].trim().replaceAll(" ", "_").toUpperCase();


    ElMessageBox.alert(KT('user.error.' + err), KT('user.error.save'), {
      confirmButtonText: 'OK'
    })
  })
}


const doSave = ()=>{

  if(isSupAdmin.value){
    ElMessage.error(KT('user.error.isSuperior'));
    return false;
  }



  if(store.state.server.isPlus && !formData.value.administrator && store.state.server.serverInfo.attributes['tarkan.enableAdvancedPerms']){

    const perms = permData.value.join("");
    const permsHex1 = parseInt(perms.substring(0,32),2).toString(16).padStart(8,'0');
    const permsHex2 = parseInt(perms.substring(32,64),2).toString(16).padStart(8,'0');
    const permsHex3 = parseInt(perms.substring(64,96),2).toString(16).padStart(8,'0');
    const permsHex4 = parseInt(perms.substring(96,128),2).toString(16).padStart(8,'0');

    const permsHex = (permsHex1+''+permsHex2+''+permsHex3+''+permsHex4).toUpperCase();

    formData.value.attributes['tarkan.advancedPerms'] = permsHex;


    formData.value.readonly = false;
    formData.value.deviceReadonly = false;
    formData.value.limitCommands = false;

  }

  formRef.value.validate((valid)=> {
    if(valid) {



      store.dispatch("users/save", formData.value).then(() => {
        show.value = false;
      }).catch((r) => {

        const err = r.response.data.split("-")[0].trim().replaceAll(" ", "_").toUpperCase();


        ElMessageBox.alert(KT('user.error.' + err), KT('user.error.save'), {
          confirmButtonText: 'OK'
        })
      })
    }else{
      ElMessage.error(KT('user.error.checkForm'));
    }
  });
}

const testNotification = ()=>{
  window.$traccar.testNotification();

  ElMessage.success(KT('notification.testSent'))

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



</style>