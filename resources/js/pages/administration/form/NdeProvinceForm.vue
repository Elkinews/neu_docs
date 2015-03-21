<template>
  <div class="nde-province-form">
    <v-form ref="form" v-model="valid" lazy-validation>
      <v-row>
        <v-col md="10" sm="12">
          <v-row>
            <v-col md="4" sm="12">
              <label>Name:</label>
              <v-text-field :rules="[rules.required]" dense single-line outlined v-model="dataProvinceDetail.name"
                aria-label="Province Name"></v-text-field>
            </v-col>
          </v-row>
        </v-col>
      </v-row>

      <p v-if="hasError" class="error--text">You have to choose at least 1 value for each section</p>
      <v-expansion-panels>
        <v-expansion-panel>
          <v-expansion-panel-header>
            Program Access
          </v-expansion-panel-header>

          <v-expansion-panel-content>
            <v-row>
              <v-col cols="12">
                <nde-program-treeview v-model="selectedPrograms" :programs="programAccess">
                </nde-program-treeview>
              </v-col>
            </v-row>
          </v-expansion-panel-content>
        </v-expansion-panel>

        <v-expansion-panel class="mt-3">
          <v-expansion-panel-header>
            Image groups
          </v-expansion-panel-header>

          <v-expansion-panel-content>
            <v-row>
              <v-col cols="12" sm="6" md="4" v-for="image_group in imageGroups" :key="image_group.id">
                <v-checkbox class="mt-0" hide-details v-model="selectedImageGroups" :value="`${image_group.id}`"
                  :label="`${image_group.label}`">
                </v-checkbox>
              </v-col>
            </v-row>
          </v-expansion-panel-content>
        </v-expansion-panel>

        <v-expansion-panel class="mt-3">
          <v-expansion-panel-header>
            Supervisors
          </v-expansion-panel-header>

          <v-expansion-panel-content>
            <v-row>
              <v-col cols="12" sm="6" md="4" v-for="supervisor in supervisors" :key="supervisor.id">
                <v-checkbox class="mt-0" hide-details v-model="selectedSupervisors" :value="`${supervisor.id}`"
                  :label="`${supervisor.label}`">
                </v-checkbox>
              </v-col>
            </v-row>
          </v-expansion-panel-content>
        </v-expansion-panel>

        <v-expansion-panel class="mt-3">
          <v-expansion-panel-header>
            Role
          </v-expansion-panel-header>

          <v-expansion-panel-content>
            <div v-for="(value, key, index) in permissionsControl.roles" :key="index">
              <div v-if="value.length !== 0" class="mb-5">
                <v-row align="center" class="ma-0 mb-3">
                  <h3 class="text-uppercase mr-2 pt-1">{{ key }}</h3>
                  <v-checkbox class="ma-0" hide-details v-model="selectedGroupRoles[key]" readonly
                    @click="clickSelectedGroupRoles(key)">
                  </v-checkbox>
                </v-row>
                <v-row>
                  <v-col cols="12" sm="6" md="4" v-for="role in value" :key="role.id">
                    <v-checkbox class="mt-0 text-lowercase" hide-details v-model="selectedRoles" :value="`${role.id}`"
                      :label="`${role.label}`">
                    </v-checkbox>
                  </v-col>
                </v-row>
              </div>
            </div>
          </v-expansion-panel-content>

        </v-expansion-panel>

        <v-expansion-panel class="mt-3">
          <v-expansion-panel-header>
            Report
          </v-expansion-panel-header>

          <v-expansion-panel-content>
            <v-row>
              <v-col cols="12" sm="6" md="4" v-for="report in reports" :key="report.id">
                <v-checkbox class="mt-0" hide-details v-model="selectedReports" :value="`${report.id}`"
                  :label="`${report.label}`">
                </v-checkbox>
              </v-col>
            </v-row>
          </v-expansion-panel-content>
        </v-expansion-panel>

        <v-expansion-panel class="mt-3">
          <v-expansion-panel-header>
            Custom On-Demand Reports
          </v-expansion-panel-header>

          <v-expansion-panel-content>
            <v-row>
              <v-col cols="12" sm="6" md="4" v-for="report in ondemandReports" :key="report.id">
                <v-checkbox class="mt-0" hide-details v-model="selectedOndemandReports" :value="`${report.id}`"
                  :label="`${report.label}`">
                </v-checkbox>

              </v-col>
            </v-row>
          </v-expansion-panel-content>
        </v-expansion-panel>
      </v-expansion-panels>
    </v-form>
  </div>
