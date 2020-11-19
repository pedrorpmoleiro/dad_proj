<template>
    <v-app class="overflow-hidden">
        <v-app-bar color="white" app>
            <v-container class="py-0 fill-height">
                <v-btn text v-on:click.prevent="goHome">
                    <h3>Food@Home</h3>
                </v-btn>

                <v-btn
                    v-for="link in links"
                    :key="link.name"
                    v-on:click.prevent="$router.push(link.location)"
                    :disabled="$router.currentRoute.path == link.location"
                    text
                >
                    {{ link.name }}
                </v-btn>
            </v-container>

            <v-spacer />
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
export default {
    data: () => ({
        links: [
            {
                name: "Master",
                location: "/master"
            }
        ]
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
