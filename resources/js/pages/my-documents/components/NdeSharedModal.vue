<template>
  <nde-modal
    :showModal="isShow"
    title="Share"
    persistent
    max-width="800"
    class="nde-shared-modal"
    @update:closeModal="closeModal"
  >
    <template slot="ndeModalContent">
      <div class="nde-shared-modal__content">
        <nde-table height="321px" dense>
          <template v-slot:ndeTableContent>
            <thead class="nde-table-header">
              <tr>
                <th class="col-selecbox"></th>
                <th class="col-users">Users</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in accounts" :key="index">
                <td>
                  <v-checkbox
                    class="nde-checkbox"
                    v-model="checkboxes[index].checked"
                    hide-details
                  />
                </td>
                <td><span v-html="item.username"></span></td>
              </tr>
            </tbody>
          </template>
        </nde-table>
      </div>
    </template>
    <template slot="ndeModalAction">
      <div class="nde-shared-modal__action d-flex justify-end">
        <nde-button class="mr-2" @click="onShare" color="primary"> Share </nde-button>
        <nde-button class="nde-button-cancel" color="error" @click="closeModal">
          Cancel
        </nde-button>
      </div>
    </template>
  </nde-modal>
</template>

<script>
import NdeModal from '@components/Modal/NdeModal.vue';
import NdeTable from '@components/Table/NdeTable.vue';
import NdeButton from '@components/Button/NdeButton.vue';
export default {
  data() {
    return {
      shareAccountList: [],
      checkboxes: [],
      documentPath: '',
    };
  },
  props: {
    isShow: {
      type: Boolean,
      default: () => false,
    },
    items: {
      type: Array,
      default: () => [],
    },
    shareItem: {
      type: Object,
      default: () => {},
    },
  },
  computed: {
    accounts() {
      return this.addedCheck();
    },
  },
  methods: {
    addedCheck() {
      this.checkboxes = this.items.map((account) => {
        return {
          checked: false,
          receiver: account.id,
        };
      });
      return this.items.sort((a, b) =>
        a.username > b.username ? 1 : b.username > a.username ? -1 : 0,
      );
    },
    closeModal() {
      this.$emit('closeModal', { item: this.shareItem });
      this.addedCheck();
    },
    onShare() {
      this.checkboxes.map((account) => {
        if (account?.checked) {
          this.shareAccountList.push(account?.receiver);
        }
      });
      this.documentPath = this.shareItem.path;
      this.shareDocsAlert();
    },
    async shareDocsAlert() {
      this.$swal({
        text: 'This action cannot be undone, are your sure you want to share?',
        icon: 'warning',
        confirmButtonText: 'OK',
        showCancelButton: true,
      }).then(async (result) => {
        if (result.isConfirmed) {
          const responseData = await this.shareDocs();
          this.$swal({
            text: responseData?.data?.data?.data?.body?.data?.message,
            icon: responseData?.data?.data?.data?.body?.data?.status,
          }).then((result) => {
            this.closeModal();
          });
        }
      });
    },
    async shareDocs() {
      this.$store.dispatch('increaseCallCount');
      const response = await axios.post('/shareMyDocsOauth', {
        filename: this.documentPath,
        receivers: this.shareAccountList,
      });
      return response;
    },
  },
  components: {
    NdeModal,
    NdeTable,
    NdeButton,
  },
  watch: {
    // selected: function(val){
    //   console.log("this is selected : ", val)
    // }
  },
};
</script>

<style lang="scss">
.nde-shared-modal__content {
  .nde-checkbox.v-input--selection-controls {
    margin-top: 0;
    .v-input__control {
      flex-grow: unset;
      width: unset;
      .v-input--selection-controls__input {
        margin-right: 0;
      }
    }
  }
  .col-selecbox {
    width: 20%;
  }
  .col-users {
    width: 80%;
  }
  .nde-table thead {
    height: 32px !important;
  }
  .nde-table tbody td {
    height: 32px !important;
  }
  .nde-table.v-data-table thead th:not(:last-child) {
    border-right: thin solid rgba(0, 0, 0, 0.12);
  }
  .nde-table.v-data-table tbody td:not(:last-child) {
    border-right: thin solid rgba(0, 0, 0, 0.12);
  }
  .theme--light.v-data-table > .v-data-table__wrapper > table > thead > tr:last-child > th {
    border-bottom: thin solid rgba(0, 0, 0, 0.12);
  }
  .nde-table.v-data-table thead th:not(:last-child) {
    border-right: thin solid rgba(0, 0, 0, 0.12);
  }
  .nde-table.v-data-table table {
    border: thin solid rgba(0, 0, 0, 0.12);
  }
}
.nde-shared-modal__action {
  width: 100%;
  .nde-button-share.theme--light.v-btn.v-btn--has-bg {
    color: $whiteColor;
    background-color: $primaryColor;
  }
  .nde-button-cancel.theme--light.v-btn.v-btn--has-bg {
    color: $whiteColor;
    background-color: $errorColor;
  }
}
</style>
