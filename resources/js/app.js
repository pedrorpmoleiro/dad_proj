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

import Vuex from "vuex";

Vue.use(Vuex);

// TODO Max Amount
const store = new Vuex.Store({
    state: {
        auth_user: null,
        shopping_cart: [],
        product: null
    },
    getters: {
        getProduct(state) {
          return state.product;
        },
        isLoggedIn(state) {
            return state.auth_user != null;
        },
        getUser(state) {
            return state.auth_user;
        },
        getShoppingCartItems(state) {
            return state.shopping_cart;
        }
    },
    mutations: {
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
            if (state.shopping_cart.length > 0)
                for (let i in state.shopping_cart)
                    if (state.shopping_cart[i].product.id === item.product.id) {
                        state.shopping_cart.splice(i, 1);
                        // return;
                    }

            state.shopping_cart.push({product: item.product, amount: item.amount});
        },
        CLEAR_CART(state) {
            state.shopping_cart = [];
        }
    },
    actions: {
        setUser(context, user) {
            context.commit("SET_AUTH_USER", user);
        },
        setProduct(context, product) {
            context.commit("SET_PRODUCT", product);
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

import Tests from './components/pages/Tests.vue';

import Home from "./components/pages/Home.vue";
import Menu from './components/pages/Menu.vue';
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
        path: "/profile",
        component: UpdateProfile
    },
    {
        path: "/foo/bar/tests",
        component: Tests
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
