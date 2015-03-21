<template>
  <div>
    <v-menu offset-y close-on-content-click>
      <template v-slot:activator="{ on, attrs }">
        <v-list-item two-line v-bind="attrs" v-on="on" style="max-width: 15rem" @click="callMyDoc">
          <v-list-item-avatar>
            <img :src="getSrc" />
          </v-list-item-avatar>

          <v-list-item-content>
            <v-list-item-title>
              <span data-v-65e7b42c="" class="v-list-item__subtitle" style="display: block"
                >Logged in as:&nbsp;</span
              >
              {{ userLoginFullName }}<v-icon style="margin-top: -0.25rem">mdi-menu-down</v-icon>
            </v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </template>
      <v-list nav class="nde-user-menu-items">
        <v-list-item
          aria-labelledby="My Documents"
          tabindex="0"
          :link="multiTask"
          :href="multiTask ? '/dashboard/support/mydocs' : ''"
          @click.prevent="gotoPage('/dashboard/support/mydocs')"
          @keyup.enter.prevent="gotoPage('/dashboard/support/mydocs')"
        >
          <v-list-item-icon>
            <v-icon>mdi-folder-outline</v-icon>
          </v-list-item-icon>
          <v-list-item-content>
            <v-list-item-title>
              My Documents <span v-if="!loadingMyDocumentFiles">({{ documentCount }})</span>
            </v-list-item-title>
          </v-list-item-content>
          <v-list-item-icon>
            <v-progress-circular
              indeterminate
              color="primary"
              v-if="loadingMyDocumentFiles"
            ></v-progress-circular>
          </v-list-item-icon>
        </v-list-item>
        <v-list-item
          tabindex="0"
          aria-labelledby="Settings"
          :link="multiTask"
          :href="multiTask ? '/setting/defaultSetting' : ''"
          @click.prevent="gotoPage('/setting/defaultSetting')"
        >
          <v-list-item-icon>
            <v-icon>mdi-cog-outline</v-icon>
          </v-list-item-icon>
          <v-list-item-content>
            <v-list-item-title> Settings </v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-list-item tabindex="0" aria-labelledby="Logout" @click="showModal = true">
          <v-list-item-icon>
            <v-icon>mdi-logout</v-icon>
          </v-list-item-icon>
          <v-list-item-content>
            <v-list-item-title> Logout </v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-menu>
    <nde-modal-logout :showModal.sync="showModal" />
  </div>
</template>

<script>
import NdeModalLogout from '../../../components/Modal/NdeModalLogout.vue';
import { mapState } from 'vuex';

export default {
  name: 'NdeDashboardUserMenu',
  components: {
    NdeModalLogout,
  },
  props: {
    multiTask: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      showModal: false,
      documentCount: 0,
      nuid: '',
      avatarSRC: null,
      account_id: '',
    };
  },
  methods: {
    callMyDoc() {
      this.$store.dispatch({ type: 'myDocumentLoadingFiles' });
    },
  },
  mounted() {},
  computed: {
    ...mapState([
      'myDocumentData',
      'accesstoken',
      'env',
      'profile_avatar_link',
      'loadingMyDocumentFiles',
    ]),
    getSrc() {
      if (this.profile_avatar_link) {
        // return this.avatarSRC ? this.avatarSRC : '/images/default-avatar.png';
        return this.profile_avatar_link;
      } else {
        return '/images/default-avatar.png';
      }
    },
  },
  watch: {
    myDocumentData: function (val) {
      this.documentCount = val.total;
    },
  },
};
</script>

<style scoped lang="scss">
.nde-user-menu-items {
  background: white;

  .v-list-item__title {
    span {
      color: rgb(0, 82, 204);
    }
  }
}
</style>
