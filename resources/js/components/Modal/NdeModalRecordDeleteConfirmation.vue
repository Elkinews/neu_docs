<template>
    <v-dialog
        v-if="showModal"
        v-model="showModal"
        max-width="1200"
        @click:outside="hideModal"
    >
        <v-card class="pa-5">
            <v-card-title
                class="
                    text-sm-left
                    font-weight-bold
                    d-flex
                    justify-space-between
                    ml-2
                    mb-3
                "
            >
                <h3>Record Delete Confirmation</h3>
                <v-icon
                    aria-label="Close"
                    size="24px"
                    color="red"
                    @click="hideModal"
                >
                    mdi-close
                </v-icon>
            </v-card-title>
            <v-card-text>
                <nde-alert-warning :message="message"></nde-alert-warning>
            </v-card-text>
            <v-card-actions>
                <v-row>
                    <v-col cols="12" md="6">
                        <nde-button-warning
                            title="Delete"
                            @click="hideModal"
                            class="ml-3"
                        ></nde-button-warning>
                    </v-col>
                    <v-col cols="12" md="6" style="text-align: right">
                        <nde-button-cancel
                            @click="hideModal"
                            class="mr-3"
                        ></nde-button-cancel>
                    </v-col>
                </v-row>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import NdeAlertWarning from "../Alert/NdeAlertWarning.vue";
import NdeButtonWarning from "../Button/NdeButtonWarning.vue";
import NdeButtonCancel from "../Button/NdeButtonCancel.vue";

export default {
    name: "NdeModalRecordDeleteConfirmation",
    components: {
        NdeAlertWarning,
        NdeButtonWarning,
        NdeButtonCancel,
    },
    props: {
        showModal: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            message: "Are you sure you want to delete this record?",
        };
    },
    methods: {
        hideModal() {
            this.$emit("update:showModal", !this.showModal);
        },
    },
};
</script>

<style scoped lang="scss">
.v-card__title {
    .v-icon:hover {
        &::after {
            @extend %afterIconModalClose;
        }
    }
}
</style>
