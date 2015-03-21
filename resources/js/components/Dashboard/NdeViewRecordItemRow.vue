<template>
  <div>
    <div
      :class="tableData.box_type === 'B' && 'clickable-tr'"
      class="d-flex mb-3 nde-view-record-item-row"
    >
      <div
        class="text-left nde-view-record-item-name"
        style="width: 40%"
        @click="onClickView()"
        tabindex="0"
        :aria-label="item.name"
      >
        <div class="text-overflow-space">
          <v-icon class="pl-6" v-if="canViewFiles"> mdi-television-play </v-icon>
          <v-icon class="pl-6"></v-icon>
          <span>{{ item.name }}</span>
        </div>
      </div>
      <div
        class="text-left"
        style="width: 15%"
        @click="onClickView()"
        tabindex="0"
        :aria-label="item.format"
      >
        {{ item.format }}
      </div>
      <div
        class="text-left"
        style="width: 20%"
        @click="onClickView()"
        tabindex="0"
        :aria-label="formatDate(item.uploaded_on)"
      >
        {{ formatDate(item.uploaded_on) }}
      </div>
      <div class="d-flex align-center" style="width: 25%; justify-content: end">
        <!-- History Button -->
        <!-- v-if="item.has_history" -->

        <div
          class="mr-6"
          @click="handleGetHistory()"
          :loading="isGettingHistory"
          tabindex="0"
          aria-label="history"
        >
          <v-icon>mdi-history</v-icon>
        </div>
        <!-- Action Menu -->
        <nde-view-record-item-row-actions @onClick="onClickAction" :item="item" />
      </div>
    </div>

    <div v-if="historyFiles.length && isLoadedHistory && isHistoryExpand">
      <v-simple-table class="mt-3 mb-5" role="table">
        <template v-slot:default>
          <thead role="rowgroup">
            <tr role="row">
              <th
                role="columnheader"
                tabindex="0"
                aria-label="Name"
                class="text-left"
                style="width: 40%"
              >
                Name
              </th>
              <th
                role="columnheader"
                tabindex="0"
                aria-label="Format"
                class="text-left"
                style="width: 10%"
              >
                Format
              </th>
              <th
                role="columnheader"
                tabindex="0"
                aria-label="Version"
                class="text-left"
                style="width: 5%"
              >
                Version
              </th>
              <th
                role="columnheader"
                tabindex="0"
                aria-label="Uploaded On"
                class="text-left"
                style="width: 20%"
              >
                Uploaded On
              </th>
              <th role="columnheader" tabindex="0" aria-label="Uploaded By" class="text-left">
                Uploaded By
              </th>
            </tr>
          </thead>
          <tbody role="rowgroup">
            <tr
              role="row"
              class="nde-view-record-item-row"
              v-for="(historyItem, index) in historyFiles"
              :key="index"
              @click="onClickViewHistoryItem(historyItem)"
            >
              <td role="cell" tabindex="0" :aria-label="historyItem.name" class="text-left">
                <v-icon class="mr-3" v-if="canViewFiles"> mdi-television-play </v-icon>
                {{ historyItem.name }}
              </td>
              <td role="cell" tabindex="0" :aria-label="historyItem.format" class="text-left">
                {{ historyItem.format }}
              </td>
              <td role="cell" tabindex="0" :aria-label="historyItem.version" class="text-left">
                {{ historyItem.version }}
              </td>
              <td
                role="cell"
                tabindex="0"
                :aria-label="formatDate(historyItem.uploaded_on)"
                class="text-left"
              >
                {{ formatDate(historyItem.uploaded_on) }}
              </td>
              <td role="cell" tabindex="0" :aria-label="historyItem.uploaded_by" class="text-left">
                {{ historyItem.uploaded_by }}
              </td>
            </tr>
          </tbody>
        </template>
      </v-simple-table>
    </div>

    <nde-view-record-item-view-doc
      :doc_id="tableData.doc_id"
      :nuid="selectedItem.nuid"
      v-if="
        selectedItem &&
        selectedItem.document_type == 'SUPPORTING DOCUMENT' &&
        !isCopyDocument &&
        !isDownload &&
        !isDelete
      "
      @onClose="close"
      :filename="selectedItem.name"
      :tabname="tabname"
      @onRefreshData="refreshData"
    ></nde-view-record-item-view-doc>

    <nde-view-record-item-view-media
      v-if="
        selectedItem &&
        (selectedItem.document_type == 'VIDEO' || selectedItem.document_type == 'AUDIO') &&
        !isCopyDocument &&
        !isDownload &&
        !isDelete &&
        (selectedItem.original_qid ||
          selectedItem.high_qid ||
          selectedItem.low_qid ||
          selectedItem.medium_qid)
      "
      :selectedItem="selectedItem"
      @onClose="close"
    ></nde-view-record-item-view-media>

    <nde-modal-rename-file
      :showModal="isRenameModal"
      @onClose="isRenameModal = false"
      :item="item"
      :doc_id="tableData.doc_id"
      @onSuccess="onRenamed"
    ></nde-modal-rename-file>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import * as fileDownload from 'js-file-download';
