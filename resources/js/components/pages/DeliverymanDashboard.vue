<template>
    <v-container>
        <v-card flat>
            <div v-if="loading">
                <v-container>
                    <v-layout align-center justify-center>
                        <v-progress-circular
                            indeterminate
                            color="primary"
                        ></v-progress-circular>
                    </v-layout>
                </v-container>
            </div>
            <div v-else-if="currentOrder != null">
                <v-card-title>
                    Order being delivered
                </v-card-title>
            </div>
            <div v-else-if="availableOrders.length > 0">
                <v-card-title>
                    Available orders to deliver
                    <v-spacer></v-spacer>
                    <v-btn icon v-on:click.prevent="getOrders">
                        <v-icon>cached</v-icon>
                    </v-btn>
                </v-card-title>
                <v-data-table :headers="headers" :items="availableOrders">
                    <template v-slot:item.customer_extra.name="{ item }">
                        {{ getUserFirstAndLastName(item.customer_extra.name) }}
                    </template>
                    <template v-slot:item.current_status_at="{ item }">
                        {{
                            new Date(
                                item.current_status_at
                            ).toLocaleTimeString()
                        }}
                    </template>
                    <template v-slot:item.actions="{ item }">
                        <v-btn
                            dark
                            depressed
                            color="green lighten-1"
                            v-on:click.prevent=""
                        >
                            <v-icon class="mr-1">local_shipping</v-icon>
                            Deliver
                        </v-btn>
                    </template>
                </v-data-table>
            </div>
            <v-card-title v-else>
                No orders to deliver
                <v-spacer></v-spacer>
                <v-btn icon v-on:click.prevent="getOrders">
                    <v-icon>cached</v-icon>
                </v-btn>
            </v-card-title>
        </v-card>
    </v-container>
</template>

<script>
import { mapGetters } from "vuex";

export default {
    data: () => ({
        availableOrders: [],
        headers: [
            {
                text: "Order Id",
                value: "id",
                sortable: false
            },
            {
                text: "Customer Name",
                value: "customer_extra.name",
                sortable: false
            },
            { text: "Time Ready", value: "current_status_at", sortable: false },
            {
                value: "actions",
                sortable: false,
                filterable: false
            }
        ],
        oldestOrderId: -1,
        currentOrder: null,
        loading: true
    }),
    methods: {
        getOrders() {
            this.loading = true;

            axios
                .get("api/deliveryman/orders")
                .then(response => {
                    console.log(response);
                    if (response.data.currentOrder)
                        this.currentOrder = response.data.currentOrder;
                    else if (response.data.availableOrders) {
                        this.availableOrders = response.data.availableOrders;
                        this.setOldestOrder();
                    } else {
                        this.availableOrders = [];
                        this.currentOrder = null;
                        this.oldestOrderId = -1;
                    }
                    this.loading = false;
                })
                .catch(e => {
                    this.availableOrders = [];
                    this.currentOrder = null;
                    this.oldestOrderId = -1;
                    this.loading = false;
                    this.$emit(
                        "show-notification",
                        "error",
                        "Failed to get Orders"
                    );
                });
        },
        async setOldestOrder() {
            // TODO
        },
        setOrderInTransit() {
            // TODO
        },
        getUserFirstAndLastName(userFullName) {
            let aux = userFullName.split(" ");
            return aux[0] + " " + aux[aux.length - 1];
        }
    },
    computed: {
        ...mapGetters(["getUser", "isUserDeliveryMan", "isAuthLoading"])
    },
    async mounted() {
        while (this.isAuthLoading)
            await new Promise(resolve => setTimeout(resolve, 500));

        if (!this.isUserDeliveryMan) this.$router.push("/home");

        this.getOrders();
    }
};
</script>
