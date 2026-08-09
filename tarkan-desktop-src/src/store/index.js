import { createStore } from 'vuex'
import server from './modules/server'
import devices from './modules/devices'
import groups from './modules/groups'
import geofences from './modules/geofence'
import commands from './modules/commands'
import users from './modules/users'
import shares from './modules/shares'
import events from './modules/events'
import attributes from './modules/computedAttributes'
import drivers from './modules/drivers'
import calendars from './modules/calendars'
import maintenance from './modules/maintenance'

import i18n from '../lang/';

//import router from '../routes.js';
import {ElMessageBox} from "element-plus/es/components/message-box";

// Create a new index instance.
const store = createStore({
    state () {
        return {
            auth: false,
            permissions: [],
            groupPref: (window.localStorage.getItem('groupPref'))?JSON.parse(window.localStorage.getItem('groupPref')):{},
            mapPref: (window.localStorage.getItem('mapPref'))?JSON.parse(window.localStorage.getItem('mapPref')):{},
            time: new Date().getTime()
        }
    },
    getters: {
        isDriver(state){
            return (state.auth && state.auth.attributes && state.auth.attributes['tarkan.isQrDriverId']);
        },
        isDriverOnDevice(state){
            return (state.auth && state.auth.attributes && state.auth.attributes['tarkan.isQrDeviceId']);
        },
        checkDeviceLimit(state){
            const dLength = store.state.devices.deviceList.length;
            const userLimit = state.auth.deviceLimit;
            const serverLimit = parseInt(store.getters['server/getAttribute']('tarkan.deviceLimit') || -1);

            const availableLimit = parseInt(store.state.server.allowedLimit);
            if(store.state.server.allowedLimit!==false && availableLimit<=0){
                return false;
            }else if(serverLimit>0 && dLength>=serverLimit){
                return false;
            }else if(userLimit>-1 && dLength>=userLimit){
                return false;
            }else{
                return true;
            }

        },
        isAdmin(state){
            if(!state.auth){
                return false;
            }

            return state.auth.administrator;
        },
        isReadonly(state){
            if(!state.auth){
                return false;
            }

            return store.state.auth.readonly;
        },
        isDeviceReadonly(state){
            if(!state.auth){
                return false;
            }

            return store.state.auth.deviceReadonly;
        },
        isLimitCommands(state){
            if(!state.auth){
                return false;
            }
            return store.state.auth.limitCommands;
        },
        mapPref(state){
            return (p)=>{
                return state.mapPref[p] || false;
            }
        },
        groupPref(state){
            return (p)=>{
                if(state.groupPref[p]==undefined && p==0){
                    return true;
                }else if(state.groupPref[p]){
                    return state.groupPref[p];
                }else{
                    return false;
                }
            }
        },
        advancedPermissions(state){
            return (a)=>{

                if((state.auth && state.auth.attributes && state.auth.attributes['tarkan.isQrDriverId'])){
                    return false;
                }else if(state.auth.administrator){
                    return true;
                }else if(!state.server.isPlus){
                    return true;
                }else if(state.server.serverInfo.attributes['tarkan.enableAdvancedPerms'] && state.permissions!==false){

                    return state.permissions[a] && parseInt(state.permissions[a]) === 1;
                }else if(state.auth.attributes['isShared']){
                    return false;
                }else{
                    return true;
                }
            }
        },
        expiresCountDown(state){
            if(state.auth && state.auth.attributes['isShared']) {

                const remaing = Math.round((new Date(state.auth.expirationTime).getTime() - state.time)/1000);

                if(remaing>86400){
                    return Math.ceil(remaing/86400)+' dias';
                }else if(remaing>3600){
                    return Math.ceil(remaing/3600)+' horas';
                }else if(remaing>60){
                    return Math.ceil(remaing/60)+' minutos';
                }else{
                    if(remaing<0){

                        store.commit("setAuth",false);
                        //store.dispatch("logout");
                        ElMessageBox.confirm(
                            'Sua sessão expirou!',
                            'Atenção',
                            {
                                confirmButtonText: 'Sair',
                                confirmButtonClass: 'danger',
                                cancelButtonText: 'Cancelar',
                                showCancelButton: false,
                                type: 'warning',
                            }
                        ).then(()=> {
                            store.commit("setAuth",false);
                            window.location.reload();
                        });
                    }

                    return remaing+' segundos';
                }



            }else{
                return 0;
            }
        },
        getDeviceAttributes(state){
            let attrs = [];

            const serverAttrs = (state.server.serverInfo.attributes['tarkan.deviceAttributes'] || '').split(",");
            const userAttrs = (state.auth.attributes['tarkan.deviceAttributes'] || '').split(",");

            serverAttrs.forEach((a)=>{
                if(!attrs.includes(a) && a!==''){
                    attrs.push({id: a, type: 'server'});
                }
            })


            userAttrs.forEach((a)=>{
                if(!attrs.includes(a) && a!==''){
                    attrs.push({id: a, type: 'user'});
                }
            })

            return attrs;


        }
    },
    mutations: {
        setAuth (state,auth) {
                state.auth = auth;


                if (state.auth !== false && auth.attributes['tarkan.lang']) {
                    i18n.global.locale = auth.attributes['tarkan.lang'];
                }

                if (state.auth && state.server.isPlus && state.server.serverInfo.attributes['tarkan.enableAdvancedPerms']) {
                    if (auth.attributes['tarkan.advancedPerms']) {

                        const p1 = parseInt(auth.attributes['tarkan.advancedPerms'].substring(0, 8), 16).toString(2).padStart(32, '0');
                        const p2 = parseInt(auth.attributes['tarkan.advancedPerms'].substring(8, 16), 16).toString(2).padStart(32, '0');
                        const p3 = parseInt(auth.attributes['tarkan.advancedPerms'].substring(16, 24), 16).toString(2).padStart(32, '0');
                        const p4 = parseInt(auth.attributes['tarkan.advancedPerms'].substring(24, 32), 16).toString(2).padStart(32, '0');

                        state.permissions = (p1 + p2 + p3 + p4).split("");
                    } else {
                        state.permissions = false;
                    }
                }
        },
        setTime(state,value){
            state.time = value;
        },
        setMap(state,value){
            state.auth.map = value;
        },
        setMapPref(state,value){
          if(!state.mapPref[value]){
              state.mapPref[value] = true;
          }else{
              state.mapPref[value] = !state.mapPref[value];
          }

          window.localStorage.setItem('mapPref',JSON.stringify(state.mapPref));


        },
        setGroupPref(state,value){
            state.groupPref[value.id] = value.value;

            window.localStorage.setItem('groupPref',JSON.stringify(state.groupPref));
        },
        setToken(state,value){
            state.auth.attributes['notificationTokens'] = value;
        }
    },
    actions: {
        setToken(context,params){
            context.commit("setToken",params);

            const tmp = JSON.parse(JSON.stringify(context.state.auth));

            tmp.attributes['notificationTokens'] = params;

            context.dispatch("users/save",tmp);
        },
        setMap(context,params){
            context.commit("setMap",params);

            const tmp = JSON.parse(JSON.stringify(context.state.auth));

            tmp.map = params;

            context.dispatch("users/save",tmp);
        },
        setMapPref(context,value){
            context.commit("setMapPref",value);
        },
        setGroupPref(context,id){

            const value = {id: id,value: !context.getters.groupPref(id)};

            context.commit("setGroupPref",value);
        },
        async loadUserDataExtra(context){

            await context.dispatch('events/load');
            await context.dispatch('groups/load');
            await context.dispatch('commands/load');
            await context.dispatch('geofences/load');
            await context.dispatch('attributes/load');
            await context.dispatch('drivers/load');
            await context.dispatch('calendars/load');
            await context.dispatch('maintenance/load');
            await context.dispatch('shares/load');

            if(context.state.auth.userLimit===-1 || context.state.auth.userLimit>0 || context.state.auth.administrator) {

                await context.dispatch('users/load')
            }

        },
        async loadUserData(context,waitDevice){


            // Positions DEVE ser carregado ANTES dos devices para que markers tenham posição
            await context.dispatch('devices/positions');
            await context.dispatch('devices/load',waitDevice);

            context.dispatch('devices/connectWs');


            store.dispatch("loadUserDataExtra");



            setInterval(()=>{
                context.commit("setTime",new Date().getTime());
            },1000);
        },
        checkSession(context){
            return new Promise((resolve,reject)=>{

                    const data = window.localStorage.getItem('rememberme')
                    if(data) {
                        const pw = atob(data).split("|");

                        window.$traccar.login(pw[0],pw[1]).then((data) => {
                            resolve(data);
                            context.commit("setAuth", data);
                        }).catch(() => {
                            reject();
                        });
                    }else {

                        window.$traccar.getSession().then(({data}) => {
                            resolve(data);
                            context.commit("setAuth", data);
                        }).catch(() => {
                            reject();
                        });
                    }
            })
        },
        logout(context){
          return new Promise((resolve,reject)=>{
              window.$traccar.deleteSession().then(({data})=>{
                  context.commit("setAuth",false);

                  window.localStorage.removeItem('rememberme');

                  window.$traccar.closeWS();
                  window.location.reload();

                  resolve(data);
              }).catch(()=>{

                  context.commit("setAuth",false);

                  window.$traccar.closeWS();
                  window.location.reload();

                  reject();
              });
          })
        },
        pinServer(context,id){
            context.dispatch("server/addFavAttr",id);
        },
        pinUser(context,id){

            let attr = context.state.auth.attributes['tarkan.deviceAttributes'];
            if(id[1]) {
                if (attr) {
                    attr += ',' + id[0];
                } else {
                    attr = id[0];
                }
            }else{
                if(attr){
                    attr = attr.split(",");
                    attr.splice(attr.findIndex((a)=> a === id[0]),1);

                    attr = attr.join(",");
                }else{
                    attr = '';
                }
            }


            let user = context.state.auth;
                 user.attributes['tarkan.deviceAttributes'] = attr;


            context.commit("setAuth",user);
            context.dispatch("users/save",user).then((data)=>{
                context.commit("setAuth",data);
            })

        }
    },
    modules: {
        server,
        devices,
        groups,
        geofences,
        commands,
        users,
        shares,
        attributes,
        events,
        drivers,
        calendars,
        maintenance
    }
})

export default store;