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
import { v4 as uuidv4 } from 'uuid';
import NdeDashboardLayout from '@shared/layouts/dashboard/NdeDashboardLayout';
import NdeFilterForm from '@components/FilterForm/NdeFilterForm.vue';
import NdeDataTable from '@components/Table/NdeDataTable.vue';
import NdePaging from '@components/Common/NdePaging.vue';
import NdeMenu from '@components/Menu/NdeMenu.vue';
import { EXPORT_MENU } from '../utils/constants';
import {
  getLoginTrackingReport,
  getReportControls,
  pageExportNew,
} from '../service';
import NdeTabItem from '@components/Tabs/NdeTabItem.vue';

export default {
  layout: NdeDashboardLayout,

  data() {
    return {
      page: 1,
      itemPerPage: 10,
      totalItems: 0,
      limit: 10,
      columns: [],
      data: [],
      tableLoaded: false,
      loadingInitally: false,
      breadcrumb: {
        text: 'Login Tracking',
        icon: 'mdi-account-edit',
        isActive: true,
      },
      reportControls: [],
      dataFilter: {},
      exportMenu: EXPORT_MENU,
      loadingGenerateReport: false,
      profile: 14,
      allPagesExport: false,
      order_by: 'username',
      order: '',
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
  },

  watch: {
    page: function () {
      this.fetchDataReport();
    },
  },

  methods: {
    handleChangeFilterData(id, value) {
      this.dataFilter[id] = value;
      this.page = 1;
      this.tableLoaded = false;
      this.loadingInitally = false;
      this.loadingGenerateReport = false;
    },

    handlePagingReport(page) {
      this.page = page;
    },

    async fetchDataReport() {
      this.loadingGenerateReport = true;
      const payload = {
        order_by: this.order_by ? this.order_by + ' ' + this.order : '',
        page: this.page,
        page_size: this.itemPerPage,
        from_date: this.dataFilter.date.from,
        to_date: this.dataFilter.date.to,
        user_id: this.dataFilter.user,
        profile_id: this.profile,
      };

      this.$store.commit('setShowProgressAPI', true);
      const resultGetLoginTrackingReport = await this.$store.dispatch('callAPI', {
        url: '/loginTrackingReportOauth',
        method: 'post',
        data: payload,
      });
      this.$store.commit('setShowProgressAPI', false);
      if (resultGetLoginTrackingReport.message === 'success') {
        this.data = resultGetLoginTrackingReport?.data?.data?.records || [];
        this.totalItems = resultGetLoginTrackingReport?.data?.data?.total || 0;
        this.tableLoaded = true;
        this.columns = resultGetLoginTrackingReport?.data?.data?.columns || [];
      } else {
        this.data = [];
        this.totalItems = 0;
        this.$swal({
          icon: 'error',
          text: this.getMessageResult(resultGetLoginTrackingReport),
        });
      }
      this.loadingGenerateReport = false;
      this.loadingInitally = false;
    },

    handleGenerateReport() {
      this.loadingInitally = true;
      this.fetchDataReport();
    },
    handleQueueReport() {},
    async pageExport() {
      this.$store.commit('setShowProgressAPI', true);
      const payload = {
        order_by: 'username asc',
        page: this.allPagesExport? 1 : this.page,
        page_size: this.allPagesExport? this.totalItems :this.itemPerPage,
        from_date: this.dataFilter.date.from,
        to_date: this.dataFilter.date.to,
        user_id: this.dataFilter.user,
        profile_id: this.profile,
        output_csv: true,
      };

      getLoginTrackingReport(payload)
        .then((data) => {
          if (data?.data?.data?.records) {
            console.log(data?.data?.data?.records);
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
        .catch((err) => {
          console.log(err);
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

    updateSortBy(value) {
      console.log(value)
      this.order_by = value;
      if(this.order == '') {
        this.fetchDataReport();
      }
    },

    updateSortDesc(value) {
      console.log(value)
      this.order = value ? 'desc' : 'asc';
      this.fetchDataReport();
    }
  },

  computed: {
    programs() {
      const programs = this.$store?.state?.programs || [];
      return programs.map((item) => ({
        text: item.name,

        value: item.id,
      }));
    },
    maxPage() {
      return Math.ceil(this.totalItems / this.limit);
    },
  },
  components: {
    NdeFilterForm,
    NdeDataTable,
    NdePaging,
    NdeMenu,
    NdeTabItem,
  },
};
</script>

<style scoped lang="scss">
.v-list-item {
  cursor: pointer;
}
.nde-data-table {
  table {
    table-layout: fixed;
    width: 100%;
  }
  thead {
    background: #ebeced;
    tr {
      display: table-row;
      vertical-align: inherit;
      border-color: inherit;
    }
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
    display: table-cell;
    vertical-align: inherit;
    color: $darkGreyColor;
    min-width: 120px;
    max-width: 250px;
    word-break: break-word;
    white-space: normal;
    overflow: hidden;
  }
}
.nde-data-table.theme--light.v-data-table > .v-data-table__wrapper > table > tbody > tr {
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
  .v-data-table.nde-data-table
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
</style>
