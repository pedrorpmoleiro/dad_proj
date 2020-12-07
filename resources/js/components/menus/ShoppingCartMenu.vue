<template>
    <v-menu offset-y>
        <template v-slot:activator="{ on, attrs }">
            <v-btn
                text
                v-bind="attrs"
                v-on="on"
            >
                <v-icon>shopping_cart</v-icon>
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
                <v-btn text color="red">
                    Logout
                </v-btn>
            </v-list-item>
        </v-list>
    </v-menu>
</template>

<script>
export default {
}
</script>
