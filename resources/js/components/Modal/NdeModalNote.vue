<template>
    <nde-modal :showModal.sync="showModal" title="Notes for Page">
        <template v-slot:ndeModalContent>
            <nde-table-note :items="items"></nde-table-note>
            <v-row class="mt-5">
                <v-col cols="12">
                    <v-textarea
                        auto-grow
                        outlined
                        rows="5"
                        row-height="16"
                    ></v-textarea>
                </v-col>
            </v-row>
        </template>
        <template v-slot:ndeModalAction>
            <v-row>
                <v-col cols="12" md="6">
                    <nde-button-cancel
                        style="width: 11.125rem"
                        @click="hideModal"
                    >
                    </nde-button-cancel>
                </v-col>
                <v-col cols="12" md="6" align="right">
                    <nde-button-primary
                        title="Save Notes"
                        @click="hideModal"
                        style="width: 8.625rem"
                    >
                    </nde-button-primary>
                </v-col>
            </v-row>
        </template>
    </nde-modal>
</template>

<script>
import NdeModal from "./NdeModal.vue";
import NdeTableNote from "../Table/NdeTableNote.vue";
import NdeButtonPrimary from "../Button/NdeButtonPrimary.vue";
import NdeButtonCancel from "../Button/NdeButtonCancel.vue";

export default {
    name: "NdeModalNote",
    components: {
        NdeModal,
        NdeTableNote,
        NdeButtonPrimary,
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
            items: [
                {
                    id: 1,
                    created: "2021-11-02 12:53:33 640017",
                    author: "26.07.2020",
                    note: "Test note",
                    extraField: "434",
                },
                {
                    id: 2,
                    created: "2021-11-02 12:53:33 640017",
                    author: "26.07.2020",
                    note: "Test note",
                    extraField: "434",
                },
            ],
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
</style>