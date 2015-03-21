<template>
    <nde-modal :showModal.sync="showModal" title="Promote Tab: Indexing Fields">
        <template v-slot:ndeModalContent>
            <nde-form-indexing-fields :item="item"></nde-form-indexing-fields>
        </template>
        <template v-slot:ndeModalAction>
            <v-row class="mb-3">
                <v-col cols="12" md="6">
                    <nde-button-primary title="Promote" @click="hideModal">
                    </nde-button-primary>
                </v-col>
                <v-col cols="12" md="6" style="text-align: right">
                    <nde-button-cancel @click="hideModal"> </nde-button-cancel>
                </v-col>
            </v-row>
        </template>
    </nde-modal>
</template>

<script>
import NdeModal from "./NdeModal.vue";
import NdeFormIndexingFields from "../Form/NdeFormIndexingFields.vue";
import NdeButtonPrimary from "../Button/NdeButtonPrimary.vue";
import NdeButtonCancel from "../Button/NdeButtonCancel.vue";

export default {
    name: "NdeModalPromoteTabIndexingFields",
    components: {
        NdeModal,
        NdeFormIndexingFields,
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
            // TODO: Will delete when there is data get from API
            item: {
                fileNumber: "5226",
                date: "2020-12-25",
                tradeName: "3911 regression A",
                fromItem: "Animal(s)",
                toItem: "Car Color",
                courtDate: "2020-12-30",
            },
        };
    },
    methods: {
        hideModal() {
            this.$emit("update:showModal", !this.showModal);
        },
    },
};
</script>
