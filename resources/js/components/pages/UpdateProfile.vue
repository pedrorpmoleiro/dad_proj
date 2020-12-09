<template>
    <v-container>
        <v-row>
            <v-col>
                <div class="mb-4">
                    <v-img
                        max-width="200"
                        max-height="200"
                        class="avatar"
                        :src="'../storage/fotos/' + getUser.photo_url"
                    ></v-img>
                </div>

                <v-card flat>
                    <v-card-title>
                        Update Photo
                    </v-card-title>

                    <v-card-text>
                        <v-form v-model="updateUserPhoto.isFormValid">
                            <v-container>
                                <v-row>
                                    <v-col>
                                        <v-file-input
                                            chips
                                            show-size
                                            clearable
                                            accept="image/jpeg, image/png"
                                            prepend-icon="add_a_photo"
                                            label="Profile Picture"
                                            v-model="
                                                updateUserPhoto.input.photo
                                            "
                                        ></v-file-input>
                                        <!-- ! TODO -->
                                    </v-col>
                                </v-row>
                            </v-container>
                        </v-form>
                    </v-card-text>

                    <v-divider></v-divider>

                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            text
                            color="green"
                            v-on:click.prevent="submit"
                            :disabled="!updateUserPhoto.isFormValid"
                        >
                            Update
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
            <v-col>
                <v-card flat>
                    <v-card-title>
                        Update Password
                    </v-card-title>

                    <v-card-text>
                        <v-form v-model="updatePassword.isFormValid">
                            <v-container>
                                <v-row>
                                    <v-col>
                                        <v-text-field
                                            v-model="
                                                updatePassword.input.passwordOld
                                            "
                                            :append-icon="
                                                'visibility' +
                                                    (updatePassword.showPassOld
                                                        ? ''
                                                        : '_off')
                                            "
                                            :rules="[
                                                rules.required,
                                                rules.min,
                                                rules.max
                                            ]"
                                            :type="
                                                updatePassword.showPassOld
                                                    ? 'text'
                                                    : 'password'
                                            "
                                            hint="At least 3 characters"
                                            label="Old Password*"
                                            counter
                                            clearable
                                            required
                                            v-on:click:append="
                                                updatePassword.showPassOld = !updatePassword.showPassOld
                                            "
                                        ></v-text-field>
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col>
                                        <v-text-field
                                            v-model="
                                                updatePassword.input.password
                                            "
                                            :append-icon="
                                                'visibility' +
                                                    (updatePassword.showPass
                                                        ? ''
                                                        : '_off')
                                            "
                                            :rules="[
                                                rules.required,
                                                rules.min,
                                                rules.max
                                            ]"
                                            :type="
                                                updatePassword.showPass
                                                    ? 'text'
                                                    : 'password'
                                            "
                                            hint="At least 3 characters"
                                            label="Password*"
                                            counter
                                            clearable
                                            required
                                            v-on:click:append="
                                                updatePassword.showPass = !updatePassword.showPass
                                            "
                                        ></v-text-field>
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col>
                                        <v-text-field
                                            v-model="
                                                updatePassword.input
                                                    .passwordConfirm
                                            "
                                            :append-icon="
                                                'visibility' +
                                                    (updatePassword.showPassConfirm
                                                        ? ''
                                                        : '_off')
                                            "
                                            :rules="[
                                                rules.required,
                                                rules.min,
                                                rules.max
                                            ]"
                                            :type="
                                                updatePassword.showPassConfirm
                                                    ? 'text'
                                                    : 'password'
                                            "
                                            hint="At least 3 characters"
                                            label="Password Confirmation*"
                                            counter
                                            clearable
                                            required
                                            v-on:click:append="
                                                updatePassword.showPassConfirm = !updatePassword.showPassConfirm
                                            "
                                        ></v-text-field>
                                    </v-col>
                                </v-row>
                            </v-container>
                        </v-form>
                    </v-card-text>

                    <v-divider></v-divider>

                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            text
                            color="green"
                            v-on:click.prevent="submit"
                            :disabled="!updatePassword.isFormValid"
                        >
                            Update
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
        <v-row>
            <v-col>
                <v-card flat>
                    <v-card-title>
                        Update User Data
                    </v-card-title>
                    <v-card-text>
                        <v-form v-model="updateUserData.isFormValid">
                            <v-container>
                                <v-row>
                                    <v-col>
                                        <v-text-field
                                            v-model="updateUserData.input.name"
                                            :rules="[
                                                rules.required,
                                                rules.name
                                            ]"
                                            label="Full Name*"
                                            clearable
                                            required
                                        ></v-text-field>
                                    </v-col>
                                    <v-col>
                                        <v-text-field
                                            v-model="updateUserData.input.email"
                                            :rules="[
                                                rules.required,
                                                rules.email,
                                                rules.max
                                            ]"
                                            label="Email*"
                                            clearable
                                            required
                                        ></v-text-field>
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col>
                                        <v-text-field
                                            v-model="
                                                updateUserData.input.address
                                            "
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
                                            v-model="updateUserData.input.phone"
                                            :rules="[
                                                rules.required,
                                                rules.phone
                                            ]"
                                            label="Phone Number*"
                                            clearable
                                            required
                                        ></v-text-field>
                                    </v-col>
                                    <v-col>
                                        <v-text-field
                                            v-model="updateUserData.input.nif"
                                            :rules="[rules.nif]"
                                            label="NIF"
                                            clearable
                                        ></v-text-field>
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
                            :disabled="!updateUserData.isFormValid"
                        >
                            Update
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import { mapGetters, mapActions } from "vuex";

