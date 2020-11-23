require("./bootstrap");

import "material-design-icons-iconfont/dist/material-design-icons.css";

import Vue from "vue";
import VueRouter from "vue-router";
import Vuetify from "vuetify/lib";

Vue.use(VueRouter);
Vue.use(Vuetify);

import App from "./components/App.vue";
import Home from "./components/pages/Home.vue";
import Master from './components/pages/Master.vue'
import Menu from './components/pages/Menu.vue'

const vuetify = new Vuetify({
    icons: {
        iconfont: 'md',
    },
});

const routes = [
    {
        path: "/",
        redirect: "/home"
    },
    {
        path: "/home",
        component: Home
    },
    {
        path: "/menu",
        component: Menu
    },
    {
        path: "/foo/bar/master",
        component: Master
    }
];

const router = new VueRouter({routes});

new Vue({
    vuetify,
    router,
    render: h => h(App)
}).$mount("#app");
