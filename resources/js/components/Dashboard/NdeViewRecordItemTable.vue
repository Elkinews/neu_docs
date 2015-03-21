<template>
  <div :class="{ 'pl-10 pr-6': !isMobile }" v-if="isExpand">
    <v-row>
      <v-col class="d-flex">
        <v-icon class="mr-2">{{ getIcon }}</v-icon>
        <span
          tabindex="0"
          :aria-label="`${tableData.description} -  ${tabName}`"
          class="d-flex align-center mr-3"
          >{{ tableData.description }} - {{ tabName }}</span
        >

        <v-menu offset-y>
          <template v-slot:activator="{ attrs, on }">
            <v-btn text icon v-bind="attrs" v-on="on" tabindex="0" role="button">
              <v-icon dark> mdi-menu </v-icon>
            </v-btn>
          </template>

          <v-list>
            <v-list-item link @click="onScanClick" v-if="authRoles.includes('SCAN') && !isMobile">
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

            <v-list-item link @click="onDeleteTab" v-if="authRoles.includes('DELETETAB')">
              <v-list-item-icon>
                <v-icon>mdi-delete-forever</v-icon>
              </v-list-item-icon>
              <v-list-item-title>Delete</v-list-item-title>
            </v-list-item>

            <v-list-item link @click="onAddFolder">
              <v-list-item-icon>
                <v-icon>mdi-folder</v-icon>
              </v-list-item-icon>
              <v-list-item-title>Add Folder</v-list-item-title>
            </v-list-item>

            <v-list-item link @click="onPasteFile" v-if="currentCopyFileOrg">
              <v-list-item-icon>
                <v-icon>mdi-content-paste</v-icon>
              </v-list-item-icon>
              <v-list-item-title>Paste File</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
      </v-col>
    </v-row>

    <nde-modal-add-folder
      :showModal="isShowAddFolder"
      :folders="suggestionFolders"
      :doc_id="imageId"
      :existFolders="folders"
      @onClose="onCloseAddFolderModal"
      @onAdded="onAddedFolder"
    ></nde-modal-add-folder>

    <h5
      v-if="!isLoading && !filesList.length && !folders.length"
      class="pa-6 d-flex justify-center"
    >
      No Files Uploaded
    </h5>

    <v-row v-if="isLoading && !filesList.length">
      <v-col cols="12" md="12" class="d-flex justify-center">
        <v-progress-circular :size="50" color="primary" indeterminate></v-progress-circular>
      </v-col>
    </v-row>

    <div v-if="filesList.length || folders.length" class="header-view-record" role="table">
      <div role="rowgroup">
        <div
          class="d-flex mb-5 mt-5"
          style="background-color: #fafafa"
          v-if="filesList.length"
          role="row"
        >
          <div
            class="text-left header-view-record-name nde-viewrecord-col pa-3"
            style="width: 40%"
            @click="sortBy('path')"
            tabindex="0"
            role="columnheader"
            aria-label="Name"
            :aria-sort="order == 'asc' ? 'ascending' : 'descending'"
          >
            Name
            <v-icon v-if="order_by != 'path'">mdi-swap-vertical</v-icon>
            <v-icon v-if="order_by == 'path'" Uploaded On>{{
              order == 'asc' ? 'mdi-sort-ascending' : 'mdi-sort-descending'
            }}</v-icon>
          </div>
          <div
            class="text-left pa-3"
            style="width: 15%"
            tabindex="0"
            role="columnheader"
            aria-label="Format"
          >
            Format
          </div>
          <div
            class="text-left nde-viewrecord-col pa-3"
            style="width: 20%"
            @click="sortBy('created_on')"
            tabindex="0"
            role="columnheader"
            aria-label="Uploaded On"
            :aria-sort="order == 'asc' ? 'ascending' : 'descending'"
          >
            Uploaded On
            <v-icon v-if="order_by != 'created_on'">mdi-swap-vertical</v-icon>
            <v-icon v-if="order_by == 'created_on'">{{
              order == 'asc' ? 'mdi-sort-ascending' : 'mdi-sort-descending'
            }}</v-icon>
          </div>
          <div
            class="text-right pa-3"
            style="width: 25%"
            tabindex="0"
            role="columnheader"
            aria-label="Action"
          >
            Action
          </div>
        </div>
      </div>
      <div class="pa-3">
        <nde-view-record-item-folder
          v-for="(item, index) in folders"
          :key="'nde_view_record_item_folder_' + index"
          :folder="item"
          :authRoles="authRoles"
          :doc_id="imageId"
          :tabname="tabName"
          :tableData="tableData"
          @onDeletedFolder="onDeletedFolder"
        >
        </nde-view-record-item-folder>
        <nde-view-record-item-row
          v-for="(item, index) in filesList.slice().reverse()"
          :key="'nde_view_record_item_row_' + index"
          :item="item"
          :tableData="tableData"
          :tabname="tabName"
          @refreshTab="refreshTab"
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
import NdeViewRecordItemFolder from './NdeViewRecordItemFolder.vue';

