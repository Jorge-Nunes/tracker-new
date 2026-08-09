'use strict';

import axios from 'axios';

import Emitter from './Emitter';

const qs = (params, encode = false) => {
    if (!params) return '';

    return '?' + Object.keys(params).map(k => k + '=' + (encode ? encodeURIComponent(params[k]) : params[k])).join('&');
};



let connector = function(server,vue){
    this.server = server;
    this.vue = vue;
    this.vm = false;
    this.ws = null;
    this.listeners = {"open": [],"message":[],"close":[]};

    let defaultObj = {
        baseURL: this.server,
        timeout: 60000,
        withCredentials: true,
        validateStatus: function (status) {
          return status < 400; // Resolve only if the status code is less than 500
        },
      };

      this.axios = axios.create(defaultObj);

    vue.mixin({created: function(){
            connector.vm = this;
        }})

    //console.log("Instance of " + server);
}



connector.prototype.startWS = function(){
    let wsServer = this.server.replace('http://','ws://').replace('https://','wss://');
    this.ws = new WebSocket(wsServer+'/socket');
    this.ws.onopen = (event)=>{
        Emitter.emit('open',event);
    }

    this.ws.onclose = (event)=>{
        Emitter.emit('close',event);
    }

    this.ws.onmessage = (d)=>{
        let data = JSON.parse(d.data);
        Emitter.emit('message',data);
    }
}

connector.prototype.closeWS = function(){
    try {
        this.ws.close();
    }catch(e){
        console.log(e);
    }
}

connector.prototype.on = function(label,fnc){
    Emitter.addListener(label,fnc);
}


connector.prototype.recoverPassword = function(email){
    return new Promise((resolve,reject)=> {
        if(email===''){
            reject('FILL_EMAIL_FIELD')
        }else {

            const params = new URLSearchParams();
            params.append('email', email);

            this.axios.post('/session/password', params).then(({data})=>{
                resolve(data);
            }).catch((r)=>{

                const err = r.response.data.split("-")[0].trim().replaceAll(" ","_").toUpperCase();

                reject(err);
            });
        }
    });
}

connector.prototype.login = function(email,password){
    return new Promise((resolve,reject)=> {
        if(email==='' || password===''){
            reject('FILL_ALL_FIELDS')
        }else {

            const params = new URLSearchParams();
            params.append('email', email);
            params.append('password', password);
            params.append('undefined', 'false');

            this.axios.post('/session', params).then(({data})=>{
                resolve(data);
            }).catch((r)=>{

                const err = r.response.data.split("-")[0].trim().replaceAll(" ","_").toUpperCase();

                reject(err);
            });
        }
    });
}


connector.prototype.loginToken = function(token){
    return new Promise((resolve,reject)=> {
        if(!token){
            reject('FILL_ALL_FIELDS')
        }else {

            this.axios.get('/session?token='+token).then(({data})=>{
                resolve(data);
            }).catch(()=>{
                reject('INVALID_LOGIN_DATA');
            });
        }
    });
}

connector.prototype.getSession = function(){
    return this.axios.get("/session");
}

connector.prototype.deleteSession = function(){
    return this.axios.delete("/session");
}

connector.prototype.getDevices = function(params){
    return this.axios.get('/devices'+qs(params));
}

connector.prototype.createDevice = function(params){
    return this.axios.post('/devices',params);
}

connector.prototype.updateDevice = function(id,params){
    return this.axios.put('/devices/'+id,params);
}


connector.prototype.updateAccumulators = function(id,params){
    return this.axios.put('/devices/'+id+'/accumulators',params);
}


connector.prototype.deleteDevice = function(id){
    return this.axios.delete("/devices/"+id);
}

connector.prototype.getGroups = function(params){
    return this.axios.get('/groups'+qs(params));
}

connector.prototype.createGroup = function(params){
    return this.axios.post("/groups",params);
}

connector.prototype.updateGroup = function(id,params){
    return this.axios.put("/groups/"+id,params);
}

connector.prototype.deleteGroup = function(id){
    return this.axios.delete("/groups/"+id);
}

