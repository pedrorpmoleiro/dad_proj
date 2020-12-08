<template>
    <v-menu offset-y>
        <template v-slot:activator="{ on, attrs }">
            <v-btn
                text
                v-bind="attrs"
                v-on="on"
            >
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
                            {{ item.product.name + " x" + item.amount }}
                        </v-list-item-content>
                        <v-list-item-action>
                            <v-btn icon v-on:click.prevent="editCartItem(item)">
                                <v-icon>create</v-icon>
                            </v-btn>
                            <v-btn icon v-on:click.prevent="removeItemFromCart(item.product.id)">
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
                                    <v-btn text color="primary" v-on:click.prevent="checkout">
                                        Checkout
                                    </v-btn>
                                </v-row>
                                <v-row>
                                    <v-btn text color="red" v-on:click.prevent="clearShoppingCart">
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
import {mapActions, mapGetters} from "vuex";

export default {
    data: () => ({}),
    computed: {
        ...mapGetters([
            "getShoppingCartItems",

        ]),
        getTotal() {
            let total = 0;

            for (let i in this.getShoppingCartItems)
                total += (this.getShoppingCartItems[i].product.price * this.getShoppingCartItems[i].amount);

            return total;
        }
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
        editCartItem(item) {
            // TODO
        }
    },
    mounted() {

    }
}
</script>
