require("./bootstrap");

require("bootstrap");

import "material-design-icons-iconfont/dist/material-design-icons.css";

import Vue from "vue";
import VueRouter from "vue-router";

import Vuetify from "vuetify/lib";

import Vuesax from 'vuesax'
import 'vuesax/dist/vuesax.css' //Vuesax styles

Vue.use(VueRouter);

Vue.use(Vuetify);

Vue.use(Vuesax)

import app from "./components/App.vue";

// import home from "./components/pages/home.vue";
const home = () => ({
    component: import("./components/pages/home.vue"),
    loading: master
});

// import master from './components/pages/master.vue'
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
    render: h => h(app)
}).$mount("#app");