connector.prototype.getGeofences = function(params){
    return this.axios.get('/geofences'+qs(params));
}

connector.prototype.createGeofence = function(params){
    return this.axios.post('/geofences',params)
}

connector.prototype.updateGeofence = function(id,params){
    return this.axios.put("/geofences/"+id,params);
}

connector.prototype.deleteGeofence = function(id){
    return this.axios.delete("/geofences/"+id)
}

connector.prototype.getServer = function(){
    return this.axios.get('/server');
}

connector.prototype.saveServer = function(params){
    return this.axios.put("/server",params);
}


connector.prototype.getUsers = function(params){
    return this.axios.get('/users'+qs(params));
}

connector.prototype.updateUser = function(id,params){
    return this.axios.put('/users/'+id,params);
}

connector.prototype.createUser = function(params){
    return this.axios.post('/users',params);
}

connector.prototype.deleteUser = function(params){
    return this.axios.delete('/users/'+params);
}

connector.prototype.getPositionByPosition = async function(ids){

    console.log("loadpositions by id",ids);

    let tmp = [];
    let accumulator = [];

    for(let v in ids){

        accumulator.push('id='+ids[v]);

        if(accumulator.length>500) {
            let d = await this.axios.get('/positions?' + accumulator.join("&"));
            tmp = tmp.concat(d.data);

            accumulator = [];
        }
    }

    if(accumulator.length>0) {
        let d = await this.axios.get('/positions?' + accumulator.join("&"));
        tmp = tmp.concat(d.data);

    }


    return tmp;
}

connector.prototype.getPositions = function(ids=[]){

    if(ids.length){
        return this.getPositionByPosition(ids);
    }else {
        return this.axios.get('/positions');
    }
}

connector.prototype.sendStopEngine = function(deviceId){
    return this.axios.post('/commands/send',{"id":0,"description":"Novo...","deviceId":deviceId,"type":"engineStop","textChannel":false,"attributes":{}})
}


connector.prototype.sendCommand = function(params){

    const _params = {...{"id":0,"description":"Novo...","deviceId":0,"type":"custom","textChannel":false,"attributes":{}},...params};
    return this.axios.post('/commands/send',_params)
}

connector.prototype.createSavedCommand = function(params){
    return this.axios.post("/commands",params);
}

connector.prototype.updateSavedCommand = function(id,params){
    return this.axios.put("/commands/"+id,params);
}

connector.prototype.deleteSavedCommand = function(id){
    return this.axios.delete("/commands/"+id);
}

connector.prototype.getSavedCommands = function(params){
    return this.axios.get("/commands"+qs(params));
}


connector.prototype.getNotifications = function(params){
    return this.axios.get("/notifications"+qs(params));
}

connector.prototype.deleteNotification = function(id){
    return this.axios.delete("/notifications/"+id);
}

connector.prototype.updateNotification = function(id,params){
    return this.axios.put("/notifications/"+id,params);
}

connector.prototype.createNotification = function(params){
    return this.axios.post("/notifications",params);
}

connector.prototype.getNotificators = function(){
    return this.axios.get("/notifications/notificators");
}

connector.prototype.getNotificationTypes = function(){
    return this.axios.get("/notifications/types");
}


connector.prototype.getDrivers = function(params){
    return this.axios.get("/drivers"+qs(params));
}


connector.prototype.createDriver = function(params){
    return this.axios.post("/drivers",params);
}
connector.prototype.updateDriver = function(id,params){
    return this.axios.put("/drivers/"+id,params);
}

connector.prototype.deleteDriver = function(id){
    return this.axios.delete("/drivers/"+id);
}


connector.prototype.getTypeCommands = function(deviceId=false){
    return this.axios.get("/commands/types"+((deviceId)?'?deviceId='+deviceId:''));
}

connector.prototype.getAvailableCommands = function(deviceId){
    return this.axios.get("/commands/send?deviceId="+deviceId);
}

connector.prototype.linkObjects = function(params){
    return this.axios.post("/permissions",params);
}

