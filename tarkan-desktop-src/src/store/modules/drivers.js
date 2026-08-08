

export default {
    namespaced: true,
    state: () => ({
        driverList: []
    }),
    getters: {
        getDriver(state){
            return (id)=>{
                return state.driverList.find((u)=> u.id === id)
            }
        },
        getDriverByUniqueId(state){
            return (id)=>{
                return state.driverList.find((u)=> u.uniqueId === id)
            }
        }
    },
    mutations: {
        setDrivers(state,value){
            state.driverList = value;
        },
        deleteDriver(state,value){
            state.driverList.splice(state.driverList.findIndex((u)=> u.id === value),1)
        },
        updateDrivers(state,value){
            const user = state.driverList.find((d)=>{
                return d.id === value.id;
            })

            Object.assign(user,value);

        },
        addDrivers(state,value){
            state.driverList.push(value);
        }
    },
    actions: {
        load(context){
            return new Promise((resolve)=> {
                const traccar = window.$traccar;
                traccar.getDrivers().then(({data}) => {
                    context.commit("setDrivers", data);

                    resolve();
                })

            });
        },
        save(context,params){
            return new Promise((resolve,reject)=> {
                const traccar = window.$traccar;
                console.log(params);
                if (params.id) {
                    traccar.updateDriver(params.id, params).then(({data}) => {
                        context.commit("updateDriver",data);

                        resolve(data);
                    }).catch((err) => {
                        console.log(err);
                        reject();
                    })
                } else {
                    traccar.createDriver(params).then(({data}) => {
                        context.commit("addDrivers",data);
                        resolve(data);
                    }).catch((err) => {
                        console.log(err.response);
                        reject();
                    })

                }
            });
        },
        deleteDriver(context,params){
            return new Promise((resolve,reject)=> {
                window.$traccar.deleteDriver(params).then(() => {
                    context.commit("deleteDriver", params);
                    resolve();
                }).catch((err) => {
                    reject(err.response.data);
                })
            });
        }
    }
}
