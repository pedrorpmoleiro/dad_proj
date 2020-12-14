<template>
    <v-file-input
        chips
        show-size
        clearable
        accept="image/jpeg, image/png"
        prepend-icon="add_a_photo"
        label="Profile Picture"
        v-model="input.photo_url"
    ></v-file-input>
    <!-- ! TODO -->
</template>

<script>
export default {};
</script>
