<template>
  <v-card class="mt-2" v-show="totalResults">
    <v-card-title>
      <div>
        <h2 class="search-result">Search results: {{ totalResults }}</h2>
      </div>
      <div class="d-md-flex justify-space-between" style="width: 100%">
        <div class="d-flex align-center info-record">
          <p>
            <span>Showing {{ isSearchLoaded ? numberResult : itemPerPage }} of {{ totalResults }} Records </span>
            <span class="ml-3 caption info-record--span__grey"
              >(sort data by clicking on any column heading)</span
            >
          </p>
        </div>
        <nde-table-action
          :selectedColumns="visibleColumns"
          :columns="showableColumns"
          :pageSize="itemPerPage"
          :showSelect="allowSelect"
          @onChangePageSize="handleOnChangePageSize"
          @onChangeVisibleColumn="handleOnChangeVisibleColumn"
          @onChangeBulkOption="handleOnChangeBulkOption"
          @onCancelSelect="handleCancelSelect"
          @onSubmitSelect="handleSubmitSelect"
        />
      </div>
    </v-card-title>
    <v-card-text>
      <nde-data-table
        ref="ndeDataTable"
        class="nde-table-search-result"
        :headers="columns"
        :items="data"
        :items-per-page="itemPerPage"
        :page="page"
        :server-items-length="totalResults"
        :loading="!isSearchLoaded"
        :showSelect="allowSelect"
        :selectAll="selectAll"
        @updateSortBy="handleUpdateSortBy"
        @updateSortDesc="handleUpdateSortDesc"
        @onclickrow="handleClickRow"
        @onUpdateSelected="handleSelected"
        :mobileBreakpoint="0"
      >
        <template slot="item.actions" slot-scope="{ item }">
          <nde-button-key-search-actions :document="item" @onUpdateNewData="handleGetNewData" />
        </template>
        <template slot="item.sort_order" slot-scope="{ item }">
          <v-text-field
            v-model="item.sort_order"
            type="number"
            dense
            solo
            hide-details
            class="input-sort-order"
          ></v-text-field>
        </template>
      </nde-data-table>
    </v-card-text>
    <v-card-title v-show="!hiddenPagination">
      <nde-footer-table :page="page" :pageCount="totalPage" @paging:update="handlePaging" @exportCSV:submit="exportCSV" />
    </v-card-title>
    <nde-modal-sort-record-order
      :headers="sortRecordOrderColumns"
      :items="dataSelected"
      @onCancelSelect="handleCancelSelect"
    />

	<nde-modal-bulk-edit-doc-fields
    v-if="showModatalBulkEditDocFields"
		:items="dataSelected"
		@hideModal="handleCancelSelect"
  />
  </v-card>
</template>

<script>
import { mapState } from 'vuex';
import NdeButtonScanAndUpload from './NdeButtonScanAndUpload.vue';
import NdeButton from '../../Button/NdeButton.vue';
import NdeTableAction from './NdeTableAction';
import NdeButtonKeySearchActions from './NdeButtonKeySearchActions.vue';
import NdeFooterTable from './NdeFooterTable.vue';
import NdeDataTable from '../NdeDataTable.vue';
import NdeModalSortRecordOrder from '../../Modal/NdeModalSortRecordOrder.vue';
import NdeModalBulkEditDocFields from '../../Modal/NdeModalBulkEditDocFields.vue';

