<template>
    <nde-modal :showModal.sync="showModal" title="Add Folder" modalWith="645px" @update:closeModal="hideModal"
        :classes="'create-new-tab-modal'">
        <template v-slot:ndeModalContent>
            <v-row>
                <v-col cols="12" sm="12">
                    <p role="alert" v-if="error" class="add-folder-error" v-html="error"></p>
                </v-col>
                <v-col cols="12" sm="12">
                    <v-combobox solo attach hide-details :items="folders" v-model="folderName"
                        label="Select the predefined folder or Enter custom folder name." @keyup.enter="addNewFolder"
                        :search-input.sync="search" ref="combobox">
                        <template v-slot:no-data>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>
                                        <p>
                                            No results matching "<strong>{{ search }}</strong>".
                                        </p>
                                        <p>Press <kbd>enter</kbd> to create <strong>{{ search }}</strong> folder.</p>
                                    </v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                        </template>
                    </v-combobox>
                </v-col>
            </v-row>
        </template>

        <template v-slot:ndeModalAction>
            <v-row>
                <v-col>
                    <nde-button-primary title="Add Folder" @click="addNewFolder" :loading="isAdding"
                        :disabled="!folderName">
                    </nde-button-primary>
                </v-col>
            </v-row>
        </template>
    </nde-modal>
</template>
  
<script>
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
            isAdding: false,
            error: ''
        };
    },

    props: {
        folders: {
            type: Array,
            defaul: []
        },
        showModal: {
            type: Boolean,
            required: true,
        },
        doc_id: {
            type: String,
            default: '',
            required: true,
        },
        parent_folder_id: {
            type: String,
            default: ''
        },
        existFolders: {
            type: Array,
            defaul: []
        }
    },
    methods: {

        hideModal() {
            this.$emit('onClose');
        },

        async addNewFolder() {
            if(this.error) {
                await this.$swal({
                    title: 'Error!',
                    text: this.error,
                    type: 'warning',
                    showCancelButton: false,
                    confirmButtonText: 'Ok',
                });
                return;
            }
            this.isAdding = true;
            let param = {
                profile_id: this.$store.state.currentProgram.id,
                folder_name: this.folderName,
                parent_folder_id: this.parent_folder_id,
                document_id: this.doc_id
            };

            const resFolder = await this.$store.dispatch('callAPI', {
                url: '/addFolderOauth',
                method: 'post',
                data: param,
            });
            this.isAdding = false;

            if (resFolder.message == 'success') {
                this.$emit('onAdded');
            } else {
                await this.$swal({
                    title: 'Error!',
                    text: "Please try again!",
                    type: 'warning',
                    showCancelButton: false,
                    confirmButtonText: 'Ok',
                });
                return;
            }
        }

    },

    watch: {
        showModal: function (val) {
            this.folderName = '';
        },

        folderName: function (val) {
            this.error = '';
            if (this.existFolders && this.existFolders.length) {
                this.existFolders.map(folder => {
                    if (folder.folder_name == val) {
                        this.error = "This folder name already exists."
                    }
                })
            }
        }
    }
};
</script>
  
<style scoped lang="scss">
.add-folder-error {
    color: $errorColor !important;
}
</style>
  