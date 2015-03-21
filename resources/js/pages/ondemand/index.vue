<template>
  <div class="nde-custom-on-demand" role="main">
    <v-card width="fit-content" class="nde-tabs d-flex align-center pa-2 mb-5">
      <nde-tab-item v-if="currentProfile"
        :text="currentProfile.name || ''"
        icon="mdi-file-document-edit-outline"
        :isActive="true"
      ></nde-tab-item>
    </v-card>
    <v-card class="ma-0">
      <v-card-text>
        <nde-filter-form
          class="mb-5"
          :defaultData="dataFilter"
          :reportControls="controls"
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
                <nde-paging :length="maxPage" v-model="page" v-if="(totalItems > itemPerPage)"></nde-paging>
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
import { mapState } from 'vuex';
import { v4 as uuidv4 } from 'uuid';
import NdeDashboardLayout from '../../shared/layouts/dashboard/NdeDashboardLayout.vue';
import NdeFilterForm from '@components/FilterForm/NdeFilterForm.vue';
import NdeDataTable from '@components/Table/NdeDataTable.vue';
import NdePaging from '@components/Common/NdePaging.vue';
import { exportAllPages, getExportDataFromColumns, pageExport } from './service';
import NdeTabItem from '@components/Tabs/NdeTabItem.vue';

export default {
  layout: NdeDashboardLayout,
  components: {
    NdeFilterForm,
    NdeDataTable,
    NdePaging,
    NdeTabItem,
  },

  data() {
    return {
      columns: [],
      controls: [],

      dataFilter: {},
      tableLoaded: false,
      page: 1,
      itemPerPage: 10,
      totalItems: 0,
      loadingInitally: false,
      loadingGenerateReport: false,

      data: [],
      CSVdata: [],
      order_by: 'username asc',

      customeProfiles: [],
      currentProfile: null,
    };
  },

  computed: {
    ...mapState(['currentProgram']),
    maxPage() {
      return Math.ceil(this.totalItems / this.itemPerPage);
    },
  },

  props: {
    profileId: {
      required: true,
      type: String,
    },
  },

  mounted() {
    this.getCustomDemandReports();
    this.getControls();
  },

  methods: {
    async handleGenerateReport() {
      this.loadingInitally = true;
      this.loadingGenerateReport = true;

      const resultCustomOnDemand = await this.$store.dispatch('callAPI', {
        url: '/customOnDemandReportOauth',
        method: 'post',
        data: {
          profile_id: this.profileId,
          order_by: this.order_by,
          page: this.page,
          page_size: this.itemPerPage,
          dataFilter: this.dataFilter,
        },
      });
      this.loadingGenerateReport = false;
      this.loadingInitally = false;
      console.log(resultCustomOnDemand);

      if (resultCustomOnDemand.message == 'success') {
        this.data = resultCustomOnDemand?.data?.data?.body?.data?.records || [];
        this.totalItems = resultCustomOnDemand?.data?.data?.body?.data?.total || 0;
        this.tableLoaded = true;
      }
    },

    handleQueueReport() {},

    handleChangeFilterData(id, value) {
      this.dataFilter[id] = value;
      this.page = 1;
      this.tableLoaded = false;
      this.loadingInitally = false;
      this.loadingGenerateReport = false;
    },

    pageExport() {
      pageExport(this.data, this.columns, this.CSVdata, this.page, 'Custom On Demand Report');
      this.CSVdata = [];
    },

    exportAll() {
      const payload = {
        exportdata: getExportDataFromColumns(this.columns),
        filename: uuidv4() + '.csv',
      };
      console.log(payload);
      exportAllPages(payload)
        .then((data) => {
          alert(
            'Download ' +
              payload.filename +
              ' is processing. Go to the My Documents link to download the file.',
          );
        })
        .catch((errors) => console.log(errors));
    },

    async getCustomDemandReports() {
      const resultCustomDemands = await this.$store.dispatch('callAPI', {
        url: '/getProfilesOauth',
        method: 'post',
        data: {
          modules: ['parentprofile', 'ondemand_reports'],
        },
      });

      if (resultCustomDemands.message == 'success') {
        let profiles = resultCustomDemands.data.data.body.data.profiles;
        this.generateCustomDemands(profiles);
      }
    },

    generateCustomDemands(profiles) {
      this.customeProfiles = [];
      profiles.map((profile) => {
        if (profile.module == 'Parent Profile') {
          if (profile.subProfiles) {
            profile.subProfiles.map((subprofile) => {
              if (subprofile.module == 'Custom On-Demand Report') {
                this.customeProfiles.push(subprofile);
              }
            });
          }
        }
      });

      this.currentProfile = this.customeProfiles.filter(item => item.id == this.profileId)[0]
    },

    async getControls() {
      this.$store.commit('setShowProgressAPI', true);
        const resultControls = await this.$store.dispatch('callAPI', {
          url: '/getCustomReportControlsOauth',
          method: 'post',
          data: {
            profile_id: this.profileId,
          },
        });
        console.log(resultControls);
        this.$store.commit('setShowProgressAPI', false);
        if (resultControls.message !== 'success') {
          return this.$swal({
            icon: 'error',
            text: this.getMessageResult(resultControls),
          });
        } else {
          this.columns = resultControls.data.data.body.data.columns;
          this.controls = resultControls.data.data.body.data.controls;

          this.controls.forEach((item) => {
            this.dataFilter[item.nql_param] = '';
          });
        }
    }
  },

  watch: {
  },
};
</script>

<style>
</style>