<template>
    <v-container>
        <v-card flat>
            <div v-if="currentOrder != null">
                <v-card-title>
                    Order being delivered
                </v-card-title>
            </div>
            <div v-else-if="availableOrders.length > 0">
                <v-card-title>
                    Available orders to deliver
                </v-card-title>
            </div>
            <v-card-title v-else>
                No orders to deliver
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
                text: "Photo",
                value: "photo_url",
                filterable: false,
                sortable: false
            },
            { text: "Name", value: "name", filterable: false },
            { text: "Description", value: "description", filterable: false },
            { text: "Type", value: "type" },
            { text: "Price", value: "price", filterable: false }
        ],
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
                    else if (response.data.availableOrders)
                        this.availableOrders = response.data.availableOrders;
                    else {
                        this.availableOrders = [];
                        this.currentOrder = null;
                    }
                    this.loading = false;
                })
                .catch(e => {
                    this.availableOrders = [];
                    this.currentOrder = null;
                    this.loading = false;
                    this.$emit(
                        "show-notification",
                        "error",
                        "Failed to get Orders"
                    );
                });
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
