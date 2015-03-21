<template>
  <v-expansion-panels accordion v-bind="$attrs" class="document-table" v-model="panel" :disabled="disabled" multiple>
    <v-expansion-panel>
      <v-expansion-panel-header class="document-table-header d-flex justify-space-between">
        <span class="document-table-title text-start">{{ title }}</span>
        <template v-slot:actions>
          <v-icon class="document-table-icon"> mdi-chevron-down </v-icon>
        </template>
      </v-expansion-panel-header>
      <v-expansion-panel-content class="document-table-content">
        <nde-data-table :headers="headers" :items="tableData" :itemsPerPage="itemsPerPage" :page="page" dense
          :loading="loadingMyDocument">
          <template slot="item.actions" slot-scope="{ item }">
            <nde-action-buttons @onClick="handleAction" :item="item" />
          </template>
        </nde-data-table>
        <div class="d-flex justify-start">
          <!-- <nde-paging class="nde-document-table-paging" v-model="page" :length="pageCount" /> -->
          <nde-footer-table :page="page" :pageCount="totalPage" @paging:update="handlePaging"
            :isVisibleExportCSV="isVisibleExportCSV" />
        </div>
      </v-expansion-panel-content>
    </v-expansion-panel>
  </v-expansion-panels>
</template>

<script>
import NdeDataTable from '@components/Table/NdeDataTable.vue';
import NdeActionButtons from './NdeActionButtons.vue';
import NdeFooterTable from '@components/Table/NdeSearchResult/NdeFooterTable.vue';
import { ACTIONS } from '@utils/constants';
import { mapState } from 'vuex';
import * as fileDownload from 'js-file-download';

export default {
  name: 'NdeDocumentTable',

  props: {
    title: {
      type: String,
      default: () => '',
    },

    headers: {
      type: Array,
      default: () => [
        {
          text: 'Name',
          value: 'filename',
          class: 'col-name',
        },
        {
          text: 'Queued At',
          value: 'queued_at',
          class: 'col-queued-at',
        },
        {
          text: 'Program',
          value: 'name',
          class: 'col-program',
        },
        {
          text: 'Format',
          value: 'format',
          class: 'col-status',
        },
        {
          text: '',
          value: 'actions',
          class: 'col-actions',
          sortable: false,
        },
      ],
    },
    data: {
      type: Array,
      default: () => [],
    },
    loadingMyDocument: {
      type: Boolean,
      default: false,
    },
  },

  data() {
    return {
      page: 1,
      itemsPerPage: 20,
      disabled: false,
      isVisibleExportCSV: false,
      tableData: [],
      panel: [0]
    };
  },
  computed: {
    ...mapState(['accesstoken', 'env']),
    totalPage() {
      return Math.ceil(this.data && this.data.length / this.itemsPerPage) || 0;
    },
  },

  methods: {
    handleAction({ action, item }) {
      switch (action) {
        case ACTIONS.DOWNLOAD:
          this.download(item);
          break;
        case ACTIONS.PROMOTE:
          this.promote(item);
          break;
        case ACTIONS.SHARE:
          this.share(item);
          break;
        case ACTIONS.GENERATE:
          this.generate(item);
          break;
        case ACTIONS.DELETE:
          this.delete(item);
          break;
      }
    },
    download(item) {
      console.log('downloading this: ', item);
      var config = {
        method: 'get',
        url:
          this.env.FS_URL + 'single/' +
          item.nuid +
          '?nuid=' +
          item.nuid +
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
          console.log(response);
          let downloadname = item.filename || item.nuid;
          fileDownload(response.data, downloadname);
        })
        .catch((error) => {
          console.log(error);
        });
    },
    promote(item) {
      this.$emit('onPromote', { item });
    },
    share(item) {
      this.$emit('onShare', { item });
    },
    generate(item) {
      this.$emit('onGenerate', { item });
    },
    async delete(item) {
      const confirmed = await this.$swal({
        title: 'Document Delete Confirmation',
        text: 'Are you sure you want to delete this document?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Delete',
        confirmButtonColor: '#c32c39',
        cancelButtonText: 'Cancel',
        cancelButtonColor: '#9a9ea1',
        reverseButtons: true
      });
      if (!confirmed.value) return false;
      this.$store.commit('setShowProgressAPI', true);
      await this.$store.dispatch('callAPI', {
        url: '/deleteDownloadOauth',
        method: 'post',
        data: {
          profile_id: this.$store.state.currentProgram.id,
          download_id: item.id,
        },
      });

      this.$store.commit('setShowProgressAPI', false);
      this.$emit('deleteItem', { item: item });
    },
    handlePaging(page) {
      this.page = page;
    },
  },

  components: {
    NdeDataTable,
    NdeActionButtons,
    NdeFooterTable,
  },

  watch: {
    data: function (val) {
      if (val.length) {
        this.tableData = [];
        this.data.map(item => {
          let arr = item.filename.split(".");
          let extension = arr.pop();
          item.format = extension;
          this.tableData.push(item);
        });

      }
    }
  }
};
</script>

<style lang="scss">
.document-table-header {
  padding-top: 0;
  padding-bottom: 0;
}

.document-table {
  margin: 0 10px;
  width: auto;
}

.document-table-content th {
  border-right-color: white;
  border-right-width: medium;
  background-color: #ebeced;
  color: $darkGreyColor;

  &.col-name {
    width: 44.5%;
  }

  &.col-queued-at {
    width: 22.2%;
  }

  &.col-program {
    width: 15.54%;
  }

  &.col-status {
    width: 8.88%;
  }

  &.col-actions {
    width: 8.88%;
  }
}

.document-table-content .theme--light.v-data-table>.v-data-table__wrapper>table>tbody>tr>td {
  cursor: pointer;
}

.document-table-content .theme--light.v-data-table>.v-data-table__wrapper>table>tbody>tr:not(:last-child)>td:not(.v-data-table__mobile-row) {
  border-bottom: none;
}

.document-table-content .theme--light.v-data-table>.v-data-table__wrapper>table>thead>tr:last-child>th {
  border-bottom: none;
}
</style>
