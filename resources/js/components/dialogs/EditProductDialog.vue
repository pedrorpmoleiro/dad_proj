<template>
    <v-dialog v-model="dialog" scrollable width="750">
        <template v-slot:activator="{ on, attrs }">
            <v-btn text v-bind="attrs" v-on="on">
                <v-icon>create</v-icon>
            </v-btn>
        </template>
        <v-card :loading="loading">
            <v-card-title dark class="headline">
                Update Product
            </v-card-title>

            <v-card-text>
                <v-form v-model="isFormValid">
                    <v-container>
                        <v-row>
                            <v-col>
                                <v-text-field
                                    v-model="input.name"
                                    :rules="[
                                        rules.required,
                                        rules.max
                                    ]"
                                    label="Product Name *"
                                    clearable
                                    required
                                ></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col>
                                <v-autocomplete
                                    clearable
                                    flat
                                    v-model="input.type"
                                    label="Product Type"
                                    :items="productTypes"
                                    :rules="[rules.required]"
                                ></v-autocomplete>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col>
                                <v-text-field
                                    v-model="input.description"
                                    :rules="[
                                        rules.required,
                                        rules.min,
                                        rules.max
                                    ]"
                                    label="Description *"
                                    clearable
                                    required
                                ></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col>
                                <v-file-input
                                    v-model="input.photo_url"
                                    accept="image/jpeg, image/png"
                                    label="Product Photo"
                                ></v-file-input>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col>
                                <v-text-field
                                    v-model.number="input.price"
                                    :rules="[rules.required, rules.price]"
                                    label="Price *"
                                    prefix="€ "
                                    clearable
                                    required
                                ></v-text-field>
                            </v-col>
                        </v-row>
                    </v-container>
                </v-form>
                <small class="red--text">* indicates required field</small>
            </v-card-text>

            <v-divider></v-divider>

            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                    text
                    color="green"
                    v-on:click.prevent="updateProduct"
                    :disabled="!isFormValid"
                >
                    Update
                </v-btn>
                <v-btn text color="red" v-on:click.prevent="dialog = false">
                    Close
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    props: ["product"],
    data: () => ({
        productTypes: ["Drink", "Dessert", "Cold dish", "Hot dish"],
        dialog: false,
        isFormValid: false,
        loading: false,
        input: {
            name: "",
            type: "",
            description: "",
            photo_url: null,
            price: ""
        },
        rules: {
            required: value => !!value || "Required",
            min: value => value.length >= 3 || "Min of 3 Characters",
            max: value => value.length < 255 || "Max of 255 Characters",
            price: value => {
                const pattern = /^(\d{0,3})|(\.\d{1,2})$/;
                return pattern.test(value) || "Only numbers are allowed";
            }
        }
    }),
    methods: {
        updateProduct() {
            this.loading = true;

            let product = {
                id: this.product.id,
                name: this.input.name,
                type: this.input.type.toLowerCase(),
                description: this.input.description,
                price: this.input.price
            };

            if (this.input.photo_url)
                product.photo_url = this.input.photo_url;
            else
                product.photo_url = this.product.photo_url;

            axios
                .put("/api/products/update", product)
                .then(response => {
                    // console.log(response);
                    this.loading = false;
                    this.$emit(
                        "show-notification",
                        "success",
                        "Product updated successfully!"
                    );
                    this.$emit("update-products");
                })
                .catch(e => {
                    // console.log(e);
                    this.loading = false;
                    this.$emit(
                        "show-notification",
                        "error",
                        "Failed to update product"
                    );
                });
        }
    },
    mounted() {
        this.input.name = this.product.name;
        this.input.type = this.product.type.charAt(0).toUpperCase() + this.product.type.slice(1);
        this.input.description = this.product.description;
        this.input.price = this.product.price;
    }
};
</script>
