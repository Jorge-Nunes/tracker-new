'use strict';

import axios from 'axios';




let connector = function(server,vue){
    this.server = server;
    this.vue = vue;
    this.vm = false;
    this.ws = null;
    this.listeners = {"open": [],"message":[],"close":[]};

    this.axios = axios.create({
        baseURL: this.server,
        timeout: 30000,
        withCredentials: false,
        validateStatus: function (status) {
            return (status < 400); // Resolve only if the status code is less than 500
        }
    });

    vue.mixin({created: function(){
            connector.vm = this;
        }})

    //console.log("Instance of " + server);
}

connector.prototype.getShares = function(){
    return this.axios.get("/shares");
}

connector.prototype.createShare = function(params){
    return this.axios.post("/shares",params);
}

connector.prototype.updateShare = function(id,params){
    return this.axios.put("/shares/"+id,params);
}

connector.prototype.deleteShare = function(shareId){
    return this.axios.delete("/shares/"+shareId);
}


connector.prototype.saveTheme = function(data){
    return this.axios.put("/theme",data);
}


connector.prototype.getUserLogs = function(userId){
    return this.axios.get("/users/"+userId+"/logs")
}
connector.prototype.getDeviceLogs = function(deviceId){
    return this.axios.get("/devices/"+deviceId+"/logs")
}

connector.prototype.getServerLogs = function(){
    return this.axios.get("/server/logs")
}

connector.prototype.checkDriver = function(id){
    return this.axios.post("/qr-driver",{id: id});
}

connector.prototype.checkOutDriver = function(){
    return this.axios.post("/qr-driver",{id: 0});
}

connector.prototype.autoLink = function(data){
    return this.axios.post("/autolink",data);
}

export default connector;

