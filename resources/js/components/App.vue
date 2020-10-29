<template>
    <v-app id="inspire">
        <v-app-bar app color="white" flat>
            <v-container class="py-0 fill-height">
                <v-avatar
                    class="mr-10"
                    color="grey darken-1"
                    size="32"
                ></v-avatar>

                <v-btn
                    v-for="link in links"
                    :key="link.name"
                    v-on:click.prevent="$router.push(link.location)"
                    text
                >
                    {{ link.name }}
                </v-btn>

                <v-spacer></v-spacer>
            </v-container>
        </v-app-bar>
        <v-main class="grey lighten-3">
            <router-view></router-view>
        </v-main>
        <v-footer app absolute>
            <v-card-text class="text-center"
                >GET 10% OFF SQUARESPACE WITH THIS LINK!!</v-card-text
            >
        </v-footer>
    </v-app>
</template>

<script>
export default {
    data: () => ({
        links: [
            {
                name: "Home",
                location: "/home"
            },
            {
                name: "Master",
                location: "/master"
            }
        ]
    })
};
</script>
