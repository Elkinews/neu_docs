<template>
    <v-btn @click="click">
        <span>Cancel</span>
    </v-btn>
</template>

<script>
export default {
    name: "NdeButtonImportCancel",
    methods: {
        click() {
            this.$emit("click");
        },
    },
};
</script>

<style scoped lang="scss">
button {
    background-color: #ebeced !important;
    border-color: #ebeced !important;
    border-radius: 8px;
    width: 11.25rem;
    height: 2.5rem;
    span {
        color: $drakColor;
        text-transform: capitalize;
        font-family: "Poppins";
        font-style: normal;
        font-weight: 500;
        font-size: 0.875rem;
        line-height: 1.5rem;
    }
}
</style>