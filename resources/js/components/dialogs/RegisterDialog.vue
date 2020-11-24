<template>
    <v-dialog v-model="dialog" scrollable width="750">
        <template v-slot:activator="{ on, attrs }">
            <v-btn text v-bind="attrs" v-on="on">
                Register
            </v-btn>
        </template>

        <v-card :loading="loading">
            <v-card-title dark class="headline red lighten-1">
                WIP
            </v-card-title>

            <v-card-text>
                <v-form v-model="isFormValid">
                    <v-container>
                        <v-row>
                            <v-col>
                                <v-text-field
                                    v-model="input.name"
                                    :rules="[rules.required, rules.name]"
                                    label="Full Name*"
                                    clearable
                                    required
                                ></v-text-field>
                            </v-col>
                            <v-col>
                                <v-text-field
                                    v-model="input.phone"
                                    :rules="[rules.required, rules.phone]"
                                    label="Phone Number*"
                                    clearable
                                    required
                                ></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col>
                                <v-text-field
                                    v-model="input.email"
                                    :rules="[rules.required, rules.email]"
                                    label="Email*"
                                    clearable
                                    required
                                ></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col>
                                <v-text-field
                                    v-model="input.password"
                                    :append-icon="
                                        'visibility' + (showPass ? '' : '_off')
                                    "
                                    :rules="[rules.required, rules.min]"
                                    :type="showPass ? 'text' : 'password'"
                                    hint="At least 3 characters"
                                    label="Password*"
                                    counter
                                    clearable
                                    required
                                    v-on:click:append="showPass = !showPass"
                                ></v-text-field>
                            </v-col>
                            <v-col>
                                <v-text-field
                                    v-model="input.passwordConfirm"
                                    :append-icon="
                                        'visibility' +
                                            (showPassConfirm ? '' : '_off')
                                    "
                                    :rules="[rules.required, rules.min]"
                                    :type="
                                        showPassConfirm ? 'text' : 'password'
                                    "
                                    hint="At least 3 characters"
                                    label="Password Confirmation*"
                                    counter
                                    clearable
                                    required
                                    v-on:click:append="
                                        showPassConfirm = !showPassConfirm
                                    "
                                ></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col>
                                <v-text-field
                                    v-model="input.address"
                                    :rules="[rules.required]"
                                    label="Address*"
                                    clearable
                                    required
                                ></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col>
                                <v-text-field
                                    v-model="input.nif"
                                    :rules="[rules.nif]"
                                    label="NIF"
                                    clearable
                                ></v-text-field>
                            </v-col>
                            <v-col>
                                <v-file-input
                                    chips
                                    show-size
                                    clearable
                                    accept="image/jpeg, image/png"
                                    prepend-icon="add_a_photo"
                                    label="Profile Picture"
                                    v-model="input.photo"
                                ></v-file-input>
                            </v-col>
                        </v-row>
                    </v-container>
                </v-form>
                <small>* indicates required field</small>
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
                    Login
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
    props: ["authToken"],
    data: () => ({
        dialog: false,
        isFormValid: false,
        loading: false,
        showPass: false,
        showPassConfirm: false,
        input: {
            name: "",
            email: "",
            password: "",
            passwordConfirm: "",
            address: "",
            phone: "",
            nif: "",
            photo: null
        },
        rules: {
            required: value => !!value || "Required",
            min: value => value.length >= 3 || "Min of 3 Characters",
            email: value => {
                const pattern = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                return pattern.test(value) || "Invalid E-mail format!!";
            },
            nif: value =>
                (value > 1 && value < 999999999) ||
                "NIF: Positive number, smaller then 999999999",
            name: value => {
                // TODO
                return true;
            },
            phone: value => {
                // TODO
                return true;
            }
        }
    }),
    methods: {
        submit() {
            // TODO
        }
    }
};
</script>
