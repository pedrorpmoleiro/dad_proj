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
                <v-btn
                    icon
                    v-bind="attrs"
                    v-on:click.prevent="closeNotification"
                >
                    <v-progress-circular
                        :value="
                            notification.time *
                                (1 / (notification.timeout / 100))
                        "
                        :rotate="-90"
                        color="white"
                    >
                        <v-icon>cancel</v-icon>
                    </v-progress-circular>
                </v-btn>
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

                <div v-if="isLoggedIn">
                    <div v-if="isUserManager">
                        <v-btn
                            v-for="link in managerLinks"
                            :key="link.name"
                            v-on:click.prevent="$router.push(link.location)"
                            :disabled="
                                $router.currentRoute.path === link.location
                            "
                            text
                        >
                            {{ link.name }}
                        </v-btn>
                        <global-message></global-message>
                    </div>
                    <div v-if="isUserCook">
                        <v-btn
                            v-for="link in cookLinks"
                            :key="link.name"
                            v-on:click.prevent="$router.push(link.location)"
                            :disabled="
                                $router.currentRoute.path === link.location
                            "
                            text
                        >
                            {{ link.name }}
                        </v-btn>
                    </div>
                    <div v-if="isUserDeliveryMan">
                        <v-btn
                            v-for="link in deliveryManLinks"
                            :key="link.name"
                            v-on:click.prevent="$router.push(link.location)"
                            :disabled="
                                $router.currentRoute.path === link.location
                            "
                            text
                        >
                            {{ link.name }}
                        </v-btn>
                    </div>
                    <div v-if="isUserCustomer">
                        <v-btn
                            v-for="link in customerLinks"
                            :key="link.name"
                            v-on:click.prevent="$router.push(link.location)"
                            :disabled="
                                $router.currentRoute.path === link.location
                            "
                            text
                        >
                            {{ link.name }}
                        </v-btn>
                    </div>
                </div>

                <v-spacer></v-spacer>

                <div v-if="isAuthLoading">
                    <v-progress-circular
                        indeterminate
                        color="primary"
                    ></v-progress-circular>
                </div>
                <div v-else>
                    <div v-if="!isLoggedIn">
                        <login-dialog
                            v-on:show-notification="openNotification"
                            v-on:user-blocked="userBlocked"
                        ></login-dialog>
                        <register-dialog
                            v-on:show-notification="openNotification"
                        ></register-dialog>
                    </div>
                    <div v-else>
                        <v-menu offset-y>
                            <template v-slot:activator="{ on, attrs }">
                                <v-btn text v-bind="attrs" v-on="on">
                                    <div v-if="getUser.photo_url">
                                        <v-list-item-avatar>
                                            <v-img
                                                :src="
                                                    '../storage/fotos/' +
                                                        getUser.photo_url
                                                "
                                            ></v-img>
                                        </v-list-item-avatar>
                                    </div>
                                    <div v-else>
                                        <v-icon class="mr-1"
                                        >account_circle
                                        </v-icon>
                                    </div>
                                    <div>
                                        {{
                                            getUserFirstAndLastName(
                                                getUser.name
                                            )
                                        }}
                                    </div>
                                </v-btn>
                            </template>
                            <v-list>
                                <v-list-item>
                                    <v-btn
                                        text
                                        v-on:click.prevent="
                                            $router.push('/profile')
                                        "
                                        :disabled="
                                            $router.currentRoute.path ===
                                                '/profile'
                                        "
                                    >
                                        Update Profile
                                    </v-btn>
                                </v-list-item>
                                <v-divider></v-divider>
                                <v-list-item>
                                    <v-btn
                                        text
                                        color="red"
                                        v-on:click.prevent="logoutUser"
                                    >
                                        Logout
                                    </v-btn>
                                </v-list-item>
                            </v-list>
                        </v-menu>
                        <shopping-cart-menu
                            v-if="isUserCustomer"
                            v-on:show-notification="openNotification"
                        ></shopping-cart-menu>
                    </div>
                </div>
            </v-container>
        </v-app-bar>

        <v-main class="grey lighten-3">
            <router-view
                v-on:show-notification="openNotification"
            ></router-view>
        </v-main>

        <!-- <v-footer app absolute>
            <v-card-text class="text-center"
                >GET 10% OFF SQUARESPACE WITH THIS LINK!!</v-card-text
            >
        </v-footer> -->
    </v-app>
