<template>
    <v-menu offset-y>
        <template v-slot:activator="{ on, attrs }">
            <v-btn
                text
                v-bind="attrs"
                v-on="on"
            >
                <v-icon>shopping_cart</v-icon>
            </v-btn>
        </template>
        <v-card>
            <v-list>
                <v-list-item>
                    Shopping Cart
                </v-list-item>
                <v-divider></v-divider>
                <div v-if="getShoppingCartItems.length === 0">
                    <v-list-item>
                        No Items in Cart
                    </v-list-item>
                </div>
                <div v-else>
                    <v-list-item v-for="item in getShoppingCartItems" :key="item.product.id">
                        <v-list-item-avatar>
                            <v-img
                                :src="'../storage/products/' + item.product.photo_url"></v-img>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            {{ item.product.name }}
                        </v-list-item-content>
                        <v-list-item-action>
                            <v-btn icon v-on:click.prevent="removeFromCart(item.product.id)">
                                <v-icon>delete</v-icon>
                            </v-btn>
                        </v-list-item-action>
                    </v-list-item>
                    <v-divider></v-divider>
                    <v-list-item>
                        <v-list-item-action>
                            <v-btn text color="primary" large v-on:click.prevent="checkout">
                                Checkout
                            </v-btn>
                            <v-btn text color="warning" v-on:click.prevent="clearCart">
                                Clear Cart
                            </v-btn>
                        </v-list-item-action>
                    </v-list-item>
                </div>
            </v-list>
        </v-card>
    </v-menu>
</template>

<script>
import {mapActions, mapGetters} from "vuex";

export default {
    data: () => ({}),
    computed: {
        ...mapGetters([
            "getShoppingCartItems",

        ]),
    },
    methods: {
        ...mapActions([
            "clearShoppingCart",
            "removeItemFromCart",
            "addUpdateItemToCart"
        ]),
        checkout() {
            // TODO
        },
        removeFromCart(itemId) {
            this.removeItemFromCart(itemId);
        },
        clearCart() {
            this.clearShoppingCart();
        }
    },
    mounted() {

    }
}
</script>
