<template>
    <v-container>
        <v-row>
            <v-col>
                <v-card>
                    <v-card-title class="red">
                        WIP Open Orders
                    </v-card-title>
                </v-card>
            </v-col>
        </v-row>
        <v-row>
            <v-col>
                <v-card>
                    <v-card-title class="red">
                        WIP Order History
                    </v-card-title>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import { mapGetters } from "vuex";

export default {
    data: () => ({
        orders: [],
        openOrders: [],
        orderHistory: []
    }),
    methods: {
        getOrders() {
            //
        }
    },
    computed: {
        ...mapGetters(["getUser", "isUserCustomer"])
    },
    mounted() {
        // TODO: Wait for user (e.g., loading)
        // if (!this.isUserCustomer) this.$router.push("/home");
        this.getOrders();
    }
};
</script>
