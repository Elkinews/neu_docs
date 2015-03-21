<template>
  <nde-modal
    :showModal.sync="showModal"
    title="Logout"
    modalWith="40rem"
    :isNotShowClose="true"
    :persistent="true"
  >
    <template v-slot:ndeModalContent>
      <h2>Are you sure you want to log out?</h2>
    </template>
    <template v-slot:ndeModalAction>
      <v-row class="justify-space-between">
        <nde-button-cancel class="mt-1" style="width: 8.625rem" @click="closeModal">
        </nde-button-cancel>

        <nde-button-primary class="mt-1" title="Yes" @click="logout" style="width: 8.625rem">
        </nde-button-primary>
      </v-row>
    </template>
  </nde-modal>
</template>

<script>
import NdeModal from './NdeModal.vue';
import NdeButtonPrimary from '../Button/NdeButtonPrimary.vue';
import NdeButtonCancel from '../Button/NdeButtonCancel.vue';

export default {
  name: 'NdeModalLogout',
  components: {
    NdeModal,
    NdeButtonPrimary,
    NdeButtonCancel,
  },
  props: {
    showModal: {
      type: Boolean,
      default: false,
    },
  },
  methods: {
    closeModal() {
      this.$emit('update:showModal', !this.showModal);
    },

    async logout() {
      const resultLogout = await this.$store.dispatch('callAPI', {
        url: '/endOauthSession',
        method: 'post',
        data: {},
      });

      this.$cookie.delete('roles');
      this.$cookie.delete('nde_frontend_session');
      this.$cookie.delete('XSRF-TOKEN');
      this.$cookie.delete('starrsProfiles');
      window.location.replace('/login');
      window.location.reload();
    },
  },
};
</script>
<style scoped lang="scss">
:v-deep .v-dialog {
  max-width: 40rem !important;
}
</style>
