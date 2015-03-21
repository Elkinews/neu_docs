<template>
  <div class="nde-table-retention-report">
    <v-card width="fit-content" class="nde-tabs d-flex align-center pa-2 mb-5">
      <nde-tab-item :text="breadcrumb.text" :icon="breadcrumb.icon" :isActive="breadcrumb.isActive"></nde-tab-item>
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
            @updateSortBy="updateSortBy"
            @updateSortDesc="updateSortDesc"
          >
            <template slot="item.set_frozen" slot-scope="{ item }">
              <div v-html="item.set_frozen"></div>
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
import NdeMenu from '@components/Menu/NdeMenu.vue';
import NdeDashboardLayout from '../../../shared/layouts/dashboard/NdeDashboardLayout';
import { getRetentionData, getReportControls, pageExportNew } from '../service';
import { EXPORT_MENU} from '../utils/constants';
import NdeTabItem from "@components/Tabs/NdeTabItem.vue";

export default {
  name: 'Retention',
  layout: NdeDashboardLayout,
  data() {
    return {
      profile: 15,
      reportControls: [],
      page: 1,
      itemPerPage: 10,
      totalItems: 0,
      limit: 10,
      data: [],
      tableLoaded: false,
      loadingInitally: false,
      columns: [],
      dataFilter: {},
      breadcrumb: {
          text: 'Retention Usage',
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
    NdeTabItem
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
      this.fetchRetentionData();
    },
  },
  methods: {
    async fetchRetentionData() {
      this.loadingGenerateReport = true;
      const payload = {
        page_size: this.itemPerPage,
        page: this.page,
        order_by: this.order_by ? this.order_by + ' ' + this.order : '',
        profile_id: this.dataFilter.profile,
		    from_date: this.dataFilter.date.from,
        to_date: this.dataFilter.date.to,
		    retention_year: this.dataFilter.retention_year,
		    include_frozen: Boolean(this.dataFilter.include_frozen),
      };
      this.$store.commit('setShowProgressAPI', true);
      const resultGetRetentionData = await this.$store.dispatch('callAPI', {
        url: '/retentionReportOauth',
        method: 'post',
        data: payload,
      });
      this.$store.commit('setShowProgressAPI', false);
      if (resultGetRetentionData.message === 'success') {
        this.data = resultGetRetentionData?.data?.data?.records || [];
        this.totalItems = resultGetRetentionData?.data?.data?.total || 0;
        this.tableLoaded = true;
        this.columns = resultGetRetentionData?.data?.data?.columns || [];
      } else {
        this.data = [];
        this.totalItems = 0;
        this.$swal({
          icon: 'error',
          text: this.getMessageResult(resultGetRetentionData),
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
      this.fetchRetentionData();
    },
    handlePagingReport(page) {
      this.page = page;
    },
    async pageExport() {
      this.$store.commit('setShowProgressAPI', true);
      const payload = {
        page_size: this.allPagesExport? this.totalItems :this.itemPerPage,
        page: this.allPagesExport? 1 : this.page,
        order_by: 'profile_name asc',
        profile_id: this.dataFilter.profile,
		    from_date: this.dataFilter.date.from,
        to_date: this.dataFilter.date.to,
		    retention_year: this.dataFilter.retention_year,
		    include_frozen: Boolean(this.dataFilter.include_frozen),
        output_csv: true,
      };
      await getRetentionData(payload)
        .then((data) => {
          if (data?.data?.data?.records) {
            let expData = data?.data?.data?.records || [];
            let expColumns = data?.data?.data?.columns || [];
            expData.shift();
            pageExportNew(expData, expColumns, [], this.page, this.breadcrumb.text + ' Report');
            this.$swal({
              icon: 'success',
              text: 'Exported successfully'
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
        this.fetchRetentionData();
      }
    },

    updateSortDesc(value) {
      this.order = value ? 'desc' : 'asc';
      this.fetchRetentionData();
    }
  },
};
</script>

