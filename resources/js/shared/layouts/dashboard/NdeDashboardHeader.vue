<template>
  <div class="nde-dashboard-header">
    <v-app-bar class="nde-app-bar white overflow-hidden">
      <v-icon @click.stop="$emit('onClickShowSiderbar')" v-show="isMobile && type !== 'login'" class="mr-2">
        mdi-menu
      </v-icon>
      <a tabindex="0" href="/dashboard" class="neudocs-masthead-logo"><img alt="NeuDocs Enterprise Logo"
          src="/images/neubus_logo_masthead.png" /></a>
      <template v-if="type !== 'login' && !isMobile">
        <nde-dashboard-program-menu :programs-json="programsJson" :profileId="profileId" :standalone="standalone"/>
        <v-spacer></v-spacer>
        <!--  User Menu -->
        <a
          tabindex="0"
          class="nde-dashboard-header-info-help-item"
          v-if="unreadNotification > 0 && !standalone"
          @click.prevent="gotoPage('/dashboard/notification')"
        >
          <v-badge color="red" :content="unreadNotification" :offset-x="10" offset-y="10">
            <v-icon aria-hidden="false" role="button" aria-label="Notification" color="dark">mdi-bell-outline</v-icon>
          </v-badge>
        </a>
        <a
          class="nde-dashboard-header-info-help-item mr-1"
          tabindex="0"
          @click.prevent="gotoPage('/dashboard/notification')"
          v-else-if="unreadNotification <= 0 && !standalone"
        >
          <v-icon aria-hidden="false" role="button" aria-label="Notification" color="dark" size="26">mdi-bell-outline</v-icon>
        </a>
        <nde-dashboard-user-menu v-if="!standalone" />
      </template>
    </v-app-bar>
  </div>
</template>

<script>
import NdeDashboardUserMenu from './NdeDashboardUserMenu.vue';
import NdeDashboardProgramMenu from './NdeDashboardProgramMenu';
import { mapState } from 'vuex';

export default {
  components: {
    NdeDashboardUserMenu,
    NdeDashboardProgramMenu,
  },
  props: {
    drawer: {
      type: Boolean,
      default: false,
    },
    type: {
      type: String,
      required: false,
      default: '',
    },
    programsJson: {
      type: String,
      required: false,
      default: '',
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
  data() {
    return {};
  },
  computed: {
    ...mapState(['unreadNotification']),
  },
};
</script>

<style scoped lang="scss">
.nde-dashboard-header {
  z-index: 2;

  &-info {
    display: flex;
    background-color: #343c42;
    justify-content: space-between;
    padding: 0.75rem 2.938rem 0.75rem 7rem;
    align-items: center;

    &-help {
      color: white;

      &-item {
        margin-right: 1rem;
        color: white;
        text-decoration: none;
        cursor: pointer;

        span {
          margin-left: 0.313rem;
          font-size: 0.813rem;

          v-badge__badge {
            font-size: 12px;
          }
        }
      }
    }

    &-contact {
      color: #cccecf;

      a {
        color: white;
      }
    }
  }
}

.nde-progams-list {
  max-height: 80vh;
}

.nde-logo {
  height: 3.188rem;
  float: left !important;
}

@media screen and (max-width: 37.5rem) {
  .nde-dashboard-header {
    flex-wrap: wrap;
    display: flex;
    flex: 1 1 auto;

    .nde-dashboard-header-info {
      padding: 1rem;
      flex-wrap: wrap;
      order: 2;

      .nde-dashboard-header-info-help {
        text-align: right;
        order: 2;
        width: 100%;

        &-item {
          margin-right: 0rem;
        }
      }

      .nde-dashboard-header-info-contact {
        order: 1;
        display: none;
      }
    }

    .nde-app-bar {
      order: 1;
      height: unset !important;

      .v-toolbar__content {
        overflow-x: hidden;
        overflow-y: hidden;
        flex-wrap: wrap;
        height: unset !important;
      }
    }
  }
}

@media screen and (max-width: 25rem) {
  .nde-dashboard-header-info-help-item {
    .nde-item-text {
      display: none;
    }
  }
}
</style>
