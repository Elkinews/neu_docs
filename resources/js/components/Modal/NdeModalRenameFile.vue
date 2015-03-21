<template>
    <nde-modal :showModal.sync="showModal" title="Rename File" modalWith="645px" @update:closeModal="hideModal">
        <template v-slot:ndeModalContent>
            <v-row>
                <v-col cols="12" sm="12">
                    <p role="alert" v-if="error" class="rename-file-error" v-html="error"></p>
                </v-col>
                <v-col cols="12" sm="12">
                    <v-text-field label="File Name" solo v-model="file_name"></v-text-field>
                </v-col>
            </v-row>
        </template>

        <template v-slot:ndeModalAction>
            <v-row>
                <v-col>
                    <nde-button-cancel title="Cancel" @click="hideModal" :disabled="isRenaming">
                    </nde-button-cancel>

                    <nde-button-primary title="Rename" @click="renameFile" :loading="isRenaming" :disabled="!file_name">
                    </nde-button-primary>
                </v-col>
            </v-row>
        </template>
    </nde-modal>
</template>
  
<script>
import NdeModal from './NdeModal.vue';
import NdeButtonCancel from '../Button/NdeButtonCancel.vue';
import NdeButtonPrimary from '../Button/NdeButtonPrimary.vue';
export default {
    components: {
        NdeModal,
        NdeButtonPrimary,
        NdeButtonCancel
    },

    data() {
        return {
            file_name: '',
            isRenaming: false,
            error: ''
        };
    },

    props: {
        item: {
            type: Object,
            required: true
        },

        showModal: {
            type: Boolean,
            required: true,
        },
        doc_id: {
            type: String,
            required: true
        }
    },
    methods: {
        hideModal() {
            this.$emit('onClose');
        },

        async renameFile() {
            this.isRenaming = true;
            const resultRename = await this.$store.dispatch('callAPI', {
                url: '/updatePieceFilenameOauth',
                method: 'post',
                data: {
                    profile_id: this.$store.state.currentProgram.id,
                    document_id: this.doc_id,
                    nuid: this.item.nuid,
                    new_name: this.file_name
                }
            });

            this.isRenaming = false;

            console.log(resultRename)
            if(resultRename.message == 'success') {
                this.$emit('onSuccess');
            }
        }

    },

    watch: {
        showModal: function (val) {
            this.file_name = this.item.name;
        },
    }
};
</script>
  
<style scoped lang="scss">
.add-folder-error {
    color: $errorColor !important;
}
</style>
  