export default {
  components: { NdeViewRecordItemRow, NdeModalAddFolder, NdeViewRecordItemFolder },
  data() {
    return {
      currentTabName: '',
      isLoading: false,
      fileData: null,
      page: 1,
      page_length: 0,
      page_size: 5,
      filesList: [],
      folders: [],
      total_files: 0,
      isLoaded: false,
      authRoles: [],
      isDeletingTab: false,
      isShowAddFolder: false,
      suggestionFolders: [],
      imageId: '',
      cloneUuid: '',
      order_by: '',
      order: '',
    };
  },

  props: {
    tableData: {
      type: Object,
      required: true,
    },
    tabName: {
      type: String,
      required: true,
    },
    isExpand: {
      type: Boolean,
      default: false,
    },
    doc_id: {
      type: String,
      default: '',
    },
  },

  computed: {
    ...mapState([
      'isModalUploadDocument',
      'currentImageId',
      'currentUploadBoxType',
      'isScanModal',
      'isModalUploadDocumentHasNewFile',
      'currentUploadFolderId',
      'currentCopyFileOrg',
      'env',
      'accesstoken',
      'currentProgram',
    ]),
    getIcon() {
      return 'mdi-folder-open-outline';
    },

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
    async loadFiles() {
      this.isLoading = true;

      const resultFiles = await this.$store.dispatch('callAPI', {
        url: '/getTabFilesOauth',
        method: 'post',
        data: {
          profile_id: this.$store.state.currentProgram.id,
          image_id: this.tableData.doc_id,
          page: this.page,
          page_size: this.page_size,
          order_by: this.order_by,
          order: this.order,
        },
      });

      this.isLoading = false;
      this.isLoaded = true;

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

    loadMore() {
      this.page++;
      this.loadFiles();
    },

    onScanClick() {
      this.$store.commit('openScanModal', this.tableData.doc_id);
    },

    onUploadClick() {
      this.$store.commit('openModalUploadDoucment', {
        imageId: this.imageId,
        box_type: this.tableData.box_type,
      });
    },

    refreshTab() {
      this.filesList = [];
      this.page = 1;
      this.total_files = 0;
      this.loadFiles();
    },

    async onDeleteTab() {
      const confirmed = await this.$swal({
        title: 'Delete Tab',
        text: 'Are you sure you want to delete ' + this.currentTabName + '?',
        type: 'warning',
        customClass: 'nde-delete-tab',
        showCancelButton: true,
        confirmButtonText: 'Delete',
        confirmButtonColor: '#c32c39',
        cancelButtonText: 'Cancel',
        cancelButtonColor: '#9a9ea1',
        reverseButtons: true,
      });

      if (confirmed.value) {
        this.handleDeleteTab();
      }
    },

    async handleDeleteTab() {
      this.isDeletingTab = true;
      const param = {
        profile_id: this.$store.state.currentProgram.id,
        document_id: this.tableData.doc_id,
      };

      const deletetab_res = await this.$store.dispatch('callAPI', {
        url: '/deleteTabOauth',
        method: 'post',
        data: param,
      });

      this.isDeletingTab = false;

      if (deletetab_res.message == 'success') {
        this.$emit('onDeletedTab');
      } else {
        const confirmed = await this.$swal({
          title: 'Delete Error',
          text: 'Failed deleting ' + this.currentTabName + '. Please try again.',
          type: 'warning',
          showCancelButton: false,
          confirmButtonText: 'Ok',
        });
      }
    },

    async onAddFolder() {
      if (!this.isLoaded) {
        await this.loadFiles();
      }

      const param = {
        profile_id: this.$store.state.currentProgram.id,
        level_no: 1,
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
      this.page = 1;
      this.total_files = 0;
      this.folders = [];
      this.loadFiles();
    },

    onDeletedFolder() {
      this.isShowAddFolder = false;
      this.filesList = [];
      this.page = 1;
      this.total_files = 0;
      this.folders = [];
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
          // folder_id: this.folder.folder_id
        })
        .then(async (response) => {
          console.log(response);
          this.$store.commit('setCopyFileOrgNuid', null);

          await this.$swal({
            title: 'File pasted successfully',
            type: 'success',
            showCancelButton: false,
            confirmButtonText: 'Ok',
          });

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

    async sortBy(field) {
      if (this.order_by == field) {
        if (this.order == 'asc') {
          this.order = 'desc';
        } else {
          this.order = 'asc';
        }
      } else {
        this.order_by = field;
        this.order = 'asc';
      }

      this.filesList = [];
      this.folders = [];
      this.page = 1;
      this.total_files = 0;
      await this.loadFiles();
    },
  },

  mounted() {
    this.currentTabName = this.tableData.description;
    this.authRoles = this.userLoginRoles();
  },

  watch: {
    isExpand: function (val) {
      if (val && !this.isLoaded) {
        this.filesList = [];
        this.folders = [];
        this.loadFiles();
      }
    },
    tableData: {
      handler: function () {
        this.isLoaded = false;
      },
      deep: true,
    },
    isModalUploadDocument: function (val) {
      if (!val && this.isModalUploadDocumentHasNewFile && !this.currentUploadFolderId) {
        if (
          // this.tableData.doc_id == this.currentImageId &&
          // this.tableData.box_type == this.currentUploadBoxType
          this.imageId == this.currentImageId
        ) {
          this.filesList = [];
          this.folders = [];
          this.page = 1;
          this.total_files = 0;
          this.loadFiles();
        }
      }
    },

    isScanModal: function (val) {
      if (!val) {
        if (this.tableData.doc_id == this.currentImageId) {
          this.filesList = [];
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
thead tr,
tbody tr:nth-of-type(even) {
  background-color: $lightGreyColor;
}

tbody tr {
  height: 57px;
}

tbody tr.clickable-tr {
  height: 57px;
  cursor: pointer;
}

@media screen and (max-width: 48rem) {
  .header-view-record {
    min-width: 80rem;

    .header-view-record-name {
      width: 25% !important;
    }
  }
}

.nde-viewrecord-col {
  cursor: pointer;

  &:hover {
    background-color: #e0e0e0;
  }
}
</style>
<style lang="scss">
.nde-delete-tab {
  .swal2-confirm,
  .swal2-cancel {
    width: 6.25rem;
  }
}
</style>
