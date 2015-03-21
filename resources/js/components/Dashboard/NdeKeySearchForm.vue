<template>
  <div>
    <v-card class="pa-6 mt-3" v-if="!viewRecord">
      <v-form ref="searchForm" @submit="submit">
        <div
          class="d-flex justify-space-between align-center nde-dashboard-acc-item"
          @click="isExpandForm = !isExpandForm"
        >
          <div class="d-flex">
            <v-icon>{{ isExpandForm ? 'mdi-menu-down' : 'mdi-menu-right' }}</v-icon>
            <nde-dashboard-search-form-breadcrumbs :ariaExpanded="isExpandForm" />
          </div>
          <!-- <div class="d-flex" v-if="isExpandForm">
            <span class="red--text">*</span>
            <span class="grey--text">= wildcard (*) permitted.</span>
          </div> -->

          <div
            class="d-flex"
            v-if="
              !isExpandForm &&
              !!Neusearch.Searchitems &&
              !!Neusearch.Searchitems.item &&
              Neusearch.Searchitems.item.length
            "
          >
            <div class="d-flex nde-dashboard-search">
              <div class="d-flex mr-3" v-for="(item, i) in Neusearch.Searchitems.item" :key="i + 1">
                <span class="mr-2">{{ item.label }}</span>
                <span class="font-weight-bold">{{
                  item.type === 'INTRANGE' ? item.value.replace('|', ' to ') : item.value
                }}</span>
              </div>
            </div>
          </div>
        </div>

        <v-divider v-if="isExpandForm"></v-divider>

        <v-expand-transition>
          <div v-show="isExpandForm" class="mt-3">
            <div v-if="searchError" v-html="searchError" class="search-error" role="alert"></div>
            <v-row>
              <nde-dashboard-search-field
                v-for="(search_field, i) in currentControls"
                :key="i + 1"
                :search_field="search_field"
                @onChange="updatedFormFieldValue"
                @onEnterKey="handleEnter"
              />
            </v-row>

            <div
              aria-controls="breadcrumbs"
              :aria-expanded="isExpandFormAdvanced ? `true` : `false`"
              class="d-flex align-center mt-6 cursor-pointer"
              @click="isExpandFormAdvanced = !isExpandFormAdvanced"
            >
              <v-icon>{{ isExpandFormAdvanced ? 'mdi-menu-down' : 'mdi-menu-right' }}</v-icon>
              <button
                class="d-flex align-end"
                aria-controls="breadcrumbs"
                :aria-expanded="isExpandFormAdvanced ? `true` : `false`"
                @click.prevent="e.preventDefault()"
                type="button"
              >
                <span class="text-h6" role="button">Advanced</span>
              </button>
            </div>

            <v-expand-transition>
              <div v-show="isExpandFormAdvanced" class="mt-3">
                <nde-advanced-search-form />
              </div>
            </v-expand-transition>

            <v-divider class="mt-2"></v-divider>
            <div class="d-flex mt-3">
              <nde-button class="mr-3" color="primary" @click="searchRecord" :loading="!isSearchLoaded"
                >Search</nde-button
              >
              <nde-button color="default" @click="reset">Clear</nde-button>
            </div>
          </div>
        </v-expand-transition>
      </v-form>
    </v-card>
    <nde-alert-success
      v-show="isShowMessageRecordNotFound"
      class="mt-3 mb-3"
      :message="messageNotFound"
    />
    <nde-table-search-result
      v-if="!viewRecord"
      ref="tableSearchResult"
      :data="searchResults"
      @changePageSize="handleOnChangePageSize"
      @changePage="handleChangePage"
      @updateSortBy="handleUpdateSortBy"
      @updateSortDesc="handleUpdateSortDesc"
      @onclickrow="handleClickRow"
      @onUpdateSelected="handleDataDownloadPdf"
      @onSubmitSelect="handleSubmitSelect"
      @onUpdateNewData="submit"
      @exportCSV="exportCSV"
    />
    <nde-scan-modal v-if="isScanModal" />
    <nde-modal-upload-document v-if="isModalUploadDocument" />
    <nde-modal-record-not-found v-if="isModalRecordNotFound" />
    <nde-view-record-vue
      v-if="viewRecord"
      :record="viewRecord"
      @onClose="closeViewRecord"
    ></nde-view-record-vue>
    <nde-modal-transfer-record v-if="isModalTransferRecord" />
    <nde-modal-edit-indexing-fields v-if="isModalEditIndexingFields" />
    <nde-modal-double-entry v-if="isModalDoubleEntry" />
    <nde-modal-transfer-record-indexing-fields v-if="isModalTransferRecordIndexingFields" />
    <nde-modal-transfer-tabs v-if="isModalTransferTabs" />
    <nde-view-document v-if="isViewDocument"></nde-view-document>
    <nde-modal-download-as-pdf
      @onClearData="handleClearDataSelected"
      :data="dataSelected"
      ref="ndeModalDownloadPdf"
    ></nde-modal-download-as-pdf>
    <nde-modal-data-feed v-if="isModalDataFeed" />
  </div>
