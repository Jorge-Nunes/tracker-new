import i18n from '../../lang/';
import A from '../../license'


export default {
    namespaced: true,
    state: () => ({
        serverInfo: {},
        serverLoaded: false,
        pageLoaded: false,
        isPlus: false,
        allowedLimit: false,
        labelConf:{
            // eslint-disable-next-line no-undef
            headLogo: CONFIG.headLogo || {image: true,text: ''},
            // eslint-disable-next-line no-undef
            whatsapp: CONFIG.whatsapp || false
        },
        push: {
            "vapidKey": null,
            "firebaseConfig": {
                "apiKey": null,
                "authDomain": null,
                "projectId": null,
                "storageBucket": null,
                "messagingSenderId": null,
                "appId": null,
                "measurementId": null
            }
        },
        licenseName: A[0]
    }),
    getters: {
        isReady(state){
           return state.serverLoaded && state.pageLoaded
        },
        getAttribute(state){
            return (k,d)=>{
                if(state.serverInfo.attributes && state.serverInfo.attributes[k]){
                    return state.serverInfo.attributes[k];
                }else{
                    return d;
                }
            }
        },
        getRegistrationEnabled(state){
            if(state.serverInfo && state.serverInfo.registration  && state.serverInfo.registration==true){
                return true;
            }else{
                return false;
            }
        },
        getLogoWidth(state){
            if(state.serverInfo!=={}){
                return (state.serverInfo && state.serverInfo.attributes && state.serverInfo.attributes['tarkan.logoWidth'] || 80) + '%';
            }else{
                return '80%';
            }
        }
    },
    mutations: {
        setPage(state,value){
            state.pageLoaded = value;
        },
        setConfig(state,value){
            state.push = value.push;

            state.labelConf.headLogo.image = value.config.headLogo.image;
            state.labelConf.headLogo.text = value.config.headLogo.text;

            state.labelConf.whatsapp = value.config.whatsapp;

            document.title = value.config.title;
        },
        setServer(state,value){
            state.serverInfo = value;
            if(value.attributes === []){
                value.attributes = {};
            }

            state.serverLoaded = true;

            if(value.attributes['tarkan.lang']){
                i18n.global.locale = value.attributes['tarkan.lang'];
            }
        },
        setLimit(state,value){
          state.allowedLimit = value;
        },
        setPlus(state,value){
            state.isPlus = value;
        }
    },
    actions: {
        loadConfig(context){
            return new Promise((resolve,reject)=> {
                fetch("/tarkan/assets/custom/config.json").then(async (response) => {
                        const data = await response.json();

                        context.commit("setConfig",data);

                        resolve();
                }).catch(()=>{
                    reject();
                });
            });
        },
        load(context){
            return new Promise((resolve,reject)=> {


                if(context.state.serverLoaded){
                    resolve();
                }else {

                    const traccar = window.$traccar;
                    traccar.getServer().then((r) => {



                        if (r.headers['licensemode']) {
                            context.commit("setPlus", true);
                        }

                        if (r.headers['devicelimit'] && r.headers['devicelimit'] !== "") {
                            context.commit("setLimit", parseInt(r.headers['devicelimit']));
                        }


                        context.commit("setServer", r.data);
                        resolve(r.data);
                    }).catch(() => {
                        reject();
                    });
                }

            });
        },
        save(context,params) {
            return new Promise((resolve, reject) => {
                window.$traccar.saveServer(params).then((r) => {

                    if(r.headers['devicelimit'] && r.headers['devicelimit']!==""){
                        context.commit("setLimit",parseInt(r.headers['devicelimit']));
                    }

                    context.commit("setServer", r.data);
                    resolve(r.data);
                }).catch(() => {
                    reject()
                });
            })
        },
        addFavAttr(context,params){
            let attr = context.state.serverInfo.attributes['tarkan.deviceAttributes'];
            if(params[1]) {
                if (attr) {
                    attr += ',' + params[0];
                } else {
                    attr = params[0];
                }
            }else{
                if(attr){
                    attr = attr.split(",");
                    attr.splice(attr.findIndex((a)=> a === params[0]),1);

                    attr = attr.join(",");
                }else{
                    attr = '';
                }
            }

            let server = context.state.serverInfo;
                server.attributes['tarkan.deviceAttributes'] = attr;


            context.dispatch("save",server);
            context.commit("setServer",server);
        }
    }
}
