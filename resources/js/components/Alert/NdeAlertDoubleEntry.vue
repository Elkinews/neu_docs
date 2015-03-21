<template>
    <v-alert text color="success">
        <ul>
            <li v-if="hasOneRequired">
                <span>Fill out at least one field marked with</span
                ><span class="ml-2 color-red">Δ</span>
            </li>
            <li v-if="hasTwoRequired">
                <span>Fill out at least one field marked with</span
                ><span class="ml-2 color-red">ΔΔ</span>
            </li>
        </ul>
    </v-alert>
</template>

<script>
export default {
    name: "NdeAlertDoubleEntry",
    props: {
      hasOneRequired: {
        type: Boolean,
        default: false,
      },
      hasTwoRequired: {
        type: Boolean,
        default: false,
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
            .color-red {
                color: #c32c39;
            }
        }
    }
}
</style>