</template>

<script>
import { mapState } from 'vuex';
import NdeTableSearchResult from '../Table/NdeSearchResult/NdeTableSearchResult.vue';
import NdeScanModal from '../Modal/NdeScanModal.vue';
import NdeModalUploadDocument from '../Modal/NdeModalUploadDocument.vue';
import NdeModalRecordNotFound from '../Modal/NdeModalRecordNotFound.vue';
import NdeModalTransferRecord from '../Modal/NdeModalTransferRecord.vue';
import NdeModalEditIndexingFields from '../Modal/NdeModalEditIndexingFields.vue';
import NdeModalTransferRecordIndexingFields from '../Modal/NdeModalTransferRecordIndexingFields.vue';
import NdeModalTransferTabs from '../Modal/NdeModalTransferTabs.vue';
import NdeModalDoubleEntry from '../Modal/NdeModalDoubleEntry.vue';
import NdeButton from '../Button/NdeButton.vue';
import NdeDashboardSearchField from './NdeDashboardSearchField.vue';
import NdeDashboardSearchFormBreadcrumbs from './NdeDashboardSearchFormBreadcrumbs.vue';
import NdeAdvancedSearchForm from './NdeAdvancedSearchForm.vue';
import NdeAlertSuccess from '../Alert/NdeAlertSuccess.vue';
import NdeViewRecordVue from './NdeViewRecord.vue';
import NdeViewDocument from './NdeViewDocument.vue';
import NdeModalDownloadAsPdf from '../Modal/NdeModalDownloadAsPdf.vue';
import NdeModalDataFeed from '../Modal/NdeModalDataFeed.vue';
import { pageExportNew } from '../../pages/report/service'

