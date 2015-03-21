<template>
  <div class="nde-view-record pa-6">
    <v-row>
      <v-col class="d-flex">
        <span @click="goBack" class="nde-view-record-goback" tabindex="0" aria-label="Go Back">
          <v-icon color="primary">mdi-menu-left</v-icon>
          <span>Go Back</span>
        </span>
        <span class="ml-6 nde-view-record-file-name">{{ filename }}</span>
      </v-col>
    </v-row>

    <div class="mt-3">
      <iframe
        id="viewDocumentIframe"
        allow="clipboard-read; clipboard-write"
        class="nde-view-document-iframe"
        width="100%"
        :src="getSrc"
        allowfullscreen
      >
      </iframe>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex';

export default {
  data() {
    return {
      ismodify: false,
      isSaved: false,
    };
  },
  props: {
    doc_id: {
      type: String,
      required: true,
    },
    nuid: {
      type: String,
      required: true,
    },
    filename: {
      type: String,
      default: '',
    },
    tabname: {
      type: String,
      required: true,
    },
  },
  methods: {
    closeDocuview() {
      if (this.isSaved) {
        this.$emit('onRefreshData');
      }
      this.$store.commit('setShowProgressAPI', false);
      this.$emit('onClose');
    },
    goBack() {
      const iframe = document.getElementById('viewDocumentIframe');
      iframe.contentWindow.postMessage('iframe', '*');
      this.validateDirtyDocument(iframe);
    },
    async validateDirtyDocument(iframe) {
      this.isSaved = false;
      let isLoading = false;
      let lockusername = null;
      window.addEventListener('message', async (evt) => {
        if (evt.data == 'false') {
          setTimeout(() => {
            this.goBack();
          }, 1000);
        } else if (typeof evt.data !== 'object') {
          const response = JSON.parse(evt.data.toLowerCase());
          this.ismodify = response?.ismodify;
          lockusername = response?.lockusername;
          this.isSaved = response?.issaved;
          if (!response.ismodify && !isLoading) {
            isLoading = true;
            this.$store.commit('setShowProgressAPI', true);
            await this.checkIfValidToUnlockFile(lockusername);
            this.closeDocuview();
          } else {
            window.addEventListener('beforeunload', this.beforeWindowUnload);
          }
        }
      });
    },
    async checkIfValidToUnlockFile(lockusername) {
      if (lockusername == this.userLoginFullName.toLowerCase()) {
        await this.unLockDocFile();
      }
    },
    async unLockDocFile() {
      await this.$store.dispatch('callAPI', {
        url: '/lockFileOauth',
        method: 'post',
        data: {
          profile_id: this.$store.state.currentProgram.id,
          nuid: this.nuid,
          unlock_file: true,
        },
      });
    },
    confirmLeave() {
      return window.confirm('Do you really want to leave? you have unsaved changes!');
    },
    confirmStayInDirtyForm() {
      return this.ismodify && !this.confirmLeave();
    },
    beforeWindowUnload(e) {
      if (this.confirmStayInDirtyForm()) {
        e.preventDefault();
        e.returnValue = '';
      }
    },
  },
  beforeDestroy() {
    window.removeEventListener('beforeunload', this.beforeWindowUnload);
  },
  computed: {
    ...mapState(['accesstoken', 'refreshtoken', 'env']),
    getSrc() {
      console.log('running this file...');
      return (
        this.env.DOCUVIEW_URL +
        '?nuid=' +
        this.nuid +
        '&access_token=' +
        this.accesstoken +
        '&profile_id=' +
        this.$store.state.currentProgram.id +
        '&doc_id=' +
        this.doc_id +
        '&refresh_token=' +
        this.refreshtoken +
        '&filename=' +
        this.filename +
        '&tabname=' +
        this.tabname
      );
    },
  },
};
</script>

<style scoped lang="scss">
.nde-view-record {
  position: fixed;
  left: 0;
  right: 0;
  top: 65px;
  bottom: 0;
  z-index: 1;
  background: $lightGreyColor;
  overflow: auto;

  &-goback {
    cursor: pointer;
    color: $primaryColor;
  }

  &-file-name {
    font-weight: bold;
  }

  .nde-view-document-iframe {
    width: 100%;
    height: calc(100vh - 210px);
  }
}
</style>
