require("./bootstrap");

import "material-design-icons-iconfont/dist/material-design-icons.css";

window.Vue = require("vue");

import Vuetify from "vuetify/lib";

Vue.use(Vuetify);

const vuetify = new Vuetify({
    icons: {
        iconfont: "md"
    }
});

import Vuex from "vuex";

Vue.use(Vuex);

const store = new Vuex.Store({
    state: {
        auth_user: null,
        auth_loading: true,
        shopping_cart: [],
    },
    getters: {
        isAuthLoading(state) {
            return state.auth_loading;
        },
        isLoggedIn(state) {
            return state.auth_user != null;
        },
        isUserCustomer(state) {
            return state.auth_user != null && state.auth_user.type === "C";
        },
        isUserManager(state) {
            return state.auth_user != null && state.auth_user.type === "EM";
        },
        isUserCook(state) {
            return state.auth_user != null && state.auth_user.type === "EC";
        },
        isUserDeliveryMan(state) {
            return state.auth_user != null && state.auth_user.type === "ED";
        },
        getUser(state) {
            return state.auth_user;
        },
        getShoppingCartItems(state) {
            return state.shopping_cart;
        }
    },
    mutations: {
        SET_AUTH_LOADING(state, loading) {
            state.auth_loading = loading;
        },
        REMOVE_AUTH(state) {
            state.auth_user = null;
        },
        SET_AUTH_USER(state, user) {
            state.auth_user = user;
        },
        SET_PRODUCT(state, product) {
            state.product = product;
        },
        REMOVE_ITEM_FROM_CART(state, itemId) {
            if (state.shopping_cart.length > 0)
                for (let i in state.shopping_cart)
                    if (state.shopping_cart[i].product.id === itemId)
                        state.shopping_cart.splice(i, 1);
        },
        ADD_UPDATE_ITEM_TO_CART(state, item) {
            if (item.amount > 99) {
                // TODO NOTIFICATION VUEX
                return;
            }

            if (state.shopping_cart.length > 0)
                for (let i in state.shopping_cart)
                    if (state.shopping_cart[i].product.id === item.product.id)
                        state.shopping_cart.splice(i, 1);

            state.shopping_cart.push({
                product: item.product,
                amount: item.amount
            });
        },
        CLEAR_CART(state) {
            state.shopping_cart = [];
        }
    },
    actions: {
        setAuthLoading(context, loading) {
            context.commit("SET_AUTH_LOADING", loading);
        },
        setUser(context, user) {
            context.commit("SET_AUTH_USER", user);
        },
        logOut(context) {
            context.commit("REMOVE_AUTH");
        },
        removeItemFromCart(context, itemId) {
            context.commit("REMOVE_ITEM_FROM_CART", itemId);
        },
        addUpdateItemToCart(context, item) {
            context.commit("ADD_UPDATE_ITEM_TO_CART", item);
        },
        clearShoppingCart(context) {
            context.commit("CLEAR_CART");
        }
    }
});

import VueRouter from "vue-router";

Vue.use(VueRouter);

import Tests from "./components/pages/Tests";

import Home from "./components/pages/Home";
import Menu from "./components/pages/Menu";
import UpdateProfile from "./components/pages/UpdateProfile";
import CustomerOrders from "./components/pages/CustomerOrders";
import CookDashboard from "./components/pages/CookDashboard";

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
        path: "/profile",
        component: UpdateProfile
    },
    {
        path: "/orders/customer",
        component: CustomerOrders
    },
    {
        path: "/cook/dashboard",
        component: CookDashboard
    },
    {
        path: "/foo/bar/tests",
        component: Tests
    }
];

const router = new VueRouter({ routes });

import App from "./App.vue";

new Vue({
    vuetify,
    router,
    store,
    render: h => h(App)
}).$mount("#app");
