<template>
  <nde-modal :showModal="isShow" title="Generate Link" persistent max-width="1280" class="nde-generate-modal"
    @update:closeModal="closeModal">
    <template slot="ndeModalContent">
      <v-row>
        <v-col md="4" sm="12">
          <h3 class="mb-3">Generate Link</h3>

          <div>
            <label>Expiration Time</label>
            <v-select dense solo attach hide-details class="nde-select" v-model="expiration_time" :items="items"
              outlined item-text="value" item-value="key"
              aria-label="Expiration Time">
            </v-select>
          </div>
          <div class="mt-5">
            <label>Notes</label>
            <v-textarea v-model="notes" solo outlined hide-details name="input-7-4" aria-label="Notes"></v-textarea>
            <span>These notes are for your reference and will not be seen by the user downloading the file.</span>
          </div>
        </v-col>
        <v-col md="8" sm="12">
          <h3 class="mb-3">Generated links for this file</h3>
          <nde-data-table class="document-table-content nde-table-generate" :headers="headers" :items="dataLinks" dense>
            <template slot="item.actions" slot-scope="{ item }">
              <v-icon class="text-center" aria-label="Close" size="24px" color="red" @click="deleteLink(item)">
                mdi-close
              </v-icon>
            </template>
            <template slot="item.copy" slot-scope="{ item }">
              <a href="javascript:;" @click="copyToClipboard(item)">Copy link</a>
            </template>
          </nde-data-table>
        </v-col>
      </v-row>
    </template>
    <template slot="ndeModalAction">
      <div class="nde-generate-modal__action d-flex justify-start">
        <nde-button class="mr-2" @click="generate" color="primary">
          Generate
        </nde-button>
        <nde-button class="nde-button-cancel" color="error" @click="closeModal">
          Cancel
        </nde-button>
      </div>
    </template>
  </nde-modal>
</template>

<script>
import NdeModal from "@components/Modal/NdeModal.vue";
import NdeDataTable from '@components/Table/NdeDataTable.vue';
import NdeButton from "@components/Button/NdeButton.vue";
import { mapState } from 'vuex'
export default {
  data() {
    return {
      notes: "",
      expiration_time: {
        key: '24',
        value: '24 Hours'
      },
      items: [
        {
          key: '2',
          value: '2 Hours'
        },
        {
          key: '4',
          value: '4 Hours'
        },
        {
          key: '8',
          value: '8 Hours'
        },
        {
          key: '24',
          value: '24 Hours'
        },
        {
          key: '168',
          value: '168 Hours'
        }
      ],
      headers: [
        {
          text: 'Generated At',
          value: 'generated_at',
          class: 'col-generated-at',
          sortable: false,
        },
        {
          text: 'Notes',
          value: 'notes',
          class: 'col-note',
          sortable: false,
        },
        {
          text: 'Expiration Time',
          value: 'expiration_time',
          class: 'col-time-accessed',
          sortable: false,
        },
        {
          text: 'Link',
          value: 'copy',
          class: 'col-time-accessed',
          sortable: false,
        },
        {
          text: 'Cancel Link',
          value: 'actions',
          class: 'col-cancel',
          sortable: false,
        },
      ],
      dataLinks: [],
    }
  },
  computed: {
    ...mapState([
      'accesstoken',
      'currentProgram',
      'env'
    ])
  },

  props: {
    isShow: {
      type: Boolean,
      default: () => false
    },

    generateLinkItem: {
      type: Object,
      default: () => { }
    }

  },
  methods: {
    closeModal() {
      this.notes = ""
      this.expiration_time = {
        key: '24',
        value: '24 Hours'
      }
      this.$emit("closeModal", this.generateLinkItem);
    },

    generate() {
      let payload = {
        nuid: this.generateLinkItem && this.generateLinkItem.nuid,
        hours: this.expiration_time.value ? this.expiration_time.key : this.expiration_time,
        profileId: this.currentProgram.id,
        notes: this.notes
      }

      let config = {
        method: 'POST',
        url: this.env.FS_URL + 'link',
        headers: {
          Authorization: 'Bearer ' + this.accesstoken,
        },
        data: payload
      }
      this.$store.dispatch('increaseCallCount');
      axios(config)
        .then((response) => {

          let date = new Date(`${response?.data?.expires_on}`).toISOString().substring(0, 10);
          // let time = new Date(`${response?.data?.expires_on}`).toLocaleTimeString('en', {hour12: false, timeZone: 'UTC' })

          let payload = {
            nuid: this.generateLinkItem.nuid,
            temp_nuid: response?.data?.nuid,
            expiration_time: `${date}`,
            note : this.notes
          }

          this.addTempLink(payload)

        })
        .catch((error) => {
          console.log(error);
          
        });
    },

    addTempLink(payload) {
      this.$store.dispatch('increaseCallCount');
      axios
        .post('/addTempLinkOauth', payload)
        .then((response) => {
          if (response?.data?.message === 'success') {
            this.$swal({
              icon: 'success',
              text: 'Generate link successfully!',
            });
            this.getLinks(this.generateLinkItem.nuid)
          }
        })
        .catch((error) => {
          this.$swal({
              icon: 'error',
              text: error?.response?.data?.data?.message?.message,
            });
        });
    },

    getLinks(nuid) {
      this.$store.dispatch('increaseCallCount');
      axios
        .post('/getLinksOauth', { nuid: nuid })
        .then((response) => {
          if (response?.data?.message === 'success') {
            this.dataLinks = response?.data?.data?.data.links
          }
        })
        .catch((error) => {
          console.error(error);
        });
    },

    cancelLink(nuid) {
      this.$store.dispatch('increaseCallCount');
      axios
        .post('/cancelLinkOauth', { nuid: nuid })
        .then(() => { })
        .catch((error) => {
          console.error(error);
        });
    },

    deleteLinkViaFS(id) {

      let config = {
        method: 'DELETE',
        url: this.env.FS_URL + `link/${id}`,
        headers: {
          Authorization: 'Bearer ' + this.accesstoken,
        }
      }
      this.$store.dispatch('increaseCallCount');

      axios(config)
        .then((response) => {
          this.getLinks(this.generateLinkItem.nuid)
          if (response?.data?.message) {
            return this.$swal({
              icon: 'success',
              text: response?.data?.message,
            });
          }
        })
        .catch((error) => {
          console.error(error);
        });
    },

    async deleteLink(item) {
      const confirmed = await this.$swal({
        text: "This will cancel the external link so that it will no longer be possible to retrieve the file by using that link. Are you sure?",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Confirm',
        cancelButtonText: 'Cancel',
      });

      if (confirmed.value) {
        Promise.all([this.cancelLink(item.nuid), this.deleteLinkViaFS(item.id)])
      }
    },

    copyToClipboard(item) {
      navigator.clipboard.writeText(this.env.FS_URL + `link/${item.nuid}`);
      return this.$swal({
        icon: 'success',
        text: 'Copy successfully!',
      });
    }
  },

  watch: {
    isShow(newVal) {
      if (newVal) {
        if (this.generateLinkItem && this.generateLinkItem.nuid) {
          this.getLinks(this.generateLinkItem.nuid)
        }
      }
    }
  },

  components: {
    NdeModal,
    NdeDataTable,
    NdeButton,
  },
};
</script>

<style lang="scss">
.document-table-content.nde-table-generate {
  th {
    padding: 0 4px !important;
  }
}
</style>
