
<div>
<show-graphic ref="showGraphicsRef"></show-graphic>
<edit-calendars ref="editCalendarsRef"></edit-calendars>
<link-objects ref="linkObjectsRef"></link-objects>
<log-objects ref="logObjectsRef"></log-objects>



<edit-share v-if="store.state.server.isPlus" ref="editShareRef"></edit-share>
<edit-shares v-if="store.state.server.isPlus" ref="editSharesRef"></edit-shares>

<edit-user ref="editUserRef"></edit-user>
<edit-notifications ref="editNotificationsRef"></edit-notifications>
<edit-group ref="editGroupRef"></edit-group>
<edit-users ref="editUsersRef"></edit-users>
<edit-device ref="editDeviceRef"></edit-device>
<edit-server ref="editServerRef"></edit-server>
<edit-drivers ref="editDriversRef"></edit-drivers>
<edit-maintenances ref="editMaintenancesRef"></edit-maintenances>
<edit-theme  v-if="store.state.server.isPlus" ref="editThemeRef"></edit-theme>

<div id="head">
  <div id="btnmenu" @click="menuShown = !menuShown">
    <i class="fas fa-bars"></i>
  </div>
  <div id="logo">
    <img src="/tarkan/assets/custom/logo.png" style="width: 11rem;" >
  </div>
  <div style="display: flex;">



    <el-tooltip :content="(store.state.events.mute)?'Ouvir Notificações':'Silenciar Notificações'">
      <div id="mute" @click="store.dispatch('events/toggleMute')" style="cursor: pointer;font-size: 1.2rem;margin: 0.3rem 0.5rem;">
        <i v-if="store.state.events.mute" class="fas fa-volume-mute" style="color: silver;"></i>
        <i v-else class="fas fa-volume-up"></i>
      </div>
    </el-tooltip>

    <push-notification-btn v-if="false"></push-notification-btn>


    <div id="user" @click="userMenu($event)" style="cursor: pointer;">
      <div class="uname" v-if="store.state.auth && !store.state.auth.attributes['isShared']" style="font-size: 0.8rem;margin: 0.5rem 0.5rem;">{{store.state.auth.name}}</div>
      <div class="uname" v-else style="text-align: right;font-size: 1.4rem;margin: 0.3rem 0.5rem;">
        <div style="font-size: 0.3rem;">Expira em:</div>
        <div style="font-size: 0.5rem">{{store.getters.expiresCountDown}}</div>
      </div>
      <i style="font-size: 1.4rem;margin: 0.3rem 0.5rem;" class="fas fa-user-circle"></i>
    </div>
  </div>