export default {
    data: () => ({
        updatePassword: {
            showPassOld: false,
            showPass: false,
            showPassConfirm: false,
            loading: false,
            isFormValid: false,
            input: {
                passwordOld: "",
                password: "",
                passwordConfirm: ""
            }
        },
        updateUserData: {
            loading: false,
            isFormValid: false,
            input: {
                name: "",
                email: "",
                address: "",
                phone: "",
                nif: ""
            }
        },
        updateUserPhoto: {
            loading: false,
            isFormValid: false,
            input: {
                photo: ""
            }
        },
        rules: {
            required: value => !!value || "Required",
            min: value => value.length >= 3 || "Min of 3 Characters",
            max: value => value.length < 255 || "Max of 255 Characters",
            email: value => {
                const pattern = /[^@ \t\r\n]+@[^@ \t\r\n]+\.[^@ \t\r\n]+/;
                return pattern.test(value) || "Invalid E-mail format!!";
            },
            name: value => {
                const pattern = /^[a-zA-Z\s]*$/;
                return pattern.test(value) || "Only letters and spaces allowed";
            },
            nif: value => {
                const pattern = /^\d{0,8}[1-9]$/;
                return (
                    pattern.test(value) ||
                    "NIF: Positive number, smaller then 999999999"
                );
            },
            phone: value => {
                const pattern = /^([\+]|[0]{2})?[1-9]\d{0,3}?[\s]?[1-9]\d{1,7}$/;
                return pattern.test(value) || "Phone number format is invalid";
            }
        }
    }),
    methods: {
        ...mapActions(["setUser"]),
        submit() {
            let user = {
                email: this.input.email,
                password: this.input.password
            };

            this.loading = true;

            axios.get("sanctum/csrf-cookie").then(() => {
                axios
                    .post("api/login", user)
                    .then(response => {
                        this.loading = false;
                        // console.log(response);
                        this.setUser(response.data);
                        axios.defaults.withCredentials = true;
                        this.dialog = false;
                        this.$emit(
                            "show-notification",
                            "success",
                            "Login Successful"
                        );
                    })
                    .catch(e => {
                        this.loading = false;
                        // console.log(e);
                        this.$emit(
                            "show-notification",
                            "error",
                            e.response.data.msg
                        );
                    });
            });
        },
        submitPassword() {
            let data = {
                oldPassword: this.updatePassword.input.oldPassword,
                password: this.updatePassword.input.password
            };

            this.updatePassword.loading = true;

            axios
                .post("api/users/update/password", data)
                .then(response => {
                    this.updatePassword.loading = false;
                    // console.log(response);
                    this.$emit(
                        "show-notification",
                        "success",
                        "Password updated successfully!"
                    );
                })
                .catch(e => {
                    // console.log(e);
                    this.updatePassword.loading = false;
                    this.$emit(
                        "show-notification",
                        "error",
                        "Failed to update password!"
                    );
                });
        },
        submitUserData() {
            let data = {
                name: this.updateUserData.input.name,
                email: this.updateUserData.input.email
            };

            this.updateUserData.loading = true;

            let route;

            if (this.getUser.type === "C") {
                data.address = this.updateUserData.input.address;
                data.phone = this.updateUserData.input.phone;
                data.nif = this.updateUserData.input.nif;

                route = "api/customers/update";
            } else route = "api/users/update";

            axios
                .post(route, data)
                .then(response => {
                    this.updateUserData.loading = false;
                    // console.log(response);
                    
                    this.setUser(response.data);

                    this.$emit(
                        "show-notification",
                        "success",
                        "Updated profile successfully!"
                    );
                })
                .catch(e => {
                    this.updateUserData.loading = false;
                    this.$emit(
                        "show-notification",
                        "error",
                        "Failed to update profile!"
                    );
                });
        }
    },
    computed: {
        ...mapGetters(["isLoggedIn", "getUser"])
    },
    mounted() {
        // if (!this.isLoggedIn)
        // this.$router.push("/home");
    }
};
</script>

<style>
.avatar {
    border-radius: 50%;
}
</style>
