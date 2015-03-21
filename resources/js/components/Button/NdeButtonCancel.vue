<template>
    <v-btn :style="styles" @click="click">
        <span>{{ title }}</span>
    </v-btn>
</template>

<script>
export default {
    name: "NdeButtonCancel",
    props: {
        title: {
            type: String,
            default: "Cancel",
        },
        styles: {
            type: Object,
            default: () => {},
        },
    },
    methods: {
        click() {
            this.$emit("click");
        },
    },
};
</script>

<style scoped lang="scss">
button {
    background-color: #EAEBEC !important;
    border-color: #EAEBEC !important;
    border-radius: 8px;
    width: 17rem;
    height: 2.5rem !important;
    box-shadow: none;
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
