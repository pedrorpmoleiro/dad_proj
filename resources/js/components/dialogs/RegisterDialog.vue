<template>
    <v-dialog v-model="dialog" scrollable width="750">
        <template v-slot:activator="{ on, attrs }">
            <v-btn text v-bind="attrs" v-on="on">
                Register
            </v-btn>
        </template>

        <v-card :loading="loading">
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
                                    label="Full Name *"
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
                                <v-text-field
                                    v-model="input.password"
                                    :append-icon="
                                        'visibility' + (showPass ? '' : '_off')
                                    "
                                    :rules="[rules.required, rules.min]"
                                    :type="showPass ? 'text' : 'password'"
                                    hint="At least 3 characters"
                                    label="Password *"
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
                                    label="Password Confirmation *"
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
                                    :rules="[rules.required, rules.max]"
                                    label="Address *"
                                    clearable
                                    required
                                ></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col>
                                <v-text-field
                                    v-model="input.phone"
                                    :rules="[rules.required, rules.phone]"
                                    label="Phone Number *"
                                    clearable
                                    required
                                ></v-text-field>
                            </v-col>
                            <v-col>
                                <v-text-field
                                    v-model="input.nif"
                                    :rules="[rules.nif]"
                                    label="NIF"
                                    clearable
                                ></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col>
                                <v-file-input
                                    chips
                                    show-size
                                    v-model="input.photo"
                                    label="Profile Picture"
                                    accept="images/png, images/jpeg"
                                    prepend-icon=""
                                    prepend-inner-icon="face"
                                    clearable
                                ></v-file-input>
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
                    Register
                </v-btn>
                <v-btn text color="red" v-on:click.prevent="dialog = false">
                    Close
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import {mapGetters} from "vuex";

export default {
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
            max: value => value.length < 255 || "Max of 255 Characters",
            email: value => {
                const pattern = /[^@ \t\r\n]+@[^@ \t\r\n]+\.[^@ \t\r\n]+/;
                return pattern.test(value) || "Invalid E-mail format!!";
            },
            nif: value => {
                if (
                    typeof value === typeof undefined ||
                    value == null ||
                    value === ""
                )
                    return true;

                const pattern = /^\d{0,8}[0-9]$/;
                return (
                    pattern.test(value) ||
                    "NIF: Positive number, smaller then 999999999"
                );
            },
            name: value => {
                const pattern = /^[a-zA-ZÀ-ÖØ-öø-ÿ\s]*$/;
                return pattern.test(value) || "Only letters and spaces allowed";
            },
            phone: value => {
                const pattern = /^([\+]|[0]{2})?[1-9]\d{0,3}?[\s]?[1-9]\d{1,8}$/;
                return pattern.test(value) || "Phone number format is invalid";
            }
        }
    }),
    computed: {
        ...mapGetters(["isLoggedIn"])
    },
    methods: {
        submit() {
            if (this.input.password !== this.input.passwordConfirm) {
                this.$emit(
                    "show-notification",
                    "error",
                    "Password and password confirmation must match"
                );
                return;
            }

            this.loading = true;

            let user = new FormData();
            user.append("name", this.input.name)
            user.append("email", this.input.email)
            user.append("password", this.input.password)
            user.append("address", this.input.address)
            user.append("phone", this.input.phone)

            if (
                typeof this.input.nif !== typeof undefined &&
                this.input.nif != null &&
                this.input.nif !== ""
            )
                user.append("nif", Number(this.input.nif));

            if (
                typeof this.input.photo !== typeof undefined &&
                this.input.photo != null &&
                this.input.photo !== ""
            )
                user.append("photo", this.input.photo);

            axios
                .post("api/customers/register", user, {headers: {"Content-Type": "multipart/form-data"}})
                .then(response => {
                    this.loading = false;
                    // console.log(response);
                    this.$emit(
                        "show-notification",
                        "success",
                        "Register was successful, please Login"
                    );
                    this.dialog = false;
                })
                .catch(e => {
                    this.loading = false;
                    // console.log(e);
                    if (e.response.data.errors.email)
                        this.$emit(
                            "show-notification",
                            "error",
                            e.response.data.errors.email[0]
                        );
                    else
                        this.$emit(
                            "show-notification",
                            "error",
                            "Failed to create new user"
                        );
                });
        }
    }
};
</script>
