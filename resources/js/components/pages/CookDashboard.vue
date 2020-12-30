<template>
    <v-container>
        <v-row>
            <v-col>
                <v-card flat :loading="cardLoading">
                    <v-card-title>
                        <h3 class="ml-3">
                            Current Order
                        </h3>
                        <v-spacer></v-spacer>
                        <v-btn
                            v-if="!loading && order != null"
                            class="mr-2"
                            depressed
                            dark
                            color="green lighten-1"
                            v-on:click.prevent="setOrderPrepared"
                        >
                            Prepared
                            <v-icon class="ml-1">done_all</v-icon>
                        </v-btn>
                        <v-btn
                            :loading="loading"
                            color="primary"
                            text
                            v-on:click.prevent="getOrder"
                        >
                            <v-icon>cached</v-icon>
                        </v-btn>
                    </v-card-title>
                    <v-divider></v-divider>
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
                    <div v-else>
                        <div v-if="order == null">
                            <v-container>
                                <v-layout align-center justify-center>
                                    <h3 class="my-3">No Order to prepare</h3>
                                </v-layout>
                            </v-container>
                        </div>
                        <div v-else>
                            <v-container>
                                <v-row class="mx-3">
                                    <v-col>
                                        <div
                                            class="subtitle-1 font-weight-bold mb-1"
                                        >
                                            Customer
                                        </div>
                                        <div class="mx-1">
                                            {{ "Id: " + order.customer.id }}
                                        </div>
                                        <div class="mx-1">
                                            {{
                                                "Name: " +
                                                order.customer_extra.name
                                            }}
                                        </div>
                                    </v-col>
                                    <v-col>
                                        <div
                                            class="subtitle-1 font-weight-bold mb-1"
                                        >
                                            Time
                                        </div>
                                        <p class="mx-1">
                                            {{ order.current_status_at }}
                                        </p>
                                    </v-col>
                                    <v-col>
                                        <div
                                            class="subtitle-1 font-weight-bold mb-1"
                                        >
                                            Order Notes
                                        </div>
                                        <p class="mx-1">{{ order.notes }}</p>
                                    </v-col>
                                </v-row>
                                <div
                                    class="subtitle-1 font-weight-bold my-1 mx-3"
                                >
                                    Order Products
                                </div>
                                <v-data-table
                                    :items="order.items"
                                    :headers="headers"
                                >
                                    <template v-slot:item.photo_url="{ item }">
                                        <v-img
                                            class="my-2"
                                            max-width="75"
                                            max-height="75"
                                            :src="
                                                '../storage/products/' +
                                                    item.photo_url
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
                            </v-container>
                        </div>
                    </div>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import {mapGetters} from "vuex";

export default {
    data: () => ({
        loading: true,
        cardLoading: false,
        order: null,
        headers: [
            {
                text: "Photo",
                value: "photo_url",
                filterable: false,
                sortable: false
            },
            {text: "Name", value: "name", filterable: false},
            {text: "Description", value: "description", filterable: false},
            {text: "Quantity", value: "pivot.quantity"},
            {text: "Type", value: "type"}
        ],
        timeElapsed: 0,
        stopTimeElapsed: false
    }),
    methods: {
        getOrder() {
            this.loading = true;

            axios
                .get("api/cook/order")
                .then(response => {
                    // console.log(response);
                    if (response.data.error) this.order = null;
                    else this.order = response.data;
                    this.loading = false;
                    this.stopTimeElapsed = false;
                    this.timeElapsedCalculator();
                })
                .catch(error => {
                    // console.log(error);
                    this.loading = false;
                    this.order = null;
                    this.timeElapsed = 0;
                    this.stopTimeElapsed = true;
                    this.$emit(
                        "show-notification",
                        "error",
                        "Failed to get Order information"
                    );
                });
        },
        setOrderPrepared() {
            this.cardLoading = true;
            axios
                .patch("api/cook/order/prepared")
                .then(response => {
                    // console.log(response);
                    this.stopTimeElapsed = true;
                    this.cardLoading = false;
                    this.$emit(
                        "show-notification",
                        "success",
                        "Order Prepared"
                    );
                    this.getOrder();
                })
                .catch(error => {
                    // console.log(error);
                    this.cardLoading = false;
                    this.$emit(
                        "show-notification",
                        "error",
                        "Failed to Update order"
                    );
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
        }
    },
    computed: {
        ...mapGetters(["isUserCook", "isAuthLoading"])
    },
    async mounted() {
        while (this.isAuthLoading)
            await new Promise(resolve => setTimeout(resolve, 500));

        if (!this.isUserCook) this.$router.push("/home");

        this.getOrder();
    }
};
</script>
