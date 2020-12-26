<template>
    <v-container>
        <v-card>
            <v-card-title>
                <v-text-field
                    v-model="search"
                    label="Search Types"
                    append-icon="search"
                    single-line
                    class="ml-3 mb-2"
                    hide-details
                ></v-text-field>
                <!--<v-spacer></v-spacer>-->
                <v-btn
                    icon
                    :loading="loading"
                    v-on:click.prevent="getProducts"
                    class="ml-3"
                >
                    <v-icon>cached</v-icon>
                </v-btn>
            </v-card-title>
            <v-data-table
                :headers="productHeaders"
                :items="products"
                :items-per-page="15"
                :loading="loading"
                loading-text="Loading available products"
                :search="search"
                light
            >
                <template v-slot:item.photo_url="{ item }">
                    <v-edit-dialog>
                        <v-img
                            class="my-2"
                            max-width="75"
                            max-height="75"
                            :src="'../storage/products/' + item.photo_url"
                        ></v-img>
                        <template v-slot:input>
                            <v-img
                                class="my-3"
                                :max-width="maxHeight"
                                :max-height="maxHeight"
                                :src="'../storage/products/' + item.photo_url"
                            ></v-img>
                        </template>
                    </v-edit-dialog>
                </template>
                <template v-slot:item.type="{ item }">
                    {{ item.type.charAt(0).toUpperCase() + item.type.slice(1) }}
                </template>
                <template v-slot:item.price="{ item }">
                    {{ "€ " + item.price }}
                </template>
            </v-data-table>
        </v-card>
    </v-container>
</template>

<script>
export default {
    data: () => ({
        productHeaders: [
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
        products: [],
        loading: true,
        search: "",
        maxHeight: window.innerHeight - 150
    }),
    methods: {
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
        }
    },
    mounted() {
        this.getProducts();
    }
};
</script>
