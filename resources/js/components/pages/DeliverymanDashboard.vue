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
                    Order being delivered:
                    <div class="ml-2 font-italic">
                        Id - {{ currentOrder.id }}
                    </div>
                    <v-spacer></v-spacer>
                    <v-btn
                        class="mr-2"
                        depressed
                        dark
                        color="green lighten-1"
                        v-on:click.prevent="setOrderDelivered"
                    >
                        Delivered
                        <v-icon class="ml-1">done_all</v-icon>
                    </v-btn>
                    <v-btn
                        :loading="loading"
                        color="primary"
                        icon
                        v-on:click.prevent="getOrders"
                    >
                        <v-icon>cached</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-container>
                    <v-row class="ml-2">
                        <v-col v-if="currentOrder.customer_extra.photo_url">
                            <h3 class="font-weight-bold">Customer Photo</h3>
                            <v-layout align-center justify-center>
                                <v-img
                                    max-width="100"
                                    max-height="100"
                                    class="avatar"
                                    :src="
                                        '../storage/fotos/' +
                                            currentOrder.customer_extra
                                                .photo_url
                                    "
                                ></v-img>
                            </v-layout>
                        </v-col>
                        <v-col>
                            <h3 class="font-weight-bold">Customer Name</h3>
                            <p>{{ currentOrder.customer_extra.name }}</p>
                        </v-col>
                        <v-col>
                            <h3 class="font-weight-bold">Customer Address</h3>
                            <p>{{ currentOrder.customer.address }}</p>
                        </v-col>
                        <v-col>
                            <h3 class="font-weight-bold">Customer Contacts</h3>
                            <div>
                                <div class="font-italic mr-1">Email:</div>
                                <div>
                                    {{ currentOrder.customer_extra.email }}
                                </div>
                            </div>
                            <div>
                                <div class="font-italic mr-1">Phone:</div>
                                <div>
                                    {{ currentOrder.customer.phone }}
                                </div>
                            </div>
                        </v-col>
                    </v-row>
                    <v-row class="my-3 ml-2">
                        <v-col>
                            <h3 class="font-weight-bold">
                                Delivery Started at:
                            </h3>
                            <p>
                                {{
                                    new Date(
                                        currentOrder.current_status_at
                                    ).toLocaleString()
                                }}
                            </p>
                        </v-col>
                        <v-col>
                            <h3 class="font-weight-bold">
                                Time Elapsed:
                            </h3>
                            <p>{{ timeElapsed }} minutes</p>
                        </v-col>
                        <v-col>
                            <h3 class="font-weight-bold">
                                Order Notes:
                            </h3>
                            <p>{{ currentOrder.notes }}</p>
                        </v-col>
                    </v-row>
                    <div>
                        <h3>
                            Order Products
                        </h3>
                        <v-data-table
                            :headers="currentOrderHeaders"
                            :items="currentOrder.items"
                            disable-filtering
                        >
                            <template v-slot:item.photo_url="{ item }">
                                <v-img
                                    class="my-2"
                                    max-width="75"
                                    max-height="75"
                                    :src="
                                        '../storage/products/' + item.photo_url
                                    "
                                ></v-img>
                            </template>
                            <template v-slot:item.type="{ item }">
                                {{
                                    item.type.charAt(0).toUpperCase() +
                                        item.type.slice(1)
                                }}
                            </template>
                        </v-data-table>
                    </div>
                </v-container>
            </div>
            <div v-else-if="availableOrders.length > 0">
                <v-card-title>
                    Available orders to deliver
                    <v-spacer></v-spacer>
                    <v-btn icon color="primary" v-on:click.prevent="getOrders">
                        <v-icon>cached</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-data-table
                    :headers="availableOrdersHeaders"
                    :items="availableOrders"
                    disable-filtering
                    sort-by="time"
                >
                    <template v-slot:item.customer_extra.name="{ item }">
                        {{ getUserFirstAndLastName(item.customer_extra.name) }}
                    </template>
                    <template v-slot:item.time="{ item }">
                        {{ new Date(item.time).toLocaleTimeString() }}
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
        availableOrdersHeaders: [
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
            { text: "Time Ready", value: "time", sortable: false },
            { value: "actions", sortable: false }
        ],
        oldestOrderId: -1,
        currentOrder: null,
        currentOrderHeaders: [
            {
                text: "Photo",
                value: "photo_url",
                sortable: false
            },
            { text: "Name", value: "name" },
            { text: "Description", value: "description" },
            { text: "Quantity", value: "pivot.quantity" },
            { text: "Type", value: "type" }
        ],
        loading: true,
        cardLoading: false,
        timeElapsed: 0,
        stopTimeElapsed: false
    }),
    methods: {
        getOrders(setLoading = true) {
            if (setLoading) this.loading = true;

            this.availableOrders = [];
            this.currentOrder = null;
            this.oldestOrderId = -1;
            this.timeElapsed = 0;
            this.stopTimeElapsed = true;

            axios
                .get("api/deliveryman/orders")
                .then(response => {
                    // console.log(response);
                    if (response.data.currentOrder) {
                        this.currentOrder = response.data.currentOrder;
                        this.stopTimeElapsed = false;
                        this.timeElapsedCalculator();
                    } else if (response.data.availableOrders) {
                        this.availableOrders = response.data.availableOrders;
                        this.availableOrders.forEach(value => {
                            value.time = new Date(
                                value.current_status_at
                            ).getTime();
                        });
                        this.setOldestOrder();
                    }
                    if (setLoading) this.loading = false;
                })
                .catch(e => {
                    // console.log(e);
                    if (setLoading) this.loading = false;
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
                    const auxTime = new Date(order.time).getTime();
                    if (auxTime < oldestTime) {
                        oldestTime = auxTime;
                        oldestId = order.id;
                    }
                }

                if (oldestId !== -1) this.oldestOrderId = oldestId;
            });
        },
        timeElapsedCalculator() {
            this.stopTimeElapsed = false;

            return new Promise(async () => {
                while (!this.stopTimeElapsed) {
                    const aux =
                        new Date().getTime() -
                        new Date(this.currentOrder.current_status_at).getTime();
                    this.timeElapsed = parseInt(aux / 1000 / 60);
                    await new Promise(resolve => setTimeout(resolve, 30000));
                }
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
                .catch(e => {
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
            this.cardLoading = true;
            const currentOrderId = this.currentOrder.id;

            axios
                .patch(`api/deliveryman/orders/delivered`)
                .then(async response => {
                    // console.log(response);
                    this.cardLoading = false;
                    this.stopTimeElapsed = true;

                    this.$emit(
                        "show-notification",
                        "success",
                        "Order Delivered"
                    );

                    this.loading = true;
                    for (let i = 0; i < 10; i++) {
                        this.getOrders(false);
                        if (this.availableOrders.length > 0) break;
                        else if (
                            this.availableOrders.length == 0 &&
                            this.currentOrder == null
                        )
                            break;
                        else if (this.currentOrder.id != currentOrderId) break;
                        await new Promise(resolve => setTimeout(resolve, 3000));
                    }
                    this.loading = false;
                })
                .catch(e => {
                    // console.log(e);
                    this.$emit(
                        "show-notification",
                        "error",
                        "Failed to set order delivered"
                    );
                    this.cardLoading = false;
                });
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