export default {
  data() {
    return {
      formData: [],
      isExpandForm: true,
      isExpandFormAdvanced: false,
      typingDebounceTimer: null,
      typeDebounceDuration: 500,
      viewRecord: null,
      dataSelected: [],
    };
  },

  computed: {
    searchResults() {
      const rawData = this.searchResult?.search_results?.images || [];
      this.$store.commit('setTotalResults', this.searchResult?.meta?.num_images);
      //console.log('this.searchResult?.search_results?.images', this.searchResult?.search_results?.images)
      const data = rawData.reduce((pre, curr) => {
        const dataOfOneRecord = [...curr.image_fields].reduce((pre, curr) => {
          pre[curr.field_name] = curr.field_value;
          return pre;
        }, {});
        dataOfOneRecord.image_id = curr.doc_id;
        dataOfOneRecord.doc_id = curr.doc_id;
        return [...pre, dataOfOneRecord];
      }, []);
      // console.log('data', data)
      return data;
    },
    messageNotFound() {
      return `No records found! ${
        this.isRoleCREATERECORD
          ? "Click 'Actions' to create a new record from these search criteria."
          : ''
      }`;
    },
    ...mapState([
      'currentProgram',
      'isSearchLoaded',
      'searchResult',
      'isModalUploadDocument',
      'searchError',
      'totalResults',
      'Neusearch',
      'oldNeusearch',
      'isModalRecordNotFound',
      'currentControls',
      'isShowMessageRecordNotFound',
      'isModalTransferRecord',
      'isModalEditIndexingFields',
      'isModalDoubleEntry',
      'isModalTransferRecordIndexingFields',
      'isModalTransferTabs',
      'isViewDocument',
      'isScanModal',
      'isModalDataFeed',
    ]),
  },

  methods: {
    handleDataDownloadPdf(data) {
      this.dataSelected = data;
    },

    handleSubmitSelect() {
      this.$refs.ndeModalDownloadPdf.showModal = true;
    },

    handleClearDataSelected() {
      this.$refs.tableSearchResult.handleCancelSelect();
    },

    handleEnter() {
      this.typingDebounceTimer && clearTimeout(this.typingDebounceTimer);

      this.typingDebounceTimer = setTimeout(() => {
        this.submit();
      }, this.typeDebounceDuration);
    },

    submit(e) {
      if (e) e.preventDefault();
      this.$store.commit('setNeusearch', {
        key: 'Searchitems',
        data: { item: this.formData },
      });
      this.$store.dispatch({ type: 'searchImages' });
    },
    searchRecord() {
      this.$store.commit('setPage', 1);
      if (this.isSearchLoaded) this.submit(null);
    },
    updatedFormFieldValue(data) {
      if (this.formData.length) {
        this.formData = this.formData.filter((item) => item.key != data.key);
      }

      if (data.value) {
        this.formData.push(data);
      }

      this.$store.commit('setNeusearch', {
        key: 'Searchitems',
        data: { item: this.formData },
      });
    },

    reset() {
      this.$refs.searchForm.reset();
      this.$store.commit('setCallAPILoaded', false);
      this.$store.state.searchError = '';
      this.$store.state.isShowMessageRecordNotFound = false;
    },
    openScanModal(imageId) {
      this.$store.commit('openScanModal', imageId);
    },

    handleOnChangePageSize: function (pageSize) {
      this.$store.commit('setPageSize', pageSize);
      if (this.isSearchLoaded) this.submit();
    },

    handleChangePage: function (page) {
      this.$store.commit('setPage', page);
      if (this.isSearchLoaded) this.submit(null);
    },

    handleUpdateSortBy: function (column) {
      // this.$store.commit('setOrderBy', column);
      // this.$store.commit('setPage', 1);
      if (column) {
        this.$store.commit('setNeusearch', { key: 'order_by', data: column });
        this.$store.commit('setNeusearch', { key: 'order', data: 'asc' });
        this.$store.commit('setNeusearch', { key: 'page', data: 1 });
      } else {
        this.$store.commit('setNeusearch', { key: 'order_by', data: '' });
        this.$store.commit('setNeusearch', { key: 'order', data: 'asc' });
        this.$store.commit('setNeusearch', { key: 'page', data: 1 });
      }

      if (this.isSearchLoaded) this.submit();
    },

    handleUpdateSortDesc: function (isDesc) {
      // this.$store.commit('setPage', 1);
      // this.$store.commit('setOrderDesc', isDesc);
      if (isDesc) {
        this.$store.commit('setNeusearch', { key: 'order', data: 'desc' });
      }
      if (this.isSearchLoaded) this.submit();
    },

    handleClickRow: function (value) {
      setTimeout(() => {
        if (!this.getSelectedText()) {
          console.log(value);
          this.viewRecord = value;
          this.$store.commit('setCurrentDocData', value);
        }
      }, 50);
    },

    getSelectedText() {
      if (window.getSelection) {
        return window.getSelection().toString();
      } else if (document.selection) {
        return document.selection.createRange().text;
      }
      return '';
    },

    closeViewRecord() {
      this.viewRecord = null;
    },

    async exportCSV(exportType) {
      this.$store.commit('setShowProgressAPI', true);
      const payload = {
        ...this.oldNeusearch,
        outputCSV: true,
      };
      if (exportType === 'all') {
        payload['pageSize'] = this.totalResults;
        payload['page'] = 1;
      }
      const resulGetSearchImages = await this.$store.dispatch('callAPI', {
        url: '/getSearchImages',
        method: 'post',
        data: payload,
      });
      this.$store.commit('setShowProgressAPI', false);
      if (resulGetSearchImages?.message !== 'success') {
        return this.$swal({
          icon: 'error',
          text: this.getMessageResult(resulGetSearchImages),
        });
      }
      // Mapping Result API TO CSV
      const images = resulGetSearchImages?.data?.data?.search_results?.images || [];
      images.shift();
      let expColumns = [];
      this.currentControls.forEach(dataField => {
        for (const keyColumn in dataField) {
          const dataColumn = dataField[keyColumn];
          if (dataColumn?.csv_column) {
            expColumns[+dataColumn.csv_column] = {
              ...dataColumn,
              keyColumn,
              text: dataColumn?.label || '',
            };
          }
        }
      });
      expColumns = expColumns.filter(Boolean);
      let expData = images.map(dataImage => {
        let dataImageFields = {};
        (dataImage[1] || []).forEach(imageField => {
          dataImageFields[imageField?.field_name] = imageField?.field_value;
        });
        return expColumns.map(dataColumn => (dataImageFields[dataColumn?.keyColumn]));
      });
      let profileName = (document.getElementById("breadcrumb_dashboard_0")?.innerText || '');
      // Remove ' / '
      profileName = profileName.substring(0, profileName.length-3);
      pageExportNew(expData, expColumns, [], exportType === 'all' ? exportType : payload.page, profileName);
      return this.$swal({
        icon: 'success',
        text: 'Exported successfully',
      });
    }
  },

  watch: {
    currentProgram: function (val) {
      // this.$refs.searchForm.reset();
      this.isExpandForm = true;
    },
    currentControls: function (val) {
      if (!val) return;
      this.formData = [];
      this.$store.commit('setNeusearch', {
        key: 'Searchitems',
        data: { item: this.formData },
      });
      this.$refs.searchForm.reset();
    },
    isSearchLoaded: function (newVal, oldVal) {
      if (newVal && !oldVal && !this.searchError) {
        this.isExpandForm = false;

        setTimeout(() => {
          var dashboardContainer = document.getElementById('nde-dashboard-container');
          dashboardContainer.scrollTop = 0;
        }, 1000);
      }
    },
  },

  components: {
    NdeTableSearchResult,
    NdeScanModal,
    NdeModalUploadDocument,
    NdeButton,
    NdeDashboardSearchField,
    NdeDashboardSearchFormBreadcrumbs,
    NdeAdvancedSearchForm,
    NdeModalRecordNotFound,
    NdeAlertSuccess,
    NdeViewRecordVue,
    NdeModalTransferRecord,
    NdeModalEditIndexingFields,
    NdeModalDoubleEntry,
    NdeModalTransferRecordIndexingFields,
    NdeModalTransferTabs,
    NdeViewDocument,
    NdeModalDownloadAsPdf,
    NdeModalDataFeed,
  },
};
</script>

<style scoped lang="scss">
.cursor-pointer {
  cursor: pointer;
}
.nde-dashboard-acc-item {
  cursor: pointer;
  .nde-dashboard-search {
    text-overflow: ellipsis;
    overflow: hidden;
    max-width: 25rem;
    white-space: nowrap;
  }
}

.search-error {
  color: $errorColor;
}

@media screen and (max-width: 37.5rem) {
  .nde-dashboard-acc-item {
    display: block !important;
    .nde-dashboard-search {
      margin-top: 0.5rem;
      margin-left: 0.5rem;
      flex-wrap: wrap;
      overflow: unset;
      white-space: normal;
      word-break: break-all;
    }
  }

  ::v-deep .info-record--span__grey {
    display: block;
    margin-left: 0 !important;
  }
  ::v-deep h2.search-result {
    font-size: 1.5rem !important;
  }
}
</style>