export default {
  name: 'NdeTableSearchResult',
  props: {
    data: {
      type: Array,
      default: () => [],
    }
  },
  data() {
    return {
      showScanAndUpload: false,
      isBulkEditDocFields: false,
      showModatalBulkEditDocFields: false,
      page: 1,
      itemPerPage: 10,
      visibleColumns: [],
      showableColumns: [],
      allowSelect: false,
      dataSelected: [],
      selectAll: false,
    };
  },
  methods: {
    handleGetNewData() {
      this.$emit('onUpdateNewData')
    },

    handleCancelSelect() {
      this.allowSelect = false;
      this.selectAll = false;
      this.$store.commit('setAllowSortRecordOrder', false);
      this.$refs.ndeDataTable.selected = [];
      this.dataSelected = [];
      this.isBulkEditDocFields = false;
      this.showModatalBulkEditDocFields = false;
    },

    async handleSubmitSelect() {
      this.dataSelected = this.$refs.ndeDataTable.selected;
      if (!this.$refs.ndeDataTable.selected.length) {
        return this.$swal({
          icon: 'error',
          text: 'Please check at least one record to continue.',
				});
      }
      if (this.allowSortRecordOrder) {
        return this.$store.commit('openModalSortRecordOrder');
      } else if (this.isBulkEditDocFields) {
        this.showModatalBulkEditDocFields = true;
        return;
      }
      return this.$emit('onSubmitSelect');
    },

    handleSelected(data) {
      this.$emit('onUpdateSelected', data);
    },

    handleOnChangePageSize(pageSize) {
      this.itemPerPage = pageSize;
      this.$emit('changePageSize', pageSize);
    },

    handleOnChangeVisibleColumn(event) {
      event.sort((a, b) => {
        return this.showableColumns.findIndex(p => p.value === a.value) - this.showableColumns.findIndex(p => p.value === b.value);
      });
      this.visibleColumns = event;
    },

    handleOnChangeBulkOption(event) {
      if (event.name === 'QueueDownloadOauth') {
        this.allowSelect = true;
        this.selectAll = true;
      } else if (event.name === 'SortRecordOrderOauth') {
        this.allowSelect = true;
        this.$store.commit('setAllowSortRecordOrder', true);
      } else if (event.name === 'BulkEditDocFieldsOauth') {
        this.allowSelect = true;
        this.selectAll = true;
        this.isBulkEditDocFields = true;
      }
    },

    handlePaging(jumpToPage) {
      this.page = jumpToPage;
      this.$emit('changePage', jumpToPage);
    },

    exportCSV(exportType) {
      this.$emit('exportCSV', exportType);
    },

    handleUpdateSortBy(column) {
      this.page = 1;
      this.$emit('updateSortBy', column);
    },

    handleUpdateSortDesc(isDesc) {
      this.page = 1;
      this.$emit('updateSortDesc', isDesc);
    },

    testingClick() {
      console.log('hello there')
    },

    handleClickRow(value) {
      if(this.allowSortRecordOrder
      ) {
        return;
      }
      if(value.doc_id !== "NDE_DUMMY_DATA"){
        this.$emit('onclickrow', value);
      }
    },
  },
  computed: {
    hiddenPagination() {
      return this.allowSelect;
    },
    totalPage() {
      return Math.ceil(this.totalResults / this.itemPerPage);
    },
    columns() {
      const actionColumns = [
        {
          text: 'Actions',
          value: 'actions',
          sortable: false,
        },
      ];
      return [...actionColumns,...this.visibleColumns];
    },
    sortRecordOrderColumns() {
      const actionColumns = [
        {
          text: '',
          value: 'sort_order',
          sortable: true,
        },
      ];
      const tableColumns = [...actionColumns, ...this.visibleColumns];
      return tableColumns;
    },
    numberResult() {
      if (!this.isSearchLoaded) return 0;
      if (this.totalResults < this.itemPerPage) return this.totalResults;
      if (this.page >= this.totalPage) return this.totalResults % this.itemPerPage;
      else return this.itemPerPage;
    },
    ...mapState([
      'currentProgram',
      'isSearchLoaded',
      'totalResults',
      'isCallAPILoaded',
      'currentControls',
      'VisibleColumnData',
      'isShowMessageRecordNotFound',
      'allowSortRecordOrder',
      'Neusearch',
    ]),
  },
  watch: {
    isShowMessageRecordNotFound: function (val) {
      this.showScanAndUpload = val;
    },
    currentControls: function (val) {
      if (val) {
        this.visibleColumns = [];
        this.showableColumns = [];

        val.map((field) => {
          const keys = Object.keys(field);
          if (field[keys[0]].display) {
            this.visibleColumns.push({
              text: field[keys[0]].label,
              value: keys[0],
            });
            this.showableColumns.push({
              text: field[keys[0]].label,
              value: keys[0],
            });
            this.$store.commit('setVisibleColumns', this.visibleColumns);
          }
        });
      }
    },
    isSearchLoaded(val) {
      if (!val) {
        this.$store.commit('setShowProgressAPI', true);
      } else {
        this.$store.commit('setShowProgressAPI', false);
      }
    },
    'Neusearch.page'(val) {
      if (val !== this.page) {
        this.page = val;
      }
    }
  },
  mounted() {
    if (this.VisibleColumnData) {
      (this.visibleColumns = this.VisibleColumnData),
        (this.showableColumns = this.VisibleColumnData);
    }
  },
  components: {
    NdeButtonScanAndUpload,
    NdeButton,
    NdeTableAction,
    NdeButtonKeySearchActions,
    NdeFooterTable,
    NdeDataTable,
    NdeModalSortRecordOrder,
	NdeModalBulkEditDocFields,
  },
};
</script>

<style lang="scss">
.nde-table-search-result {
  thead {
    background: #ebeced;
  }
  tbody tr:nth-of-type(even) {
    background-color: $lightGreyColor;
  }
  .action-btn {
    margin: 10px 0;
  }
  tbody td {
    text-align: center;
  }
  th,
  td {
    color: $darkGreyColor;
    min-width: 120px;
    max-width: 250px;
    white-space: nowrap;
    word-wrap: break-word;
    .input-sort-order {
      font-size: 0.75rem;
      border: 1px solid $drakColor;
    }
  }
}
.nde-table-search-result.theme--light.v-data-table > .v-data-table__wrapper > table > tbody > tr {
  height: 57px;
  cursor: pointer;
  &:nth-of-type(odd):hover {
    background-color: unset;
  }
  &:nth-of-type(even):hover {
    background-color: $lightGreyColor;
  }
  &:hover {
    .scan-and-upload {
      opacity: 1;
    }
  }
  td {
    font-size: 0.75rem;
  }
}

.v-application--is-ltr
  .v-data-table.nde-table-search-result
  > .v-data-table__wrapper
  > table
  > thead
  > tr
  > th {
  color: $darkGreyColor;
  text-align: center;
  font-size: 0.75rem;
  font-weight: 500;
}
.row-action-scan-and-upload {
  .scan-and-upload {
    opacity: 0;
  }
}

@media screen and (max-width: 48rem) {
  .nde-table-search-result.theme--light.v-data-table > .v-data-table__wrapper > table > tbody > tr {
    height: auto;
  }

  .nde-table-search-result {
    td {
      max-width: 100%;
    }
  }
}
</style>
