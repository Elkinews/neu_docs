<template>
  <v-navigation-drawer
    v-model="drawer"
    :mini-variant.sync="mini"
    :permanent="!isMobile"
    :absolute="isMobile"
    :temporary="isMobile"
    width="445"
    role="navigation"
    height="95vh"
  >
    <div v-if="isMobile" style="margin-left: -1rem">
      <v-list-item class="close-sidebar">
        <v-icon color="red" aria-label="Close" size="1.5rem" @click.prevent="drawer = false"
          >mdi-window-close</v-icon
        >
      </v-list-item>
      <v-list-item>
        <nde-dashboard-user-menu />
      </v-list-item>
      <v-divider></v-divider>
      <v-list-item class="mt-2 mb-2 pl-1">
        <nde-dashboard-program-menu :programs-json="programsJson" :profileId="profileId" />
      </v-list-item>
      <v-divider></v-divider>
    </div>

    <v-list dense nav>
      <v-list-item v-if="!isMobile">
        <v-list-item-icon>
          <v-tooltip right role="tooltip">
            <template v-slot:activator="{ on, attrs }">
              <button
                v-bind="attrs"
                v-on="on"
                role="button"
                aria-label="Menu List"
                :aria-expanded="!mini ? 'true' : 'false'"
                aria-controls="main-menu"
                type="button"
                @click.prevent="mini = !mini"
                :class="{ 'mdi-menu': mini, 'mdi-window-close': !mini }"
                class="v-icon notranslate close-open-icon v-icon--link mdi theme--light"
              ></button>
            </template>
            <span>Menu list</span>
          </v-tooltip>
        </v-list-item-icon>
      </v-list-item>
      <v-list-item
        link
        :href="isMultiTask ? '/dashboard' : ''"
        @click.prevent="gotoPage('/dashboard')"
        value="Record Search"
        v-if="hasModule('ESD')"
      >
        <v-list-item-icon aria-labelledby="record-search">
          <v-tooltip right role="tooltip">
            <template v-slot:activator="{ on, attrs }">
              <v-icon v-bind="attrs" v-on="on" :color="isProfilePage ? 'red' : ''"
                >mdi-file-search-outline</v-icon
              >
            </template>
            <span>Record Search</span>
          </v-tooltip>
        </v-list-item-icon>

        <v-list-item-content aria-labelledby="record-search">
          <v-list-item-title :class="{ activeText: isProfilePage }"
            >Record Search</v-list-item-title
          >
        </v-list-item-content>
      </v-list-item>

      <v-list-group @click="getWorkFlow" v-if="hasModule('Workflow')">
        <template v-slot:activator>
          <v-list-item value="Workflow" class="px-0">
            <v-list-item-icon aria-labelledby="workflow">
              <v-tooltip right role="tooltip">
                <template v-slot:activator="{ on, attrs }">
                  <v-icon v-bind="attrs" v-on="on">mdi-sitemap</v-icon>
                </template>
                <span>Workflow</span>
              </v-tooltip>
            </v-list-item-icon>

            <v-list-item-content>
              <v-list-item-title>Workflow</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </template>

        <template v-slot:appendIcon v-if="isLoadingWorkflow && !isLoadedWorkflow">
          <v-progress-circular indeterminate color="primary"></v-progress-circular>
        </template>

        <v-list-item
          v-for="(item, i) in workflows"
          :key="i"
          link
          :href="isMultiTask ? env.STARRS_URL : ''"
          @click.prevent="gotoPage(env.STARRS_URL)"
        >
          <v-list-item-content class="pl-15">
            <v-list-item-title v-text="item.name"></v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list-group>

      <v-list-group
        @click="getReports"
        :color="isReportPage ? 'red' : ''"
        v-if="hasModule('Report')"
      >
        <template v-slot:activator>
          <v-list-item value="reports" class="px-0">
            <v-list-item-icon aria-labelledby="reports">
              <v-tooltip right role="tooltip">
                <template v-slot:activator="{ on, attrs }">
                  <v-icon :color="isReportPage ? 'red' : ''" v-bind="attrs" v-on="on"
                    >mdi-file-document-multiple-outline</v-icon
                  >
                </template>
                <span>Reports</span>
              </v-tooltip>
            </v-list-item-icon>

            <v-list-item-content>
              <v-list-item-title :class="{ activeText: isReportPage }">Reports</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </template>

        <template v-slot:appendIcon v-if="isLoadingReports && !isLoadedReports">
          <v-progress-circular indeterminate color="primary"></v-progress-circular>
        </template>

        <v-list-item
          v-for="(item, i) in reports"
          :key="i"
          :link="isMultiTask"
          :href="isMultiTask ? item.path : ''"
          @click.prevent="gotoPage(item.path)"
        >
          <v-list-item-content class="pl-15">
            <v-list-item-title v-text="item.name"></v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list-group>

      <v-list-group
        @click="getCustomDemandReports"
        :color="isCustomePage ? 'red' : ''"
        v-if="hasModule('Custom On-Demand Report')"
      >
        <template v-slot:activator>
          <v-list-item value="Custom On-Demand Report" class="px-0">
            <v-list-item-icon aria-labelledby="custom-on-demand-report">
              <v-tooltip right role="tooltip">
                <template v-slot:activator="{ on, attrs }">
                  <v-icon :color="isCustomePage ? 'red' : ''" v-bind="attrs" v-on="on"
                    >mdi-file-document-edit-outline</v-icon
                  >
                </template>
                <span>Custom On-Demand Report</span>
              </v-tooltip>
            </v-list-item-icon>

            <v-list-item-content>
              <v-list-item-title :class="{ activeText: isCustomePage }"
                >Custom On-Demand Report</v-list-item-title
              >
            </v-list-item-content>
          </v-list-item>
        </template>

        <template v-slot:appendIcon v-if="isLoadingOnDemand && !isLoadedOnDemand">
          <v-progress-circular indeterminate color="primary"></v-progress-circular>
        </template>

        <v-list-item
          v-for="(item, i) in customeProfiles"
          :key="i"
          :link="isMultiTask"
          value="Custom On-Demand Report"
          :href="isMultiTask ? '/ondemand_report?profileId=' + item.id : ''"
          @click.prevent="gotoPage('/ondemand_report?profileId=' + item.id)"
        >
          <v-list-item-content class="pl-15">
            <v-list-item-title v-text="item.name"></v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list-group>

      <v-list-item
        v-if="
          hasModule('User Management') || 
          userLoginRoles().includes('USERMANAGEMENT') ||
          userLoginRoles().includes('NEUBUSADMIN') ||
          administrationRole
        "
        value="Administration"
        :link="isMultiTask"
        :href="isMultiTask ? '/administration/general' : ''"
        @click.prevent="gotoPage('/administration/general')"
      >
        <v-list-item-icon aria-labelledby="administration">
          <v-tooltip right role="tooltip">
            <template v-slot:activator="{ on, attrs }">
              <v-icon v-bind="attrs" v-on="on" :color="isAdminPage ? 'red' : ''"
                >mdi-cog-outline</v-icon
              >
            </template>
            <span>Administration</span>
          </v-tooltip>
        </v-list-item-icon>

        <v-list-item-content>
          <v-list-item-title
            :class="{ activeText: isAdminPage }"
            v-text="'Administration'"
          ></v-list-item-title>
        </v-list-item-content>
      </v-list-item>
    </v-list>

    <div class="sidebar-bottom">
      <v-divider></v-divider>
      <v-list-item
        :href="isMultiTask ? '/faq' : ''"
        @click.prevent="gotoPage('/faq')"
        @keyup.enter="gotoPage('/faq')"
        value="faq"
        role="button"
        tabindex="0"
        aria-labelledby="FAQ"
      >
        <v-list-item-icon>
          <v-tooltip right role="tooltip">
            <template v-slot:activator="{ on, attrs }">
              <v-icon v-bind="attrs" v-on="on" :color="isFaqPage ? 'red' : ''">mdi-phone</v-icon>
            </template>
            <span>FAQ</span>
          </v-tooltip>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title :class="{ activeText: isFaqPage }">FAQ</v-list-item-title>
        </v-list-item-content>
      </v-list-item>

      <v-list-item
        :href="isMultiTask ? '/help-center' : ''"
        @click.prevent="gotoPage('/help-center')"
        @keyup.enter="gotoPage('/help-center')"
        value="help"
        role="button"
        tabindex="0"
        aria-labelledby="Help"
      >
        <v-list-item-icon aria-labelledby="help">
          <v-tooltip right role="tooltip">
            <template v-slot:activator="{ on, attrs }">
              <v-icon v-bind="attrs" v-on="on" :color="isHelpPage ? 'red' : ''"
                >mdi-comment-outline</v-icon
              >
            </template>
            <span>Help</span>
          </v-tooltip>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title :class="{ activeText: isHelpPage }">Help</v-list-item-title>
        </v-list-item-content>
      </v-list-item>
    </div>
  </v-navigation-drawer>
