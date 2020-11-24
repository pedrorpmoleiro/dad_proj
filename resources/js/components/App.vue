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
                    <v-icon>cancel</v-icon>
                </v-btn>
            </template>
        </v-snackbar>
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

                <v-spacer></v-spacer>

                <div v-if="!isLoggedIn">
                    <login-dialog
                        v-on:set-token="setToken"
                        v-on:logged-in="onLoggedIn"
                        v-on:invalid-login="onInvalidLogin"
                    ></login-dialog>
                    <register-dialog
                        v-on:set-token="setToken"
                    ></register-dialog>
                </div>
                <div v-else>
                    <v-btn text>
                        Profile - FOO BAR
                    </v-btn>
                    <v-btn text color="red" v-on:click.prevent="logoutUser">
                        Logout
                    </v-btn>
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
import LoginDialog from "./dialogs/LoginDialog.vue";
import RegisterDialog from "./dialogs/RegisterDialog.vue";

export default {
    components: {
        "login-dialog": LoginDialog,
        "register-dialog": RegisterDialog
    },
    data: () => ({
        pubLinks: [
            {
                name: "Menu",
                location: "/menu"
            },
            {
                name: "Master",
                location: "/foo/bar/master"
            }
        ],
        authToken: "",
        notification: {
            color: "",
            showing: false,
            text: ""
        }
    }),
    methods: {
        goHome() {
            if (this.$router.currentRoute.path !== "/home") {
                this.$router.push("/home");
            }
        },
        closeNotification() {
            this.notification.showing = false;
            this.notification.color = "";
            this.notification.text = "";
        },
        setToken(token) {
            if (!this.authToken) this.authToken = token;
        },
        openNotification(color, message) {
            this.notification.color = color;
            this.notification.text = message;
            this.notification.showing = true;
        },
        onLoggedIn() {
            if (this.authToken != null)
                axios.defaults.headers.common.Authorization =
                    "Bearer " + this.authToken;

            this.notification.color = "success";
            this.notification.text = "Login Success";
            this.notification.showing = true;
        },
        onInvalidLogin(message) {
            this.notification.color = "error";
            this.notification.text = message;
            this.notification.showing = true;
        },
        logoutUser() {
            if (!this.authToken) return;

            axios
                .post("api/logout")
                .then(() => {
                    localStorage.clear();
                    this.authToken = "";
                    axios.defaults.headers.common.Authorization = "";
                    this.notification.color = "success";
                    this.notification.text = "Logout Success";
                    this.notification.showing = true;
                    this.goHome();
                })
                .catch(e => {
                    console.log("Error");
                    this.notification.color = "error";
                    this.notification.text = "Logout Error";
                    this.notification.showing = true;
                    console.log(e);
                });
        }
    },
    computed: {
        isLoggedIn() {
            return this.authToken;
        }
    },
    mounted() {
        axios.default.baseURL = process.env.APP_URL;

        let token = localStorage.getItem("access-token");
        if (!this.authToken && token != null) {
            this.authToken = token;
            axios.defaults.headers.common.Authorization =
                "Bearer " + this.authToken;
        }
    }
};
</script>
