require("./bootstrap");

import "material-design-icons-iconfont/dist/material-design-icons.css";

window.Vue = require('vue');

import Vuetify from "vuetify/lib";
Vue.use(Vuetify);

const vuetify = new Vuetify({
    icons: {
        iconfont: 'md',
    },
});

import Toasted from "vue-toasted";
Vue.use(Toasted, {
    position: "bottom-center",
    duration: 5000,
    keepOnHover: true,
    type: "info",
    theme: "bubble",
    // theme: "toasted-primary",
    className: ["toasted",]
});

import Vuex from "vuex";
Vue.use(Vuex);

const store = new Vuex.Store({
    state: {
        auth_user: null
    },
    getters: {
        isLoggedIn(state) {
            return state.auth_user != null;
        },
        getUser(state) {
            return state.auth_user;
        }
    },
    mutations: {
        REMOVE_AUTH(state) {
            state.auth_user = null;
        },
        SET_AUTH_USER(state, user) {
            state.auth_user = user;
        }
    },
    actions: {
        setUser(context, user) {
            context.commit("SET_AUTH_USER", user);
        },
        logOut(context) {
            context.commit("REMOVE_AUTH");
        }
    }
});

import VueRouter from "vue-router";
Vue.use(VueRouter);

import Home from "./components/pages/Home.vue";
import Master from './components/pages/Master.vue';
import Menu from './components/pages/Menu.vue';
import NewMenu from './components/pages/NewMenu.vue'
import UpdateProfile from "./components/pages/UpdateProfile.vue";


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
        path: "/menu/new",
        component: NewMenu
    },
    {
      path: "/profile",
      component: UpdateProfile
    },
    {
        path: "/foo/bar/master",
        component: Master
    }
];

const router = new VueRouter({routes});

import App from "./App.vue";

new Vue({
    vuetify,
    router,
    store,
    render: h => h(App)
}).$mount("#app");
