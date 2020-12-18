<template>
    <v-container>
        <v-row>
            <v-col>
                <v-card flat :loading="loading">
                    <v-card-title class="red lighten-1">
                        WIP - Current Order
                    </v-card-title>
                    <div v-if="loading">
                        <v-layout align-center justify-center>
                            <v-progress-circular
                                indeterminate
                                color="primary"
                            ></v-progress-circular>
                        </v-layout>
                    </div>
                    <div v-else>
                        <div v-if="order">
                            <v-layout align-center justify-center>
                                <h3>No Order to prepare</h3>
                            </v-layout>
                        </div>
                        <div v-else>
                            <v-container>
                                <h1>HAS ORDER</h1>
                            </v-container>
                        </div>
                    </div>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import { mapGetters } from "vuex";

export default {
    data: () => ({
        loading: true,
        order: {}
    }),
    methods: {
        getOrder() {
            this.loading = true;

            axios
                .get("api/orders/cook")
                .then(response => {
                    console.log(response);
                    this.order = response.data;
                    this.loading = false;
                })
                .catch(error => {
                    console.log(error);
                    this.loading = false;
                    this.order = null;
                    this.$emit(
                        "show-notification",
                        "error",
                        "Failed to get Order information"
                    );
                });
        }
    },
    computed: {
        ...mapGetters(["getUser", "isUserCook", "isAuthLoading"])
    },
    async mounted() {
        while (this.isAuthLoading)
            await new Promise(resolve => setTimeout(resolve, 500));

        if (!this.isUserCook) this.$router.push("/home");

        this.getOrder();
    }
};
</script>
