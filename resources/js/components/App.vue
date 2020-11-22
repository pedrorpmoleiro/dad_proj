<template>
    <v-app class="overflow-hidden">
        <v-app-bar color="white" app>
            <v-container class="py-0 fill-height">
                <v-btn text v-on:click.prevent="goHome">
                    <h3>Food@Home</h3>
                </v-btn>

                <v-btn
                    v-for="link in pubLinks"
                    :key="link.name"
                    v-on:click.prevent="$router.push(link.location)"
                    :disabled="$router.currentRoute.path == link.location"
                    text
                >
                    {{ link.name }}
                </v-btn>

                <v-spacer />

                <div v-if="auth == false">
                    <login-dialog/>
                    <register-dialog/>
                </div>
                <div v-else>
                    <v-btn text>
                        Profile - FOO BAR
                    </v-btn>
                    <v-btn text>
                        LogOut - BAR FOO
                    </v-btn>
                </div>

            </v-container>
        </v-app-bar>
        <v-main class="grey lighten-3">
            <router-view />
        </v-main>
        <!-- <v-footer app absolute>
            <v-card-text class="text-center"
                >GET 10% OFF SQUARESPACE WITH THIS LINK!!</v-card-text
            >
        </v-footer> -->
    </v-app>
</template>

<script>
import LoginDialog from './dialogs/loginDialog.vue';
import RegisterDialog from './dialogs/RegisterDialog.vue';

export default {
    components: {
        "login-dialog": LoginDialog,
        "register-dialog": RegisterDialog
    },
    data: () => ({
        pubLinks: [
            {
                name: "Menu",
                location: "Menu"
            },
            {
                name: "Master",
                location: "/foo/bar/master"
            }
        ],
        auth: false
    }),
    methods: {
        goHome() {
            if (this.$router.currentRoute.path != "/home") {
                this.$router.push("/home");
            }
        }
    }
};
</script>
