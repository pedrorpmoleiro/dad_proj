<template>
    <v-container>
        <v-row>
            <v-col>
                <v-card flat :loading="employees.loading">
                    <v-toolbar rounded flat>
                        <v-toolbar-title class="font-weight-bold"
                            >Employees</v-toolbar-title
                        >
                        <v-spacer></v-spacer>
                        <v-btn
                            icon
                            :loading="employees.loading"
                            v-on:click.prevent="getEmployees"
                            color="primary"
                        >
                            <v-icon>cached</v-icon>
                        </v-btn>
                    </v-toolbar>
                    <v-divider></v-divider>
                    <v-data-table
                        :headers="employees.headers"
                        :items="employees.list"
                    >
                        <template v-slot:item.photo="{ item }">
                            <v-layout align-center justify-center>
                                <div v-if="item.photo_url">
                                    <v-img
                                        class="my-2 avatar"
                                        max-width="75"
                                        max-height="75"
                                        :src="
                                            '../storage/fotos/' + item.photo_url
                                        "
                                    ></v-img>
                                </div>
                                <div v-else>
                                    <v-icon x-large>account_circle </v-icon>
                                </div>
                            </v-layout>
                        </template>
                        <template v-slot:item.type="{ item }">
                            <div v-if="item.type == 'EC'">
                                Cook
                            </div>
                            <div v-else-if="item.type == 'ED'">
                                Delivery Man
                            </div>
                            <div v-else-if="item.type == 'EM'">
                                Manager
                            </div>
                        </template>
                        <template v-slot:item.started="{ item }">
                            <div v-if="item.logged_at != null">
                                {{
                                    new Date(
                                        item.logged_at
                                    ).toLocaleTimeString()
                                }}
                            </div>
                        </template>
                        <template v-slot:item.status="{ item }">
                            <v-badge
                                v-if="item.logged_at == null"
                                class="font-italic"
                                inline
                                left
                                dot
                                color="red"
                            >
                                Offline
                            </v-badge>
                            <div v-else-if="item.available_at == null">
                                <v-badge
                                    v-if="item.type == 'EC'"
                                    class="font-italic"
                                    inline
                                    left
                                    dot
                                    color="yellow"
                                >
                                    Preparing
                                </v-badge>
                                <v-badge
                                    v-else-if="item.type == 'ED'"
                                    class="font-italic"
                                    inline
                                    left
                                    dot
                                    color="yellow"
                                >
                                    Delivering
                                </v-badge>
                            </div>
                            <v-badge
                                v-else
                                class="font-italic"
                                inline
                                left
                                dot
                                color="green"
                            >
                                Available
                            </v-badge>
                        </template>
                    </v-data-table>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import { mapGetters } from "vuex";

export default {
    data: () => ({
        employees: {
            loading: true,
            list: [],
            headers: [
                {
                    text: "Photo",
                    value: "photo"
                },
                {
                    text: "Id",
                    value: "id"
                },
                {
                    text: "Type",
                    value: "type"
                },
                {
                    text: "Name",
                    value: "name"
                },
                {
                    text: "Started at",
                    value: "started"
                },
                {
                    text: "Available",
                    value: "status"
                }
            ]
        }
    }),
    methods: {
        getEmployees() {
            this.employees.loading = true;

            axios
                .get("api/employees")
                .then(response => {
                    // console.log(response);
                    this.employees.list = response.data;
                    this.employees.loading = false;
                })
                .catch(e => {
                    // console.log(e);
                    this.employees.loading = false;
                });
        }
    },
    computed: {
        ...mapGetters(["isUserManager", "isAuthLoading"])
    },
    async mounted() {
        while (this.isAuthLoading)
            await new Promise(resolve => setTimeout(resolve, 500));

        if (!this.isUserManager) this.$router.push("/home");

        this.getEmployees();
    }
};
</script>
