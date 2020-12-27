<template>
    <v-container>
        <v-card flat>
            <v-toolbar
                rounded
                flat
            >
                <v-toolbar-title>Manager Dashboard</v-toolbar-title>
                <v-divider
                    class="mx-4"
                    inset
                    vertical
                ></v-divider>
                <v-spacer></v-spacer>
                <v-dialog v-model="dialogDelete" max-width="500px">
                    <v-card>
                        <v-card-title class="headline">Confirm delete action</v-card-title>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="red darken-1" text @click="closeDelete">Cancel</v-btn>
                            <v-btn color="green darken-1" text @click="deleteItemConfirm">OK</v-btn>
                            <v-spacer></v-spacer>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
            </v-toolbar>
            <v-data-table
                :headers="headers"
                :items="users"
                sort-by="id"
                class="elevation-1"
            >
                <template v-slot:item.actions="{ item }">
                    <manager-edit-user-dialog v-if="isUserManager"
                                              v-on:show-notification="openNotification"
                                              v-on:update-products="getUsers">
                    </manager-edit-user-dialog>

                    <v-icon
                        small
                        @click="deleteItem(item)"
                    >
                        delete
                    </v-icon>
                </template>
                <template v-slot:no-data>
                    <v-btn
                        color="primary"
                        @click="initialize"
                    >
                        cached
                    </v-btn>
                </template>
            </v-data-table>
        </v-card>
    </v-container>
</template>

<script>
import ManagerEditUserDialog from "../dialogs/ManagerEditUserDialog";
import {mapGetters} from "vuex";

export default {
    components: {
        "manager-edit-user-dialog": ManagerEditUserDialog
    },
    data: () => ({
        dialogDelete: false,
        headers: [
            {
                text: 'ID',
                align: 'start',
                sortable: false,
                value: 'id',
            },
            {text: 'Name', value: 'name'},
            {text: 'Email', value: 'email'},
            {text: 'Type', value: 'type'},
            {text: 'Actions', value: 'actions', sortable: false},
        ],
        users: [],
        editedIndex: -1,
        defaultItem: {
            id: 0,
            name: '',
            email: '',
            type: '',
        },
    }),

    computed: {
        ...mapGetters([
            "isLoggedIn",
            "isUserManager"
        ])
    },

    watch: {
        dialogDelete(val) {
            val || this.closeDelete()
        },
    },

    created() {
        this.initialize()
    },

    methods: {
        initialize() {
            this.users = [
                {
                    id: 1,
                    name: 'Maria',
                    email: 'maria@mail.pt',
                    type: 'EM',
                    created: '2020-05-13 16:30:30'
                },
                {
                    id: 1,
                    name: 'Maria Martins',
                    email: 'mariaMartins@mail.pt',
                    type: 'C',
                    created: '2020-07-13 16:30:30'
                }
            ]
        },

        editItem(item) {
            // TODO
        },

        deleteItem(item) {
            this.dialogDelete = true;
        },

        deleteItemConfirm() {
            this.users.splice(this.editedIndex, 1)
            this.closeDelete()
        },

        closeDelete() {
            this.dialogDelete = false
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem)
                this.editedIndex = -1
            })
        },

        save() {
            if (this.editedIndex > -1) {
                Object.assign(this.users[this.editedIndex], this.editedItem)
            } else {
                this.users.push(this.editedItem)
            }
            this.close()
        },
        getTypeName(item) {
            switch (item) {
                case "C":
                    this.users.type = "Client";
                    break;
                case "EM":
                    this.users.type = "Manager";
                    break;
                case "EC":
                    this.users.type = "Cook";
                    break;
                case "ED":
                    this.users.type = "Delivery Man";
                    break;
            }
        },
        getUsers() {
            // TODO
        },
        openNotification(color, message, timeout = 6000) {
            this.$emit("show-notification", color, message, timeout);
        }
    },
}
</script>

<style scoped>

</style>
