<template>
    <v-dialog v-model="dialog" scrollable width="750">
        <template v-slot:activator="{ on, attrs }">
            <v-icon small v-bind="attrs" v-on="on">create</v-icon>
        </template>
        <v-card :loading="loading">
            <v-card-title dark class="headline">
                Edit User Information
            </v-card-title>

            <v-card-text>
                <v-form v-model="isFormValid">
                    <v-container>
                        <v-row>
                            <v-col
                                cols="12"
                                sm="6"
                                md="4"
                            >
                                <v-text-field
                                    v-model="editedItem.name"
                                    label="Name"
                                ></v-text-field>
                            </v-col>
                            <v-col
                                cols="12"
                                sm="6"
                                md="4"
                            >
                                <v-text-field
                                    v-model="editedItem.email"
                                    label="Email"
                                ></v-text-field>
                            </v-col>
                            <v-col
                                cols="12"
                                sm="6"
                                md="4"
                            >
                                <v-autocomplete
                                    clearable
                                    flat
                                    v-model="editedItem.type"
                                    label="Type"
                                    :items="userTypes"
                                ></v-autocomplete>
                            </v-col>
                            <v-spacer></v-spacer>
                            <v-btn color="red darken-1" text @click="closeDialog">Cancel</v-btn>
                            <v-btn color="green darken-1" text @click="saveEdit">OK</v-btn>
                            <v-spacer></v-spacer>
                        </v-row>
                    </v-container>
                </v-form>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    props: ["user"],
    name: "ManagerEditUserDialog",
    data: () => ({
        isFormValid: false,
        loading: false,
        dialog: false,
        userTypes: ["Client", "Manager", "Cook", "Delivery Man"],
        editedItem: {
            id: 0,
            name: '',
            email: '',
            type: '',
        },
    }),
    methods: {
        getTypeName(item) {
            switch (item) {
                case "Client":
                    return "C";
                    break;
                case "Manager":
                    return "EM";
                    break;
                case "Cook":
                    return "EC";
                    break;
                case "Delivery Man":
                    return "ED";
                    break;
            }
        },
        closeDialog() {
            this.dialog = false;
        },
        saveEdit() {
            let data = {
                name: this.editedItem.name,
                email: this.editedItem.email,
                type: this.editedItem.type
            };

            this.loading = true;

            axios
                .post("api/users/update", data)
                .then(response => {

                    this.$emit(
                        "show-notification",
                        "success",
                        "Updated user successfully!"
                    );
                })
                .catch(e => {
                    this.loading = false;
                    this.$emit(
                        "show-notification",
                        "error",
                        "Failed to update user!"
                    );
                });
        }
    },

    async mounted() {
        this.editedItem.name = this.user.name;
        this.editedItem.email = this.user.email;

        switch (this.user.type) {
            case "C":
                this.editedItem.type = "Client";
                break;
            case "EM":
                this.editedItem.type = "Manager";
                break;
            case "EC":
                this.editedItem.type = "Cook";
                break;
            case "ED":
                this.editedItem.type = "Delivery Man";
                break;
        }
    }
}
</script>

<style scoped>

</style>
