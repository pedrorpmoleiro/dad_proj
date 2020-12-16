<template>
    <v-dialog v-model="dialog" scrollable width="750">
        <template v-slot:activator="{ on, attrs }">
            <v-btn icon v-bind="attrs" v-on="on">
                <v-icon>launch</v-icon>
            </v-btn>
        </template>
        <v-card>
            <v-card-title class="red lighten-1">
                WIP - {{ "Order " + order.id }}
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text>
                <div v-if="loading">
                    <v-layout align-center justify-center>
                        <v-progress-circular
                            indeterminate
                            color="primary"
                        ></v-progress-circular>
                    </v-layout>
                </div>
                <div v-else>
                    <v-container>
                        <v-col v-if="order.notes">
                            <v-row>
                                <p class="font-weight-bold">
                                    Notes
                                </p>
                                <p class="text-justify">
                                    {{ order.notes }}
                                </p>
                            </v-row>
                        </v-col>
                        <v-layout align-center justify-center>
                            <v-timeline>
                                <div v-if="order.status === 'C'">
                                    <v-timeline-item color="red">
                                        <div class="py-4">
                                            <h2 class="headline font-weight-light mb-4">Order was cancelled</h2>
                                        </div>
                                        <template v-slot:opposite>
                                        <span class="headline">
                                            {{ (new Date(order.closed_at)).toLocaleTimeString() }}
                                        </span>
                                        </template>
                                    </v-timeline-item>
                                </div>

                                <div v-if="order.status === 'D'">
                                    <v-timeline-item color="primary">
                                        <div class="py-4">
                                            <h2 class="headline font-weight-light mb-4">Order finished</h2>
                                            <p v-if="order.total_time">
                                                Order took {{ parseInt(order.total_time / 60) }} minutes total
                                            </p>
                                        </div>
                                        <template v-slot:opposite>
                                            <span class="headline">
                                                {{ (new Date(order.closed_at)).toLocaleTimeString() }}
                                            </span>
                                        </template>
                                    </v-timeline-item>
                                    <v-timeline-item color="primary">
                                        <div class="py-4">
                                            <h2 class="headline font-weight-light mb-4">Order was delivered</h2>
                                            <p v-if="order.delivery_man">
                                                Delivery by: {{ getUserFirstAndLastName(order.delivery_man.name) }}
                                            </p>
                                            <p v-if="order.delivery_time">
                                                Took {{ parseInt(order.delivery_time / 60) }} minutes to deliver
                                            </p>
                                        </div>
                                    </v-timeline-item>
                                    <v-timeline-item color="primary">
                                        <div class="py-4">
                                            <h2 class="headline font-weight-light mb-4">Order prepared</h2>
                                            <p v-if="order.cook">
                                                Prepared by: {{ getUserFirstAndLastName(order.cook.name) }}
                                            </p>
                                            <p v-if="order.preparation_time">
                                                Took {{ parseInt(order.preparation_time / 60) }} minutes to prepare
                                            </p>
                                        </div>
                                    </v-timeline-item>
                                </div>

                                <div v-if="order.status === 'T'">
                                    <v-timeline-item color="red">
                                        <div class="py-4">
                                            <h2 class="headline font-weight-light mb-4">Order is being delivered to
                                                you</h2>
                                            <p v-if="order.delivery_man">
                                                Being delivered by: {{ getUserFirstAndLastName(order.delivery_man.name) }}
                                            </p>
                                        </div>
                                        <template v-slot:opposite>
                                        <span class="headline">
                                            {{ (new Date(order.current_status_at)).toLocaleTimeString() }}
                                        </span>
                                        </template>
                                    </v-timeline-item>
                                    <v-timeline-item color="primary">
                                        <div class="py-4">
                                            <h2 class="headline font-weight-light mb-4">Order prepared</h2>
                                            <p v-if="order.cook">
                                                Prepared by: {{ getUserFirstAndLastName(order.cook.name) }}
                                            </p>
                                            <p v-if="order.preparation_time">
                                                Took {{ parseInt(order.preparation_time / 60) }} minutes to prepare
                                            </p>
                                        </div>
                                    </v-timeline-item>
                                </div>

                                <div v-if="order.status === 'R'">
                                    <v-timeline-item color="red">
                                        <div class="py-4">
                                            <h2 class="headline font-weight-light mb-4">Order is Ready for Delivery</h2>
                                        </div>
                                        <template v-slot:opposite>
                                        <span class="headline">
                                            {{ (new Date(order.current_status_at)).toLocaleTimeString() }}
                                        </span>
                                        </template>
                                    </v-timeline-item>
                                    <v-timeline-item color="primary">
                                        <div class="py-4">
                                            <h2 class="headline font-weight-light mb-4">Order prepared</h2>
                                            <p v-if="order.cook">
                                                Prepared by: {{ getUserFirstAndLastName(order.cook.name) }}
                                            </p>
                                            <p v-if="order.preparation_time">
                                                Took {{ parseInt(order.preparation_time / 60) }} minutes to prepare
                                            </p>
                                        </div>
                                    </v-timeline-item>
                                </div>

                                <div v-if="order.status === 'P'">
                                    <v-timeline-item color="red">
                                        <div class="py-4">
                                            <h2 class="headline font-weight-light mb-4">Order is being prepared</h2>
                                            <p v-if="order.cook">
                                                Being prepared by: {{ getUserFirstAndLastName(order.cook.name) }}
                                            </p>
                                        </div>
                                        <template v-slot:opposite>
                                        <span class="headline">
                                            {{ (new Date(order.current_status_at)).toLocaleTimeString() }}
                                        </span>
                                        </template>
                                    </v-timeline-item>
                                </div>

                                <v-timeline-item color="primary">
                                    <div class="py-4">
                                        <h2 class="headline font-weight-light mb-4">Order Created</h2>
                                        <p>
                                            {{ (new Date(order.created_at)).toLocaleDateString() }}
                                        </p>
                                    </div>
                                    <template v-slot:opposite>
                                    <span class="headline">
                                        {{ (new Date(order.created_at)).toLocaleTimeString() }}
                                    </span>
                                    </template>
                                </v-timeline-item>
                            </v-timeline>
                        </v-layout>
                    </v-container>
                </div>
            </v-card-text>

            <v-divider></v-divider>

            <v-card-actions>
                <v-btn text color="primary" v-on:click.prevent="refreshOrderData" :loading="loading">
                    <v-icon>cached</v-icon>
                    Refresh
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn text color="red" v-on:click.prevent="dialog = false">
                    Close
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    props: ["orderProp"],
    data: () => ({
        dialog: false,
        loading: false,
        order: {}
    }),
    methods: {
        refreshOrderData() {
            this.loading = true;

            axios.get(`api/orders/${this.order.id}`).then(response => {
                console.log(response);
                this.order = response.data;
                this.loading = false;
            }).catch(error => {
                console.log(error);
                this.loading = false;
            });
        },
        getUserFirstAndLastName(fullName) {
            let aux = fullName.split(" ");
            return aux[0] + " " + aux[aux.length - 1];
        }
    },
    mounted() {
        this.order = this.orderProp;

        // TODO Thread that refreshes the page every Y seconds
    }
};
</script>
