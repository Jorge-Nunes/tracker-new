
import 'element-plus/es/components/notification/style/css'

import {ElNotification} from "element-plus/es/components/notification";
import KT from '../../tarkan/func/kt';

export default {
    namespaced: true,
    state: () => ({
        eventsList: [],
        notificationList: [],
        mute: window.localStorage.getItem('isMuted') || false,
        audios: {

        }
    }),
    getters: {
        getNotificationById(state){
            return (id)=>{
                return state.eventsList.find((f)=> f.id === id);
            }
        },
        getNotification(state){
            return (e)=>{
                return state.eventsList.find((t)=>{
                    if(t.notificators.match('web')){
                        if(t.type===e.type){
                            if(t.type==='alarm' && !t.attributes['alarms'].match(e.attributes.alarm)){
                                return false;
                            }else{
                                return true;
                            }
                        }else{
                            return false;
                        }
                    }else{
                        return false;
                    }
                })
            }
        }
    },
    mutations: {
        toggleMute(state){
          state.mute = !state.mute;
          window.localStorage.setItem('isMuted',state.mute);
        },
        addAudio(state,value){
            state.audios[value.key] = document.createElement("audio");
            state.audios[value.key].src = value.src;
        },
        setEvents(state,value){
            state.eventsList = value;
        },
        addEvent(state,value){
            state.eventsList.push(value);
        },
        removeEvent(state,value){
            state.eventsList.splice(state.eventsList.findIndex((e)=> e.id === value),1);
        },
        updateEvent(state,value){
            state.eventsList.splice(state.eventsList.findIndex((e)=> e.id === value.id),1,value)
        },
        addNotification(state,value){

            state.notificationList.push(value);
        }

    },
    actions: {
        playSound(context,v){

            console.log("play sound "+v)
            context.state.audios[v].play();
        },
        toggleMute(context){
          context.commit("toggleMute");
        },
        proccessNotifications(context,params){



            params.forEach((e)=>{


                const notification = context.getters.getNotification(e);
                const device = context.rootGetters['devices/getDevice'](e.deviceId);
                const geofence = context.rootGetters['geofences/getGeofenceById'](e.geofenceId);

                if(!context.state.mute){
                    if(notification && notification.attributes['tarkan.sound']) {
                        if(notification.attributes['tarkan.sound']==='custom' && notification.attributes['tarkan.soundURL']){
                            const tmp = document.createElement("audio");
                                  tmp.onload = ()=>{
                                      tmp.play();
                                  }

                                  tmp.src = notification.attributes['tarkan.soundURL'];
                        }else if(notification.attributes['tarkan.sound']!=='mute'){
                            if(context.state.audios[notification.attributes['tarkan.sound']]) {
                                context.state.audios[notification.attributes['tarkan.sound']].play();
                            }else{
                                context.state.audios['audio1'].play();
                            }
                        }
                    }else{
                        context.state.audios['audio1'].play();
                    }

                }

                context.commit("addNotification",e);

                if(e.type==='alarm'){
                    ElNotification({
                        title: (device)?device.name:KT('alert'),
                        customClass: (notification && notification.attributes['tarkan.color'])?'notification-'+notification.attributes['tarkan.color']:'',
                        duration: (notification && notification.attributes['tarkan.pin'])?0:7000,
                        message: KT('alarms.' + e.attributes['alarm']),
                        type: 'info',
                        position: (notification && notification.attributes['tarkan.position'])?notification.attributes['tarkan.position']:'bottom-right',
                    });
                }else if(e.type==='commandResult'){
                    ElNotification({
                        title: (device)?device.name:KT('alert'),
                        customClass: (notification && notification.attributes['tarkan.color'])?'notification-'+notification.attributes['tarkan.color']:'',
                        duration: (notification && notification.attributes['tarkan.pin'])?0:7000,
                        message: KT('notification.messages.' + e.type,e.attributes),
                        type: 'info',
                        position: (notification && notification.attributes['tarkan.position'])?notification.attributes['tarkan.position']:'bottom-right',
                    });
                }else{
                    if(geofence) {
                        ElNotification({
                            title: (device) ? device.name : KT('alert'),
                            customClass: (notification && notification.attributes['tarkan.color']) ? 'notification-' + notification.attributes['tarkan.color'] : '',
                            duration: (notification && notification.attributes['tarkan.pin']) ? 0 : 7000,
                            message: KT('notification.messages.' + e.type+'Name', (geofence) ? geofence : e.attributes),
                            type: 'info',
                            position: (notification && notification.attributes['tarkan.position']) ? notification.attributes['tarkan.position'] : 'bottom-right',
                        });
                    }else{
                        ElNotification({
                            title: (device) ? device.name : KT('alert'),
                            customClass: (notification && notification.attributes['tarkan.color']) ? 'notification-' + notification.attributes['tarkan.color'] : '',
                            duration: (notification && notification.attributes['tarkan.pin']) ? 0 : 7000,
                            message: KT('notification.messages.' + e.type, (geofence) ? geofence : e.attributes),
                            type: 'info',
                            position: (notification && notification.attributes['tarkan.position']) ? notification.attributes['tarkan.position'] : 'bottom-right',
                        });
                    }
                }
            })
        },
        load(context){
            return new Promise((resolve)=> {

                context.commit("addAudio",{key: 'audio1',src: '/tarkan/assets/custom/sounds/1-Apple.mp3'});
                context.commit("addAudio",{key: 'audio2',src: '/tarkan/assets/custom/sounds/2-huawei.mp3'});
                context.commit("addAudio",{key: 'audio3',src: '/tarkan/assets/custom/sounds/3-huawei_tune.mp3'});
                context.commit("addAudio",{key: 'audio4',src: '/tarkan/assets/custom/sounds/4-apple_msg_tone.mp3'});
                context.commit("addAudio",{key: 'audio5',src: '/tarkan/assets/custom/sounds/5-cave_huawei_p8.mp3'});
                context.commit("addAudio",{key: 'audio6',src: '/tarkan/assets/custom/sounds/6-apple_pay.mp3'});
                context.commit("addAudio",{key: 'audio7',src: '/tarkan/assets/custom/sounds/7-bright.mp3'});
                context.commit("addAudio",{key: 'audio8',src: '/tarkan/assets/custom/sounds/8-crosswalk_10db.mp3'});
                context.commit("addAudio",{key: 'audio9',src: '/tarkan/assets/custom/sounds/9-japan_crosswalk.mp3'});
                context.commit("addAudio",{key: 'audio10',src: '/tarkan/assets/custom/sounds/10-definite.mp3'});
                context.commit("addAudio",{key: 'audio11',src: '/tarkan/assets/custom/sounds/11-iphone_ding.mp3'});
                context.commit("addAudio",{key: 'audio12',src: '/tarkan/assets/custom/sounds/12-netflix.mp3'});
                context.commit("addAudio",{key: 'audio13',src: '/tarkan/assets/custom/sounds/13-coin_nintendo.mp3'});
                context.commit("addAudio",{key: 'audio14',src: '/tarkan/assets/custom/sounds/14-nintendo_switch.mp3'});
                context.commit("addAudio",{key: 'audio15',src: '/tarkan/assets/custom/sounds/15-sonic_rings.mp3'});
                context.commit("addAudio",{key: 'audio16',src: '/tarkan/assets/custom/sounds/16-police_alarm.mp3'});
                context.commit("addAudio",{key: 'audio17',src: '/tarkan/assets/custom/sounds/17-police_tone.mp3'});
                context.commit("addAudio",{key: 'audio18',src: '/tarkan/assets/custom/sounds/18-police_woop_woop.mp3'});
                context.commit("addAudio",{key: 'audio19',src: '/tarkan/assets/custom/sounds/19-police_notification.mp3'});
                context.commit("addAudio",{key: 'audio20',src: '/tarkan/assets/custom/sounds/20-police.mp3'});
                context.commit("addAudio",{key: 'audio21',src: '/tarkan/assets/custom/sounds/21-claro.mp3'});
                context.commit("addAudio",{key: 'audio22',src: '/tarkan/assets/custom/sounds/22-whistle.mp3'});
                context.commit("addAudio",{key: 'audio23',src: '/tarkan/assets/custom/sounds/23-s8_note.mp3'});
                context.commit("addAudio",{key: 'audio24',src: '/tarkan/assets/custom/sounds/24-car_alarm.mp3'});

                const traccar = window.$traccar;
                traccar.getNotifications().then(({data}) => {
                    context.commit("setEvents", data);

                    resolve();
                })

            });
        },
        delete(context,params){
            return new Promise((resolve,reject)=> {
                const traccar = window.$traccar;
                traccar.deleteNotification(params).then(({data}) => {
                    context.commit("removeEvent",params);
                    resolve(data);
                }).catch((err) => {
                    console.log(err.response);
                    reject(err);
                })
            });
        },
        save(context,params){
            return new Promise((resolve,reject)=>{
                if(params.id>0){
                    window.$traccar.updateNotification(params.id,params).then(({data}) => {

                        context.commit("updateEvent", data);

                        resolve(data);
                    }).catch(reject);
                }else {
                    window.$traccar.createNotification(params).then(({data}) => {

                        context.commit("addEvent", data);

                        resolve(data);
                    }).catch(reject);
                }

            })
        }
    }
}
