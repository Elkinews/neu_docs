<template>
    <v-btn @click="click">
        <v-icon left x-small color="white">mdi-triangle-outline</v-icon>
        <span>Import</span>
    </v-btn>
</template>

<script>
export default {
    name: "NdeButtonImport",
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
    width: 7.5rem;
    height: 2.5rem;
    span {
        color: #ffffff;
        text-transform: capitalize;
        font-family: "Poppins";
        font-style: normal;
        font-weight: 500;
        font-size: 0.875rem;
        line-height: 1.5rem;
    }
    i {
        transform: rotate(90deg);
    }
}
</style>
