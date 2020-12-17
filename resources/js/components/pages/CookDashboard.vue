<template>
    <v-container>
        <v-row>
            <v-col>
                <v-card flat>
                    <v-card-title class="red lighten-1">
                        WIP - Current Order
                    </v-card-title>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import { mapGetters } from "vuex";

export default {
    data: () => ({}),
    methods: {},
    computed: {
        ...mapGetters(["getUser", "isUserCook"])
    },
    mounted() {
        // TODO: Wait for user (e.g., loading)
        // if (!this.isUserCook) this.$router.push("/home");
    }
};
</script>
