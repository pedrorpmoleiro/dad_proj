<template>
    <v-card flat outlined class="ma-6">
        <v-img :height="height" src="../storage/background_home.jpg">
            <v-container fill-height class="justify-center">
                <v-layout align-center justify-center>
                    <v-card class="d-flex justify-center">
                        <v-card-title class="mx-3">
                            <h3>Welcome to Food@Home</h3>
                        </v-card-title>
                    </v-card>
                </v-layout>
            </v-container>
        </v-img>
    </v-card>
</template>

<script>
export default {
    data: () => ({
        height: window.innerHeight - 115
    })
};
</script>
