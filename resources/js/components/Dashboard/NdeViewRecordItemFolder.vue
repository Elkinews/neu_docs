<template>
  <div>
    <div class="d-flex align-center mb-3">
      <div
        class="d-flex align-center mr-6 folder-name"
        @click="onClickFolder"
        role="button"
        tabindex="0"
        :aria-label="folder.folder_name"
        :aria-expanded="isExpand ? 'true' : 'false'"
      >
        <v-icon v-if="!isLoading">{{ isExpand ? 'mdi-menu-down' : 'mdi-menu-right' }}</v-icon>
        <v-progress-circular v-if="isLoading" indeterminate color="primary"></v-progress-circular>
        <v-icon>mdi-folder</v-icon>
        <span class="ml-6">{{ folder.folder_name }}</span>
      </div>

      <v-menu offset-y>
        <template v-slot:activator="{ attrs, on }">
          <v-btn text icon v-bind="attrs" v-on="on">
            <v-icon dark> mdi-menu </v-icon>
          </v-btn>
        </template>

        <v-list>
          <v-list-item link @click="onScanClick" v-if="authRoles.includes('SCAN')">
            <v-list-item-icon>
              <v-icon>mdi-scanner</v-icon>
            </v-list-item-icon>
            <v-list-item-title>Scan</v-list-item-title>
          </v-list-item>

          <v-list-item link @click="onUploadClick" v-if="authRoles.includes('EUPLOAD')">
            <v-list-item-icon>
              <v-icon>mdi-cloud-upload</v-icon>
            </v-list-item-icon>
            <v-list-item-title>Upload</v-list-item-title>
          </v-list-item>

          <v-list-item link @click="onAddFolder">
            <v-list-item-icon>
              <v-icon>mdi-folder</v-icon>
            </v-list-item-icon>
            <v-list-item-title>Add Folder</v-list-item-title>
          </v-list-item>

          <v-list-item link @click="onDeleteFolder">
            <v-list-item-icon>
              <v-icon>mdi-delete-forever</v-icon>
            </v-list-item-icon>
            <v-list-item-title>Delete Folder</v-list-item-title>
          </v-list-item>

          <v-list-item link @click="onRenameFoler">
            <v-list-item-icon>
              <v-icon>mdi-folder-edit-outline</v-icon>
            </v-list-item-icon>
            <v-list-item-title>Rename Folder</v-list-item-title>
          </v-list-item>

          <v-list-item link @click="onPasteFile" v-if="currentCopyFileOrg">
            <v-list-item-icon>
              <v-icon>mdi-content-paste</v-icon>
            </v-list-item-icon>
            <v-list-item-title>Paste File</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>
    </div>
    <nde-modal-add-folder
      :showModal="isShowAddFolder"
      :folders="suggestionFolders"
      :existFolders="folders"
      :doc_id="imageId"
      :parent_folder_id="folder.folder_id"
      @onClose="onCloseAddFolderModal"
      @onAdded="onAddedFolder"
    >
    </nde-modal-add-folder>

    <nde-modal-rename-folder
      :showModal="isRenameFolderModal"
      :doc_id="imageId"
      @onClose="onCloseRenameFolderModal"
      @onRenamed="onRenamedFolder"
      :folder="folder"
    >
    </nde-modal-rename-folder>

    <div v-if="isExpand && (filesList.length || folders.length)" class="header-view-record pl-12">
      <div class="pl-3">
        <nde-view-record-item-folder
          v-for="(item, index) in folders"
          :key="index"
          :folder="item"
          :authRoles="authRoles"
          :doc_id="imageId"
          :tableData="tableData"
          @onDeletedFolder="onDeletedFolder"
        >
        </nde-view-record-item-folder>
        <nde-view-record-item-row
          v-for="(item, index) in filesList.slice().reverse()"
          :key="index"
          :item="item"
          :tableData="tableData"
          :tabname="tabName"
          @onDeleted="onDeleted"
        ></nde-view-record-item-row>
        <div class="text-center mt-6" v-if="btnShow">
          <v-btn
            text
            color="primary"
            @click="loadMore()"
            :loading="isLoading"
            :disabled="total_files == filesList.length"
            >VIEW MORE
          </v-btn>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import NdeViewRecordItemRow from './NdeViewRecordItemRow.vue';
import NdeModalAddFolder from '../Modal/NdeModalAddFolder.vue';
import NdeModalRenameFolder from '../Modal/NdeModalRenameFolder.vue';

