<template>
    <v-dialog v-model="dialog" scrollable width="750">
        <template v-slot:activator="{ on, attrs }">
            <v-btn small v-bind="attrs" v-on="on">
                <v-icon>create</v-icon>
            </v-btn>
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
                        </v-row>
                    </v-container>
                </v-form>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    // TODO: Add Save and Cancel BTN
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
                case "C":
                    break;
                case "EM":
                    break;
                case "EC":
                    break;
                case "ED":
                    break;
            }
        }
    }
}
</script>

<style scoped>

</style>