connector.prototype.unlinkObjects = function(params){
    return this.axios.delete("/permissions",{data: params});
}

connector.prototype.getPermissions = function(params){
    return this.axios.get('/permissions'+qs(params, true));
}

connector.prototype.linkObjectsBulk = function(params){
    return this.axios.post("/permissions/bulk",params);
}

connector.prototype.unlinkObjectsBulk = function(params){
    return this.axios.delete("/permissions/bulk",{data: params});
}

connector.prototype.loadTrips = function(id,from,to){
    return this.axios.get('/reports/trips?_dc=1616717856816&deviceId='+id+'&type=allEvents&from='+from+'&to='+to+'&daily=false&page=1&start=0&limit=25');
}


connector.prototype.loadRoute = function(id,from,to,exp=false){

    const _from = encodeURIComponent(new Date(from).toISOString());
    const _to = encodeURIComponent(new Date(to).toISOString());

    let conf = {};
    if(exp){
        conf = {
            responseType: 'blob',
            headers: {
                Accept: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            }
        }
    }


    return this.axios.get('/reports/route?_dc=1616717856816&deviceId='+id+'&type=allEvents&from='+_from+'&to='+_to+'&daily=false',conf);
}

connector.prototype.attributeTest = function(id,params){
    return this.axios.post("/attributes/computed/test?deviceId="+id,params);
}


connector.prototype.getComputedAttributes = function(params){
    return this.axios.get("/attributes/computed"+qs(params));
}

connector.prototype.createComputedAttribute = function(params){
    return this.axios.post("/attributes/computed",params);
}

connector.prototype.updateComputedAttribute = function(id,params){
    return this.axios.put("/attributes/computed/"+id,params);
}

connector.prototype.deleteComputedAttribute = function(id){
    return this.axios.delete("/attributes/computed/"+id);
}

connector.prototype.getCalendars = function(params){
    return this.axios.get("/calendars"+qs(params));
}


connector.prototype.updateCalendar = function(id,params){
    return this.axios.put("/calendars/"+id,params)
}

connector.prototype.createCalendar = function(params){
    return this.axios.post("/calendars",params);
}

connector.prototype.deleteCalendar = function(id){
    return this.axios.delete("/calendars/"+id);
}

connector.prototype.getMaintenance = function(params){
    return this.axios.get("/maintenance"+qs(params));
}

connector.prototype.createMaintenance = function(params){
    return this.axios.post("/maintenance",params);
}

connector.prototype.updateMaintenance = function(id,params){
    return this.axios.put("/maintenance/"+id,params);
}

connector.prototype.deleteMaintenance = function(id){
    return this.axios.delete("/maintenance/"+id)
}


connector.prototype.getReport = function(endpoint,deviceIds,groupIds,from,to,exp=false){

    let objects = [];
    deviceIds.forEach(d => objects.push("deviceId="+d));
    groupIds.forEach(g => objects.push("groupId="+g));

    let conf = {};
        if(exp){
            conf = {
                responseType: 'blob',
                headers: {
                    Accept: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                }
            }
        }

    return this.axios.get("/reports/"+endpoint+"?"+objects.join("&")+"&type=allEvents&from="+from+"&to="+to+"&daily=false",conf);
}

connector.prototype.getReportSummary = function(deviceIds,groupIds,from,to,exp=false){
    return this.getReport('summary', deviceIds, groupIds, from, to, exp);
}

connector.prototype.getReportTravels = function(deviceIds,groupIds,from,to,exp=false){
    return this.getReport('trips', deviceIds, groupIds, from, to, exp);
}

connector.prototype.testNotification = function(){
    return this.axios.post("/notifications/test",{});
}

connector.prototype.restartServer = function(){
    return this.axios.post("/server/restart",{});
}

connector.prototype.getReportStops = function(deviceIds,groupIds,from,to,exp=false){
    return this.getReport('stops', deviceIds, groupIds, from, to, exp);
}


connector.prototype.getReportEvents = function(deviceIds,groupIds,from,to,exp=false){
    return this.getReport('events', deviceIds, groupIds, from, to, exp);
}

export default connector;

