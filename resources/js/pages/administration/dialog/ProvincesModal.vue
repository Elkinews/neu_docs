<template>
  <div>
    <v-dialog v-model="dialog" scrollable max-width="1208px" content-class="dialog-w-custombar" :persistent="true">
      <v-card>
        <v-card-title class="d-flex justify-space-between pb-0 title-provinces-modal">
          <p class="mb-0">
            Manage Province
          </p>
          <v-icon aria-label="Close" size="24px" color="red" @click="closeModal">
            mdi-close
          </v-icon>
        </v-card-title>
        <v-card-title class="pt-0 pb-0">
          <v-btn @click="clickCreateNewProvince" depressed outlined primary light color="success" width="180px"
            class="btn-create-province my-5">
            Create New Province
          </v-btn>
        </v-card-title>
        <v-card-text>
          <nde-data-table :itemsPerPage="data && data.length" :isLoading="isLoading" :headers="headers" :items="data"
            class="nde-table-search-result">
            <template slot="item.neubus_predefined" slot-scope="{ item }">
              {{ item.neubus_predefined === 'f' ? 'No' : 'Yes' }}
            </template>
            <template slot="item.actions" slot-scope="{ item }">
              <div style="position: relative;">
                <nde-button-menu-items :items="actions" :document="item" @onEditProvince="editProvince"
                  @onDeleteProvince="deleteProvince" />
              </div>

            </template>
          </nde-data-table>
        </v-card-text>
      </v-card>
    </v-dialog>
    <NdeManageProvincesModal v-if="isModalProvince" @onGetProvinces="getProvinces" />
  </div>

</template>

<script>
import { mapState } from "vuex";

import NdeDataTable from "@components/Table/NdeDataTable.vue";
import NdeButtonMenuItems from '@components/Table/NdeSearchResult/NdeButtonMenuItems.vue';
import NdeManageProvincesModal from "./NdeManageProvincesModal.vue";

export default {
  components: {
    NdeDataTable,
    NdeButtonMenuItems,
    NdeManageProvincesModal
  },

  props: {
    data: {
      type: Array,
      default: () => []
    }
  },

  data() {
    return {
      dialog: false,
      headers: [
        {
          text: '',
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
          text: 'Neubus Predefined',
          value: 'neubus_predefined',
          class: 'col-neubus-predefined'
        },
        {
          text: 'Date Created',
          value: 'created_on',
          class: 'col-created-created'
        },
      ],
      // data: [],
      actions: [
        {
          id: 1,
          content: 'Edit this Province',
          status: 'active',
          icon: 'edit.png',
        },


        {
          id: 2,
          content: 'Delete this Province',
          status: 'warning',
          icon: 'delete.png',
        },
      ],
      isLoading: false
    }
  },

  computed: {
    ...mapState([
      'isModalProvince',
      'permissionsControl',
    ]),
    ondemandReports() {
      return this.uniqueArr(this.permissionsControl.ondemand_reports)
    },
    valuesOndemandReports() {
      return this.ondemandReports.filter(val => val.id).map(val => `${val.id}`) || [];
    },
    reports() {
      return this.uniqueArr(this.permissionsControl.reports)
    },
    valuesReports() {
      return this.reports.filter(val => val.id).map(val => `${val.id}`) || [];
    },
  },


  methods: {
    uniqueArr(arr) {
      return arr.filter((v, i, a) => a.findIndex(e => (e.id === v.id)) === i)
    },
    closeModal() {
      this.dialog = false;
    },
    async getProvinces() {
      this.$emit("onGetProvinces");
    },
    clickCreateNewProvince() {
      this.$store.commit('openModalProvince');
    },
    editProvince(dataProvince) {
      const reports = (dataProvince?.reports || dataProvince?.profiles || []).map(data => `${data.id}`);
      const ondemandReports = (dataProvince?.ondemand_reports || dataProvince?.reports || dataProvince?.profiles || []).map(data => `${data.id}`) || [];

      this.$store.commit('openModalProvince', {
        ...dataProvince,
        programs: (dataProvince?.profiles || []).map(data => `${data.id}`),
        reports: reports.filter(id => this.valuesReports.includes(id)),
        ondemand_reports: ondemandReports.filter(id => this.valuesOndemandReports.includes(id)),
        roles: (dataProvince?.roles || []).map(data => `${data.id}`),
        image_groups: (dataProvince?.image_groups || []).map(data => `${data.id}`),
        supervisors: (dataProvince?.supervisors || []).map(data => `${data.id}`),
      });
    },
    async deleteProvince(dataProvince) {
      const confirmed = await this.$swal({
        title: 'Province Delete Confirmation',
        text: 'Are you sure you want to delete this province?',
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
      const resultDeleteProvinceOauth = await this.$store.dispatch('callAPI', {
        url: '/deleteProvinceInfoOauth',
        method: 'post',
        data: {
          province_id: dataProvince.id,
        },
      });
      this.$store.commit('setShowProgressAPI', false);
      if (resultDeleteProvinceOauth.message !== 'success') {
        return this.$swal({
          icon: 'error',
          text: this.getMessageResult(resultDeleteProvinceOauth),
        });
      }
      await this.$swal({
        icon: 'success',
        text: this.getMessageResult(resultDeleteProvinceOauth),
      });
      this.getProvinces();
    }
  },
}
</script>
<style lang="scss" scoped>
.title-provinces-modal {
  .v-icon:hover {
    &::after {
      @extend %afterIconModalClose;
    }
  }
}

::v-deep .nde-table-search-result {
  .v-data-table__wrapper {
    padding-bottom: 6.25rem;
  }

  ::v-deep table {
    margin-bottom: 6rem;
  }
}
</style>
