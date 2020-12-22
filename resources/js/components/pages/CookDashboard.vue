<template>
    <v-container>
        <v-row>
            <v-col>
                <v-card flat>
                    <v-card-title>
                        <h3 class="ml-2">
                            Current Order
                        </h3>
                        <v-spacer></v-spacer>
                        <v-btn :loading="loading" color="primary" icon v-on:click.prevent="getOrder">
                            <v-icon>cached</v-icon>
                        </v-btn>
                    </v-card-title>
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
                                        <div class="subtitle-1 font-weight-bold mb-1">
                                            Customer
                                        </div>
                                        <div class="mx-1">{{ "Id: " + order.customer.id }}</div>
                                        <div class="mx-1 mb-2">{{ "Name: " + order.customer_extra.name }}</div>
                                    </v-col>
                                    <v-col>
                                        <div class="subtitle-1 font-weight-bold mb-1">
                                            Time
                                        </div>
                                        <!-- TODO TIME ELAPSED -->
                                        <p class="mx-1">{{ order.current_status_at }}</p>
                                    </v-col>
                                    <v-col>
                                        <div class="subtitle-1 font-weight-bold mb-1">
                                            Order Notes
                                        </div>
                                        <p class="mx-1">{{ order.notes }}</p>
                                    </v-col>
                                </v-row>
                                <div class="subtitle-1 font-weight-bold mb-1 mx-3">
                                    Order Products
                                </div>
                                <v-data-table
                                    :items="order.items"
                                    :headers="headers"
                                    :search="search"
                                >
                                    <template v-slot:item.photo_url="{ item }">
                                        <v-img
                                            class="my-2"
                                            max-width="75"
                                            max-height="75"
                                            :src="'../storage/products/' + item.photo_url"
                                        ></v-img>
                                    </template>
                                    <template v-slot:item.type="{ item }">
                                        {{ item.type.charAt(0).toUpperCase() + item.type.slice(1) }}
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
            {text: "Type", value: "type"},
        ],
        search: ""
    }),
    methods: {
        getOrder() {
            this.loading = true;

            axios
                .get("api/cook/order")
                .then(response => {
                    // console.log(response);
                    if (response.data.error)
                        this.order = null
                    else
                        this.order = response.data;
                    this.loading = false;
                })
                .catch(error => {
                    // console.log(error);
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
