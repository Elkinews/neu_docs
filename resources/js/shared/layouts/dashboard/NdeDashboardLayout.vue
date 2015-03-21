<template>
  <v-app>
    <nde-dashboard-sidebar
      v-if="isMobile && !standalone"
      ref="sidebar"
      :programs-json="programsJson"
      :profileId="profileId"
    >
    </nde-dashboard-sidebar>
    <nde-dashboard-header
      type="dashboard"
      :programs-json="programsJson"
      :profileId="profileId"
      v-if="cookieObj"
      @onClickShowSiderbar="$refs.sidebar.open()"
      :standalone="standalone"
    >
    </nde-dashboard-header>
    <div class="nde-dashboard-side-container">
      <nde-dashboard-sidebar tabindex="3" v-if="!isMobile && !standalone"></nde-dashboard-sidebar>
      <div class="nde-dashboard-container" id="nde-dashboard-container">
        <div class="nde-dashboard-container-slot" :style="styleObject">
          <slot v-if="cookieObj" />
        </div>

        <nde-dashboard-footer></nde-dashboard-footer>
      </div>
    </div>
    <nde-session-time-out-count-down
      v-if="isAskSessionTimeout"
      :timeout="currentTimeoutValue"
      @onContinue="onContinue"
    />
    <nde-overlay-progress-api />
  </v-app>
</template>

<script>
import { mapState } from 'vuex';
import NdeDashboardHeader from './NdeDashboardHeader.vue';
import NdeDashboardSidebar from './NdeDashboardSidebar.vue';
import NdeDashboardFooter from './NdeDashboardFooter.vue';
import NdeSessionTimeOutCountDown from './NdeSessionTimeOutCountDown.vue';
import NdeOverlayProgressApi from '../../../components/Overlay/NdeOverlayProgressAPI.vue';

export default {
  data() {
    return {
      isAskSessionTimeout: false,
      cookieObj: null,
      timeOutId: null,
      timeOut: 0,
      currentTimeoutValue: 0,
    };
  },
  computed: {
    ...mapState(['apiCallCount', 'accesstoken', 'refreshtoken']),
    styleObject() {
      return {
        padding: `1rem ${this.isMobile ? 1 : 3}rem`,
      };
    },
  },
  created() {
    this.$store.commit('setBulkOptions', this.bulkActions);
    this.$store.dispatch({ type: 'getAccesstoken' });
    this.$store.dispatch({ type: 'getProfileAvatarInfo' });
    if (this.profileId) {
      this.$store.dispatch('getDocMimeTypeOauth', { profile_id: this.profileId });
    }

    this.$store.dispatch({ type: 'getENV' });
    this.$store.dispatch({ type: 'getUnreadNotificationOauth' });
    this.$store.dispatch({ type: 'getModulesOauth' });
  },

  methods: {
    onContinue() {
      clearTimeout(this.timeOutId);
      this.isAskSessionTimeout = false;
      var me = this;
      this.timeOutId = setTimeout(function () {
        me.isAskSessionTimeout = true;
        me.currentTimeoutValue = 120;
        console.log('------------------- time out ----------------');
      }, me.timeOut);
    },
  },

  props: {
    programsJson: {
      type: String,
      required: true,
    },
    bulkActions: {
      type: String,
    },
    roles: {
      type: Object,
    },
    profileId: {
      type: String,
    },
    standalone: {
      type: Boolean,
      required: false,
      default: false
    }
  },
  components: {
    NdeDashboardHeader,
    NdeDashboardSidebar,
    NdeDashboardFooter,
    NdeSessionTimeOutCountDown,
    NdeOverlayProgressApi,
  },
  mounted() {
    if (!this.$cookie.get('roles')) {
      var rolesObject = this.roles;
      if (Array.isArray(rolesObject?.roles)) {
        let userRoles = [];
        rolesObject.roles.map((role) => {
          userRoles.push(role.name);
        });
        rolesObject.roles = userRoles;
      }

      this.$cookie.set('roles', btoa(JSON.stringify(this.roles)));
      this.cookieObj = JSON.parse(atob(this.$cookie.get('roles')));
    } else {
      this.cookieObj = JSON.parse(atob(this.$cookie.get('roles')));
    }
    console.log(this.cookieObj);
    var me = this;
    this.$store.dispatch('increaseCallCount');
    axios
      .post('/getSystemSettingOauth', {})
      .then((response) => {
        if (response?.data?.message === 'success') {
          console.log(response.data.data.data.body.data.value);
          if (parseInt(response.data.data.data.body.data.value) != me.currentTimeoutValue) {
            me.timeOut = (parseInt(response.data.data.data.body.data.value) - 120) * 1000;
            me.timeOutId = setTimeout(function () {
              me.currentTimeoutValue = 120;
              me.isAskSessionTimeout = true;
              console.log('------------------- time out ----------------');
            }, me.timeOut);
          }
          me.currentTimeoutValue = parseInt(response.data.data.data.body.data.value);
          if (me.currentTimeoutValue < 120) {
            me.isAskSessionTimeout = true;
          }
        }
      })
      .catch((error) => {
        console.log(error);
      });
  },
  watch: {
    apiCallCount: function (val) {
      if (this.timeOutId) {
        clearTimeout(this.timeOutId);
        var me = this;
        this.timeOutId = setTimeout(function () {
          me.isAskSessionTimeout = true;
          me.currentTimeoutValue = 120;
          console.log('------------------- time out ----------------');
        }, me.timeOut);
      }
    },
  },
};
</script>

<style scoped lang="scss">
.nde-dashboard-side-container {
  display: flex;
  height: 100%;
}

.nde-dashboard-container {
  width: 100%;
  background-color: #f7f7f7;

  max-height: calc(100vh - 4rem);
  overflow-y: scroll;

  .nde-dashboard-container-slot {
    min-height: calc(100vh - 8.2rem);

    :v-deep .nde-tab-item {
      min-width: 2rem;
    }
  }
}

@media screen and (max-width: 48rem) {
  .nde-dashboard-side-container {
    .v-navigation-drawer--close {
      visibility: inherit !important;
    }

    .nde-dashboard-container-slot {
      min-width: 100vw;
    }

    aside {
      transform: translateX(0) !important;
    }
  }
}
</style>
