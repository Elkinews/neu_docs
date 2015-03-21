<template>
  <div class="nde-manage-users" role="main">
    <nde-tab-administration v-if="isShowManageUser" />
    <v-card class="mt-5 pa-5" v-if="isShowManageUser">
      <v-card-title>
        <v-row class="align-center">
          <v-col md="4" v-if="!isMobile">
            <h3 class="font-weight-bold mb-0">Manage Users</h3>
          </v-col>
          <v-col md="4" offset-md="4">
            <v-text-field dense outlined append-icon="mdi-magnify" placeholder="Search this user" hide-details
              :loading="isSearching" @input="handleTypeSearch" required aria-label="Search this user">
            </v-text-field>
          </v-col>
        </v-row>

        <div v-if="isMobile" class="d-flex flex-column my-5 w-100">
          <v-btn depressed outlined primary light color="primary" class="btn-provinces w-100"
            @click="openProvincesModal" v-if="authRoles.includes('ALLOWPROVINCE')">
            <v-icon left color="primary">mdi-home</v-icon>
            Provinces
          </v-btn>
          <v-btn class="mt-3 w-100" depressed outlined primary light color="success" @click="createNewUserOauth">
            <v-icon left color="#547d36">mdi-account-plus-outline </v-icon>
            Create New User
          </v-btn>
        </div>
      </v-card-title>
      <v-divider></v-divider>
      <v-card-text :class="{ 'd-flex justify-space-between filter-action': !isMobile, 'mt-3': isMobile }">
        <div :class="{ 'd-flex': !isMobile }">
          <v-checkbox class="mt-0 mr-3" :class="{ 'my-2': isMobile }" hide-details v-for="checkbox in checkboxes"
            :key="checkbox.value" v-model="selected" :value="checkbox.value" :label="checkbox.text" />
          <v-checkbox class="mt-0 mr-3" :class="{ 'my-2': isMobile }" hide-details v-model="selectedProvince"
            label="Is in province" />
          <v-select v-if="selectedProvince" dense solo attach hide-details class="nde-select mr-3 w-200px"
            v-model="province_id" :items="dataProvinces" @change="handleChangeProvince" outlined item-text="name"
            item-value="id" label="Provinces">
          </v-select>
          <div :class="{ 'my-5': isMobile, 'd-flex': !isMobile }">
            <nde-button color="primary" class="nde-auth-primary-btn mr-2" @click="applyFilter">Apply filter</nde-button>
            <nde-button @click="clear" class="nde-clear-btn">Clear</nde-button>
          </div>
        </div>
        <div v-if="!isMobile" class="d-flex">
          <v-btn depressed outlined primary light color="primary" class="btn-provinces" @click="openProvincesModal"
            v-if="authRoles.includes('ALLOWPROVINCE')">
            <v-icon left color="primary">mdi-home</v-icon>
            Provinces
          </v-btn>

          <v-btn depressed outlined primary light color="success" @click="createNewUserOauth" class="btn-create-new-user">
            <v-icon left color="#547d36">mdi-account-plus-outline </v-icon>
            Create New User
          </v-btn>
        </div>
      </v-card-text>

      <nde-data-table :itemsPerPage="defaultPayload['page_size']" :page="defaultPayload['page']" :isLoading="isLoading"
        :headers="headers" :items="users" class="nde-table-users nde-table-search-result"
        @updateSortBy="handleSortColumn" @updateSortDesc="handleSortDesc">
        <template slot="item.actions" slot-scope="{ item }">
          <nde-button-menu-items :items="actions" :document="item" @onEditUser="editUserInfoOauth"
            @onRequestResetAccountQuestion="resetSecurityQuestion" @onRequestResetPassword="resetUserPassOauth"
            @onDeleteUser="deleteUser" />
        </template>
        <template slot="item.is_new" slot-scope="{ item }">
          {{ item.is_new ? 'Yes' : 'No' }}
        </template>
        <template slot="item.supervisors" slot-scope="{ item }">
          <span v-for="(supervisor, index) in (item.supervisors)" :key="index">
            {{ supervisor.name }}<span v-if="index !== item.supervisors.length - 1">,</span>
          </span>
        </template>
        <template slot="item.provinces" slot-scope="{ item }">
          <span v-for="(province, index) in item.provinces" :key="index">
            {{ province.name }}<span v-if="index !== item.provinces.length - 1">,</span>
          </span>
        </template>
        <template slot="item.is_expired" slot-scope="{ item }">
          <span>
            {{ item.password_status === 'current' ? 'No' : item.password_status === 'expired' ? 'Yes' : "" }}
          </span>
        </template>
      </nde-data-table>

      <v-card-title>
        <nde-footer-table :page="defaultPayload['page']" :pageCount="totalPage" @paging:update="handlePaging"
          :isVisibleExportCSV="false" />
      </v-card-title>
    </v-card>
    <v-alert type="error" v-else class="mt-5">
      You don't have permission to access this page
    </v-alert>

    <ProvincesModal ref="provincesModal" :data="dataProvinces" @onGetProvinces="getProvinces" />

    <ManageUser ref="manageUsersModal" :isEditUser="isEditUser" :dataProvinces="dataProvinces"
      @onUpdateStatusModal="handleResetModal" @onUpdateUserInfo="handleUpdateUserInfo" />

  </div>
