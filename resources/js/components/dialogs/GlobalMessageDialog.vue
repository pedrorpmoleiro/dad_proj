<template>
    <v-dialog v-model="dialog" width="500">
        <template v-slot:activator="{ on, attrs }">
            <v-btn text v-bind="attrs" v-on="on">
                Send Message to all
            </v-btn>
        </template>
        <v-card :loading="loading">
            <v-card-title>
                Global Message
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text>
                <v-form>
                    <v-container>
                        <v-row>
                            <v-col>
                                <v-text-field
                                    v-model="input.message"
                                    label="Message Text *"
                                    clearable
                                    counter
                                    required
                                    :rules="[rules.required]"
                                ></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col>
                                <v-autocomplete
                                    v-model="input.color"
                                    label="Message Color *"
                                    :items="colors"
                                    clearable
                                    :rules="[rules.required]"
                                ></v-autocomplete>
                            </v-col>
                        </v-row>
                    </v-container>
                </v-form>
                <small class="red--text">* indicates required field</small>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                    text
                    color="green"
                    v-on:click.prevent="submit"
                >
                    Submit
                </v-btn>
                <v-btn text color="red" v-on:click.prevent="dialog = false">
                    Cancel
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import {mapGetters} from "vuex";

export default {
    data: () => ({
        dialog: false,
        loading: false,
        input: {
            message: "",
            color: ""
        },
        colors: ["Red", "Green", "Yellow", "Orange", "White"],
        rules: {
            required: value => !!value || "Required"
        }
    }),
    computed: {
        ...mapGetters(["getUser"]),
    },
    methods: {
        submit() {
            this.loading = true;

            let payload = {
                user: {
                    id: this.getUser.id,
                    type: this.getUser.type
                },
                message: this.input.message,
                color: this.input.color.toLowerCase()
            }

            this.$socket.emit("global_message", payload);

            this.loading = false;
            this.dialog = false;
        }
    }
};
</script>
