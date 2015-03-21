<template>
    <nde-modal :showModal.sync="showModal" title="Rename Folder" modalWith="645px" @update:closeModal="hideModal"
        :classes="'create-new-tab-modal'">
        <template v-slot:ndeModalContent>
            <v-row>
                <v-col cols="12" sm="12">
                    <!-- <v-combobox solo attach hide-details :items="folders" v-model="folderName"
                        label="Select a new folder name or input." @keyup.enter="addNewFolder">
                    </v-combobox> -->
                    <v-text-field label="Input the folder name" placeholder="Folder Name" solo
                        @keyup.enter="renameFolder" ide-details v-model="folderName"></v-text-field>
                </v-col>
            </v-row>
        </template>

        <template v-slot:ndeModalAction>
            <v-row>
                <v-col>
                    <nde-button-primary title="Add Folder" @click="renameFolder" :loading="isRenaming"
                        :disabled="!folderName || folderName == folder.folder_name">
                    </nde-button-primary>
                </v-col>
            </v-row>
        </template>
    </nde-modal>
</template>
  
<script>
import { mapState } from 'vuex';
import NdeModal from './NdeModal.vue';
import NdeButtonPrimary from '../Button/NdeButtonPrimary.vue';
export default {
    components: {
        NdeModal,
        NdeButtonPrimary,
    },

    data() {
        return {
            folderName: '',
            isRenaming: false,
        };
    },

    props: {
        showModal: {
            type: Boolean,
            required: true,
        },
        doc_id: {
            type: String,
            default: '',
            required: true,
        },
        folder: {
            type: Object,
            required: true
        }
    },
    methods: {
        hideModal() {
            this.$emit('onClose');
        },

        async renameFolder() {
            if(this.folderName != this.folder.folder_name) {
                this.isRenaming = true;
                let param = {
                    profile_id: this.$store.state.currentProgram.id,
                    folder_id: this.folder.folder_id,
                    document_id: this.doc_id,
                    new_name: this.folderName
                };

                const resFolder = await this.$store.dispatch('callAPI', {
                    url: '/renameFolderOauth',
                    method: 'post',
                    data: param,
                });

                this.isRenaming = false;
                if(resFolder.message == 'success') {
                    this.$emit('onRenamed', {new_name: this.folderName});
                } else {

                }
                
            } else {
                this.error = 'Please rename the folder name!';
            }
            
        }

    },

    mounted() {
        this.folderName = this.folder.folder_name;
    },

    computed: {

    },
};
</script>
  
<style scoped lang="scss">

</style>
  