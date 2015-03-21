<template>
  <div class="nde-view-record pa-6">
    <v-row>
      <v-col class="d-flex">
        <span @click="goBack" class="nde-view-record-goback" tabindex="0" aria-label="Go Back">
          <v-icon color="primary">mdi-menu-left</v-icon>
          <span>Go Back</span>
        </span>
        <span class="ml-6 nde-view-record-file-name">{{ selectedItem.name || '' }}</span>
      </v-col>
    </v-row>

    <div class="mt-3">
      <iframe class="nde-view-document-iframe" width="100%" :src="getSrc" allowfullscreen> </iframe>
    </div>
  </div>
</template>

  <script>
import { mapState } from 'vuex';

export default {
  props: {
    selectedItem: {
      type: Object,
      required: true,
    },
  },
  methods: {
    goBack() {
      this.$emit('onClose');
    },
  },

  computed: {
    ...mapState(['accesstoken', 'refreshtoken', 'env']),
    getSrc() {
      return (
        this.env.MEDIA_URL +
        this.selectedItem.nuid +
        '?profileId=' +
        this.$store.state.currentProgram.id +
        '&original_qid=' +
        this.selectedItem.original_qid +
        '&medium_qid=' +
        this.selectedItem.medium_qid +
        '&low_qid=' +
        this.selectedItem.low_qid +
        '&access_token=' +
        this.accesstoken +
        '&type=' +
        (this.selectedItem.document_type || 'video').toLowerCase() +
        '&refresh_token=' +
        this.refreshtoken
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