export default {
  name: 'nde-view-record-item-folder',
  components: { NdeViewRecordItemRow, NdeModalAddFolder, NdeModalRenameFolder },
  data() {
    return {
      isShowAddFolder: false,
      suggestionFolders: [],

      isLoading: false,
      page: 1,
      page_length: 0,
      page_size: 5,
      isLoaded: false,
      filesList: [],
      folders: [],
      total_files: 0,
      fileData: null,

      isExpand: false,
      imageId: '',
      isRenameFolderModal: false,

      cloneUuid: '',
    };
  },

  props: {
    folder: {
      type: Object,
      required: true,
    },
    authRoles: {
      type: Array,
      required: true,
    },
    doc_id: {
      type: String,
      required: true,
    },
    tabname: {
      type: String,
      required: true,
      default: '',
    },
    tableData: {
      type: Object,
      required: true,
    },
  },

  computed: {
    ...mapState([
      'isModalUploadDocument',
      'currentImageId',
      'currentUploadFolderId',
      'isModalUploadDocumentHasNewFile',
      'currentCopyFileOrg',
      'accesstoken',
      'currentProgram',
      'env',
    ]),
    btnShow() {
      if (!this.isLoaded) {
        return true;
      }

      if (this.total_files == this.filesList.length) {
        return false;
      }

      if (this.fileData.files.length) {
        return true;
      }

      return false;
    },
  },

  methods: {
    onScanClick() {
      this.$store.commit('openScanModal', this.folder.folder_id);
    },

    async onUploadClick() {
      if (!this.isLoaded) {
        this.filesList = [];
        this.folders = [];
        this.page = 1;
        this.total_files = 0;
        await this.loadFiles();
      }
      this.$store.commit('openModalUploadDoucment', {
        imageId: this.imageId,
        box_type: this.tableData.box_type,
        folder_id: this.folder.folder_id,
      });
    },

    onClickFolder() {
      this.isExpand = !this.isExpand;
      this.getFolderFiles();
    },

    getFolderFiles() {
      if (!this.isLoaded) {
        this.filesList = [];
        this.folders = [];
        this.page = 1;
        this.total_files = 0;
        this.loadFiles();
      }
    },

    async loadFiles() {
      this.isLoading = true;

      const resultFiles = await this.$store.dispatch('callAPI', {
        url: '/getTabFilesOauth',
        method: 'post',
        data: {
          profile_id: this.$store.state.currentProgram.id,
          image_id: this.doc_id,
          page: this.page,
          page_size: this.page_size,
          folder_id: this.folder.folder_id,
        },
      });

      this.isLoading = false;
      this.isLoaded = true;
      this.isExpand = true;

      if (resultFiles.message == 'success') {
        const keys = Object.keys(resultFiles?.data?.data);
        this.folders = resultFiles?.data?.data?.folders;
        this.imageId = resultFiles?.data?.data?.image_id;
        if (keys.length) {
          this.fileData = resultFiles.data.data;
          if (this.fileData?.files?.length) {
            this.page_length = Math.ceil(this.fileData?.total_files / this.page_size);
            this.total_files = this.fileData?.total_files;
            this.filesList = this.filesList.concat(this.fileData?.files);
          }
        }
      }
    },

    async onAddFolder() {
      if (!this.isLoaded) {
        await this.loadFiles();
      }

      const param = {
        profile_id: this.$store.state.currentProgram.id,
        level_no: 2 + parseInt(this.folder.level_no),
      };

      const resFolders = await this.$store.dispatch('callAPI', {
        url: '/getFolderPredefinedOauth',
        method: 'post',
        data: param,
      });

      console.log(resFolders);
      if (resFolders.message == 'success') {
        this.suggestionFolders = resFolders.data.data.folders;
        if (this.folders && this.folders.length) {
          this.folders.map((folder) => {
            this.suggestionFolders = this.suggestionFolders.filter(
              (s_folder) => s_folder != folder.folder_name,
            );
          });
        }

        this.isShowAddFolder = true;
      } else {
        this.isShowAddFolder = false;
        this.suggestionFolders = [];
      }
    },

    onCloseAddFolderModal() {
      this.isShowAddFolder = false;
    },

    onAddedFolder() {
      this.isShowAddFolder = false;
      this.filesList = [];
      this.folders = [];
      this.page = 1;
      this.total_files = 0;
      this.loadFiles();
    },

    async onDeleteFolder() {
      const confirmed = await this.$swal({
        title: 'Delete ' + this.folder.folder_name,
        text: 'Are you sure you want to delete ' + this.folder.folder_name + '?',
        type: 'warning',
        customClass: 'nde-delete-tab',
        showCancelButton: true,
        confirmButtonText: 'Delete',
        confirmButtonColor: '#c32c39',
        cancelButtonText: 'Cancel',
        cancelButtonColor: '#9a9ea1',
        reverseButtons: true,
      });

      console.log(confirmed.value);
      if (confirmed.value) {
        this.onHandleDeleteFolder();
      }
    },

    async onHandleDeleteFolder() {
      if (!this.isLoaded) {
        this.filesList = [];
        this.folders = [];
        this.page = 1;
        this.total_files = 0;
        await this.loadFiles();
      }
      const resultDelete = await this.$store.dispatch('callAPI', {
        url: '/deleteFolderOauth',
        method: 'post',
        data: {
          profile_id: this.$store.state.currentProgram.id,
          folder_id: this.folder.folder_id,
          document_id: this.imageId,
        },
      });

      console.log(resultDelete);
      this.$emit('onDeletedFolder');
    },

    onDeletedFolder() {
      this.isShowAddFolder = false;
      this.folders = [];
      this.filesList = [];
      this.page = 1;
      this.total_files = 0;
      this.loadFiles();
    },

    async onRenameFoler() {
      if (!this.isLoaded) {
        this.filesList = [];
        this.folders = [];
        this.page = 1;
        this.total_files = 0;
        await this.loadFiles();
      }

      this.isRenameFolderModal = true;
    },

    onCloseRenameFolderModal() {
      this.isRenameFolderModal = false;
    },

    onRenamedFolder(data) {
      this.folder.folder_name = data.new_name;
      this.isRenameFolderModal = false;
      this.folders = [];
      this.filesList = [];
      this.page = 1;
      this.total_files = 0;
      this.loadFiles();
    },

    async onPasteFile() {
      if (!this.isLoaded) {
        this.filesList = [];
        this.folders = [];
        this.page = 1;
        this.total_files = 0;
        await this.loadFiles();
      }
      await this.cloneFIle();
      console.log(this.cloneUuid);

      if (!this.cloneUuid) {
        await this.$swal({
          title: 'Clone Error!',
          text: 'Please try again!',
          type: 'warning',
          showCancelButton: false,
          confirmButtonText: 'Ok',
        });
        return;
      }

      this.$store.dispatch('increaseCallCount');

      await axios
        .post('/uploadFileMetadataOauth', {
          profileId: this.currentProgram.id,
          boxType: this.currentCopyFileOrg.box_type,
          nuid: this.cloneUuid,
          filePath: this.currentCopyFileOrg.name,
          docId: this.imageId,
          folder_id: this.folder.folder_id,
        })
        .then(async (response) => {
          console.log(response);
          await this.$swal({
            title: 'File pasted successfully',
            type: 'success',
            showCancelButton: false,
            confirmButtonText: 'Ok',
          });

          this.$store.commit('setCopyFileOrgNuid', null);

          this.filesList = [];
          this.folders = [];
          this.page = 1;
          this.total_files = 0;

          await this.loadFiles();
        })
        .catch(async (error) => {
          console.log(error);
          await this.$swal({
            title: 'Update UploadFile Error!',
            text: 'Please try again!',
            type: 'warning',
            showCancelButton: false,
            confirmButtonText: 'Ok',
          });
        });
    },

    async cloneFIle() {
      try {
        let data = new FormData();
        data.append('profileId', this.$store.state.currentProgram.id);
        data.append('transcode', true);
        const config = {
          method: 'post',
          url: this.env.FS_URL + `single/${this.currentCopyFileOrg.nuid}/clone`,
          headers: {
            Authorization: 'Bearer ' + this.accesstoken,
          },
          data: data,
        };

        this.$store.dispatch('increaseCallCount');

        await axios(config)
          .then((response) => {
            this.cloneUuid = response?.data?.nuid;
          })
          .catch(async (error) => {
            console.log(error);
            await this.$swal({
              title: 'Clone Error!',
              text: 'Please try again!',
              type: 'warning',
              showCancelButton: false,
              confirmButtonText: 'Ok',
            });
          });
      } catch (e) {
        console.log(e);
        await this.$swal({
          title: 'Update UploadFile Error!',
          text: 'Please try again!',
          type: 'warning',
          showCancelButton: false,
          confirmButtonText: 'Ok',
        });
      }
    },
  },

  watch: {
    isModalUploadDocument: function (val) {
      if (!val && this.isModalUploadDocumentHasNewFile && this.currentUploadFolderId) {
        if (
          this.imageId == this.currentImageId &&
          this.currentUploadFolderId == this.folder.folder_id
        ) {
          this.filesList = [];
          this.folders = [];
          this.page = 1;
          this.total_files = 0;
          this.loadFiles();
        }
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.folder-name {
  cursor: pointer;
}
</style>
