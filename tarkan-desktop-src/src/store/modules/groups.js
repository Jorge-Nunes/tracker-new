

export default {
    namespaced: true,
    state: () => ({
        groupList: []
    }),
    getters: {
        getGroupNameById(state){
            return (id)=>{
                const group = state.groupList.find((g)=> g.id === id);
                return (group)?group.name:'--';
            }
        },
        getGroup(state){
            return (id)=>{
                return state.groupList.find((g)=> g.id === id);
            }
        }
    },
    mutations: {
        setGroups(state,value){
            state.groupList = value;
        },
        removeGroup(state,value){
            state.groupList.splice(state.groupList.findIndex((a)=> a.id === value),1)
        },
        addGroup(state,value){
            state.groupList.push(value);
        },
        updateGroup(state,value){
            state.groupList.splice(state.groupList.findIndex((a)=> a.id === value.id),1,value)
        }
    },
    actions: {
        load(context){
            return new Promise((resolve)=> {
                const traccar = window.$traccar;
                traccar.getGroups().then(({data}) => {
                    context.commit("setGroups", data);

                    resolve();
                })

            });
        },
        delete(context,params){
            return new Promise((resolve,reject)=>{

                if(params>0){
                    window.$traccar.deleteGroup(params).then(({data}) => {
                        context.commit("removeGroup", params);
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

                    window.$traccar.updateGroup(params.id,params).then(({data}) => {

                        context.commit("updateGroup", data);

                        resolve(data);
                    }).catch(reject);
                }else {

                    window.$traccar.createGroup(params).then(({data}) => {

                        context.commit("addGroup", data);

                        resolve(data);
                    }).catch(reject);
                }

            })
        }
    }
}
