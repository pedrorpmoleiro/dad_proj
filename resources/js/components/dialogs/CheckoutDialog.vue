<template>
    <v-dialog v-model="dialog" width="500">
        <template v-slot:activator="{ on, attrs }">
            <v-btn text color="primary" v-bind="attrs" v-on="on">
                Checkout
            </v-btn>
        </template>
        <v-card :loading="loading">
            <v-card-title>
                Order Options
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text>
                <v-form>
                    <v-container>
                        <v-row>
                            <v-textarea
                                v-model="input"
                                label="Notes"
                                hint="Add notes for cook and delivery here"
                                auto-grow
                                clearable
                                counter
                                no-resize
                                rows="6"
                                required
                            ></v-textarea>
                        </v-row>
                    </v-container>
                </v-form>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                    text
                    color="green"
                    v-on:click.prevent="submit"
                >
                    Place Order
                </v-btn>
                <v-btn text color="red" v-on:click.prevent="dialog = false">
                    Cancel
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import {mapActions, mapGetters} from "vuex";

export default {
    data: () => ({
        dialog: false,
        loading: false,
        input: ""
    }),
    computed: {
        ...mapGetters(["getShoppingCartItems", "getUser"]),
    },
    methods: {
        ...mapActions(["clearShoppingCart"]),
        submit() {
            this.loading = true;

            let order = {
                "notes": this.input,
                "products": []
            };

            for (let i in this.getShoppingCartItems)
                order.products.push({
                    product_id: this.getShoppingCartItems[i].product.id,
                    quantity: this.getShoppingCartItems[i].amount
                })

            axios
                .post("api/orders/new", order)
                .then(response => {
                    // console.log(response);
                    this.loading = false;
                    this.dialog = false;
                    this.$emit(
                        "show-notification",
                        "success",
                        "Order Placed"
                    );

                    this.$socket.emit("order_created", {type: this.getUser.type, id: this.getUser.id});

                    this.clearShoppingCart();

                    // TODO MOVE TO ORDERS PAGE
                    // TODO RECEIVE ORDER INFO
                    // TODO WITH ORDER INFO SET THE COOK
                })
                .catch(e => {
                    // console.log(e);
                    this.loading = false;
                    this.$emit(
                        "show-notification",
                        "error",
                        "Unable to checkout"
                    );
                });
        }
    }
};
</script>