</template>

<script>
import { mapState } from "vuex";
import NdeDashboardLayout from "../../shared/layouts/dashboard/NdeDashboardLayout.vue";
import NdeTabAdministration from "../../components/Tabs/NdeTabAdministration.vue";
import ProvincesModal from "./dialog/ProvincesModal.vue";
import ManageUser from "./dialog/ManageUsersModal.vue";
import NdeDataTable from "@components/Table/NdeDataTable.vue";
import NdeButton from "../../components/Button/NdeButton.vue";
import NdeButtonMenuItems from '@components/Table/NdeSearchResult/NdeButtonMenuItems.vue';
import NdeFooterTable from '@components/Table/NdeSearchResult/NdeFooterTable.vue';
import { uniq } from 'lodash'

export default {
  layout: NdeDashboardLayout,
  components: {
    NdeTabAdministration,
    ProvincesModal,
    NdeButton,
    NdeButtonMenuItems,
    NdeDataTable,
    NdeFooterTable,
    ManageUser,
  },
  data() {
    return {
      authRoles: [],
      isLoading: false,
      isSearching: false,
      typingDebounceTimer: null,
      typeDebounceDuration: 800,
      selected: [],
      selectedProvince: false,
      province_id: null,
      checkboxes: [
        {
          value: 'current',
          text: 'Has current password'
        },
        {
          value: 'expired',
          text: 'Has expired password'
        },
      ],
      headers: [
        {
          text: 'Actions',
          value: 'actions',
          class: 'col-actions',
          sortable: false
        },
        {
          text: 'Name',
          value: 'name',
          class: 'col-name'
        },
        {
          text: 'Username',
          value: 'username',
          class: 'col-username'
        },
        {
          text: 'Email',
          value: 'email',
          class: 'col-email'
        },
        {
          text: 'Supervisors',
          value: 'supervisors',
          class: 'col-supervisors',
          sortable: false
        },
        {
          text: 'Province',
          value: 'provinces',
          class: 'col-province',
          sortable: false,
        },
        {
          text: 'Is Expired',
          value: 'is_expired',
          class: 'col-is-expired'
        },
        {
          text: 'Temporary Password',
          value: 'temp_password',
          class: 'col-temporary-password',
          sortable: false,
        },
        {
          text: 'Is New User',
          value: 'is_new',
          class: 'col-is-new-user'
        },

      ],
      users: [],
      actions: [
        {
          id: 1,
          content: 'Edit This User',
          status: 'active',
          icon: 'edit.png',
        },

        {
          id: 3,
          content: 'Reset Password',
          status: 'active',
          icon: 'Lock.png',
        },
        {
          id: 4,
          content: 'Reset Security Questions',
          status: 'active',
          icon: 'Reset.png',
        },
        {
          id: 5,
          content: 'Delete This User',
          status: 'warning',
          icon: 'delete.png',
        },
      ],

      dataProvinces: [],

      total: null,
      defaultPayload: {
        "page_size": 20,
        "page": 1,
        "order_by": "username asc",
      },

      debounceDuration: 800,
      debounceTimer: null,

      isEditUser: false,
      column: '',
      isDesc: false,

      isShowManageUser: false
    }
  },

  computed: {
    totalPage() {
      return Math.ceil(this.total / this.page_size) || 0
    },
    ...mapState(['permissionsControl']),
    reports() {
      return this.uniqueArr(this.permissionsControl.reports)
    },
    ondemandReports() {
      return this.uniqueArr(this.permissionsControl.ondemand_reports)
    },
  },

  methods: {
    uniqueArr(arr) {
      return arr.filter((v, i, a) => a.findIndex(e => (e.id === v.id)) === i)
    },

    handleSortColumn(column) {
      this.column = column
    },

    getOrderByColumn() {
      if (this.column === 'is_expired') {
        return 'password_status'
      }
      if (this.column === 'is_new') {
        return 'is_new_user'
      }
      return this.column;
    },

    handleSortDesc(isDesc) {
      if (!this.column) {
        // Default Order by
        this.defaultPayload['order_by'] = 'username asc';
      } else {
        this.defaultPayload['order_by'] = `${this.getOrderByColumn()} ${isDesc ? 'desc' : 'asc'}`;
      }
      return this.getUsersOauth(this.defaultPayload);
    },

    handleResetModal(value) {
      this.isEditUser = value
    },

    handleTypeSearch(value) {
      delete this.defaultPayload['name_search'];
      if (value) {
        this.defaultPayload['name_search'] = value.trim();
      }
      this.getUsersOauth(this.defaultPayload)
    },

    openProvincesModal() {
      this.$refs.provincesModal.dialog = true
    },

    clear() {
      this.selected = []
      this.defaultPayload.page = 1
      this.selectedProvince = false

      if (this.defaultPayload.password_status) delete this.defaultPayload.password_status
      if (this.defaultPayload.in_province) delete this.defaultPayload.in_province

      this.getUsersOauth(this.defaultPayload)
    },

    resetSecurityQuestion(item) {
      this.$store.dispatch('callAPI',
        {
          url: '/accountDeleteSecurityAnswersOauth',
          method: 'delete',
          data: {
            reset_account_id: parseInt(item.account_id)
          }
        }).then(response => {
          let message = ""
          if (response.message === 'success') {
            alert('Successully Reset User Security Questions.')
            message = `Reset security questions for Account ID ${item.id}`
            this.insertIntoUserAuditOauth(item.id, message)
          } else {
            message = response?.data?.data?.message
            alert(message)
          }

        })
    },

    resetUserPassOauth(item) {
      this.$store.dispatch('increaseCallCount');
      axios
        .post('/accountForcePasswordResetOauth', {
          userID: parseInt(item.account_id)
        })
        .then(async (response) => {
          if (response?.data?.message === 'success') {
            this.getUsersOauth(this.defaultPayload);
            await this.$swal({
              title: 'Reset Password',
              text: 'Successfully Reset User Password.!',
              type: 'success',
              showCancelButton: false,
              confirmButtonText: 'Ok',
            });
            const temp_pass = response?.data?.data?.message?.temp_pass
            const message = `Reset account id: ${item.id} password to ${temp_pass}`

            this.insertIntoUserAuditOauth(item.id, message)
          }
        })
        .catch((error) => {
          console.error(error);
        });
    },

    insertIntoUserAuditOauth(accountId, message) {
      this.$refs.manageUsersModal.isLoading = true

      let payload = {
        level: 4,
      }

      if (!message) {
        payload.message = this.isEditUser ? `Edit Account ID ${accountId} Information` : `Create Account ID ${accountId}`
      } else {
        payload.message = message
      }
      this.$store.dispatch('increaseCallCount');
      axios
        .post('/insertIntoUserAuditOauth', payload)
        .catch((error) => {
          console.error(error);
        });
    },

    async handleUpdateUserInfo(payload) {
      this.$refs.manageUsersModal.isLoading = true;
      this.$store.commit('setShowProgressAPI', true);

      let profiles = payload.programs

      // Merge reports into profiles
      if (payload.reports && payload.reports.length) {
        if (!Array.isArray(payload.reports)) {
          profiles.push(payload.reports)
        } else {
          payload.reports.forEach(value => {
            profiles.push(value)
          })
        }
      }

      // Merge ondemandReports into profiles
      if (payload.ondemandReports && payload.ondemandReports.length) {
        if (!Array.isArray(payload.ondemandReports)) {
          profiles.push(payload.ondemandReports)
        } else {
          payload.ondemandReports.forEach(value => {
            profiles.push(value)
          })
        }
      }

      profiles = profiles.map(Number);
      const reportParentProfiles = [];
      profiles.forEach(profileId => {
        const dataReport = this.reports.find(report => +report.id === profileId);
        const parentId = +dataReport?.display_parent;
        if (parentId && !reportParentProfiles.includes(parentId)) {
          reportParentProfiles.push(parentId)
        }
      });

      profiles.push(...reportParentProfiles);


      const ondemandReportsParentProfiles = [];
      profiles.forEach(profileId => {
        const dataReport = this.ondemandReports.find(report => +report.id === profileId);
        const parentId = +dataReport?.display_parent;
        if (parentId && !ondemandReportsParentProfiles.includes(parentId)) {
          ondemandReportsParentProfiles.push(parentId)
        }
      });

      profiles.push(...ondemandReportsParentProfiles);

      profiles = uniq(profiles);

      if (payload.type === 'province') {
        profiles = [];
        payload.roles = [];
        payload.image_groups = [];
        payload.supervisors = [];
      } else {
        payload.provinces = [];
      }

      let newData = {
        'user_info': {
          'fullname': payload.name,
          'username': payload.username,
          'email': payload.email,
          'view_empty_tabs': JSON.stringify(payload.view_empty_tabs),
          'type': payload.type,
          'profiles': profiles,
          'roles': payload.roles.map(Number),
          'image_groups': payload.image_groups.map(Number),
          'supervisors': payload.supervisors.map(Number),
          'provinces': payload.provinces.map(Number),
        },
      }

      let editData = {
        'user_info': {
          'fullname': payload.name,
          'username': payload.username,
          'email': payload.email,
          'view_empty_tabs': payload.view_empty_tabs,
          'starrs_access': 'false',
          'can_be_assigned': payload.can_be_assigned,
          'user_id': payload.account_id,
          'is_esd': 'true',
          'mobile_status': 'false',
          'has_mfa': payload.has_mfa,
          'is_mfa_setup': payload.is_mfa_setup,
          'email_mfa': payload.email_mfa,
          'type': payload.type,
          'profiles': profiles,
          'roles': payload.roles.map(Number),
          'image_groups': payload.image_groups.map(Number),
          'supervisors': payload.supervisors.map(Number),
          'provinces': payload.provinces.map(Number),
        },
      }

      // if (!this.isEditUser) {
      //   delete data['user_info'].id
      // }

      const resultUpdateUserOauth = await this.$store.dispatch('callAPI', {
        url: this.isEditUser ? '/editUserInfoOauth' : '/createNewUserOauth',
        method: 'post',
        data: this.isEditUser ? editData : newData,
      });

      this.$refs.manageUsersModal.isLoading = false;
      this.$store.commit('setShowProgressAPI', false);

      if (resultUpdateUserOauth.message !== 'success') {
        return this.$swal({
          icon: 'error',
          text: this.getMessageResult(resultUpdateUserOauth),
        });
      }
      console.log("success message: ", resultUpdateUserOauth?.data?.message?.message)
      await this.$swal({
        icon: 'success',
        text: resultUpdateUserOauth?.data?.message?.message,
      });

      this.insertIntoUserAuditOauth(payload.id);
      this.getUsersOauth(this.defaultPayload);
      this.$refs.manageUsersModal.hideModal();
    },

    editUserInfoOauth(item) {
      this.isEditUser = true
      this.$refs.manageUsersModal.showModal = true
      this.$refs.manageUsersModal.item = item
    },

    createNewUserOauth() {
      this.isEditUser = false
      this.$refs.manageUsersModal.showModal = true
      this.$refs.manageUsersModal.isLoading = false
      // this.$refs.manageUsersModal.dataUserDetail.email_intake_status = 'false';
      this.$refs.manageUsersModal.dataUserDetail.view_empty_tabs = true;
      this.$refs.manageUsersModal.dataUserDetail.hasMfa = 'off';
      this.$refs.manageUsersModal.dataUserDetail.isMfaSetup = 'off';
      this.$refs.manageUsersModal.dataUserDetail.emailMfa = 'off';
    },

    getUsersOauth(data) {
      this.isLoading = true
      let payload = data || {}

      this.debounceTimer && clearTimeout(this.debounceTimer)

      this.debounceTimer = setTimeout(() => {
        this.$store.dispatch('increaseCallCount');
        axios
          .get('/adminGetUsersOauth', { params: payload })
          .then((response) => {
            this.isLoading = false
            if (response && response.data && response.data.message === "success") {
              this.users = response?.data?.data?.message?.users || []
              this.page = response?.data?.data?.message?.page
              this.page_size = response?.data?.data?.message?.count
              this.total = response?.data?.data?.message?.total
            }
          })
          .catch((error) => {
            this.users = []
            this.isLoading = false
            console.error(error);
          });
      }, this.debounceDuration)

    },

    getProvinces() {
      this.$store.commit('setShowProgressAPI', true);
      this.$store.dispatch('getProvinces', { page: 1, page_size: 500 }).then((response) => {
        this.$store.commit('setShowProgressAPI', false);
        if (response && response.data && response.data.provinces && response.data.provinces.length) {
          this.dataProvinces = response.data.provinces
        }
      })
    },

    applyFilter() {

      let password_status = ""

      if (this.selected.length) {
        this.selected.length > 1 ? password_status = this.selected.join(',') : password_status = this.selected[0]
        this.defaultPayload.password_status = password_status
        this.defaultPayload.page = 1
      } else {
        delete this.defaultPayload.password_status
      }


      if (!this.selectedProvince) {
        delete this.defaultPayload.in_province
      }

      this.getUsersOauth(this.defaultPayload)

    },

    handlePaging(page) {
      this.defaultPayload.page = page
      this.getUsersOauth(this.defaultPayload)
    },

    handleChangeProvince(province_id) {
      this.defaultPayload.in_province = province_id
    },

    async deleteUser(dataUser) {
      const confirmed = await this.$swal({
        title: 'User Delete Confirmation',
        text: 'Are you sure you want to delete this user?',
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
      const accountId = dataUser.id || dataUser.account_id;
      const resultInsertIntoUserAuditOauth = await this.$store.dispatch('callAPI', {
        url: '/insertIntoUserAuditOauth',
        method: 'post',
        data: {
          level: 4,
          message: `Delete Account ID ${accountId} Information`,
        }
      });

      if (resultInsertIntoUserAuditOauth.message !== 'success') {
        this.$store.commit('setShowProgressAPI', false);
        return this.$swal({
          icon: 'error',
          text: this.getMessageResult(resultInsertIntoUserAuditOauth),
        });
      }

      const resultDeleteUserOauth = await this.$store.dispatch('callAPI', {
        url: '/deleteUserInfoOauth',
        method: 'post',
        data: {
          user_id: dataUser.id || dataUser.account_id,
        },
      });
      this.$store.commit('setShowProgressAPI', false);
      if (resultDeleteUserOauth.message !== 'success') {
        return this.$swal({
          icon: 'error',
          text: this.getMessageResult(resultDeleteUserOauth),
        });
      }
      await this.$swal({
        icon: 'success',
        text: this.getMessageResult(resultDeleteUserOauth),
      });
      this.getUsersOauth(this.defaultPayload);
    }

  },

  async mounted() {
    await this.getUsersOauth(this.defaultPayload)
    await this.getProvinces()

    this.authRoles = this.userLoginRoles();
    if (this.authRoles.includes('USERMANAGEMENT')) {
      this.isShowManageUser = true
    }

  },
  watch: {
    selectedProvince(newValue) {
      if (newValue) {
        this.handleChangeProvince(this.province_id);
      }
    }
  }
}
</script>

<style lang="scss">
.w-100 {
  width: 100% !important;
}

.btn-provinces {
  background: $primaryGreyColor;
  margin-right: 8px;
}

.btn-provinces,
.btn-manage {
  text-transform: unset !important;
}

button {
  text-transform: unset !important;
}

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
    word-wrap: break-word;
    text-align: left;
  }
}

