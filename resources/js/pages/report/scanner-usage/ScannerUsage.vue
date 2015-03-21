<template>
  <div class="nde-table-deletion-report">
    <v-card width="fit-content" class="nde-tabs d-flex align-center pa-2 mb-5">
      <nde-tab-item
        :text="breadcrumb.text"
        :icon="breadcrumb.icon"
        :isActive="breadcrumb.isActive"
      ></nde-tab-item>
    </v-card>

    <v-card class="ma-0">
      <v-card-text>
        <nde-filter-form
          class="mb-5"
          :textInfo="textInfo"
          :defaultData="dataFilter"
          :reportControls="reportControls"
          :enableButton="tableLoaded"
          @onGenerateReport="handleGenerateReport"
          @onQueueReport="handleQueueReport"
          @updateFilterData="handleChangeFilterData"
        />
      </v-card-text>
      <div class="d-flex justify-center" v-if="loadingInitally && !tableLoaded">
        <v-progress-linear indeterminate color="primary"></v-progress-linear>
      </div>
      <v-card class="ma-0" v-if="!loadingInitally && tableLoaded">
        <v-divider></v-divider>
        <v-card-text>
          <nde-data-table
            class="nde-data-table"
            :headers="columns"
            :items="data"
            :isLoading="loadingGenerateReport"
            :items-per-page="itemPerPage"
            :page="page"
            @updateSortBy="updateSortBy"
            @updateSortDesc="updateSortDesc"
          >
            <template slot="item.recInfo" slot-scope="{ item }">
              <div v-html="item.recInfo"></div>
            </template>
          </nde-data-table>
        </v-card-text>
        <v-card-title>
          <v-col cols="12">
            <v-row>
              <v-col cols="12" md="6">
                <nde-paging :length="maxPage" v-model="page"></nde-paging>
              </v-col>
              <v-col cols="12" md="6" align="right">
                <v-menu offset-y attach top>
                  <template v-slot:activator="{ attrs, on }">
                    <v-btn v-bind="attrs" v-on="on" color="primary"> Export to CSV </v-btn>
                  </template>
                  <v-list>
                    <v-list-item-group color="primary">
                      <v-list-item>
                        <v-list-item-title @click="pageExport"> Page </v-list-item-title>
                      </v-list-item>
                      <v-list-item>
                        <v-list-item-title @click="exportAll"> All Pages </v-list-item-title>
                      </v-list-item>
                    </v-list-item-group>
                  </v-list>
                </v-menu>
              </v-col>
            </v-row>
          </v-col>
        </v-card-title>
      </v-card>
    </v-card>
  </div>
</template>

<script>
import { ExportToCsv } from 'export-to-csv';
import { v4 as uuidv4 } from 'uuid';
import NdeFilterForm from '@components/FilterForm/NdeFilterForm.vue';
import NdeDataTable from '@components/Table/NdeDataTable.vue';
import NdePaging from '@components/Common/NdePaging.vue';
import NdeMenu from '@components/Menu/NdeMenu.vue';
import NdeDashboardLayout from '../../../shared/layouts/dashboard/NdeDashboardLayout';
import moment from 'moment';
import { DEFAULT_DATE_FORMAT } from '@utils/constants';
import {
  getScannerUsageData,
  getReportControls,
  pageExportNew,
} from '../service';
import { EXPORT_MENU, SCANNER_USAGE_COLUMNS } from '../utils/constants';
import NdeTabItem from '@components/Tabs/NdeTabItem.vue';