</template>

<script>
import LoginDialog from "./components/dialogs/LoginDialog";
import RegisterDialog from "./components/dialogs/RegisterDialog";
import ShoppingCartMenu from "./components/menus/ShoppingCartMenu";
import GlobalMessageDialog from "./components/dialogs/GlobalMessageDialog";

import {mapActions, mapGetters} from "vuex";

// TODO FORGOT PASSWORD LINK!
// TODO RESEND EMAIL VERIFICATION!
// TODO SHOW IF EMAIL IS VERIFIED!

export default {
    components: {
        "login-dialog": LoginDialog,
        "register-dialog": RegisterDialog,
        "shopping-cart-menu": ShoppingCartMenu,
        "global-message": GlobalMessageDialog
    },
    data: () => ({
        pubLinks: [
            {
                name: "Menu",
                location: "/menu"
            }
        ],
        cookLinks: [
            {
                name: "Cook Dashboard",
                location: "/cook/dashboard"
            }
        ],
        deliveryManLinks: [
            {
                name: "Deliveryman Dashboard",
                location: "/deliveryman/dashboard"
            }
        ],
        managerLinks: [
            {
                name: "Manage User Accounts",
                location: "/manager/accounts"
            },
            {
                name: "Manager Dashboard",
                location: "/manager/dashboard"
            }
        ],
        customerLinks: [
            {
                name: "My Orders",
                location: "/orders/customer"
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
    sockets: {
        global_message(payload) {
            this.openNotification(payload.color, payload.message, 20000);
        }
    },
    methods: {
        ...mapActions(["logOut", "setUser", "setAuthLoading"]),
        userBlocked() {
            this.logoutUser(false);
            this.openNotification("error", "Your account has been blocked");
        },
        goHome() {
            if (this.$router.currentRoute.path !== "/home")
                this.$router.push("/home");
        },
        logoutUser(socket = true) {
            if (!this.isLoggedIn) return;

            axios
                .post("api/auth/logout")
                .then(response => {
                    // console.log(response);
                    axios.defaults.withCredentials = false;
                    this.openNotification("success", "Logout Success");
                    this.goHome();
                    if (socket)
                        this.$socket.emit("user_logged_out", {type: this.getUser.type, id: this.getUser.id});
                    this.logOut();
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
        openNotification(color, message, timeout = 6000) {
            this.closeNotification();
            this.notification.color = color;
            this.notification.text = message;
            this.notification.time = 0;
            this.notification.timeout = timeout;
            this.notification.showing = true;
            this.notificationProgress();
        },
        async notificationProgress() {
            let old = Date.now();
            while (this.notification.time < this.notification.timeout) {
                let aux = Date.now();
                this.notification.time += aux - old;
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
            "getUser",
            "isUserManager",
            "isUserCook",
            "isUserDeliveryMan",
            "isUserCustomer",
            "isAuthLoading",
            "isUserBlocked"
        ])
    },
    mounted() {
        // ? A way to check if user is logged in -- Investigate more
        this.setAuthLoading(true);
        axios
            .get("/api/users/me")
            .then(response => {
                // Logged in
                // console.dir(response);
                axios.defaults.withCredentials = true;
                this.setUser(response.data);
                this.setAuthLoading(false);

                if (this.isUserBlocked)
                    // User is denied access
                    this.userBlocked();
                else
                    this.$socket.emit("user_logged_in", {type: this.getUser.type, id: this.getUser.id});
            })
            .catch(e => {
                // console.log(e);
                // Not Logged in
                this.setAuthLoading(false);
            });
    }
};
</script>
