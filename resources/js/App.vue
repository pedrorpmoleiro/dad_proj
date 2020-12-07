<template>
    <v-app>
        <v-snackbar
            v-model="notification.showing"
            timeout="-1"
            rounded
            :color="notification.color"
        >
            {{ notification.text }}

            <v-spacer></v-spacer>

            <template v-slot:action="{ attrs }">
                <v-progress-circular :value="notification.time * ( 1 / (notification.timeout / 100))" :rotate="-90"
                                     color="white">
                    <v-btn
                        icon
                        v-bind="attrs"
                        v-on:click.prevent="closeNotification"
                    >
                        <v-icon>cancel</v-icon>
                    </v-btn>
                </v-progress-circular>
            </template>
        </v-snackbar>

        <!-- TODO Mobile Hamburger Menu & 'Auth' Menu -->
        <v-app-bar color="white" app style="z-index: 99">
            <v-container class="py-0 fill-height">
                <v-btn text v-on:click.prevent="goHome">
                    <h3>Food@Home</h3>
                </v-btn>

                <v-btn
                    v-for="link in pubLinks"
                    :key="link.name"
                    v-on:click.prevent="$router.push(link.location)"
                    :disabled="$router.currentRoute.path === link.location"
                    text
                >
                    {{ link.name }}
                </v-btn>

                <register-dialog v-on:show-notification="openNotification"></register-dialog>

                <v-spacer></v-spacer>

                <div v-if="!isLoggedIn">
                    <login-dialog v-on:show-notification="openNotification"></login-dialog>
                    <register-dialog v-on:show-notification="openNotification"></register-dialog>
                </div>
                <div v-else>
                    <v-menu offset-y>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                text
                                v-bind="attrs"
                                v-on="on"
                            >
                                <v-icon class="mr-1">account_circle</v-icon>
                                <div>{{ getUserFirstAndLastName(getUser.name) }}</div>
                            </v-btn>
                        </template>
                        <v-list>
                            <v-list-item>
                                <v-btn
                                    text
                                    v-on:click.prevent="$router.push('/profile'); on=!on"
                                    :disabled="$router.currentRoute.path === '/profile'"
                                >
                                    Update Profile
                                </v-btn>
                            </v-list-item>
                            <v-divider></v-divider>
                            <v-list-item>
                                <v-btn text color="red" v-on:click.prevent="logoutUser">
                                    Logout
                                </v-btn>
                            </v-list-item>
                        </v-list>
                    </v-menu>
                    <shopping-cart-menu v-on:show-notification="openNotification"></shopping-cart-menu>
                </div>
            </v-container>
        </v-app-bar>

        <v-main class="grey lighten-3">
            <router-view v-on:show-notification="openNotification"></router-view>
        </v-main>

        <!-- <v-footer app absolute>
            <v-card-text class="text-center"
                >GET 10% OFF SQUARESPACE WITH THIS LINK!!</v-card-text
            >
        </v-footer> -->
    </v-app>
</template>

<script>
import LoginDialog from "./components/dialogs/LoginDialog.vue";
import RegisterDialog from "./components/dialogs/RegisterDialog.vue";
import ShoppingCartMenu from "./components/menus/ShoppingCartMenu.vue";

import {mapActions, mapGetters} from "vuex";

export default {
    components: {
        "login-dialog": LoginDialog,
        "register-dialog": RegisterDialog,
        "shopping-cart-menu": ShoppingCartMenu
    },
    data: () => ({
        pubLinks: [
            {
                name: "Menu",
                location: "/menu"
            },
            {
                name: "New Menu",
                location: "/menu/new"
            },
            {
                name: "Master",
                location: "/foo/bar/master"
            }
        ],
        notification: {
            color: "",
            showing: false,
            text: "",
            time: 0,
            timeout: 6000
        }
    }),
    methods: {
        ...mapActions([
            "logOut",
            "setUser"
        ]),
        goHome() {
            if (this.$router.currentRoute.path !== "/home")
                this.$router.push("/home");
        },
        logoutUser() {
            if (!this.isLoggedIn) return;

            axios
                .post("api/logout")
                .then(() => {
                    this.logOut();
                    axios.defaults.withCredentials = false;
                    this.openNotification("success", "Logout Success");
                    this.goHome();
                })
                .catch(e => {
                    console.log("Error");
                    console.log(e);
                    this.openNotification("error", "Logout Error");
                });
        },
        getUserFirstAndLastName(userFullName) {
            let aux = userFullName.split(" ");
            return aux[0] + " " + aux[aux.length - 1];
        },
        closeNotification() {
            this.notification.showing = false;
            this.notification.color = "";
            this.notification.text = "";
            this.notification.time = this.notification.timeout;
        },
        openNotification(color, message, timeout=6000) {
            this.notification.color = color;
            this.notification.text = message;
            this.notification.time = 0;
            this.notification.showing = true;
            this.notificationProgress();
        },
        async notificationProgress() {
            let old = Date.now();
            while (this.notification.time < this.notification.timeout) {
                let aux = Date.now();
                this.notification.time += (aux - old);
                old = aux;
                await new Promise(resolve => setTimeout(resolve, 100));
            }
            await new Promise(resolve => setTimeout(resolve, 250));
            this.closeNotification();
        }
    },
    computed: {
        ...mapGetters([
            "isLoggedIn",
            "getUser"
        ])
    },
    mounted() {
        // ? A way to check if user is logged in -- Investigate more
        axios.get('/api/users/me').then(response => {
            // Logged in
            // console.dir(response);
            this.setUser(response.data);
        });
        /* .catch(error => {
            // Not Logged in
            console.log(error);
        }); */
    }
};
</script>
