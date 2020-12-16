<template>
    <v-container>
        <v-row>
            <v-col>
                <v-card flat>
                    <v-toolbar rounded flat>
                        <v-toolbar-title class="font-weight-bold">Open Orders</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-btn
                            icon
                            :loading="open.loading"
                            v-on:click.prevent="getOpenOrders"
                        >
                            <v-icon>cached</v-icon>
                        </v-btn>
                    </v-toolbar>
                    <div v-if="open.loading || open.orders.length > 0">
                        <v-data-table :headers="headers" :items="open.orders"
                                      :loading="open.loading">
                            <template v-slot:item.actions="{ item }">
                                <view-order v-bind:order-prop="item"></view-order>
                            </template>
                            <template v-slot:item.total_price="{ item }">
                                {{ "€ " + item.total_price }}
                            </template>
                        </v-data-table>
                    </div>
                    <div v-else>
                        <v-container>
                            <p class="text-justify heading">
                                No Orders found
                            </p>
                        </v-container>
                    </div>
                </v-card>
            </v-col>
        </v-row>
        <v-row>
            <v-col>
                <v-card flat>
                    <v-toolbar rounded flat>
                        <v-toolbar-title class="font-weight-bold">Order History</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-btn
                            icon
                            :loading="history.loading"
                            v-on:click.prevent="getHistoryOrders"
                        >
                            <v-icon>cached</v-icon>
                        </v-btn>
                    </v-toolbar>
                    <div v-if="history.loading || history.orders.length > 0">
                        <v-data-table :headers="headers" :items="history.orders"
                                      :loading="history.loading">
                            <template v-slot:item.actions="{ item }">
                                <view-order v-bind:order-prop="item"></view-order>
                            </template>
                            <template v-slot:item.total_price="{ item }">
                                {{ "€ " + item.total_price }}
                            </template>
                        </v-data-table>
                    </div>
                    <div v-else>
                        <v-container>
                            <p class="text-justify heading">
                                No Orders found
                            </p>
                        </v-container>
                    </div>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import ViewOrderDialog from "../dialogs/ViewOrderDialog";

import {mapGetters} from "vuex";

export default {
    components: {
        "view-order": ViewOrderDialog
    },
    data: () => ({
        open: {
            orders: [],
            loading: false
        },
        history: {
            orders: [],
            loading: false
        },
        headers: [
            {
                value: "actions"
            },
            {
                text: "Order Number",
                value: "id"
            },
            {
                text: "Status",
                value: "status_text",
                sortable: false
            },
            {
                text: "Date",
                value: "date"
            },
            {
                text: "Price",
                value: "total_price"
            }
        ]
    }),
    methods: {
        getAllOrders() {
            this.history.loading = true;
            this.open.loading = true;

            this.history.orders = [];
            this.open.orders = [];

            axios.get("api/orders").then(response => {
                response.data.forEach(order => {
                    switch (order.status) {
                        case "H":
                            order.status_text = "Holding";
                            break;
                        case "P":
                            order.status_text = "Preparing";
                            break;
                        case "R":
                            order.status_text = "Ready";
                            break;
                        case "T":
                            order.status_text = "Transit";
                            break;
                        case "D":
                            order.status_text = "Delivered";
                            break;
                        case "C":
                            order.status_text = "Cancelled";
                            break;
                    }

                    if (order.status === "D" || order.status === "C")
                        this.history.orders.push(order);
                    else
                        this.open.orders.push(order)
                });

                this.history.loading = false;
                this.open.loading = false;
            }).catch(error => {
                this.$emit(
                    "show-notification",
                    "Error",
                    error.response.data
                );
            });
        },
        getHistoryOrders() {
            this.history.loading = true;
            this.history.orders = [];

            axios.get("api/orders/history").then(response => {
                response.data.forEach(order => {
                    switch (order.status) {
                        case "H":
                            order.status_text = "Holding";
                            break;
                        case "P":
                            order.status_text = "Preparing";
                            break;
                        case "R":
                            order.status_text = "Ready";
                            break;
                        case "T":
                            order.status_text = "Transit";
                            break;
                        case "D":
                            order.status_text = "Delivered";
                            break;
                        case "C":
                            order.status_text = "Cancelled";
                            break;
                    }

                    this.history.orders.push(order);
                });

                this.history.loading = false;
            }).catch(error => {
                this.$emit(
                    "show-notification",
                    "Error",
                    error.response.data
                );
            });
        },
        getOpenOrders() {
            this.open.loading = true;
            this.open.orders = [];

            axios.get("api/orders/open").then(response => {
                response.data.forEach(order => {
                    switch (order.status) {
                        case "H":
                            order.status_text = "Holding";
                            break;
                        case "P":
                            order.status_text = "Preparing";
                            break;
                        case "R":
                            order.status_text = "Ready";
                            break;
                        case "T":
                            order.status_text = "Transit";
                            break;
                        case "D":
                            order.status_text = "Delivered";
                            break;
                        case "C":
                            order.status_text = "Cancelled";
                            break;
                    }

                    this.open.orders.push(order)
                });

                this.open.loading = false;
            }).catch(error => {
                this.$emit(
                    "show-notification",
                    "Error",
                    error.response.data
                );
            });
        }
    },
    computed: {
        ...mapGetters(["getUser", "isUserCustomer"])
    },
    mounted() {
        // TODO: Wait for user (e.g., loading)
        // if (!this.isUserCustomer) this.$router.push("/home");
        this.getAllOrders();
    }
};
</script>
