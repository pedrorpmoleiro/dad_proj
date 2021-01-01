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
                                :rules="[
                                    rules.required,
                                    rules.email,
                                    rules.max
                                ]"
                                label="Email *"
                                clearable
                                required
                            ></v-text-field>
                        </v-row>
                        <v-row>
                            <v-text-field
                                v-model="input.password"
                                :append-icon="
                                    'visibility' + (showPass ? '' : '_off')
                                "
                                :rules="[rules.required, rules.min, rules.max]"
                                :type="showPass ? 'text' : 'password'"
                                hint="At least 3 characters"
                                label="Password *"
                                counter
                                clearable
                                required
                                v-on:click:append="showPass = !showPass"
                            ></v-text-field>
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
            max: value => value.length < 255 || "Max of 255 Characters",
            email: value => {
                const pattern = /[^@ \t\r\n]+@[^@ \t\r\n]+\.[^@ \t\r\n]+/;
                return pattern.test(value) || "Invalid E-mail format!!";
            }
        }
    }),
    methods: {
        ...mapActions(["setUser", "setAuthLoading"]),
        submit() {
            this.loading = true;
            this.setAuthLoading(true);

            let user = {
                email: this.input.email,
                password: this.input.password
            };

            axios.get("sanctum/csrf-cookie").then(() => {
                axios
                    .post("api/auth/login", user)
                    .then(response => {
                        // console.log(response);
                        const user = response.data;

                        this.loading = false;
                        this.dialog = false;
                        this.setAuthLoading(false);

                        if (user.blocked === 1) {
                            this.$emit("user-blocked")
                            return;
                        }

                        this.setUser(user);
                        axios.defaults.withCredentials = true;

                        this.$emit(
                            "show-notification",
                            "success",
                            "Login Successful"
                        );

                        this.$socket.emit("user_logged_in", {type: user.type, id: user.id});
                    })
                    .catch(e => {
                        // console.log(e);
                        this.loading = false;
                        this.setAuthLoading(false);
                        this.$emit(
                            "show-notification",
                            "error",
                            e.response.data.msg
                        );
                    });
            });
        }
    }
};
</script>
