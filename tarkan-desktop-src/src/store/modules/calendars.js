

export default {
    namespaced: true,
    state: () => ({
        calendarList: []
    }),
    getters: {
        getCalendarById(state){
            return (id)=>{
                return state.calendarList.find((u)=> u.id === id)
            }
        }
    },
    mutations: {
        setCalendars(state,value){
            state.calendarList = value;
        },
        deleteCalendar(state,value){
            state.calendarList.splice(state.calendarList.findIndex((u)=> u.id === value),1)
        },
        updateCalendar(state,value){
            const user = state.calendarList.find((d)=>{
                return d.id === value.id;
            })

            Object.assign(user,value);

        },
        addCalendar(state,value){
            state.calendarList.push(value);
        }
    },
    actions: {
        load(context){
            return new Promise((resolve)=> {
                const traccar = window.$traccar;
                traccar.getCalendars().then(({data}) => {
                    context.commit("setCalendars", data);

                    resolve();
                })

            });
        },
        save(context,params){
            return new Promise((resolve,reject)=> {
                const traccar = window.$traccar;
                console.log(params);
                if (params.id) {
                    traccar.updateCalendar(params.id, params).then(({data}) => {
                        context.commit("updateCalendar",data);

                        resolve(data);
                    }).catch((err) => {
                        console.log(err);
                        reject();
                    })
                } else {
                    traccar.createCalendar(params).then(({data}) => {
                        context.commit("addCalendar",data);
                        resolve(data);
                    }).catch((err) => {
                        console.log(err.response);
                        reject();
                    })

                }
            });
        },
        deleteCalendar(context,params){
            return new Promise((resolve,reject)=> {
                window.$traccar.deleteCalendar(params).then(() => {
                    context.commit("deletecalendar", params);
                    resolve();
                }).catch((err) => {
                    reject(err.response.data);
                })
            });
        }
    }
}
