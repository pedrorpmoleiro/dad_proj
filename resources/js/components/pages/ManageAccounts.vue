<template>
    <v-container>
        <v-card flat>
            <v-toolbar
                rounded
                flat
            >
                <v-toolbar-title>
                    Manager Dashboard
                </v-toolbar-title>
                <v-divider
                    class="mx-4"
                    inset
                    vertical
                ></v-divider>
                <v-spacer></v-spacer>
                <v-text-field
                    v-model="search"
                    label="Search"
                    append-icon="search"
                    single-line
                    class="ml-3 mb-2"
                    hide-details
                ></v-text-field>
                <v-dialog v-model="dialogDelete" max-width="500px">
                    <v-card>
                        <v-card-title class="headline">Confirm delete action</v-card-title>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="red darken-1" text @click.prevent="closeDelete">Cancel</v-btn>
                            <v-btn color="green darken-1" text @click.prevent="deleteItemConfirm">OK</v-btn>
                            <v-spacer></v-spacer>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
                <v-dialog v-model="dialogBlock" max-width="500px">
                    <v-card>
                        <v-card-title class="headline">Confirm block action</v-card-title>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="red darken-1" text @click.prevent="closeBlock">Cancel</v-btn>
                            <v-btn color="green darken-1" text @click.prevent="blockUserConfirm">OK</v-btn>
                            <v-spacer></v-spacer>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
            </v-toolbar>
            <div v-if="loading || users.length > 0">
                <v-data-table
                    :headers="headers"
                    :items="users"
                    :loading="loading"
                    sort-by="id"
                    class="elevation-1"
                >
                    <template v-slot:item.type="{ item }">
                        {{ getTypeName(item.type) }}
                    </template>

                    <template v-slot:item.actions="{ item }">
                        <manager-edit-user-dialog v-if="isUserManager"
                                                  v-bind:user="item"
                                                  v-on:show-notification="openNotification"
                                                  v-on:get-users="getUsers">
                        </manager-edit-user-dialog>

                        <v-icon
                            small
                            v-if="isUserManager"
                            v-on:show-notification="openNotification"
                            v-on:update-products="getUsers"
                            @click="deleteItemConfirm(item)"
                        >
                            delete
                        </v-icon>

                        <v-icon
                            small
                            v-if="isUserManager && !isThisUserBlocked(item.blocked)"
                            v-on:show-notification="openNotification"
                            v-on:update-products="getUsers"
                            @click="blockUserConfirm(item)"
                        >
                            lock
                        </v-icon>
                        <v-icon
                            small
                            v-if="isUserManager && isThisUserBlocked(item.blocked)"
                            v-on:show-notification="openNotification"
                            v-on:update-products="getUsers"
                            @click="blockUserConfirm(item)"
                        >
                            open_lock
                        </v-icon>
                    </template>
                </v-data-table>
            </div>
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
        dialogBlock: false,
        loading: false,
        headers: [
            {
                text: 'ID',
                align: 'start',
                value: 'id',
            },
            {text: 'Name', value: 'name'},
            {text: 'Email', value: 'email'},
            {text: 'Type', value: 'type'},
            {text: 'Block Status', value: 'blocked'},
            {text: 'Actions', value: 'actions', sortable: false},
        ],
        users: [],
        search: '',
        editedIndex: -1,
        defaultItem: {
            id: 0,
            name: '',
            email: '',
            type: '',
            blocked: '',
        },
    }),

    computed: {
        ...mapGetters([
            "isUserManager",
            "isAuthLoading"
            // "isUserBlocked",
        ])
    },

    watch: {
        dialogDelete(val) {
            val || this.closeDelete()
        },
    },

    methods: {
        isThisUserBlocked(item) {
            console.log(item);
            return item === 1;
        },

        // TODO: Not using confirmation dialogs for now
        deleteItemConfirm(item) {
            axios
                .delete(`api/users/delete/${item.id}`)
                .then(response => {
                    console.log(response);
                    this.$emit(
                        "show-notification",
                        "success",
                        "User deleted successfully"
                    );
                    this.getUsers();
                })
                .catch(e => {
                    console.log(e);
                    this.$emit(
                        "show-notification",
                        "error",
                        "Failed to delete user"
                    );
                });

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
                    return "Client";
                    break;
                case "EM":
                    return "Manager";
                    break;
                case "EC":
                    return "Cook";
                    break;
                case "ED":
                    return "Delivery Man";
                    break;
            }
        },
        getUsers() {
            this.loading = true;
            // Cleans data table
            this.users = [];

            // Gets Users from DB
            axios
                .get("api/users")
                .then(response => {
                    this.users = response.data;
                    this.loading = false;
                })
                .catch(e => {
                    console.log(e.response);
                    this.$emit(
                        "show-notification",
                        "error",
                        "Unable to load users"
                    );
                });
        },
        openNotification(color, message, timeout = 6000) {
            this.$emit("show-notification", color, message, timeout);
        },
        // TODO: Not using confirmation dialogs for now
        blockUser(item) {
            this.dialogBlock = true;
        },
        blockUserConfirm(item) {
            axios
                .patch(`api/users/block/${item.id}`)
                .then(response => {
                    console.log(response);
                    this.$emit(
                        "show-notification",
                        "success",
                        "User blocked/unblocked successfully"
                    );
                    this.getUsers();
                })
                .catch(e => {
                    console.log(e);
                    this.$emit(
                        "show-notification",
                        "error",
                        "Failed to block/unblock user"
                    );
                });
        },
        closeBlock() {
            this.dialogBlock = false;
        }
    },

    async mounted() {
        while (this.isAuthLoading)
            await new Promise(resolve => setTimeout(resolve, 500));

        if (!this.isUserManager) this.$router.push("/home");

        this.getUsers();
    },
}
</script>

<style scoped>

</style>
