

export default {
    namespaced: true,
    state: () => ({
        userList: []
    }),
    getters: {
        getUser(state){
            return (id)=>{
                return state.userList.find((u)=> u.id === id)
            }
        },
        getUsers(state){
            return state.userList.filter((u)=>{
                if(u.attributes['isShared'] && u.attributes['isShared']!==null){
                    return false;
                }else{
                    return true;
                }
            })
        }
    },
    mutations: {
        setUsers(state,value){
            state.userList = value;
        },
        deleteUser(state,value){
            state.userList.splice(state.userList.findIndex((u)=> u.id === value),1)
        },
        updateUser(state,value){
            const user = state.userList.find((d)=>{
                return d.id === value.id;
            })

            if(user) {
                Object.assign(user, value);
            }

        },
        addUser(state,value){
            state.userList.push(value);
        }
    },
    actions: {
        load(context){
            return new Promise((resolve)=> {
                const traccar = window.$traccar;
                traccar.getUsers().then(({data}) => {
                    context.commit("setUsers", data);

                    resolve();
                })

            });
        },
        save(context,params){
            return new Promise((resolve,reject)=> {
                const traccar = window.$traccar;
                console.log(params);
                if (params.id) {
                    traccar.updateUser(params.id, params).then(({data}) => {
                        context.commit("updateUser",data);
                        if(context.rootState.auth.id === data.id){
                            context.commit("setAuth",data,{root: true});
                        }
                        resolve(data);
                    }).catch((err) => {
                        reject(err);
                    })
                } else {
                    traccar.createUser(params).then(({data}) => {
                        context.commit("addUser",data);
                        resolve(data);
                    }).catch((err) => {
                        reject(err);
                    })

                }
            });
        },
        deleteUser(context,params){
            return new Promise((resolve,reject)=> {
                window.$traccar.deleteUser(params).then(() => {
                    context.commit("deleteUser", params);
                    resolve();
                }).catch((err) => {
                    reject(err.response.data);
                })
            });
        }
    }
}
