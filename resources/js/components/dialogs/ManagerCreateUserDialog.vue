<template>
    <v-dialog v-model="dialog" scrollable width="750">
        <template v-slot:activator="{ on, attrs }">
            <v-btn depressed color="green" dark v-bind="attrs" v-on="on">
                <v-icon>add</v-icon>
                Create Employee
            </v-btn>
        </template>

        <v-card :loading="loading">
            <v-card-title dark class="headline">
                Create User
            </v-card-title>

            <v-form v-model="isFormValid">
                <v-card-text>
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
                                    label="Name *"
                                    clearable
                                    required
                                ></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col>
                                <v-text-field
                                    v-model="input.email"
                                    :rules="[
                                        rules.required,
                                        rules.email,
                                        rules.max
                                    ]"
                                    label="Email *"
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
                                    required
                                    v-model="input.type"
                                    label="Type *"
                                    :rules="[rules.required]"
                                    placeholder="Choose user type"
                                    :items="userTypes"
                                ></v-autocomplete>
                            </v-col>
                        </v-row>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                    <v-btn color="green darken-1" text v-on:click.prevent="submit" :disabled="!isFormValid">Save</v-btn>
                    <v-spacer></v-spacer>
                    <v-btn color="red darken-1" text v-on:click.prevent="dialog = false">Cancel</v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    props: ["user"],
    data: () => ({
        isFormValid: false,
        loading: false,
        dialog: false,
        userTypes: ["Manager", "Cook", "Delivery Man"],
        input: {
            id: 0,
            name: '',
            email: '',
            password: '123',
            type: '',
            photo: ''
        },
        rules: {
            required: value => !!value || "Required",
            max: value => value.length < 255 || "Max of 255 Characters",
            email: value => {
                const pattern = /[^@ \t\r\n]+@[^@ \t\r\n]+\.[^@ \t\r\n]+/;
                return pattern.test(value) || "Invalid E-mail format!!";
            },
            name: value => {
                const pattern = /^[a-zA-ZÀ-ÖØ-öø-ÿ\s]*$/;
                return pattern.test(value) || "Only letters and spaces allowed";
            }
        }
    }),
    methods: {
        getTypeName(item) {
            switch (item) {
                case "Manager":
                    return "EM";
                case "Cook":
                    return "EC";
                case "Delivery Man":
                    return "ED";
            }
        },
        submit() {
            let data = {
                id: this.input.id,
                name: this.input.name,
                email: this.input.email,
                password: this.input.password,
                type: this.getTypeName(this.input.type),
                photo_url: this.input.photo
            };

            this.loading = true;

            axios
                .post("api/users/create", data)
                .then(response => {

                    this.$emit(
                        "show-notification",
                        "success",
                        "User created successfully!"
                    );

                    this.dialog = false;
                    this.$emit("get-users");
                })
                .catch(e => {
                    this.loading = false;
                    this.$emit(
                        "show-notification",
                        "error",
                        "Failed to create user!"
                    );
                });
        }
    },
    async mounted() {
        this.input.id = this.user.id;
        this.input.name = this.user.name;
        this.input.email = this.user.email;

        switch (this.user.type) {
            case "C":
                this.input.type = "Customer";
                break;
            case "EM":
                this.input.type = "Manager";
                break;
            case "EC":
                this.input.type = "Cook";
                break;
            case "ED":
                this.input.type = "Delivery Man";
                break;
        }
    }
}
</script>
