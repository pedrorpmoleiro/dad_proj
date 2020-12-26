9
<template>
    <v-container>
        <v-data-iterator
            :items="products"
            :search="search"
            :sort-by="sortBy.toLowerCase()"
            :sort-desc="sortDesc"
            :custom-filter="customSearchFilter"
            :loading="loading"
            loading-text="Loading Menu"
        >
            <template v-slot:header>
                <v-card flat>
                    <v-container>
                        <v-col>
                            <v-row>
                                <v-autocomplete
                                    chips
                                    clearable
                                    flat
                                    v-model="search"
                                    prepend-inner-icon="search"
                                    label="Search Types"
                                    solo-inverted
                                    :items="productTypes"
                                ></v-autocomplete>

                                <v-spacer></v-spacer>

                                <v-autocomplete
                                    v-model="sortBy"
                                    flat
                                    solo-inverted
                                    chips
                                    clearable
                                    :items="keys"
                                    prepend-inner-icon="search"
                                    label="Sort by"
                                ></v-autocomplete>

                                <v-spacer></v-spacer>

                                <v-btn
                                    large
                                    depressed
                                    v-on:click.prevent="sortDesc = !sortDesc"
                                >
                                    <v-icon>{{
                                            sortDesc
                                                ? "arrow_downward"
                                                : "arrow_upward"
                                        }}
                                    </v-icon>
                                </v-btn>

                                <v-spacer></v-spacer>
                                
                                <create-product-dialog v-if="isUserManager"
                                                       v-on:show-notification="openNotification"
                                ></create-product-dialog>

                                <v-spacer v-if="isUserManager"></v-spacer>

                                <v-btn
                                    large
                                    depressed
                                    :loading="loading"
                                    v-on:click.prevent="getProducts"
                                >
                                    <v-icon>cached</v-icon>
                                </v-btn>
                            </v-row>
                        </v-col>
                    </v-container>
                </v-card>
            </template>

            <template v-slot:default="props">
                <v-row>
                    <v-col
                        v-for="item in props.items"
                        :key="item.id"
                        cols="12"
                        sm="6"
                        md="4"
                        lg="3"
                    >
                        <v-card flat>
                            <v-img
                                max-height="300"
                                :src="'../storage/products/' + item.photo_url"
                            ></v-img>

                            <v-card-title class="subheading font-weight-bold">
                                {{ item.name }}
                            </v-card-title>

                            <v-card-text>
                                <p class="subtitle-1 font-italic">
                                    {{
                                        item.type.charAt(0).toUpperCase() +
                                        item.type.slice(1)
                                    }}
                                </p>
                                <p class="text-justify">
                                    {{ item.description }}
                                </p>
                                <p class="font-weight-bold text--primary">
                                    {{ "€" + item.price }}
                                </p>
                            </v-card-text>
                            <div v-if="isLoggedIn">
                                <v-divider></v-divider>
                                <v-card-actions>
                                    <div v-if="isUserCustomer">
                                        <v-text-field
                                            rounded
                                            label="Quantity"
                                            placeholder="1"
                                            :rules="[rules.amount]"
                                            v-model.number="
                                                orderAmount[item.id]
                                            "
                                        ></v-text-field>
                                        <v-spacer></v-spacer>
                                        <v-btn
                                            text
                                            color="primary lighten-1"
                                            v-on:click.prevent="addToCart(item)"
                                        >
                                            <v-icon>add_shopping_cart</v-icon>
                                        </v-btn>
                                    </div>
                                    <div v-if="isUserManager">
                                        <v-spacer></v-spacer>

                                        <edit-product-dialog v-on:show-notification="openNotification"></edit-product-dialog>

                                        <v-btn
                                            text
                                            color="red lighten-1"
                                            v-on:click.prevent="deleteProduct(item)"
                                        >
                                            <v-icon>delete</v-icon>
                                        </v-btn>
                                    </div>
                                </v-card-actions>
                            </div>
                        </v-card>
                    </v-col>
                </v-row>
            </template>
        </v-data-iterator>
    </v-container>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import CreateProductDialog from "../dialogs/CreateProductDialog";
import EditProductDialog from "../dialogs/EditProductDialog";

export default {
    components: {
        "create-product-dialog": CreateProductDialog,
        "edit-product-dialog": EditProductDialog
    },
    data() {
        return {
            search: "",
            sortDesc: false,
            sortBy: "name",
            keys: ["Name", "Price", "Type"],
            productTypes: ["Drink", "Dessert", "Cold dish", "Hot dish"],
            rules: {
                amount: value => {
                    const pattern = /^[1-9][0-9]?$/;
                    return (
                        pattern.test(value) || "Value must be between 1 and 99"
                    );
                }
            },
            products: [],
            loading: false,
            orderAmount: []
        };
    },
    computed: {
        ...mapGetters([
            "isLoggedIn",
            "getShoppingCartItems",
            "getUser",
            "isUserCustomer",
            "isUserManager"
        ])
    },
    methods: {
        ...mapActions(["addUpdateItemToCart", "setProduct"]),
        customSearchFilter(items, search) {
            if (
                search === "" ||
                search == null ||
                typeof search === typeof undefined
            )
                return items;

            let matchingProducts = [];

            for (let k in items) {
                let item = items[k];
                if (
                    item.type.toString().toUpperCase() ===
                    search.toString().toUpperCase()
                )
                    matchingProducts.push(item);
            }

            return matchingProducts;
        },
        setEditProduct(product) {
            axios.get("api/products/product", product).then(response => {
                this.setProduct(response.data);

                this.$emit(
                    "show-notification",
                    "success",
                    "Got Product successfully!"
                );

            }).catch(e => {
                this.$emit("show-notification", "error", "Failed to get products");
            });
        },
        getProducts() {
            this.loading = true;
            this.products = [];

            axios
                .get("api/products")
                .then(response => {
                    this.products = response.data;
                    this.loading = false;
                })
                .catch(e => {
                    console.log(e.response);
                    this.loading = false;
                    this.$emit(
                        "show-notification",
                        "error",
                        "Unable to load products"
                    );
                });
        },
        addToCart(product) {
            let amount = this.orderAmount[product.id] || 1;

            if (this.getShoppingCartItems.length > 0)
                for (let i in this.getShoppingCartItems)
                    if (this.getShoppingCartItems[i].product.id === product.id)
                        amount += this.getShoppingCartItems[i].amount;

            this.addUpdateItemToCart({product, amount});
            this.$emit(
                "show-notification",
                "success",
                "Item was added to cart"
            );
        },
        deleteProduct(item) {
            console.log(item);
            axios
                .delete("api/products/delete", item)
                .then(response => {
                    this.loading = false;

                    this.$emit(
                        "show-notification",
                        "success",
                        "Product deleted successfully"
                    );
                }).catch(e => {
                this.loading = false;
                this.$emit(
                    "show-notification",
                    "error",
                    "Failed to delete product"
                );
            });
        },
        openNotification(color, message, timeout = 6000) {
            this.$emit("show-notification", color, message, timeout);
        }
    },
    mounted() {
        this.getProducts();
    }
};
</script>
