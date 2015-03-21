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
                    mb-3
                    ml-3
                "
            >
                <h3>Copy To Work Queue</h3>
                <v-icon
                    aria-label="Close"
                    size="24px"
                    color="red"
                    @click="hideModal"
                >
                    mdi-close
                </v-icon>
            </v-card-title>
            <v-card-text class="mb-3">
                <label class="mb-3 ml-3 text-color-black">Fields</label>
                <nde-alert-copy-to-work-queue
                    class="mr-3 ml-3"
                ></nde-alert-copy-to-work-queue>
                <nde-form-copy-to-work-queue></nde-form-copy-to-work-queue>
            </v-card-text>
            <v-card-actions>
                <v-row>
                    <v-col cols="12" md="6">
                        <nde-button-copy-work-queue-upload
                            @click="hideModal"
                            class="ml-3"
                        ></nde-button-copy-work-queue-upload>
                    </v-col>
                    <v-col cols="12" md="6" style="text-align: right">
                        <nde-button-copy-work-queue-cancel
                            @click="hideModal"
                            class="mr-3"
                        ></nde-button-copy-work-queue-cancel>
                    </v-col>
                </v-row>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import NdeAlertCopyToWorkQueue from "../Alert/NdeAlertCopyToWorkQueue.vue";
import NdeFormCopyToWorkQueue from "../Form/NdeFormCopyToWorkQueue.vue";
import NdeButtonCopyWorkQueueCancel from "../Button/NdeButtonCopyWorkQueueCancel.vue";
import NdeButtonCopyWorkQueueUpload from "../Button/NdeButtonCopyWorkQueueUpload.vue";

export default {
    name: "NdeModalCopyToWorkQueue",
    components: {
        NdeAlertCopyToWorkQueue,
        NdeFormCopyToWorkQueue,
        NdeButtonCopyWorkQueueCancel,
        NdeButtonCopyWorkQueueUpload,
    },
    props: {
        workQueueId: {
            type: Number,
        },
        showModal: {
            type: Boolean,
            default: false,
        },
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
.text-color-black {
    color: #000000;
}
</style>