export default {
  name: 'ScannerUsage',
  layout: NdeDashboardLayout,
  data() {
    return {
      textInfo: 'This Report is for estimation only, it will not match the billing report.',
      profile: 25,
      reportControls: [],
      page: 1,
      itemPerPage: 10,
      totalItems: 0,
      limit: 10,
      data: [],
      tableLoaded: false,
      loadingInitally: false,
      columns: [],
      dataFilter: {
        program: '1',
        user: 0,
        range: {
          from: moment().subtract(1, 'years').format(DEFAULT_DATE_FORMAT),
          to: moment().format(DEFAULT_DATE_FORMAT),
        },
      },
      breadcrumb: {
        text: 'Scanner Usage',
        icon: 'mdi-account-edit',
        isActive: true,
      },
      users: null,
      exportMenu: EXPORT_MENU,
      loadingGenerateReport: false,
      allPagesExport: false,
      order_by: '',
      order: '',
    };
  },
  components: {
    NdeFilterForm,
    NdeDataTable,
    NdePaging,
    NdeMenu,
    NdeTabItem,
  },
  async created() {
    const controlData = await getReportControls({
      profile: this.profile,
    });
    this.reportControls = [...controlData?.data?.data?.controls];
    this.reportControls.forEach((item) => {
      this.dataFilter[item.key] = '';
    });
  },
  computed: {
    maxPage() {
      return Math.ceil(this.totalItems / this.limit);
    },
  },
  watch: {
    page: function () {
      this.fetchScannerUsageData();
    },
  },
  methods: {
    async fetchScannerUsageData() {
      this.loadingGenerateReport = true;
      const payload = {
        from_date: this.dataFilter.date.from,
        page_size: this.itemPerPage,
        page: this.page,
        order_by: this.order_by ? this.order_by + ' ' + this.order : '',
        profile_id: this.dataFilter.program,
        to_date: this.dataFilter.date.to,
        user_id: this.dataFilter.user,
      };
      this.$store.commit('setShowProgressAPI', true);
      const resultGetScannerUsageData = await this.$store.dispatch('callAPI', {
        url: '/scannerUsageReportOauth',
        method: 'post',
        data: payload,
      });
      this.$store.commit('setShowProgressAPI', false);
      if (resultGetScannerUsageData.message === 'success') {
        this.data = resultGetScannerUsageData?.data?.data?.records || [];
        this.totalItems = resultGetScannerUsageData?.data?.data?.total || 0;
        this.tableLoaded = true;
        this.columns = resultGetScannerUsageData?.data?.data?.columns || [];
      } else {
        this.data = [];
        this.totalItems = 0;
        this.$swal({
          icon: 'error',
          text: this.getMessageResult(resultGetScannerUsageData),
        });
      }
      this.loadingGenerateReport = false;
      this.loadingInitally = false;
    },
    handleChangeFilterData(id, value) {
      this.dataFilter[id] = value;
      this.page = 1;
      this.tableLoaded = false;
      this.loadingInitally = false;
      this.loadingGenerateReport = false;
    },
    handleGenerateReport() {
      this.loadingInitally = true;
      this.fetchScannerUsageData();
    },
    handlePagingReport(page) {
      this.page = page;
    },
    async pageExport() {
      this.$store.commit('setShowProgressAPI', true);
      const payload = {
        from_date: this.dataFilter.date.from,
        page_size: this.allPagesExport? this.totalItems :this.itemPerPage,
        page: this.allPagesExport? 1 : this.page,
        order_by: 'scanner asc',
        profile_id: this.dataFilter.program,
        to_date: this.dataFilter.date.to,
        user_id: this.dataFilter.user,
        output_csv: true,
      };
      await getScannerUsageData(payload)
        .then((data) => {
          if (data?.data?.data?.records) {
            let expData = data?.data?.data?.records || [];
            let expColumns = data?.data?.data?.columns || [];
            expData.shift();
            pageExportNew(expData, expColumns, [], this.page, this.breadcrumb.text + ' Report');
            this.$swal({
              icon: 'success',
              text: 'Exported successfully',
            });
          } else {
            this.$swal({
              icon: 'error',
              text: 'Please try again!',
            });
          }
        })
        .catch((errors) => {
          console.log(errors);
          this.$swal({
            icon: 'error',
            text: 'Please try again!',
          });
        })
        .finally(() => this.$store.commit('setShowProgressAPI', false));
    },
    async exportAll() {
      this.allPagesExport = true;
      await this.pageExport();
      this.allPagesExport = false;
    },
    handleQueueReport() {},

    updateSortBy(value) {
      this.order_by = value;
      if(this.order == '') {
        this.fetchScannerUsageData();
      }
    },

    updateSortDesc(value) {
      this.order = value ? 'desc' : 'asc';
      this.fetchScannerUsageData();
    }
  },
};
</script>
