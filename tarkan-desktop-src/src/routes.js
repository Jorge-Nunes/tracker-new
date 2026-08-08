

import {createRouter,createWebHistory} from 'vue-router';
import store from './store/index'

const routes = [
    {path: '/:pathMatch(.*)*',redirect: '/home'},
    {path: '/share/:token', component: ()=>import('@/templates/login.vue'),name: 'Share', meta: {public: true}},
    { path: '/home', component: ()=>import('./templates/home'), meta: {closed: true}},
    {
        path: '/devices',
        meta: {title: 'menu.devices',shown: true},
        component: ()=>import("./components/empty-rv"),
        children: [
            { path: '', components: {default: ()=>import('./templates/devices')}, meta: {closed: true,allowExpand: false,backBtn: '/home'}},
            { path: ':deviceId', components: {default: ()=>import('./templates/devices.internal')}, meta: {mobileBottom: true,closed: true,backBtn: '/devices'}}
        ]
    },
    { path: '/computed', component: ()=>import('./templates/computed'), meta: {title: 'menu.computedAttributes',shown: true}},
    { path: '/autolink', component: ()=>import('./templates/autolink'), meta: {title: 'menu.autoLink',shown: true}},
    { path: '/autolink/:id', component: ()=>import('./templates/autolink'), meta: {title: 'menu.autoLink',shown: true}},
    { path: '/notifications', component: ()=>import('./templates/notifications'), meta: {title: 'menu.notifications',shown: true}},
    { path: '/history', component: ()=>import('./templates/history'), meta: {title: 'menu.history',shown: true,showRoute:true}},
    {
        path: '/reports',
        meta: {title: 'menu.reports',shown: true},
        component: ()=>import("./components/empty-rv"),
        children: [
            { path: '', components: {default: ()=>import('./templates/reportTypes')}, meta: {closed: true,allowExpand: false,showRoute:true}},
            { path: 'resume', components: {default: ()=>import('./templates/reportResume')}, meta: {title: 'menu.reportsResume',mobileBottom: true,closed: true,backBtn: '/reports',showRoute:true}},
            { path: 'travels', components: {default: ()=>import('./templates/reportTravels')}, meta: {title: 'menu.reportsTravels',mobileBottom: true,closed: true,backBtn: '/reports',showRoute:true}},
            { path: 'stops', components: {default: ()=>import('./templates/reportStops')}, meta: {title: 'menu.reportsStops',mobileBottom: true,closed: true,backBtn: '/reports',showRoute:true}},
            { path: 'events', components: {default: ()=>import('./templates/reportEvents')}, meta: {title: 'menu.reportsEvents',mobileBottom: true,closed: true,backBtn: '/reports',showRoute:true}},
            { path: 'history', components: {default: ()=>import('./templates/history')}, meta: {title: 'menu.reportsHistory',mobileBottom: true,closed: true,backBtn: '/reports',showRoute:true}}
        ]
    },
    { path: '/geofence', component: ()=>import('./templates/geofence'), meta: {title: 'menu.geofence',shown: true}},
    {
        path: '/users',
        component: ()=>import('./templates/users'),
        meta: {title: 'Usuários',shown: true}
    },
    { path: '/groups', component: ()=>import('./templates/groups'), meta: {title: 'menu.groups',shown: true}},
    { path: '/commands', component: ()=>import('./templates/commands'), meta: {title: 'menu.commands',shown: true} },
    {path: '/login', component: ()=>import('@/templates/login.vue'),name: 'Login', meta: {public: true}},
    {path: '/register', component: ()=>import('@/templates/register.vue'),name: 'Register', meta: {public: true}},
    {path: '/login/recover', component: ()=>import('@/templates/recoverPassword.vue'),name: 'PasswordRecover', meta: {public: true}},
    {path: '/motorista', component: ()=>import('@/templates/login.vue'),name: 'DriverLogin', meta: {public: true,isDriver: true}},
    {path: '/qr-driver', component: ()=>import('@/templates/qr-driver.vue'),name: 'Driver', meta: {isDriver: true}},
    {path: '/qr-driver/scan/:id', component: ()=>import('@/templates/qr-driver.vue'),name: 'Driver2', meta: {isDriver: true}},
    {path: '/maintenance', component: ()=>import('@/templates/maintenance.vue'),name: 'Maintenance', meta: {public: true}},
]

const router = createRouter({
    history: createWebHistory(),
    routes, // short for `routes: routes`
})

router.beforeEach((to, from, next) => {

    store.commit("server/setPage",false);
    //window.scrollTo(0, 0);
    if(to.name==='Maintenance'){
        next();
    }else if(store.state.auth || to.meta.public){

        next();

        store.dispatch("server/load").then(()=> {

        }).catch(()=>{
            router.push("/maintenance")
        });
    }else if(store.state.auth===false && to.name!=='Login' && to.name!=='DriverLogin' && to.name!=='Share') {
        store.dispatch("server/load").then(()=> {
            store.dispatch("checkSession").then(() => {

                store.dispatch("loadUserData",to.name!=='Driver' && to.name!=='Driver2').then(() => {
                    next();
                });
            }).catch(() => {
                if(to.name==='Driver' || to.name==='Driver2'){
                    router.push("/motorista?to="+to.path);
                }else {
                    router.push("/login?to="+to.path);
                }
            });
        }).catch(()=>{
            router.push("/maintenance")
        });
    }else{
        if(to.name==='Driver' || to.name==='Driver2'){
            router.push("/motorista?to="+to.path);
        }else {
            router.push("/login?to="+to.path);
        }
    }
});

router.afterEach(()=>{
    store.commit("server/setPage",true);
})

export default router;