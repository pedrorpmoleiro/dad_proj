<template>
    <v-container>
        <v-card>
            <v-card-title header color="red lighten-1"> TESTING PAGE</v-card-title>
            <v-card-text>
                <v-btn v-on:click.prevent="toast">
                    TOAST ME!
                </v-btn>
                <v-btn v-on:click.prevent="snackbar">
                    SNACK ME!
                </v-btn>
            </v-card-text>
        </v-card>
    </v-container>
</template>

<script>
export default {
    data: () => ({}),
    methods: {
        toast() {
            this.$toasted.show('THIS IS A TOAST TEST');
        },
        snackbar() {
            this.$emit("show-notification", "blue", "THIS IS A SNACKBAR")
        }
    }
};
</script>
