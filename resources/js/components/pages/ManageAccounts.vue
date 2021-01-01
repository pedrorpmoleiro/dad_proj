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
                <v-spacer></v-spacer>
                <manager-create-user-dialog
                    v-on:show-notification="openNotification"
                    v-on:get-users="getUsers">
                </manager-create-user-dialog>
                <v-spacer></v-spacer>
                <v-text-field
                    v-model="search"
                    label="Search"
                    append-icon="search"
                    single-line
                    class="ml-3 mb-2"
                    hide-details
                ></v-text-field>
                <v-btn :loading="loading" color="primary" text v-on:click.prevent="getUsers">
                    <v-icon>cached</v-icon>
                </v-btn>
            </v-toolbar>
            <v-divider></v-divider>
            <v-data-table
                :headers="headers"
                :items="users"
                :loading="loading"
                :search="search"
                sort-by="id"
                class="elevation-1"
            >
                <template v-slot:item.type="{ item }">
                    {{ getTypeName(item.type) }}
                </template>
                <template v-slot:item.blocked="{ item }">
                    <div v-if="item.blocked === 1">
                        Blocked
                    </div>
                    <div v-else>
                        Not blocked
                    </div>
                </template>
                <template v-slot:item.actions="{ item }">
                    <div v-if="getUser.id !== item.id">
                        <manager-edit-user-dialog
                            v-if="item.type !== 'C'"
                            v-bind:user="item"
                            v-on:show-notification="openNotification"
                            v-on:get-users="getUsers">
                        </manager-edit-user-dialog>

                        <v-btn icon color="red lighten-1"
                               v-on:click="deleteUser(item)">
                            <v-icon>delete</v-icon>
                        </v-btn>

                        <v-btn icon v-on:click.prevent="blockUser(item)">
                            <v-icon>{{ item.blocked === 0 ? 'lock' : 'lock_open' }}</v-icon>
                        </v-btn>
                    </div>
                </template>
            </v-data-table>
        </v-card>
    </v-container>
</template>

<script>
import ManagerEditUserDialog from "../dialogs/ManagerEditUserDialog";

import {mapGetters} from "vuex";
import ManagerCreateUserDialog from "../dialogs/ManagerCreateUserDialog";

export default {
    components: {
        "manager-edit-user-dialog": ManagerEditUserDialog,
        "manager-create-user-dialog": ManagerCreateUserDialog
    },
    data: () => ({
        loading: true,
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
    }),
    computed: {
        ...mapGetters([
            "isUserManager",
            "isAuthLoading",
            "getUser"
        ])
    },
    methods: {
        deleteUser(item) {
            axios
                .delete(`api/users/delete/${item.id}`)
                .then(response => {
                    // console.log(response);
                    this.$emit(
                        "show-notification",
                        "success",
                        "User deleted successfully"
                    );
                    this.getUsers();
                })
                .catch(e => {
                    // console.log(e);
                    this.$emit(
                        "show-notification",
                        "error",
                        "Failed to delete user"
                    );
                });
        },
        getTypeName(item) {
            switch (item) {
                case "C":
                    return "Customer";
                case "EM":
                    return "Manager";
                case "EC":
                    return "Cook";
                case "ED":
                    return "Delivery Man";
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
                    // console.log(e.response);
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
        blockUser(item) {
            axios
                .patch(`api/users/block/${item.id}`)
                .then(response => {
                    // console.log(response);
                    this.$emit(
                        "show-notification",
                        "success",
                        "User blocked/unblocked successfully"
                    );
                    this.getUsers();
                })
                .catch(e => {
                    // console.log(e);
                    this.$emit(
                        "show-notification",
                        "error",
                        "Failed to block/unblock user"
                    );
                });
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
