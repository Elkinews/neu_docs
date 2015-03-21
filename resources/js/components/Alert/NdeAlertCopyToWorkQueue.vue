<template>
    <v-alert v-show="messages.length" text color="success">
        <ul>
            <li v-for="(message, index) in messages" :key="index">
                <span>{{ message }}</span>
            </li>
        </ul>
    </v-alert>
</template>

<script>
export default {
    name: "NdeAlertCopyToWorkQueue",
    props: {
        messages: {
            type: Array,
            default: () => [
                // TODO: Will delete when there is data get from API
                "This record does not yet exist in the Work Queue. Some fields below are populated based on the Key Search record.",
                "If the selected Status is not in the selected Status Type then the first Status for that Status Type will be selected.",
            ],
        },
    },
};
</script>

<style scoped lang="scss">
.v-alert {
    padding: 0.5rem;
    .v-alert__content {
        ul {
            color: $drakColor;
            font-family: "Poppins";
            font-style: normal;
            font-weight: 400;
            font-size: 1rem;
            line-height: 1.5rem;
        }
    }
}
</style>
