<template>
    <v-container>
        <v-card flat :loading="cardLoading">
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
                <v-data-table
                    :headers="headers"
                    :items="availableOrders"
                    disable-filtering
                    disable-sort
                    sort-by="time"
                >
                    <template v-slot:item.customer_extra.name="{ item }">
                        {{ getUserFirstAndLastName(item.customer_extra.name) }}
                    </template>
                    <template v-slot:item.time="{ item }">
                        {{ item.time.toLocaleTimeString() }}
                    </template>
                    <template v-slot:item.actions="{ item }">
                        <v-btn
                            v-if="item.id === oldestOrderId"
                            dark
                            depressed
                            color="green lighten-1"
                            v-on:click.prevent="setOrderInTransit"
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
                value: "id"
            },
            {
                text: "Customer Name",
                value: "customer_extra.name"
            },
            { text: "Time Ready", value: "time" },
            { value: "actions" }
        ],
        oldestOrderId: -1,
        currentOrder: null,
        loading: true,
        cardLoading: false
    }),
    methods: {
        getOrders() {
            this.loading = true;

            axios
                .get("api/deliveryman/orders")
                .then(response => {
                    // console.log(response);
                    if (response.data.currentOrder)
                        this.currentOrder = response.data.currentOrder;
                    else if (response.data.availableOrders) {
                        this.availableOrders = response.data.availableOrders;
                        this.availableOrders.forEach(
                            value =>
                                (value.time = new Date(value.current_status_at))
                        );
                        this.setOldestOrder();
                    } else {
                        this.availableOrders = [];
                        this.currentOrder = null;
                        this.oldestOrderId = -1;
                    }
                    this.loading = false;
                })
                .catch(e => {
                    // console.log(e);
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
        setOldestOrder() {
            return new Promise(() => {
                let oldestId = -1;
                let oldestTime = new Date().getTime();

                for (const i in this.availableOrders) {
                    const order = this.availableOrders[i];
                    const auxTime = order.time.getTime();
                    if (auxTime < oldestTime) {
                        oldestTime = auxTime;
                        oldestId = order.id;
                    }
                }

                if (oldestId !== -1) this.oldestOrderId = oldestId;
            });
        },
        setOrderInTransit() {
            this.cardLoading = true;

            axios
                .patch(`api/deliveryman/orders/transit/${this.oldestOrderId}`)
                .then(response => {
                    // console.log(response);
                    this.$emit(
                        "show-notification",
                        "success",
                        "Currently delivering updated"
                    );
                    this.cardLoading = false;
                })
                .catch(error => {
                    // console.log(e);
                    this.$emit(
                        "show-notification",
                        "error",
                        "Failed to set order in transit"
                    );
                    this.cardLoading = false;
                });
            this.getOrders();
        },
        setOrderDelivered() {
            // TODO
            // this.getOrders();
        },
        getUserFirstAndLastName(userFullName) {
            let aux = userFullName.split(" ");
            return aux[0] + " " + aux[aux.length - 1];
        }
    },
    computed: {
        ...mapGetters(["isUserDeliveryMan", "isAuthLoading"])
    },
    async mounted() {
        while (this.isAuthLoading)
            await new Promise(resolve => setTimeout(resolve, 500));

        if (!this.isUserDeliveryMan) this.$router.push("/home");

        this.getOrders();
    }
};
</script>
