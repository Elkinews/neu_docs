<template>
  <div class="nde-table-modification-report">
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
import { v4 as uuidv4 } from 'uuid';
import NdeFilterForm from '@components/FilterForm/NdeFilterForm.vue';
import NdeDataTable from '@components/Table/NdeDataTable.vue';
import NdePaging from '@components/Common/NdePaging.vue';
import NdeButtonWarning from '@components/Button/NdeButtonWarning.vue';
import NdeDashboardLayout from '../../../shared/layouts/dashboard/NdeDashboardLayout';
import {
  getNetworkScannerUsageData,
  getReportControls,
  pageExportNew,
} from '../service';
import { EXPORT_MENU } from '../utils/constants';
import NdeTabItem from '@components/Tabs/NdeTabItem.vue';

export default {
  layout: NdeDashboardLayout,
  data() {
    const todayDate = new Date();
    const lastMonthDate = new Date();
    lastMonthDate.setTime(todayDate.getTime() - 30 * 24 * 3600000);
    return {
      page: 1,
      itemPerPage: 10,
      totalItems: 0,
      limit: 10,
      columns: [],
      data: [],
      tableLoaded: false,
      loadingInitally: false,
      dataFilter: {},
      reportControls: [],
      breadcrumb: {
        text: 'Network Scanner Usage',
        icon: 'mdi-account-edit',
        isActive: true,
      },
      exportMenu: EXPORT_MENU,
      loadingGenerateReport: false,
      profile: 1252,
      scannerSerial: {},
    };
  },
  async created() {
    const controlData = await getReportControls({
      profile: this.profile,
    });
    this.reportControls = [...controlData?.data?.data?.controls];
    this.reportControls.forEach((item) => {
      this.dataFilter[item.key] = '';
    });
    this.reportControls.forEach((item) => {
      if (item.key === 'serialnumber') {
        this.scannerSerial = item?.map;
      }
    });
  },
  components: {
    NdeFilterForm,
    NdeDataTable,
    NdePaging,
    NdeButtonWarning,
    NdeTabItem,
  },
  computed: {
    maxPage() {
      return Math.ceil(this.totalItems / this.limit);
    },
  },
  watch: {
    page: function () {
      this.fetchNetworkScannerUsage();
    },
  },
  methods: {
    async fetchNetworkScannerUsage() {
      this.loadingGenerateReport = true;
      const payload = {
        from_date: this.dataFilter.date.from,
        to_date: this.dataFilter.date.to,
        serial_number: this.scannerSerial[this.dataFilter.serialnumber],
      };
      this.$store.commit('setShowProgressAPI', true);
      const resultGetNetworkScannerUsageData = await this.$store.dispatch('callAPI', {
        url: '/networkScannerUsageReportOauth',
        method: 'post',
        data: payload,
      });
      this.$store.commit('setShowProgressAPI', false);
      if (resultGetNetworkScannerUsageData.message === 'success') {
        this.data = resultGetNetworkScannerUsageData?.data?.data?.data?.records || [];
        this.totalItems = resultGetNetworkScannerUsageData?.data?.data?.data?.total || 0;
        this.tableLoaded = true;
        this.columns = resultGetNetworkScannerUsageData?.data?.data?.data?.columns || [];
      } else {
        this.data = [];
        this.totalItems = 0;
        this.$swal({
          icon: 'error',
          text: this.getMessageResult(resultGetNetworkScannerUsageData),
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
      this.fetchNetworkScannerUsage();
    },
    handleQueueReport() {},
    handlePagingReport(page) {
      this.page = page;
    },
    async pageExport() {
      this.$store.commit('setShowProgressAPI', true);
      const payload = {
        from_date: this.dataFilter.date.from,
        to_date: this.dataFilter.date.to,
        serial_number: this.scannerSerial[this.dataFilter.serialnumber],
        output_csv: true,
      };
      await getNetworkScannerUsageData(payload)
        .then((data) => {
          if (data?.data?.data?.data?.records) {
            let expData = data?.data?.data?.data?.records || [];
            let expColumns = data?.data?.data?.data?.columns || [];
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
    exportAll() {
      this.pageExport();
    },
  },
};
</script>


