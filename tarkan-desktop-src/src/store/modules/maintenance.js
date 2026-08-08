

export default {
    namespaced: true,
    state: () => ({
       list: []
    }),
    getters: {
        getMaintenanceById(state){
            return (id)=>{
                return state.list.find((g)=> g.id === id);
            }
        }
    },
    mutations: {
        set(state,value){
            state.list = value;
        },
        remove(state,value){
            state.list.splice(state.list.findIndex((a)=> a.id === value),1)
        },
        add(state,value){
            state.list.push(value);
        },
        update(state,value){
            state.list.splice(state.list.findIndex((a)=> a.id === value.id),1,value)
        }
    },
    actions: {
        load(context){
            return new Promise((resolve)=> {
                const traccar = window.$traccar;
                traccar.getMaintenance().then(({data}) => {
                    context.commit("set", data);

                    resolve();
                })

            });
        },
        delete(context,params){
            return new Promise((resolve,reject)=>{

                if(params>0){
                    window.$traccar.deleteMaintenance(params).then(({data}) => {
                        context.commit("remove", params);
                        resolve(data);
                    }).catch(reject);
                }else{
                    reject();
                }
            })
        },
        save(context,params){
            return new Promise((resolve,reject)=>{

                if(params.id>0){

                    window.$traccar.updateMaintenance(params.id,params).then(({data}) => {

                        context.commit("update", data);

                        resolve(data);
                    }).catch(reject);
                }else {

                    window.$traccar.createMaintenance(params).then(({data}) => {

                        context.commit("add", data);

                        resolve(data);
                    }).catch(reject);
                }

            })
        }
    }
}
