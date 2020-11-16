require("./bootstrap");

require("bootstrap");

import "material-design-icons-iconfont/dist/material-design-icons.css";

import Vue from "vue";
import VueRouter from "vue-router";

import Vuetify from "vuetify/lib";

Vue.use(VueRouter);

Vue.use(Vuetify);


import app from "./components/App.vue";
import loading from "./components/loading.vue";

import home from "./components/pages/home.vue";
// const home = () => ({
//     component: import("./components/pages/home.vue"),
//     loading: loading
// });

import master from './components/pages/master.vue'
// const master = () => import("./components/pages/master.vue");

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
    render: h => h(app)
}).$mount("#app");
