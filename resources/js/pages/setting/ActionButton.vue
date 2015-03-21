<template>
    <v-card-actions class="py-5 px-5" :class="{'d-flex flex-column' : isMobile}">
        <nde-button-cancel @click="onCancel" :class="{'order-2 ': isMobile}"> </nde-button-cancel>
        <nde-button-primary :class="{'order-1 ml-0 mb-3': isMobile}" :disabled="disabled" :loading="loading" title="Save" @click="onSave"> </nde-button-primary>
    </v-card-actions>
</template>

<script>
import NdeButtonCancel from "../../components/Button/NdeButtonCancel.vue";
import NdeButtonPrimary from "../../components/Button/NdeButtonPrimary.vue";
export default {
    components: {
        NdeButtonCancel,
        NdeButtonPrimary
    },

    props: {
        loading: {
            type: Boolean,
            default: false
        },

        disabled: {
            type: Boolean,
            default: false
        }
    },

    methods: {
        onCancel() {
            this.$emit('onCancel')
        },

        onSave() {
            this.$emit('onSave')
        }
    }
}
</script>