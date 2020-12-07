<template>
    <v-container>
        <v-data-iterator
            :items="products"
            :items-per-page.sync="productsPerPage"
            :page="page"
            :search="search"
            :sort-by="sortBy.toLowerCase()"
            :sort-desc="sortDesc"
            hide-default-footer
        >
            <template v-slot:header>
                <!-- TODO CARD! -->
                <v-toolbar
                    rounded
                    flat
                    class="mb-1"
                >
                    <v-text-field
                        v-model="search"
                        clearable
                        flat
                        solo-inverted
                        hide-details
                        prepend-inner-icon="search"
                        label="Search"
                    ></v-text-field>
                    <template v-if="$vuetify.breakpoint.mdAndUp">
                        <v-spacer></v-spacer>
                        <v-select
                            v-model="sortBy"
                            flat
                            solo-inverted
                            hide-details
                            :items="keys"
                            prepend-inner-icon="search"
                            label="Sort by"
                        ></v-select>
                        <v-spacer></v-spacer>
                        <v-btn-toggle
                            v-model="sortDesc"
                            mandatory
                        >
                            <v-btn
                                large
                                depressed
                                :value="false"
                            >
                                <v-icon>arrow_upward</v-icon>
                            </v-btn>
                            <v-btn
                                large
                                depressed
                                :value="true"
                            >
                                <v-icon>arrow_downward</v-icon>
                            </v-btn>
                        </v-btn-toggle>
                        <v-spacer></v-spacer>
                        <v-btn large depressed :loading="loading" v-on:click.prevent="getProducts">
                            <v-icon>cached</v-icon>
                        </v-btn>
                    </template>
                </v-toolbar>
            </template>

            <template v-slot:default="props">
                <v-row>
                    <v-col
                        v-for="item in props.items"
                        :key="item.name"
                        cols="12"
                        sm="6"
                        md="4"
                        lg="3"
                    >
                        <v-card flat>
                            <v-img :src="'../storage/products/' + item.photo_url"></v-img>

                            <v-card-title class="subheading font-weight-bold">
                                {{ item.name }}
                            </v-card-title>

                            <v-card-text>
                                <p class="subtitle-1 font-italic">
                                    {{ item.type.charAt(0).toUpperCase() + item.type.slice(1) }}
                                </p>
                                <p class="text-justify">
                                    {{ item.description }}
                                </p>
                                <p class="font-weight-bold orange--text">
                                    {{ "€" + item.price }}
                                </p>
                            </v-card-text>
                            <div v-if="isLoggedIn">
                                <v-divider></v-divider>
                                <v-card-actions>
                                    <v-text-field rounded label="Quantity" placeholder="1"
                                                  v-model="orderAmount[item.id]"></v-text-field>
                                    <v-spacer></v-spacer>
                                    <v-btn
                                        text
                                        color="orange lighten-1"
                                        v-on:click.prevent="addToCart(item.id)"
                                    >
                                        <v-icon>add_shopping_cart</v-icon>
                                    </v-btn>
                                </v-card-actions>
                            </div>
                        </v-card>
                    </v-col>
                </v-row>
            </template>

            <template v-slot:footer>
                <v-row
                    class="mt-2"
                    align="center"
                    justify="center"
                >
                    <div>Items per page</div>
                    <v-menu offset-y>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                dark
                                text
                                color="primary"
                                class="ml-2"
                                v-bind="attrs"
                                v-on="on"
                            >
                                {{ productsPerPage }}
                                <v-icon>chevron_down</v-icon>
                            </v-btn>
                        </template>
                        <v-list>
                            <v-list-item
                                v-for="(number, index) in productsPerPageArray"
                                :key="index"
                                v-on:click.prevent="updateProductsPerPage(number)"
                            >
                                <v-list-item-title>{{ number }}</v-list-item-title>
                            </v-list-item>
                        </v-list>
                    </v-menu>

                    <v-spacer></v-spacer>

                    <span>
                        Page {{ page }} of {{ numberOfPages }}
                    </span>
                    <v-btn
                        icon
                        class="mr-1"
                        v-on:click.prevent="formerPage"
                    >
                        <v-icon>chevron_left</v-icon>
                    </v-btn>
                    <v-btn
                        icon
                        class="ml-1"
                        v-on:click.prevent="nextPage"
                    >
                        <v-icon>chevron_right</v-icon>
                    </v-btn>
                </v-row>
            </template>
        </v-data-iterator>
    </v-container>
</template>

<script>
import {mapActions, mapGetters} from "vuex";

export default {
    data() {
        return {
            productsPerPageArray: [8, 12, 16, 24],
            search: '',
            filter: {},
            sortDesc: false,
            page: 1,
            productsPerPage: 8,
            sortBy: 'name',
            keys: [
                'Name',
                'Price',
                'Type'
            ],
            productTypes: [
                // TODO
            ],
            products: [],
            loading: false,
            orderAmount: []
        }
    },
    computed: {
        ...mapGetters(["isLoggedIn"]),
        numberOfPages() {
            return Math.ceil(this.products.length / this.productsPerPage)
        }
    },
    methods: {
        ...mapActions(["addItemToCart"]),
        nextPage() {
            if (this.page + 1 <= this.numberOfPages) this.page += 1
        },
        formerPage() {
            if (this.page - 1 >= 1) this.page -= 1
        },
        updateProductsPerPage(number) {
            this.productsPerPage = number
        },
        customSearchFilter(items, search) {
            if (search === "" || search == null || typeof search === typeof undefined)
                return items;

            let matchingProducts = [];

            for (let itemsKey in items) {
                let item = items[itemsKey];
                if (item.type.toString().toUpperCase()) {}
            }

            return matchingProducts;
        },
        getProducts() {
            this.loading = true;
            this.products = [];

            axios.get("api/products").then(response => {
                this.products = response.data;
                this.loading = false;
            }).catch(e => {
                console.log(e.response);
                this.loading = false;
                this.$emit("show-notification", "error", "Unable to load products");
            })
        },
        addToCart(itemId) {
            let itemAmount = this.orderAmount[itemId] || 1;
            // TODO
            this.$emit("show-notification", "orange", "Item: " + itemId + ", has quantity: " + itemAmount);
        }
    },
    mounted() {
        this.getProducts();
    }
}
</script>
