

export default {
    namespaced: true,
    state: () => ({
        list: []
    }),
    getters: {
        getShare(state){
            return (id)=>{
                return state.list.find((f)=> f.id === id);
            }
        }
    },
    mutations: {
        set(state,value){
            state.list = value;
        },
        delete(state,value){
            state.list.splice(state.list.findIndex((u)=> u.id === value),1)
        },
        update(state,value){
            const user = state.list.find((d)=>{
                return d.id === value.id;
            })

            Object.assign(user,value);

        },
        add(state,value){
            state.list.push(value);
        }
    },
    actions: {
        load(context){
            return new Promise((resolve)=> {
                const tarkan = window.$tarkan;
                tarkan.getShares().then(({data}) => {
                    context.commit("set", data);

                    resolve();
                }).catch(()=>{
                    resolve();
                })

            });
        },
        save(context,params){
            return new Promise((resolve,reject)=> {
                const tarkan = window.$tarkan;
                console.log(params);
                if (params.id) {
                    tarkan.updateShare(params.id, params).then(({data}) => {
                        context.commit("update",data);
                        resolve(data);
                    }).catch((err) => {
                        console.log(err);
                        reject();
                    })
                } else {
                    tarkan.createShare(params).then(({data}) => {
                        context.commit("add",data);
                        resolve(data);
                    }).catch((err) => {
                        console.log(err.response);
                        reject();
                    })

                }
            });
        },
        delete(context,params){
            return new Promise((resolve,reject)=> {
                window.$tarkan.deleteShare(params).then(() => {
                    context.commit("delete", params);
                    resolve();
                }).catch((err) => {
                    reject(err.response.data);
                })
            });
        }
    }
}
