<template>
    <v-container>
        <v-row>
            <v-col>
                <v-card flat :loading="employees.loading">
                    <v-toolbar rounded flat>
                        <v-toolbar-title class="font-weight-bold"
                        >Employees
                        </v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-autocomplete
                            class="mt-4"
                            v-model="employees.search"
                            label="Search Employees Types"
                            :items="employees.types"
                            clearable
                            prepend-inner-icon="search"
                        ></v-autocomplete>
                        <v-btn
                            icon
                            :loading="employees.loading"
                            v-on:click.prevent="getEmployees"
                            color="primary"
                            class="ml-1"
                        >
                            <v-icon>cached</v-icon>
                        </v-btn>
                    </v-toolbar>
                    <v-divider></v-divider>
                    <v-data-table
                        :headers="employees.headers"
                        :items="employees.list"
                        :search="employees.search"
                        :custom-filter="customSearchFilterEmployees"
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
                            {{ employeeTypeToString(item.type) }}
                        </template>
                        <template v-slot:item.name="{ item }">
                            {{ getUserFirstAndLastName(item.name) }}
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
                                v-if="
                                    (item.type === 'EC' ||
                                        item.type === 'ED') &&
                                    item.order
                                "
                            >
                                <view-order
                                    v-bind:order-prop="item.order"
                                    v-bind:manager="true"
                                ></view-order>
                            </div>
                        </template>
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
                        </v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-autocomplete
                            class="mt-4"
                            v-model="orders.search"
                            label="Search Orders"
                            :items="orders.status"
                            clearable
                            prepend-inner-icon="search"
                        ></v-autocomplete>
                        <v-btn
                            icon
                            :loading="orders.loading"
                            v-on:click.prevent="getOrders"
                            color="primary"
                            class="ml-1"
                        >
                            <v-icon>cached</v-icon>
                        </v-btn>
                    </v-toolbar>
                    <v-divider></v-divider>
                    <v-data-table
                        :headers="orders.headers"
                        :items="orders.list"
                        :search="orders.search"
                        :custom-filter="customSearchFilterOrders"
                    >
                        <template v-slot:item.status="{ item }">
                            {{ orderStatusToString(item.status) }}
                        </template>
                        <template v-slot:item.name="{ item }">
                            <div v-if="item.status === 'P'">
                                {{ getUserFirstAndLastName(item.cook.name) }}
                            </div>
                            <div v-else-if="item.status === 'T'">
                                {{ getUserFirstAndLastName(item.delivery_man.name) }}
                            </div>
                        </template>
                        <template v-slot:item.lastUpdate="{ item }">
                            {{
                                new Date(
                                    item.current_status_at
                                ).toLocaleString()
                            }}
                        </template>
                        <template v-slot:item.actions="{ item }">
                            <v-edit-dialog
                                v-if="item.status === 'H'"
                                v-on:save="assignOrderToCook(item)"
                                v-on:open="loadAvailableCooks">
                                <v-btn icon>
                                    <v-icon>person_add</v-icon>
                                </v-btn>
                                <template v-slot:input>
                                    <v-autocomplete
                                        v-model="assignCookInput[item.id]"
                                        label="Available cooks to assign to order"
                                        :items="availableCooks.names"
                                        hint="Press enter to assign order"
                                        clearable
                                        :rules="[rules.required]"
                                        prepend-inner-icon="person"
                                    ></v-autocomplete>
                                </template>
                            </v-edit-dialog>
                            <view-order
                                v-bind:order-prop="item"
                                v-bind:manager="true"
                            ></view-order>
                            <v-btn icon v-on:click.prevent="cancelOrder(item)" color="red lighten-1">
                                <v-icon>clear</v-icon>
                            </v-btn>
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
        "view-order": ViewOrderDialog,
    },
    data: () => ({
        employees: {
            loading: true,
            list: [],
            search: "",
            types: ["Cook", "Manager", "Delivery Man"],
            headers: [
                {
                    text: "Photo",
                    value: "photo",
                    filterable: false,
                    sortable: false,
                },
                {
                    text: "Id",
                    value: "id",
                    filterable: false
                },
                {
                    text: "Type",
                    value: "type",
                },
                {
                    text: "Name",
                    value: "name",
                    filterable: false
                },
                {
                    text: "Started at",
                    value: "started",
                    filterable: false
                },
                {
                    text: "Available",
                    value: "status",
                    filterable: false
                },
                {
                    text: "Order",
                    value: "order",
                    filterable: false,
                    sortable: false
                },
            ],
        },
        orders: {
            loading: true,
            list: [],
            search: "",
            status: ["Holding", "Preparing", "Ready", "In transit", "Delivered", "Cancelled"],
            headers: [
                {
                    text: "Id",
                    value: "id",
                    filterable: false,
                },
                {
                    text: "Status",
                    value: "status",
                },
                {
                    text: "Employee name",
                    value: "name",
                    filterable: false,
                },
                {
                    text: "Last Updated",
                    value: "lastUpdate",
                    filterable: false,
                },
                {
                    text: "Actions",
                    value: "actions",
                    filterable: false,
                    sortable: false,
                },
            ],
        },
        availableCooks: {
            list: [],
            names: []
        },
        assignCookInput: [],
        rules: {
            required: value => !!value || "Required",
        }
    }),
    sockets: {
        order_created() {
            this.getOrders();
        },
        order_updated() {
            this.getOrders();
            this.getEmployees();
        },
        user_logged_out() {
            this.getEmployees();
        },
        user_logged_in() {
            this.getEmployees();
        }
    },
    methods: {
        getEmployees() {
            this.employees.loading = true;
            this.employees.list = [];
            this.availableCooks.list = [];
            this.availableCooks.names = [];
            this.assignCookInput = [];

            axios
                .get("api/employees")
                .then((response) => {
                    // console.log(response);
                    this.employees.list = response.data;
                    this.employees.loading = false;
                })
                .catch((e) => {
                    // console.log(e);
                    this.employees.loading = false;
                });
        },
        getOrders() {
            this.orders.loading = true;
            this.orders.list = [];

            axios
                .get("api/orders/open")
                .then((response) => {
                    // console.log(response);
                    this.orders.list = response.data;
                    this.orders.loading = false;
                })
                .catch((e) => {
                    // console.log(e);
                    this.orders.loading = false;
                });
        },
        employeeTypeToString(type) {
            switch (type) {
                case 'EC':
                    return 'Cook';
                case 'ED':
                    return 'Delivery Man';
                case 'EM':
                    return 'Manager';
            }
        },
        customSearchFilterEmployees(value, search) {
            if (
                search === "" ||
                search == null ||
                typeof search === typeof undefined
            )
                return true;

            return this.employeeTypeToString(value.toString().toUpperCase()) ===
                search.toString()
        },
        orderStatusToString(status) {
            switch (status) {
                case 'H':
                    return 'Holding';
                case 'P':
                    return 'Preparing';
                case 'R':
                    return 'Ready';
                case 'T':
                    return 'In transit';
                case 'D':
                    return 'Delivered';
                case 'C':
                    return 'Cancelled';
            }
        },
        customSearchFilterOrders(value, search) {
            if (
                search === "" ||
                search == null ||
                typeof search === typeof undefined
            )
                return true;

            return this.orderStatusToString(value.toString().toUpperCase()) ===
                search.toString()
        },
        cancelOrder(order) {
            this.orders.loading = true;

            axios
                .patch(`api/orders/cancel/${order.id}`)
                .then((response) => {
                    // console.log(response);
                    this.orders.loading = false;

                    let payload = {
                        user: {type: this.getUser.type, id: this.getUser.id},
                        order: {status: order.status, customerID: order.customer.id}
                    };

                    if (payload.order.status.toUpperCase() === "P")
                        payload.order.cookId = order.cook.id;
                    else if (payload.order.status.toUpperCase() === "T") {
                        payload.order.deliveryManId = order.delivery_man.id;
                    }

                    this.$socket.emit("order_canceled", payload);

                    this.getOrders();
                    this.$emit(
                        "show-notification",
                        "success",
                        "Order Cancelled Successfully"
                    );
                })
                .catch((e) => {
                    // console.log(e);
                    this.$emit(
                        "show-notification",
                        "error",
                        "Failed to cancel order"
                    );
                    this.orders.loading = false;
                });
        },
        getUserFirstAndLastName(userFullName) {
            let aux = userFullName.split(" ");
            return aux[0] + " " + aux[aux.length - 1];
        },
        loadAvailableCooks() {
            if (this.availableCooks.list.length > 0)
                return;

            this.employees.list.forEach((value) => {
                if (value.type === 'EC' &&
                    value.logged_at && value.available_at) {
                    this.availableCooks.list.push(value);
                    this.availableCooks.names.push(this.getUserFirstAndLastName(value.name));
                }
            });
        },
        assignOrderToCook(order) {
            this.orders.loading = true;

            const assignment = {
                orderId: order.id,
                cookId: this.availableCooks.list[this.availableCooks.names.indexOf(this.assignCookInput[order.id])].id
            };

            axios.patch('api/orders/cook/assign', assignment).then(response => {
                // console.log(response);
                this.orders.loading = false;
                this.$emit(
                    "show-notification",
                    "success",
                    "Order assigned to cook"
                );

                this.$socket.emit("assign_order", {
                    user: {id: this.getUser.id, type: this.getUser.type},
                    cookID: assignment.cookId,
                    order: {customerID: order.customer.id}
                });

                this.getOrders();
            }).catch(e => {
                // console.log(e);
                this.$emit(
                    "show-notification",
                    "error",
                    "Failed to assign order"
                );
                this.orders.loading = false;
            })
        }
    },
    computed: {
        ...mapGetters(["isUserManager", "isAuthLoading", "getUser"]),
    },
    async mounted() {
        while (this.isAuthLoading)
            await new Promise((resolve) => setTimeout(resolve, 500));

        if (!this.isUserManager) this.$router.push("/home");

        this.getEmployees();
        this.getOrders();
    },
};
</script>
