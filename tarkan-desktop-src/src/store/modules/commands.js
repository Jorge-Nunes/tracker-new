

export default {
    namespaced: true,
    state: () => ({
        commandList: []
    }),
    getters: {
    },
    mutations: {
        setCommands(state,value){
            state.commandList = value;
        },
        addCommand(state,value){
            state.commandList.push(value);
        },
        updateCommand(state,value){
            state.commandList.splice(state.commandList.findIndex((c)=> c.id === value.id),1,value)
        }        ,
        removeCommand(state,value){
            state.commandList.splice(state.commandList.findIndex((c)=> c.id === value),1)
        }

    },
    actions: {
        load(context){
            return new Promise((resolve)=> {
                const traccar = window.$traccar;
                traccar.getSavedCommands().then(({data}) => {
                    context.commit("setCommands", data);

                    resolve();
                })

            });
        },
        delete(context,params){
            return new Promise((resolve,reject)=> {
                const traccar = window.$traccar;
                traccar.deleteSavedCommand(params).then(({data}) => {
                    context.commit("removeCommand", data);
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
                    window.$traccar.updateSavedCommand(params.id,params).then(({data}) => {

                        context.commit("updateCommand", data);

                        resolve(data);
                    }).catch(reject);
                }else {
                    window.$traccar.createSavedCommand(params).then(({data}) => {

                        context.commit("addCommand", data);

                        resolve(data);
                    }).catch(reject);
                }

            })
        }
    }
}
