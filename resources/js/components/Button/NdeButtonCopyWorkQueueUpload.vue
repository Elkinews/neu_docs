<template>
    <v-btn @click="click">
        <span>Upload</span>
    </v-btn>
</template>

<script>
export default {
    name: "NdeButtonCopyWorkQueueUpload",
    methods: {
        click() {
            this.$emit("click");
        },
    },
};
</script>

<style scoped lang="scss">
button {
    background-color: $primaryColor !important;
    border-color: $primaryColor !important;
    border-radius: 8px;
    width: 17rem;
    height: 2.5rem !important;
    box-shadow: none;
    span {
        color: #ffffff;
        text-transform: capitalize;
        font-family: "Poppins";
        font-style: normal;
        font-weight: 500;
        font-size: 0.875rem;
        line-height: 1.5rem;
    }
}
</style>