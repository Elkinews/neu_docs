<template>
  <div class="scan-and-upload">
    <nde-scan-button class="action-btn mr-2" @click="scan" v-if="isRoleScan" />
    <nde-upload-button class="action-btn" @click="upload" v-if="isRoleUpload" />
  </div>
</template>

<script>
import NdeScanButton from '../../Button/NdeScanButton.vue';
import NdeUploadButton from '../../Button/NdeUploadButton.vue';
import getDataCookieRole from '../../../mixins/cookies';
export default {
  name: 'NdeButtonScanAndUpload',
  mixins: [getDataCookieRole],

  computed: {
    isRoleScan() {
      return this.userLoginRoles().includes('SCAN');
    },

    isRoleUpload() {
      return this.userLoginRoles().includes('EUPLOAD');
    },
  },

  components: {
    NdeScanButton,
    NdeUploadButton,
  },
  methods: {
    scan() {
      //to do event when button scan be clicked
      this.$store.commit('openScanModal', this.item.doc_id);
    },

    upload() {
      //to do event when button upload be clicked
      this.$store.commit('openModalUploadDoucment', this.item.doc_id);
    },
  },
  props: {
    item: {
      type: Object,
    },
  },
};
</script>
<style lang="scss" scoped>
.scan-and-upload {
  text-transform: unset;
  button {
    background: $primaryGreyColor;
  }
}
</style>
