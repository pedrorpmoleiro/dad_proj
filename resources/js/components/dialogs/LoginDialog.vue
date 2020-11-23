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
                            />
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
                                @click:append="showPass = !showPass"
                            />
                        </v-row>
                    </v-container>
                </v-form>
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
export default {
    props: ["authToken"],
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
        submit() {
            let user = {
                email: this.input.email,
                password: this.input.password
            };

            this.loading = true;
            axios.post("api/login", user).then(response => {
                this.loading = false;
                let token = response.data.access_token
                localStorage.setItem("access-token", token)
                this.$emit("set-token", token)
                this.$emit("logged-in");
                this.dialog = false;
            }).catch(e => {
                this.loading = false;
                this.$emit("invalid-login", e.response.data.msg)
            })
        }
    },
    mounted() {}
};
</script>
