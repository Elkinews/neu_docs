<template>
    <v-alert text>
        <span>{{ message }}</span>
    </v-alert>
</template>

<script>
export default {
    name: "NdeAlertWarning",
    props: {
        message: {
            type: String,
            default: () => "Are you sure you want to delete this item?",
        },
    },
};
</script>

<style scoped lang="scss">
.v-alert {
    background: #f9e1e1 !important;
    border-radius: 4px;
    text-align: center;
    .v-alert__content {
        span {
            color: #c32c39;
            font-family: "Poppins";
            font-style: normal;
            font-weight: 500;
            font-size: 0.875rem;
            line-height: 1.5rem;
        }
    }
}
</style>
