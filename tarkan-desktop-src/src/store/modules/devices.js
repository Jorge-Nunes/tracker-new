import store from "../index";

// caches de getters pesados (5K devices: evita re-sort/re-contagem a cada update de posição)
let _orderedCacheKey = "";
let _orderedCacheValue = [];
let _countCacheKey = "";
let _countCacheValue = null;

export default {
    namespaced: true,
    state: () => ({
        deviceKeys: [],
        deviceList: {},
        positionsList: {},
        positionHistory: [],
        isFollowingId: 0,
        showRoutes: false,
        trail: false,
        streetview: false,
        _sorting: 'name-asc',
        _rev: 0,
        _revDevices: 0,
        applyFilters: {
            showOnlyId: 0,
            statusFilter: 'all',
            motionFilter: false,
            hideCategory: []
        }
    }),
    getters: {
        sorting(state){
          return state._sorting;
        },
        byGroupId(state){
            return (id)=>{
                return [...state.deviceKeys].filter((a)=>{
                    return (state.deviceList[a].groupId==id);
                });
            }
        },
        getOrderedDevices(state){
            const _sortKey = state._sorting.split("-")[0];
            const _cacheKey = state._revDevices + "|" + state.deviceKeys.join(",") + "|" + state._sorting;
            if (_sortKey !== "state" && _orderedCacheKey === _cacheKey) {
                return _orderedCacheValue;
            }
           /* let tmp = [];

            console.log("LAG?");

            for(var K of Object.keys(state.deviceList)){
                tmp.push(state.deviceList[K]);
            }

            return tmp;*/
            //return state.deviceKeys;

            const p = state._sorting.split("-");

            const _sorted = [...state.deviceKeys].sort((ak,bk)=>{
                const a = state.deviceList[ak];
                const b = state.deviceList[bk];

                if(p[0]==='state'){

                    const as = state.positionsList[ak];
                    const bs = state.positionsList[bk];

                    if(!as){
                        return 1;
                    }
                    if(!bs){
                        return -1;
                    }

                    if(p[1]==='motion'){
                        if(as.attributes['motion']===undefined){
                            return 1;
                        }
                        if(bs.attributes['motion']===undefined){
                            return -1;
                        }

                        return (as.attributes['motion']===bs.attributes['motion'])?0:(as.attributes['motion']===true)?-1:(bs.attributes['motion']===true)?1:0;
                    }

                    if(p[1]==='anchor'){

                        const aa = store.getters['geofences/isAnchored'](a.id)?true:false;
                        const ba = store.getters['geofences/isAnchored'](b.id)?true:false;

                        return (aa==ba)?0:(aa===true)?-1:(ba===true)?1:0;
                    }

                    if(p[1]==='locked'){
                        if(as.attributes['blocked']===undefined){
                            return 1;
                        }
                        if(bs.attributes['blocked']===undefined){
                            return -1;
                        }


                        return (as.attributes['blocked']===bs.attributes['blocked'])?0:(as.attributes['blocked']===true)?-1:(bs.attributes['blocked']===true)?1:0;
                    }
                    if(p[1]==='ignition'){

                        if(as.attributes['ignition']===undefined){
                            return 1;
                        }
                        if(bs.attributes['ignition']===undefined){
                            return -1;
                        }

                        return (as.attributes['ignition']===bs.attributes['ignition'])?0:(as.attributes['ignition']===true)?-1:(bs.attributes['ignition']===true)?1:0;
                    }

                    if(p[1]==='driver'){

                        const at = as.attributes['driverUniqueId']?true:false;
                        const bt = bs.attributes['driverUniqueId']?true:false;

                        return (at===bt)?0:(at===true)?-1:(bt===true)?1:0;
                    }


                    if(p[1]==='alert'){

                        const at = as.attributes['alarm']?true:false;
                        const bt = bs.attributes['alarm']?true:false;

                        return (at===bt)?0:(at===true)?-1:(bt===true)?1:0;
                    }


                }else if(p[0]==='lastUpdate'){

                    if(a[p[0]]===null){
                        return 1;
                    }else if(b[p[0]]===null){
                        return -1;
                    }else if(new Date(a[p[0]]).getTime()<new Date(b[p[0]]).getTime()){
                        return (p[1]==='asc')?1:-1;
                    }else if(new Date(a[p[0]]).getTime()>new Date(b[p[0]]).getTime()){
                        return (p[1]==='asc')?-1:1;
                    }else{
                        return 0;
                    }
                }else if(a[p[0]]>b[p[0]]){
                    return (p[1]==='asc')?1:-1;
                }else if(a[p[0]]<b[p[0]]){
                    return (p[1]==='desc')?1:-1;
                }else{
                    return 0;
                }
            })

            _orderedCacheKey = _cacheKey;
            _orderedCacheValue = _sorted;
            return _sorted;

        },
        isHiddenFilter(state){
          return (id)=>{
              return !state.applyFilters.hideCategory.find((f) => {

                  return f == id
              });
          }
        },
        // eslint-disable-next-line no-unused-vars
        getFilters(state){
            return state.applyFilters;
        },
        getDevice(state){
            return (deviceId)=>{
                if(!deviceId){
                    return false;
                }
                return state.deviceList[deviceId];
            }
        },
        getPosition(state){
            return (positionId)=>{
                if(!positionId){
                    return false;
                }
                return state.positionsList[positionId];
            }
        },
        getTrails(state){
            let tmp = [];

            state.positionHistory.forEach((p) => {
                tmp.push([p.latitude, p.longitude])
            });


            return tmp;
        },
        deviceCount(state){
            const _cKey = state._rev + "|" + state.deviceKeys.length + "|" + Object.keys(state.positionsList).length;
            if (_countCacheKey === _cKey) {
                return _countCacheValue;
            }
            let tmp ={
                online: 0,
                offline: 0,
                unknown: 0,
                motion: 0
            };


            const all = state.deviceList;
            const _all = Object.keys(all);
            const positions = state.positionsList;

            _all.forEach((K)=>{
                const f = all[K];
                const pos = positions[K];

                if(f.status==='online'){
                    tmp.online++;
                }else if(f.status==='offline'){
                    tmp.offline++;
                }else if(f.status==='unknown'){
                    tmp.unknown++;
                }

                if(pos && pos.attributes['motion'] && pos.attributes['motion']===true){
                    tmp.motion++;
                }


            });

            tmp.all = _all.length;

            _countCacheKey = _cKey;
            _countCacheValue = tmp;
            return tmp;
        }
    },
    mutations: {
        setRoute(state,value){
            state.showRoutes = value;
        },
        setDeviceFilter(state,value){
          state.applyFilters.showOnlyId = value;
        },
        toggleHiddenFilter(state,value){
            const idx = state.applyFilters.hideCategory.findIndex((f)=> f === value);

            //console.log(value,idx);

            if(idx>-1){
                state.applyFilters.hideCategory.splice(idx,1);
            }else{
                state.applyFilters.hideCategory.push(value);
            }
        },
        setSorting(state,value){
            state._sorting = value;

            //const p = state._sorting.split("-");



        },
        setStatusFilter(state,value){
            state.applyFilters.statusFilter = value;
        },
        toggleMotionFilter(state){
          state.applyFilters.motionFilter = !state.applyFilters.motionFilter;
        },
        toggleStreet(state){
            state.streetview = !state.streetview;
        },
        setTrail(state,value){
            if(value===false) {
                state.positionHistory = [];
            }else{

                const p = state.positionsList[value];

                state.positionHistory = [];
                state.positionHistory.push(p);
            }
            state.trail = value;
            state.isFollowingId = value;
        },
        setFollow(state,value){
          state.isFollowingId = value;
        },
        setDevices(state,value){
            state.deviceList = value;
            state.deviceKeys = [];
            value.forEach((d)=>{
                state.deviceKeys.push(d.id);
            })
        },
        async addDevice(state,value){
            state._rev++;
            state._revDevices++;

            if(!state.deviceKeys.includes(value.id)) {
                state.deviceKeys.push(value.id);
            }
            state.deviceList[value.id] = value;


            if(typeof L != 'undefined') {
                value.icon = window.addDevice(value);
            }


        },
        updateDevice(state,value){
            state._rev++;
            state._revDevices++;

            if(!state.deviceKeys.includes(value.id)) {
                state.deviceKeys.push(value.id);
            }

            const device = state.deviceList[value.id];

            if(device.status !== value.status){
                device.icon.updateStatus(value.status);
            }


            //console.log(device.attributes['tarkan.color'],value.attributes['tarkan.color']);

            if(device.category!==value.category ||
                (value.attributes['tarkan.color'] && (!device.attributes['tarkan.color'] || device.attributes['tarkan.color']!==value.attributes['tarkan.color'])) ||
                (value.attributes['tarkan.color_extra'] && (!device.attributes['tarkan.color_extra'] || device.attributes['tarkan.color_extra']!==value.attributes['tarkan.color_extra']))){


                Object.assign(device,value);
                device.icon.updateCanva(device);
            }else{

                Object.assign(device,value);
            }




        },
        removeDevice(state,value){
            state._rev++;
            state._revDevices++;
            delete state.deviceList[value];
            state.deviceKeys.splice(state.deviceKeys.findIndex((d)=> d === value),1);
        },
        setPositions(state,value){
            state._rev++;
            state.positionsList = {};

            value.forEach((p)=>{
                state.positionsList[p.deviceId] = p;
            });

        },
        addPosition(state,value){
            state.positionsList.push(value);
        },
        updatePosition(state,p){
            state._rev++;
            state.positionsList[p.deviceId] = p;

            const device = state.deviceList[p.deviceId];
            if(device && device.icon){
                // eslint-disable-next-line no-undef
                const latlng = L.latLng(p.latitude,p.longitude);

                device.icon.options.img.hide = false;
                device.icon.options.img.rotate = p.course;

                if(p.deviceId === state.trail) {
                    state.positionHistory.push(p);
                }

                if(state.isFollowingId===device.id){
                    device.icon.moveTo(latlng,500);
                    // eslint-disable-next-line no-undef
                    window.$setMapCenter(latlng);
                }else{
                    // sem animação: com 5K devices o moveTo(...,500) por device trava a UI
                    device.icon.setLatLng(latlng);
                }

                if(window.$updatePano){
                    window.$updatePano(device.id);
                }
            }
        },
        updatePositions(state,positions){
            state._rev++;
            positions.forEach((p)=>{
                state.positionsList[p.deviceId] = p;

                const device = state.deviceList[p.deviceId];
                if(device && device.icon){
                    // eslint-disable-next-line no-undef
                    const latlng = L.latLng(p.latitude,p.longitude);

                    device.icon.options.img.hide = false;
                    device.icon.options.img.rotate = p.course;

                    if(p.deviceId === state.trail) {
                        state.positionHistory.push(p);
                    }

                    if(state.isFollowingId===device.id){
                        device.icon.moveTo(latlng,500);
                        // eslint-disable-next-line no-undef
                        window.$setMapCenter(latlng);
                    }else{
                        // sem animação: com 5K devices o moveTo(...,500) por device trava a UI
                        device.icon.setLatLng(latlng);
                    }

                    if(window.$updatePano){
                        window.$updatePano(device.id);
                    }
                }
            })
        }
    },
    actions: {
        setSorting(context,p){
            if(context.state._sorting.split("-")[0]===p){
                context.commit("setSorting",p+"-"+(context.state._sorting.split("-")[1]==='asc'?'desc':'asc'))
            }else{
                context.commit("setSorting",p+"-asc")
            }
        },
        setSortingState(context,p){
            context.commit("setSorting",p);
        },
        refreshOneDevice(context,fk){
            const f = context.state.deviceList[fk];

            const statusFilter = context.state.applyFilters.statusFilter;
            const motionFilter = context.state.applyFilters.motionFilter;
            const filterQuery = window.localStorage.getItem("query") || false;


                if(filterQuery){

                    f.icon.remove();

                    for(let k of Object.keys(f)){
                        if(k==='status' && String(f[k]).toLowerCase().replace('unknown','desconhecido').match(filterQuery.toLowerCase())){
                            f.icon.addToMap();
                            return true;
                        }else if(String(f[k]).toLowerCase().match(filterQuery.toLowerCase())){
                            f.icon.addToMap();
                            return true;
                        }
                    }



                    for(let k of Object.keys(f.attributes)){
                        if(f.attributes[k] && f.attributes[k].toString().toLowerCase().match(filterQuery.toLowerCase())){
                            f.icon.addToMap();
                            return true;
                        }
                    }

                }else if(context.state.applyFilters.showOnlyId!==0){
                    if(f.id === context.state.applyFilters.showOnlyId){
                        f.icon.addToMap();
                    }else{
                        f.icon.remove();
                    }

                }else {

                    let visible = true;

                    if (!f.category) {
                        f.category = 'default';
                    }

                    if (context.state.applyFilters.hideCategory.find((c) => {

                        //console.log(c,f.category);

                        return c === f.category;
                    })) {
                        f.icon.remove();
                        visible = false;
                    } else {
                        f.icon.addToMap();
                        visible = true;
                    }


                    if (statusFilter === 'online' && f.status !== 'online') {
                        f.icon.remove();
                        visible = false;
                    } else if (statusFilter === 'offline' && f.status !== 'offline') {
                        f.icon.remove();
                        visible = false;
                    } else if (statusFilter === 'unknown' && f.status !== 'unknown') {
                        f.icon.remove();
                        visible = false;
                    }

                    if (motionFilter && visible) {
                        const pos = context.getters.getPosition(f.id);
                        if (pos && !pos.attributes.motion) {
                            f.icon.remove();
                        }
                    }
                }

        },
        toggleHiddenFilter(context,value){
            context.commit("toggleHiddenFilter",value);
            context.dispatch("refreshDevices");
        },
        setDeviceFilter(context,value){
          context.commit("setDeviceFilter",value);
          context.dispatch("refreshDevices");
        },
        refreshDevices(context){
            context.state.deviceKeys.forEach((f)=>{
                context.dispatch("refreshOneDevice",f);
            });
        },
        setStatusFilter(context,value){
          context.commit("setStatusFilter",value);
          context.dispatch("refreshDevices");
        },
        toggleMotionFilter(context) {
            context.commit("toggleMotionFilter")
            context.dispatch("refreshDevices");
        },
        setTrail(context,value){
            context.commit("setTrail",value);
        },
        toggleStreet(context){
            context.commit("toggleStreet")
        },
        // eslint-disable-next-line no-unused-vars
        connectWs(context){
            const traccar = window.$traccar;

            traccar.on('open',()=>{
                //console.log("WS OPEN")
            })
            traccar.on('close',()=>{
                window.setTimeout(()=>{
                    traccar.startWS();
                },5000);
            })
            traccar.on('message',(m)=>{
                if(m.positions){
                    // commit em lote: 1 mutação por mensagem (5K devices)
                    context.commit("updatePositions",m.positions);
                }

                if(m.devices){
                    // eslint-disable-next-line no-unused-vars
                    m.devices.forEach((d)=>{
                        context.commit("updateDevice",d)

                        setTimeout(()=> {
                            context.dispatch("refreshOneDevice", d.id);
                        },500);
                    })
                }

                if(m.events){
                    context.dispatch("events/proccessNotifications",m.events,{root: true});
                }
            })

            traccar.startWS();
        },
        waitForDevice(){
            return new Promise((resolve)=> {
                const checkDevice = ()=>{
                    if(window.addDevice){
                        resolve();
                    }else{
                        setTimeout(checkDevice,1000);
                    }
                }

                checkDevice();
            });
        },
        load(context,waitDevice=true){
            return new Promise((resolve)=> {

                const doLoad = () => {
                    const traccar = window.$traccar;
                    // all=true é obrigatório no Traccar 6.x (sem ele o admin recebe 0 devices)
                    traccar.getDevices({all: true}).then(({data}) => {
                        const valid = data.filter((d) => {
                            return !(d.uniqueId.split("-").length == 3 && d.uniqueId.split("-")[0] === "deleted");
                        });

                        // carrega em lotes p/ não travar o main thread com 5K markers de uma vez
                        const BATCH = 250;
                        let i = 0;
                        const step = () => {
                            const slice = valid.slice(i, i + BATCH);
                            slice.forEach((d) => {
                                context.commit("addDevice", d);
                            });
                            i += BATCH;
                            if (i < valid.length) {
                                setTimeout(step, 0);
                            } else {
                                resolve();
                            }
                        };
                        step();
                    });
                };

                if(waitDevice) {
                    context.dispatch("waitForDevice").then(() => {
                        window.loadModels().then(doLoad);
                    });
                }else{
                    doLoad();
                }
            });
        },
        delete(context,params){
            return new Promise((resolve,reject)=> {
                const traccar = window.$traccar;
                traccar.deleteDevice(params).then(({data}) => {
                    context.commit("removeDevice",params);
                    resolve(data);
                }).catch((err) => {
                    console.log(err);
                    reject();
                })

            });


        },
        save(context,params){
            return new Promise((resolve,reject)=> {
                const traccar = window.$traccar;


                if (params.id) {
                    traccar.updateDevice(params.id, params).then(({data}) => {
                        context.commit("updateDevice",data);
                        resolve(data);
                    }).catch((err) => {
                        reject(err);
                    })
                } else {
                    traccar.createDevice(params).then(({data}) => {
                        context.commit("addDevice",data);
                        resolve(data);
                    }).catch((err) => {
                        reject(err);
                    })

                }
            });
        },
        accumulators(context,params){
            return new Promise((resolve,reject)=> {
                const traccar = window.$traccar;


                if (params.deviceId) {
                    traccar.updateAccumulators(params.deviceId, params).then(({data}) => {
                        //context.commit("updateDevice",data);
                        resolve(data);
                    }).catch((err) => {
                        reject(err);
                    })
                }
            });
        },
        positions(context){
            return new Promise((resolve)=> {
                const traccar = window.$traccar;
                traccar.getPositions().then(({data}) => {
                    context.commit("setPositions", data);

                    if(data.length>0) {
                        let tmp = [];
                        for(var p in data){
                            tmp.push([data[p].latitude,data[p].longitude]);
                        }

                            setTimeout(() => {

                                if(typeof L != 'undefined') {
                                    const zoom = (context.rootState.server.serverInfo.attributes && context.rootState.server.serverInfo.attributes['web.selectZoom']) ? context.rootState.server.serverInfo.attributes['web.selectZoom'] : 17;

                                    // eslint-disable-next-line no-undef
                                    const bds = L.latLngBounds(tmp);
                                    // eslint-disable-next-line no-undef
                                    window.$map.fitBounds(bds, {maxZoom: zoom});
                                }

                            }, 500);


                    }

                    resolve();
                })
            });
        }
    }
}
