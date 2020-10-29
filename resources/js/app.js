require("./bootstrap");

require("bootstrap");

import "material-design-icons-iconfont/dist/material-design-icons.css";

import Vue from "vue";
import Vuetify from "vuetify/lib";

import VueRouter from "vue-router";

Vue.use(Vuetify);
Vue.use(VueRouter);

import App from "./components/App.vue";

//import Home from "./components/pages/home";
//import Master from "./components/pages/master";

const home = () => ({
    component: import("./components/pages/home.vue"),
    loading: master
});
const master = () => import("./components/pages/master.vue");

let vuetify = new Vuetify();

const routes = [
    {
        path: "/",
        redirect: "/home"
    },
    {
        path: "/home",
        component: home
    },
    {
        path: "/master",
        component: master
    }
];

let router = new VueRouter({ routes });

new Vue({
    vuetify,
    router,
    render: h => h(App)
}).$mount("#app");