import NdeViewRecordItemViewDoc from './NdeViewRecordItemViewDoc.vue';
import NdeViewRecordItemViewMedia from './NdeViewRecordItemViewMedia.vue';
import NdeViewRecordItemRowActions from './NdeViewRecordItemRowActions.vue';
import NdeModalRenameFile from '../Modal/NdeModalRenameFile.vue';
import moment from 'moment';
export default {
  components: {
    NdeViewRecordItemViewDoc,
    NdeViewRecordItemViewMedia,
    NdeViewRecordItemRowActions,
    NdeModalRenameFile,
  },
  data() {
    return {
      isHistory: false,
      isCopyDocument: false,
      isDownload: false,
      isDelete: false,
      selectedItem: null,
      isGettingHistory: false,
      isLoadedHistory: false,
      historyFiles: [],
      isHistoryExpand: true,
      authRoles: [],
      isRenameModal: false,
    };
  },

  props: {
    item: {
      type: Object,
      require: true,
    },
    tableData: {
      type: Object,
      required: true,
    },
    tabname: {
      type: String,
      required: true,
    },
  },

  computed: {
    ...mapState([
      'isModalUploadDocument',
      'currentImageId',
      'currentUploadBoxType',
      'accesstoken',
      'env',
    ]),
    canViewFiles() {
      if (this.item.document_type === 'SUPPORTING DOCUMENT') {
        return true;
      }
      if (
        (this.item.document_type == 'VIDEO' || this.item.document_type == 'AUDIO') &&
        (this.item.original_qid || this.item.high_qid || this.item.low_qid || this.item.medium_qid)
      ) {
        return true;
      }
      return false;
    },
  },

  mounted() {
    this.authRoles = this.userLoginRoles();
  },

  methods: {
    formatDate(date) {
      const format = 'YYYY-MM-DD hh:mm A';
      return moment(date).format(format);
    },
    refreshData() {
      this.$emit('refreshTab');
    },

    onClickAction(action) {
      if (action === 'Copy To My Documents') {
        this.copyToMyDocument();
      } else if (action === 'Replace') {
        this.replace();
      } else if (action === 'Download') {
        this.downloadRecord();
      } else if (action === 'Delete') {
        this.deletePiece();
      } else if (action == 'Copy File') {
        this.$store.commit('setCopyFileOrgNuid', {
          ...this.item,
          box_type: this.tableData.box_type,
        });
      } else if (action == 'Cancel Copy File') {
        this.$store.commit('setCopyFileOrgNuid', null);
      } else if (action == 'Rename File') {
        this.onRenameFile();
      }
    },

    onClickView() {
      console.log('click view item.....');
      this.selectedItem = this.item;
      if (
        this.item.nuid &&
        (this.item.document_type === 'VIDEO' || this.item.document_type === 'AUDIO')
      ) {
        if (
          !this.item.original_qid &&
          !this.item.high_qid &&
          !this.item.low_qid &&
          !this.item.medium_qid &&
          !this.isHistory
        ) {
          alert('Only available for downloading.');
        }
      }
    },

    onClickViewHistoryItem(item) {
      this.selectedItem = item;

      if (item.nuid && (item.document_type === 'VIDEO' || item.document_type === 'AUDIO')) {
        if (!item.original_qid && !item.high_qid && !item.low_qid && !item.medium_qid) {
          alert('Only available for downloading.');
        }
      }
    },

    async handleGetHistory() {
      this.isHistory = true;
      if (this.isLoadedHistory) {
        this.isHistoryExpand = !this.isHistoryExpand;
        return;
      }
      this.isGettingHistory = true;

      const resultGetHistory = await this.$store.dispatch('callAPI', {
        url: '/getTabFilesHistoryOauth',
        method: 'post',
        data: {
          profile_id: this.$store.state.currentProgram.id,
          bookmark_id: this.item.bookmark_id,
          page: 1,
          page_size: 100,
        },
      });

      this.isGettingHistory = false;
      this.isLoadedHistory = true;

      if (resultGetHistory.message == 'success') {
        this.historyFiles = resultGetHistory.data?.data?.files || [];
      }
      this.selectedItem = null;
      this.isHistory = false;
    },

    async cloneFIle() {
      let cloneUuid = '';
      let data = new FormData();
      data.append('profileId', this.$store.state.currentProgram.id);
      data.append('transcode', true);
      const config = {
        method: 'post',
        url: this.env.FS_URL + `single/${this.item.nuid}/clone`,
        headers: {
          Authorization: 'Bearer ' + this.accesstoken,
        },
        data: data,
      };

      this.$store.dispatch('increaseCallCount');

      await axios(config)
        .then((response) => {
          cloneUuid = response?.data?.nuid;
        })
        .catch((error) => {
          console.log(error);
        });

      return cloneUuid;
    },

    async copyToMyDocument() {
      const newNuid = await this.cloneFIle();
      this.isCopyDocument = true;
      const copiedDocument = await this.$store.dispatch('callAPI', {
        url: '/copyToMyDocumentsOauth',
        method: 'post',
        data: {
          profile_id: this.$store.state.currentProgram.id,
          new_nuid: newNuid,
          old_nuid: this.item.nuid,
        },
      });
      if (copiedDocument.message !== 'success') {
        return this.$swal({
          icon: 'error',
          text: this.getMessageResult(copiedDocument),
        });
      }
      await this.$swal({
        icon: 'success',
        text: this.getMessageResult(copiedDocument),
      });
      console.log('copy document: ', Object.keys(copiedDocument));
      this.selectedItem = null;
      this.isCopyDocument = false;
    },

    async deletePiece() {
      this.isDelete = true;
      const confirmed = await this.$swal({
        title: 'File Delete Confirmation',
        text: 'Are you sure you want to delete this file?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Delete',
        confirmButtonColor: '#c32c39',
        cancelButtonText: 'Cancel',
        cancelButtonColor: '#9a9ea1',
        reverseButtons: true,
      });
      if (!confirmed.value) return false;
      this.$store.commit('setShowProgressAPI', true);
      const resultDeletePieceOauth = await this.$store.dispatch('callAPI', {
        url: '/deletePieceOauth',
        method: 'post',
        data: {
          profile_id: this.$store.state.currentProgram.id,
          nuid: this.item.nuid,
        },
      });

      this.$store.commit('setShowProgressAPI', false);

      if (resultDeletePieceOauth.message == 'success') {
        this.$emit('refreshTab');
      }
      this.selectedItem = null;
      this.isDelete = false;
    },

    async downloadRecord() {
      this.isDownload = true;
      var config = {
        method: 'get',
        url:
          this.env.FS_URL +
          'single/' +
          this.item.nuid +
          '?nuid=' +
          this.item.nuid +
          '&profileId=' +
          this.$store.state.currentProgram.id,
        headers: {
          Authorization: 'Bearer ' + this.accesstoken,
        },
        responseType: 'blob',
      };

      this.$store.dispatch('increaseCallCount');

      axios(config)
        .then((response) => {
          let downloadname = this.item.name || this.item.nuid;
          fileDownload(response.data, downloadname);
        })
        .catch((error) => {
          console.log(error);
        });
      this.selectedItem = null;
      this.isDownload = false;
    },

    close() {
      this.selectedItem = null;
    },

    async replace() {
      this.$store.commit('setShowProgressAPI', true);

      this.$store.dispatch('increaseCallCount');

      await axios
        .post('/getFileLockStatusOauth', {
          profile_id: this.$store.state.currentProgram.id,
          nuid: this.item.nuid,
        })
        .then(async (response) => {
          const isLocked = response?.data?.data?.data?.is_locked;
          if (isLocked) {
            const lockedBy = response?.data?.data?.data?.locked_by;
            this.$store.commit('setShowProgressAPI', false);
            return this.$swal({
              icon: 'error',
              text: 'This file can not be edited, it is currently locked by ' + lockedBy,
            });
          } else {
            this.$store.commit('setShowProgressAPI', false);
            this.$store.commit('openModalUploadDoucmentForReplace', {
              imageId: this.tableData.doc_id,
              box_type: this.tableData.box_type,
              old_nuid: this.item.nuid,
              name: this.item.name,
            });
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },

    onRenameFile() {
      this.isRenameModal = true;
    },

    onRenamed() {
      this.isRenameModal = false;
      this.$emit('refreshTab');
    },
  },
};
</script>

<style lang="scss" scoped>
.nde-view-record-item-row {
  cursor: pointer;
}

.text-overflow-space {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;

  &:hover {
    overflow: visible;
    white-space: unset;
    word-break: break-all;
  }
}

@media screen and (max-width: 48rem) {
  .nde-view-record-item-row {
    .nde-view-record-item-name {
      width: 25% !important;
    }
  }
}
</style>
