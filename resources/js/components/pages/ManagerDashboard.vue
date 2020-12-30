<template>
    <v-container>
        <v-row>
            <v-col>
                <v-card flat :loading="employees.loading">
                    <v-toolbar rounded flat>
                        <v-toolbar-title class="font-weight-bold"
                        >Employees
                        </v-toolbar-title
                        >
                        <v-spacer></v-spacer>
                        <v-btn
                            icon
                            :loading="employees.loading"
                            v-on:click.prevent="getEmployees"
                            color="primary"
                        >
                            <v-icon>cached</v-icon>
                        </v-btn>
                    </v-toolbar>
                    <v-divider></v-divider>
                    <v-data-table
                        :headers="employees.headers"
                        :items="employees.list"
                    >
                        <template v-slot:item.photo="{ item }">
                            <v-layout align-center justify-center>
                                <div v-if="item.photo_url">
                                    <v-img
                                        class="my-2 avatar"
                                        max-width="75"
                                        max-height="75"
                                        :src="
                                            '../storage/fotos/' + item.photo_url
                                        "
                                    ></v-img>
                                </div>
                                <div v-else>
                                    <v-icon x-large>account_circle</v-icon>
                                </div>
                            </v-layout>
                        </template>
                        <template v-slot:item.type="{ item }">
                            <div v-if="item.type === 'EC'">
                                Cook
                            </div>
                            <div v-else-if="item.type === 'ED'">
                                Delivery Man
                            </div>
                            <div v-else-if="item.type === 'EM'">
                                Manager
                            </div>
                        </template>
                        <template v-slot:item.started="{ item }">
                            <div v-if="item.logged_at != null">
                                {{
                                    new Date(
                                        item.logged_at
                                    ).toLocaleTimeString()
                                }}
                            </div>
                        </template>
                        <template v-slot:item.status="{ item }">
                            <v-badge
                                v-if="item.logged_at == null"
                                class="font-italic"
                                inline
                                left
                                dot
                                color="red"
                            >
                                Offline
                            </v-badge>
                            <div v-else-if="item.available_at == null">
                                <v-badge
                                    v-if="item.type === 'EC'"
                                    class="font-italic"
                                    inline
                                    left
                                    dot
                                    color="yellow"
                                >
                                    Preparing
                                </v-badge>
                                <v-badge
                                    v-else-if="item.type === 'ED'"
                                    class="font-italic"
                                    inline
                                    left
                                    dot
                                    color="yellow"
                                >
                                    Delivering
                                </v-badge>
                            </div>
                            <v-badge
                                v-else
                                class="font-italic"
                                inline
                                left
                                dot
                                color="green"
                            >
                                Available
                            </v-badge>
                        </template>
                        <template v-slot:item.order="{ item }">
                            <div
                                v-if="(item.type === 'EC' || item.type === 'ED') && item.order"
                            >
                                <view-order v-bind:order-prop="item.order" v-bind:manager="true"></view-order>
                            </div>
                        </template
                        >
                    </v-data-table>
                </v-card>
            </v-col>
        </v-row>
        <v-row>
            <v-col>
                <v-card flat :loading="orders.loading">
                    <v-toolbar rounded flat>
                        <v-toolbar-title class="font-weight-bold"
                        >Open Orders
                        </v-toolbar-title
                        >
                        <v-spacer></v-spacer>
                        <v-btn
                            icon
                            :loading="orders.loading"
                            v-on:click.prevent="getOrders"
                            color="primary"
                        >
                            <v-icon>cached</v-icon>
                        </v-btn>
                    </v-toolbar>
                    <v-divider></v-divider>
                    <v-data-table
                        :headers="orders.headers"
                        :items="orders.list"
                    >
                        <template v-slot:item.status="{ item }">
                            <div v-if="item.status === 'H'">
                                Holding
                            </div>
                            <div v-else-if="item.status === 'P'">
                                Preparing
                            </div>
                            <div v-else-if="item.status === 'R'">
                                Ready
                            </div>
                            <div v-else-if="item.status === 'T'">
                                In transit
                            </div>
                            <div v-else-if="item.status === 'D'">
                                Delivered
                            </div>
                            <div v-else-if="item.status === 'C'">
                                Cancelled
                            </div>
                        </template>
                        <template v-slot:item.name="{ item }">
                            <div v-if="item.status === 'P'">
                                {{ item.cook.name }}
                            </div>
                            <div v-else-if="item.status === 'T'">
                                {{ item.delivery_man.name }}
                            </div>
                        </template>
                        <template v-slot:item.lastUpdate="{ item }">
                            {{
                                new Date(
                                    item.current_status_at
                                ).toLocaleString()
                            }}
                        </template>
                        <template v-slot:item.order="{ item }">
                            <view-order v-bind:order-prop="item" v-bind:manager="true"></view-order>
                        </template>
                    </v-data-table>
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
        employees: {
            loading: true,
            list: [],
            headers: [
                {
                    text: "Photo",
                    value: "photo"
                },
                {
                    text: "Id",
                    value: "id"
                },
                {
                    text: "Type",
                    value: "type"
                },
                {
                    text: "Name",
                    value: "name"
                },
                {
                    text: "Started at",
                    value: "started"
                },
                {
                    text: "Available",
                    value: "status"
                },
                {
                    text: "Order",
                    value: "order"
                }
            ]
        },
        orders: {
            loading: true,
            list: [],
            headers: [
                {
                    text: "Id",
                    value: "id"
                },
                {
                    text: "Status",
                    value: "status"
                },
                {
                    text: "Employee name",
                    value: "name"
                },
                {
                    text: "Last Updated",
                    value: "lastUpdate"
                },
                {
                    text: "Order",
                    value: "order"
                }
            ]
        }
    }),
    methods: {
        getEmployees() {
            this.employees.loading = true;

            axios
                .get("api/employees")
                .then(response => {
                    // console.log(response);
                    this.employees.list = response.data;
                    this.employees.loading = false;
                })
                .catch(e => {
                    // console.log(e);
                    this.employees.loading = false;
                });
        },
        getOrders() {
            this.orders.loading = true;

            axios
                .get("api/orders/open")
                .then(response => {
                    // console.log(response);
                    this.orders.list = response.data;
                    this.orders.loading = false;
                })
                .catch(e => {
                    // console.log(e);
                    this.orders.loading = false;
                });
        }
    },
    computed: {
        ...mapGetters(["isUserManager", "isAuthLoading"])
    },
    async mounted() {
        while (this.isAuthLoading)
            await new Promise(resolve => setTimeout(resolve, 500));

        if (!this.isUserManager) this.$router.push("/home");

        this.getEmployees();
        this.getOrders();
    }
};
</script>