</template>

<script>
import { mapState } from "vuex"
import { intersection, uniq } from "lodash";
import NdeProgramTreeview from "@components/Inputs/NdeProgramTreeview.vue";

export default {
  components: {
    NdeProgramTreeview,
  },
  props: {
    dataProvinceDetail: {
      type: Object,
      default: () => { }
    },

    isEditProvince: {
      type: Boolean,
      default: false
    }
  },

  computed: {
    ...mapState(['permissionsControl']),
    programAccess() {
      return this.uniqueArr(this.permissionsControl.programs)
    },

    imageGroups() {
      return this.uniqueArr(this.permissionsControl.image_groups)
    },

    supervisors() {
      return this.uniqueArr(this.permissionsControl.supervisors)
    },

    reports() {
      return this.uniqueArr(this.permissionsControl.reports)
    },

    ondemandReports() {
      return this.uniqueArr(this.permissionsControl.ondemand_reports)
    },
  },

  data() {
    const selectedGroupRoles = {};
    for (const keyRole in this.permissionsControl?.roles) {
      selectedGroupRoles[keyRole] = false;
    }
    return {
      rules: {
        required: (value) => !!value || "Name is required.",
      },
      selectedPrograms: [],
      selectedImageGroups: [],
      selectedSupervisors: [],
      selectedReports: [],
      selectedOndemandReports: [],
      selectedRoles: [],
      selectedGroupRoles,
      hasError: false,
      valid: true,
    }
  },

  methods: {
    clickSelectedGroupRoles(keyRole) {
      const oldSelectedGroupRoles = this.selectedGroupRoles[keyRole];
      const roleIds = this.permissionsControl.roles[keyRole].map(dataRole => dataRole.id) || [];
      if (oldSelectedGroupRoles) {
        this.selectedRoles = this.selectedRoles.filter(roleId => !roleIds.includes(roleId));
      } else {
        this.selectedRoles = uniq([
          ...roleIds,
          ...this.selectedRoles,
        ]);
      }
    },

    updateSelectedGroupRoles() {
      for (const keyRole in this.permissionsControl.roles) {
        const roleIds = this.permissionsControl.roles[keyRole].map(dataRole => dataRole.id) || [];
        this.selectedGroupRoles[keyRole] = intersection(this.selectedRoles, roleIds).length === roleIds.length;
      }
    },

    uniqueArr(arr) {
      return arr.filter((v, i, a) => a.findIndex(e => (e.id === v.id)) === i)
    },

    mappingData(data) {
      this.selectedPrograms = data && data.programs
      this.selectedImageGroups = data && data.image_groups
      this.selectedSupervisors = data && data.supervisors
      this.selectedReports = data && data.reports
      this.selectedRoles = data && data.roles
      this.selectedRoles = data && data.roles
      this.selectedOndemandReports = data && data.ondemand_reports
    }
  },

  mounted() {
    if (this.isEditProvince) {
      this.mappingData(this.dataProvinceDetail)
      this.updateSelectedGroupRoles();
    } else {
      // We need to make options in Image groups already ticked when we open the window, as it is on the Create New User page.
      // const selectedImageGroups = [];
      // (this.imageGroups || []).forEach(dataImageGroup => {
      //   selectedImageGroups.push(dataImageGroup.id);
      // })
      // this.selectedImageGroups = selectedImageGroups;
    }


  },

  watch: {
    dataProvinceDetail(newVal) {
      if (this.isEditProvince) {
        this.mappingData(newVal)
      }
    },
    selectedRoles: {
      handler(newVal) {
        this.updateSelectedGroupRoles();
      },
      deep: true
    }
  }
}
</script>

<style lang="scss" scoped>
.nde-province-form {
  h3 {
    color: #9A9EA1;
  }
}
</style>