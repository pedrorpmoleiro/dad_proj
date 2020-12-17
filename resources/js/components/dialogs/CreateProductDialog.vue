<template>
    <v-dialog v-model="dialog" scrollable width="750">
        <template v-slot:activator="{ on, attrs }">
            <v-btn depressed color="green" dark v-bind="attrs" v-on="on">
                <v-icon>add</v-icon>
                Create
            </v-btn>
        </template>

        <v-card :loading="loading">
            <v-card-title dark class="headline red lighten-1">
                Create Product
                <!-- ! TODO -->
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
                                        rules.name,
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
                                <v-text-field
                                    v-model.number="input.price"
                                    :rules="[rules.required, rules.price]"
                                    label="Price *"
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
                    v-on:click.prevent="submit"
                    :disabled="!isFormValid"
                >
                    Create
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
    data: () => ({
        productTypes: [],
        dialog: false,
        isFormValid: false,
        loading: false,
        input: {
            name: "",
            type: "",
            description: "",
            price: ""
        },
        rules: {
            required: value => !!value || "Required",
            min: value => value.length >= 3 || "Min of 3 Characters",
            max: value => value.length < 255 || "Max of 255 Characters",
            name: value => {
                const pattern = /^[a-zA-Z\s]*$/;
                return pattern.test(value) || "Only letters and spaces allowed";
            },
            price: value => {
                const pattern = /^\s*-?[1-9]\d{0,3}(\.\d{1,2})?\s*$/;
                return pattern.test(value) || "Only numbers are allowed";
            }
        }
    }),
    methods: {
        submit() {
            let product = {
                name: this.input.name,
                type: this.input.type,
                description: this.input.description,
                price: this.input.price
            };
        }
    }
};
</script>
