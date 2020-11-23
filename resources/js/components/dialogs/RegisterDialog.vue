<template>
    <v-dialog v-model="dialog" width="500">
        <template v-slot:activator="{ on, attrs }">
            <v-btn text v-bind="attrs" v-on="on">
                Register
            </v-btn>
        </template>

        <v-card>
            <v-card-title class="headline grey lighten-2">
                TODO!!
            </v-card-title>

            <v-card-text>
                TODO!!
            </v-card-text>

            <v-divider></v-divider>

            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="primary" text @click="dialog = false">
                    I accept
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    props: ["authToken"],
    data: () => ({
        dialog: false
    })
};
</script>
