<template>
    <v-menu offset-y>
        <template v-slot:activator="{ on, attrs }">
            <v-btn text v-bind="attrs" v-on="on">
                <div v-if="getShoppingCartItems.length">
                    <v-badge :content="getShoppingCartItems.length">
                        <v-icon>shopping_cart</v-icon>
                    </v-badge>
                </div>
                <div v-else>
                    <v-icon>shopping_cart</v-icon>
                </div>
            </v-btn>
        </template>
        <v-card :max-height="height">
            <v-list expand>
                <div v-if="getShoppingCartItems.length === 0">
                    <v-list-item>
                        No Items in Cart
                    </v-list-item>
                </div>
                <div v-else>
                    <v-list-item
                        v-for="item in getShoppingCartItems"
                        :key="item.product.id"
                    >
                        <v-list-item-avatar>
                            <v-img
                                :src="
                                    '../storage/products/' +
                                        item.product.photo_url
                                "
                            ></v-img>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <p>
                                {{ item.product.name }}
                            </p>
                            <p class="subtitle-1 font-italic">
                                {{ "x" + item.amount }}
                            </p>
                        </v-list-item-content>
                        <v-list-item-action>
                            <v-edit-dialog
                                v-on:save="saveCartItem(item)"
                                v-on:open="openEditItem(item)"
                            >
                                <v-btn icon>
                                    <v-icon>create</v-icon>
                                </v-btn>
                                <template v-slot:input>
                                    <v-text-field
                                        v-model="input[item.product.id]"
                                        type="number"
                                        label="Amount"
                                    ></v-text-field>
                                </template>
                            </v-edit-dialog>
                            <v-btn
                                icon
                                v-on:click.prevent="
                                    removeItemFromCart(item.product.id)
                                "
                            >
                                <v-icon color="red">delete</v-icon>
                            </v-btn>
                        </v-list-item-action>
                    </v-list-item>
                    <v-divider></v-divider>
                    <v-list-item>
                        <v-list-item-content>
                            <v-container>
                                <v-row>
                                    <v-col>
                                        <p class="font-weight-bold">
                                            Total:
                                        </p>
                                    </v-col>
                                    <v-spacer></v-spacer>
                                    <v-col>
                                        {{ "€" + getTotal }}
                                    </v-col>
                                </v-row>
                            </v-container>
                        </v-list-item-content>
                        <v-list-item-action>
                            <v-col>
                                <v-row>
                                    <checkout-dialog v-on:show-notification="openNotification"></checkout-dialog>
                                </v-row>
                                <v-row>
                                    <v-btn
                                        text
                                        color="red"
                                        v-on:click.prevent="clearShoppingCart"
                                    >
                                        Clear Cart
                                    </v-btn>
                                </v-row>
                            </v-col>
                        </v-list-item-action>
                    </v-list-item>
                </div>
            </v-list>
        </v-card>
    </v-menu>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

import CheckoutDialog from "../dialogs/CheckoutDialog";

export default {
    components: {
        'checkout-dialog': CheckoutDialog
    },
    data: () => ({
        input: [],
        height: window.innerHeight - 115
    }),
    computed: {
        ...mapGetters(["getShoppingCartItems"]),
        getTotal() {
            let total = 0;

            for (let i in this.getShoppingCartItems)
                total +=
                    this.getShoppingCartItems[i].product.price *
                    this.getShoppingCartItems[i].amount;

            return total.toFixed(2);
        }
    },
    methods: {
        ...mapActions([
            "clearShoppingCart",
            "removeItemFromCart",
            "addUpdateItemToCart"
        ]),
        saveCartItem(item) {
            this.addUpdateItemToCart({
                product: item.product,
                amount: this.input[item.product.id]
            });
        },
        openEditItem(item) {
            this.input[item.product.id] = item.amount;
        },
        openNotification(color, message, timeout = 6000) {
            this.$emit("show-notification", color, message, timeout);
        }
    }
};
</script>
