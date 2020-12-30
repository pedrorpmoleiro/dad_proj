<template>
    <v-dialog v-model="dialog" scrollable width="750">
        <template v-slot:activator="{ on, attrs }">
            <v-btn icon v-bind="attrs" v-on="on">
                <v-icon>create</v-icon>
            </v-btn>
        </template>

        <v-card :loading="loading">
            <v-card-title dark class="headline">
                Edit User Information
            </v-card-title>

            <v-form v-model="isFormValid">
                <v-card-text>
                    <v-container>
                        <v-row>
                            <v-col>
                                <v-text-field
                                    v-model="editedItem.id"
                                    label="ID"
                                    disabled
                                ></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col>
                                <v-text-field
                                    v-model="editedItem.name"
                                    label="Name"
                                ></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col>
                                <v-text-field
                                    v-model="editedItem.email"
                                    label="Email"
                                ></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col>
                                <v-autocomplete
                                    clearable
                                    flat
                                    v-model="editedItem.type"
                                    label="Type"
                                    :items="userTypes"
                                ></v-autocomplete>
                            </v-col>
                        </v-row>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                    <v-btn color="green darken-1" text v-on:click.prevent="saveEdit" :disabled="!isFormValid">Save
                    </v-btn>
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
        userTypes: ["Customer", "Manager", "Cook", "Delivery Man"],
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
                case "Customer":
                    return "C";
                case "Manager":
                    return "EM";
                case "Cook":
                    return "EC";
                case "Delivery Man":
                    return "ED";
            }
        },
        saveEdit() {
            let data = {
                id: this.editedItem.id,
                name: this.editedItem.name,
                email: this.editedItem.email,
                type: this.getTypeName(this.editedItem.type)
            };

            this.loading = true;

            axios
                .patch(`api/users/patch/${this.editedItem.id}`, data)
                .then(response => {

                    this.$emit(
                        "show-notification",
                        "success",
                        "Updated user successfully!"
                    );

                    this.dialog = false;
                    this.$emit("get-users");
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
        this.editedItem.id = this.user.id;
        this.editedItem.name = this.user.name;
        this.editedItem.email = this.user.email;

        switch (this.user.type) {
            case "C":
                this.editedItem.type = "Customer";
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
