

export default {
    namespaced: true,
    state: () => ({
        attributesList: []
    }),
    getters: {
    },
    mutations: {
        setAttributes(state,value){
            state.attributesList = value;
        },
        addAttribute(state,value){
            state.attributesList.push(value);
        },
        updateAttribute(state,value){
            state.attributesList.splice(state.attributesList.findIndex((a)=> a.id === value.id),1,value)
        },
        removeAttribute(state,value){
            state.attributesList.splice(state.attributesList.findIndex((a)=> a.id === value),1)
        }
    },
    actions: {
        load(context){
            return new Promise((resolve)=> {
                const traccar = window.$traccar;
                traccar.getComputedAttributes().then(({data}) => {
                    context.commit("setAttributes", data);

                    resolve();
                })

            });
        },
        delete(context,params){
            return new Promise((resolve,reject)=>{


                if(params>0){
                    window.$traccar.deleteComputedAttribute(params).then(({data}) => {
                        context.commit("removeAttribute", params);
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

                    window.$traccar.updateComputedAttribute(params.id,params).then(({data}) => {

                        context.commit("updateAttribute", data);

                        resolve(data);
                    }).catch(reject);
                }else {

                    window.$traccar.createComputedAttribute(params).then(({data}) => {

                        context.commit("addAttribute", data);

                        resolve(data);
                    }).catch(reject);
                }

            })
        }
    }
}
