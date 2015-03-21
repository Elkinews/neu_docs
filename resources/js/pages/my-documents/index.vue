<template>
  <div role="main">
    <v-container fluid>
      <h3 class="my-document-header" role="heading">My Documents</h3>
      <nde-document-table class="mt-3" title="PDF, CSV, Others" :data="allFiles"
        :loadingMyDocument="loadingMyDocumentFiles" @onShare="toggleShareModal" @onPromote="togglePromoteModal"
        @onGenerate="toggleGenerateLinkModal" @deleteItem="onDeleteItem" />
      <nde-shared-modal :isShow="showShareModal" :items="shareableAccounts" :shareItem="shareItem"
        @closeModal="toggleShareModal" @share="handleShare" />
      <nde-promote-record-modal :isShow="showPromoteModal" @closeModal="togglePromoteModal" @onPromote="handlePromote"
        :promoteItem="promoteItem" v-if="promoteItem" />
      <nde-generate-link-modal :isShow="showGenerateLinkModal" @closeModal="toggleGenerateLinkModal"
        :generateLinkItem="generateLinkItem" />
    </v-container>
  </div>
</template>

<script>
import NdeDocumentTable from './components/NdeDocumentTable/NdeDocumentTable.vue';
import NdeDashboardLayout from '../../shared/layouts/dashboard/NdeDashboardLayout';
import NdeSharedModal from './components/NdeSharedModal.vue';
import NdeGenerateLinkModal from './components/NdeGenerateLinkModal.vue';
import NdePromoteRecordModal from './components/NdePromoteRecordModal/NdePromoteRecordModal.vue';
import { mapState } from 'vuex';

export default {
  layout: NdeDashboardLayout,
  data() {
    return {
      loadingMyDocument: false,
      showShareModal: false,
      showPromoteModal: false,
      showGenerateLinkModal: false,
      shareableAccounts: [],
      allFiles: [],
      shareItem: {},
      promoteItem: null,
      generateLinkItem: {}
    };
  },
  props: {
    profileId: {
      type: String,
      default: '',
    },
  },
  components: {
    NdeDocumentTable,
    NdeSharedModal,
    NdePromoteRecordModal,
    NdeGenerateLinkModal
  },
  computed: {
    ...mapState(['myDocumentData', 'loadingMyDocumentFiles']),
  },
  methods: {
    onDeleteItem({ item }) {
      this.allFiles = this.allFiles.filter((file) => file.id !== item.id);
    },
    toggleShareModal({ item }) {
      this.showShareModal = !this.showShareModal;
      this.shareItem = item;
    },
    togglePromoteModal(data) {
      this.showPromoteModal = !this.showPromoteModal;
      if (this.showPromoteModal) {
        this.promoteItem = data.item;
      } else {
        this.promoteItem = null;
      }
    },
    toggleGenerateLinkModal({ item }) {
      this.showGenerateLinkModal = !this.showGenerateLinkModal;
      if (this.showGenerateLinkModal) {
        this.generateLinkItem = item
      }
    },
    handleShare() { },

    handlePromote() { },

    noDocumentAlert() {
      this.$swal({
        text: 'You have no files in My Documents.',
        icon: 'info',
        confirmButtonText: 'Close',
      });
    },
    getSharableAccounts() {
      axios
        .post('/getSharableAccountsOauth', {})
        .then((response) => {
          this.shareableAccounts = response?.data?.data?.data?.body?.data?.accounts;
        })
        .catch((error) => {
          console.log(error);
        });
    },
  },

  mounted() {
    this.$store.dispatch({type: 'myDocumentLoadingFiles'});
    this.$store.dispatch('handleVisibleProgramBtn', false);
    window.document.title = 'neuDocs Enterprise My Documents';
    this.getSharableAccounts();
  },

  beforeDestroy() {
    this.$store.dispatch('handleVisibleProgramBtn', true);
  },

  watch: {
    myDocumentData: function (val) {
      if (val?.total === '0') {
        this.noDocumentAlert();
      } else {
        this.allFiles = val?.download_requests;
        this.loadingMyDocument = false;
      }
    },
  },
};
</script>

<style scoped lang="scss">
.my-document-header {
  margin: 10px;
  font-weight: bold;
}
</style>