</template>

<script>
import { mapState } from 'vuex';
import axios from 'axios';
import NdeDashboardUserMenu from './NdeDashboardUserMenu.vue';
import NdeDashboardProgramMenu from './NdeDashboardProgramMenu';

export default {
  components: {
    NdeDashboardUserMenu,
    NdeDashboardProgramMenu,
  },
  props: {
    type: {
      type: String,
      required: false,
      default: '',
    },
    programsJson: {
      type: String,
      required: false,
      default: '',
    },
    profileId: {
      type: String,
    },
  },
  data() {
    return {
      mini: false,
      drawer: false,
      isLoadingOnDemand: false,
      isLoadedOnDemand: false,
      customeProfiles: [],

      isLoadingReports: false,
      isLoadedReports: false,
      reports: [],

      isLoadingWorkflow: false,
      isLoadedWorkflow: false,
      workflows: [],

      items: [
        { title: 'Dashboard', icon: 'mdi-view-dashboard' },
        { title: 'Photos', icon: 'mdi-image' },
        { title: 'About', icon: 'mdi-help-box' },
      ],
      right: null,
    };
  },
  computed: {
    ...mapState(['programs', 'env', 'modules']),
    administrationRole() {
      var Arole = false;
      if (Array.isArray(this.programs)) {
        this.programs.map((program) => {
          if (program.name === 'Administration') {
            Arole = true;
          }
        });
      }
      return Arole;
    },

    isProfilePage() {
      const url = window.location.href;
      if (
        (url.includes('/dashboard') || url.includes('/search-profile')) &&
        !url.includes('/dashboard/report')
      ) {
        return true;
      }

      return false;
    },

    isReportPage() {
      const url = window.location.href;
      if (url.includes('/dashboard/report')) {
        return true;
      }

      return false;
    },

    isCustomePage() {
      const url = window.location.href;
      if (url.includes('/ondemand_report')) {
        return true;
      }

      return false;
    },

    isAdminPage() {
      const url = window.location.href;
      if (url.includes('/administration')) {
        return true;
      }

      return false;
    },

    isFaqPage() {
      const url = window.location.href;
      if (url.includes('/faq')) {
        return true;
      }

      return false;
    },

    isHelpPage() {
      const url = window.location.href;
      if (url.includes('/help-center')) {
        return true;
      }

      return false;
    },
  },
  methods: {
    open() {
      this.drawer = true;
    },
    hasModule(module) {
      const isExist = this.modules.find(x => x.name.toLowerCase() == module.toLowerCase());
      return isExist ? true : false;
    },
    async getCustomDemandReports() {
      if (!this.isLoadedOnDemand) {
        this.isLoadingOnDemand = true;

        const resultCustomDemands = await this.$store.dispatch('callAPI', {
          url: '/getProfilesOauth',
          method: 'post',
          data: {
            modules: ['parentprofile', 'ondemand_reports'],
          },
        });

        this.isLoadingOnDemand = false;

        console.log(resultCustomDemands);

        if (resultCustomDemands.message == 'success') {
          let profiles = resultCustomDemands.data.data.body.data.profiles;
          this.generateCustomDemands(profiles);
          this.isLoadedOnDemand = true;
        } else {
          this.isLoadedOnDemand = false;
        }
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
    },

    async getReports() {
      if (!this.isLoadedReports) {
        this.isLoadingReports = true;

        const resultReports = await this.$store.dispatch('callAPI', {
          url: '/getProfilesOauth',
          method: 'post',
          data: {
            modules: ['parentprofile', 'report'],
          },
        });

        this.isLoadingReports = false;

        console.log(resultReports);

        if (resultReports.message == 'success') {
          let reports = resultReports.data.data.body.data.profiles;
          this.generateReports(reports);
          this.isLoadedReports = true;
        } else {
          this.isLoadedReports = false;
        }
      }
    },

    generateReports(profiles) {
      this.reports = [];
      profiles.map((profile) => {
        if (profile.module == 'Parent Profile') {
          if (profile.subProfiles) {
            profile.subProfiles.map((subprofile) => {
              if (subprofile.module == 'Report') {
                this.reports.push(subprofile);
              }
            });
          }
        }
      });

      this.reports.forEach((report) => {
        const reportPathName = (report?.name || '').replace(/&/g, '').replace(/ /g, '');
        report.path =
          '/dashboard/report/' + reportPathName.charAt(0).toLowerCase() + reportPathName.slice(1);
      });
    },

    getWorkFlow() {
      if (!this.isLoadedWorkflow) {
        this.isLoadingWorkflow = true;
        this.$store.dispatch('increaseCallCount');
        axios
          .get('/getWorkflowProfilesOauth')
          .then((response) => {
            this.isLoadingWorkflow = false;
            console.log(response);
            if (response?.data?.message == 'success') {
              this.isLoadedWorkflow = true;
              this.workflows = response?.data?.data?.profiles;
            } else {
            }
          })
          .catch((error) => {
            console.log(error);
            this.isLoadingWorkflow = false;
            this.$swal({
              icon: 'error',
              text: 'Please try again!',
            });
          });
      }
    },
  },
  mounted() {},
  created() {
    if (!this.isMobile) this.mini = true;
  },
  watch: {},
};
</script>

<style scoped lang="scss">
.activeText {
  color: red;
}

::v-deep .v-navigation-drawer__content {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}
.nde-dashboard-sidebar {
  // display: flex;
  // flex-direction: column;
  // justify-content: space-between;
  height: calc(100vh - 250px);
  overflow-y: auto;
  overflow-x: hidden;
}
.close-open-icon.v-icon:focus::after {
  opacity: 0 !important;
}
.v-list-item {
  &.active {
    &::before {
      opacity: 0.04;
    }
  }
}

/* width */
::-webkit-scrollbar {
  width: 5px;
}

/* Track */
::-webkit-scrollbar-track {
  background: #fff;
}

/* Handle */
::-webkit-scrollbar-thumb {
  background: $primaryColor;
  border-radius: 3px;
}

/* Handle on hover */
::-webkit-scrollbar-thumb:hover {
  background: $primaryColor;
}
.close-sidebar {
  flex-direction: row-reverse;
  .v-icon:hover {
    &::after {
      @extend %afterIconModalClose;
    }
  }
}
</style>

<style lang="scss">
.v-list--nav div:focus-visible,
.v-list--nav div.v-list-group:focus-visible,
.v-list--nav a.v-list-group:focus-visible,
.v-list--nav div.v-list-item:focus-visible .sidebar-bottom div.v-list-item:focus-visible {
  outline: -webkit-focus-ring-color auto 1px;
}
</style>
