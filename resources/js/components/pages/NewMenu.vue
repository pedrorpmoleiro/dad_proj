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
                                <div class="subtitle-1">
                                    {{ item.type.charAt(0).toUpperCase() + item.type.slice(1) }}
                                </div>
                                <div>
                                    {{ item.description }}
                                </div>
                                <div class="font-weight-bold">
                                    {{ "€" + item.price }}
                                </div>
                            </v-card-text>

                            <v-card-actions>
                                <!-- TODO -->
                                <v-btn
                                    color="deep-purple lighten-2"
                                    text
                                    @click="reserve"
                                >
                                    Reserve
                                </v-btn>
                            </v-card-actions>
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
            products: [],
            loading: false
        }
    },
    computed: {
        numberOfPages() {
            return Math.ceil(this.products.length / this.productsPerPage)
        },
        filteredKeys() {
            return this.keys.filter(key => key !== 'Name')
        },
    },
    methods: {
        nextPage() {
            if (this.page + 1 <= this.numberOfPages) this.page += 1
        },
        formerPage() {
            if (this.page - 1 >= 1) this.page -= 1
        },
        updateProductsPerPage(number) {
            this.productsPerPage = number
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
        }
    },
    mounted() {
        this.getProducts();
    }
}
</script>