</div>
<div id="content">
  <div id="menu" :class="{isopen: menuShown}" v-if="store.state.auth && !store.state.auth.attributes['isShared']">
    <ul>
      <router-link to="/devices" custom v-slot="{ href,  navigate, isActive, isExactActive }">
        <li :class="{active: isActive || isExactActive,'exact-active': isExactActive}">
          <a :href="href" @click="navigate">
            <el-icon>
              <i class="fas fa-car"></i>
            </el-icon>
            <span class="text" >{{$t('menu.devices')}}</span>
          </a>
        </li>
      </router-link>


      <router-link to="/history" custom v-slot="{ href,  navigate, isActive, isExactActive }">
        <li :class="{active: isActive,'exact-active': isExactActive}">
          <a :href="href" @click="navigate">
            <el-icon>
              <i class="fas fa-route"></i>
            </el-icon>
            <span class="text">{{$t('menu.history')}}</span>
          </a>
        </li>
      </router-link>



      <router-link to="/reports" custom v-slot="{ href,  navigate, isActive, isExactActive }">
        <li :class="{active: isActive,'exact-active': isExactActive}">
          <a :href="href" @click="navigate">
            <el-icon>
              <i class="fas fa-chart-bar"></i>
            </el-icon>
            <span class="text" >{{$t('menu.reports')}}</span>
          </a>
        </li>
      </router-link>


      <router-link to="/geofence" custom v-slot="{ href,  navigate, isActive, isExactActive }">
        <li :class="{active: isActive,'exact-active': isExactActive}">
          <a :href="href" @click="navigate">
            <el-icon>
              <i class="fas fa-draw-polygon"></i>
            </el-icon>
            <span class="text" >{{$t('menu.geofence')}}</span>
          </a>
        </li>
      </router-link>


      <router-link to="/commands" custom v-slot="{ href,  navigate, isActive, isExactActive }">
        <li :class="{active: isActive,'exact-active': isExactActive}">
          <a :href="href" @click="navigate">
            <el-icon>
              <i class="far fa-keyboard"></i>
            </el-icon>
            <span class="text" >{{$t('menu.commands')}}</span>
          </a>
        </li>
      </router-link>



      <router-link to="/groups" custom v-slot="{ href,  navigate, isActive, isExactActive }">
        <li :class="{active: isActive,'exact-active': isExactActive}">
          <a :href="href" @click="navigate">
            <el-icon>
              <i class="far fa-object-group"></i>
            </el-icon>
            <span class="text" >{{$t('menu.groups')}}</span>
          </a>
        </li>
      </router-link>

      <router-link to="/notifications" custom v-slot="{ href,  navigate, isActive, isExactActive }">
        <li :class="{active: isActive,'exact-active': isExactActive}">
          <a :href="href" @click="navigate">
            <el-icon>
              <i class="fas fa-bell"></i>
            </el-icon>
            <span class="text" >{{$t('menu.notifications')}}</span>
          </a>
        </li>
      </router-link>



      <div class="indicator"></div>


    </ul>


    <div id="version">{{$t('version')}}</div>
  </div>
  <div id="open" :class="{bottom: ($route.meta.mobileBottom),mobileExpanded: mobileExpand,shown: ($route.meta.shown),editing: store.state.geofences.mapEditing,allowExpand: $route.meta.allowExpand,expanded: ($route.meta.allowExpand && $route.query.expand==='true') }">
    <div style="width: calc(100%); " :style="{display: (store.state.geofences.mapEditing)?'none':'' }">
      <div id="heading">
        <span @click="$route.meta.backBtn?$router.push($route.meta.backBtn):$router.back()"><i class="fas fa-angle-double-left"></i></span>
        {{KT($route.meta.title || 'page')}}
        <span @click="$router.push('/home')"><i class="fas fa-times-circle"></i></span></div>

      <div v-if="($route.meta.mobileBottom)" @click="$router.push('/home')" class="showOnMobile" style="position: absolute;right: 5px;top: 5px;font-size: 18px;"><i class="fas fa-times-circle"></i></div>
      <div v-if="($route.meta.mobileBottom)" id="expander" @click="mobileExpand = !mobileExpand">
        <i v-if="!mobileExpand" class="fas fa-angle-double-up"></i>
        <i v-else class="fas fa-angle-double-down"></i>
      </div>


      <div id="rv"><router-view ></router-view></div>

    </div>
    <div v-if="store.state.geofences.mapEditing">
      <div style="padding: 10px;"><el-button @click="store.dispatch('geofences/disableEditing')" type="primary">Concluir</el-button></div>
      <div style="padding: 10px;"><el-button type="warning" plain>{{KT('reset')}}</el-button></div>
      <div style="padding: 10px;"><el-button type="danger" plain>{{KT('cancel')}}</el-button></div>
    </div>

    <div v-if="$route.meta.allowExpand" class="expandBtn" @click="$router.push({query: {expand: $route.query.expand==='true'?'false':'true'}})"><i class="fas fa-angle-double-right"></i></div>
  </div>

  <div id="main"
       :class="{menuShown: menuShown,editing: store.state.geofences.mapEditing}" :style="{width: (store.state.auth.attributes['isShared'])?'100vw':''}">

    <street-view v-if="store.state.devices.streetview" ></street-view>

    <kore-map></kore-map>


  </div>
</div>
</div>