.nde-clear-btn {
  color: #C32C39 !important;
  background-color: #F9E1E1 !important;
  border: 1px solid #C32C39;
  border-radius: 8px;
  width: 7.188rem;
  box-shadow: none;
}

.nde-auth-primary-btn {
  border-radius: 8px
}

.dialog-w-custombar {

  /* width */
  ::-webkit-scrollbar {
    width: 4px;
  }

  /* Track */
  ::-webkit-scrollbar-track {
    background: #f1f1f1;
  }

  /* Handle */
  ::-webkit-scrollbar-thumb {
    background: $primaryColor;
    border-radius: 8px
  }

  /* Handle on hover */
  ::-webkit-scrollbar-thumb:hover {
    background: $primaryColor;
  }
}


.nde-manage-users {
  .w-100 {
    width: 100% !important;
  }

  .nde-select {
    width: 15.625rem;
  }
}

@media screen and (max-width: 48rem) {
  .nde-manage-users {

    .v-card__title,
    .v-card__text {
      padding: 0 !important
    }

    .nde-table-search-result td {
      max-width: 18.75rem;
    }
  }
}

@media screen and (min-width: 60rem) and (max-width:79rem) {
  .nde-manage-users {
    .filter-action {
      overflow: hidden;
      overflow-x: scroll
    }
  }
}
</style>
