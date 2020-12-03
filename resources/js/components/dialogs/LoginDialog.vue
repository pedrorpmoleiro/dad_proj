<template>
    <v-dialog v-model="dialog" width="500">
        <template v-slot:activator="{ on, attrs }">
            <v-btn text v-bind="attrs" v-on="on">
                Login
            </v-btn>
        </template>
        <v-card :loading="loading">
            <v-card-text>
                <v-form v-model="isFormValid">
                    <v-container>
                        <v-row>
                            <v-text-field
                                v-model="input.email"
                                :rules="[rules.required, rules.email]"
                                label="Email*"
                                clearable
                                required
                            ></v-text-field>
                        </v-row>
                        <v-row>
                            <v-text-field
                                v-model="input.password"
                                :append-icon="'visibility' + (showPass ? '' : '_off')"
                                :rules="[rules.required, rules.min]"
                                :type="showPass ? 'text' : 'password'"
                                hint="At least 3 characters"
                                label="Password*"
                                counter
                                clearable
                                required
                                v-on:click:append="showPass = !showPass"
                            ></v-text-field>
                        </v-row>
                    </v-container>
                </v-form>
                <small>* indicates required field</small>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn text color="green" v-on:click.prevent="submit" :disabled="!isFormValid">
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
import {mapActions} from "vuex";

export default {
    data: () => ({
        dialog: false,
        loading: false,
        showPass: false,
        isFormValid: false,
        input: {
            email: "",
            password: ""
        },
        rules: {
            required: value => !!value || "Required",
            min: value => value.length >= 3 || "Min of 3 Characters",
            email: value => {
                const pattern = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                return (
                    pattern.test(value) || "Invalid E-mail format!!"
                );
            }
        },
    }),
    methods: {
        ...mapActions([
            "setUser"
        ]),
        submit() {
            let user = {
                email: this.input.email,
                password: this.input.password
            };

            this.loading = true;

            axios.get("sanctum/csrf-cookie").then(() => {
                axios.post("api/login", user).then(response => {
                    this.loading = false;
                    // console.log(e);
                    this.setUser(response.data);
                    axios.defaults.withCredentials = true;
                    this.dialog = false;
                }).catch(e => {
                    this.loading = false;
                    // console.log(e);
                    this.$emit("show-notification", "error", e.response.data.msg)
                });
            });
        }
    }
};
</script>
