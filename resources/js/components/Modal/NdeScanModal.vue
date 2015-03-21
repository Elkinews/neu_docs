<template>
  <v-dialog v-model="isOpen" persistent @click:outside="isHideModal">
    <v-icon aria-label="Close" size="30px" style="position: absolute; right: 0; top: calc(5vh - 30px); width: 30px"
      color="red" @click="isHideModal">
      mdi-close
    </v-icon>
    <div class="nde-scan-modal">
      <iframe id="documentIframe" allow="clipboard-read; clipboard-write" width="100%" height="100%" :src="previewURL"></iframe>
    </div>
  </v-dialog>
</template>

<script>
import { mapState } from 'vuex';
import NdeScanModalForm from './NdeScanModalForm.vue';
import NdeScanModalPreview from './NdeScanModalPreview.vue';

export default {
  components: { NdeScanModalForm, NdeScanModalPreview },
  data() {
    return {
      isOpen: true,
      access_token: '',
      profileId: '',
      docId: '',
      nuid: '',
      previewURL: '',
      ismodify: false,
    };
  },
  async created() {
    this.$store.commit('setShowProgressAPI', true);
    this.$store.dispatch('increaseCallCount');
    const tokenData = await axios.get('/getAccessToken');
    this.access_token = tokenData.data.data.token;
    this.profileId = this.$store.state.currentProgram.id;
    this.docId = this.$store.state.currentImageId;

    this.$store.commit('setShowProgressAPI', false);

    this.nuid = '';
    this.previewURL = this.env.DOCUVIEW_URL + `?nuid=${this.nuid}&access_token=${this.access_token}&profile_id=${this.profileId}&doc_id=${this.docId}&scan=true`;
  },

  methods: {
    isHideModal() {
      var iframe = document.getElementById("documentIframe");
      iframe.contentWindow.postMessage('iframe', '*');
      this.messageEvent();
    },
    messageEvent() {
      window.addEventListener("message", evt => {
        if (typeof (evt.data) !== 'object') {
          const response = JSON.parse(evt.data.toLowerCase());
          if (!response.ismodify || !response) {
            this.$store.commit('closeScanModal');
          } else {
            if (response.closeframe) {
              this.$store.commit('closeScanModal');
            } else {
              this.isOpen = true
            }
            this.isOpen = true
          }
        }
      });
    },
  },
  computed: {
    ...mapState(['isScanModal', 'env']),
  },
  mounted() {
    document.addEventListener('keydown', (e) => {
      if (e.keyCode == 27) {
        this.$store.commit('closeScanModal');
      }
    });
    this.isHideModal();
  },

  watch: {
    isScanModal: function (val) {
      this.$store.commit('closeScanModal');
    },
    isOpen: ((val) => {
      if (!val) {
        this.isHideModal();
      }
    })
  },
};
</script>

<style scoped lang="scss">
.nde-scan-modal {
  background: white;
  display: flex;
  align-items: stretch;
  height: 90vh;
}
</style>
