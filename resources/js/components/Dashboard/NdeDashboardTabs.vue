<template>
  <div>
    <v-card class="nde-dashboard-tabs d-flex align-center">
      <ul v-if="!isMobile" class="d-flex align-center pl-0">
        <li
          v-for="(item, index) in tabOptions"
          :key="index"
          class="d-flex align-center"
          tabindex="0"
        >
          <div v-if="index" class="nde-dashboard-tab-divider"></div>
          <div
            class="nde-dashboard-tab-item"
            :class="{ active: selectedTab == item.value, 'mr-2': tabOptions.length - 1 > index }"
            @click="changeTab(item.value)"
          >
            <v-icon left :color="selectedTab == item.value ? 'primary' : ''">{{
              item.icon
            }}</v-icon>
            <span :id="`main-title-${index}`" class="nde-dashboard-tab-text">{{ item.text }}</span>
          </div>
        </li>
      </ul>
      <template v-if="isMobile">
        <v-select v-model="selectedTab" :items="tabOptions" attach>
          <template v-slot:selection="{ item }">
            <v-icon left>{{ item.icon }}</v-icon>
            <span>{{ item.text }}</span>
          </template>
          <template v-slot:item="{ item }">
            <nde-dashboard-tab-item :icon="item.icon" :text="item.text"> </nde-dashboard-tab-item>
          </template>
        </v-select>
      </template>
    </v-card>

    <nde-key-search v-if="selectedTab == 0" />
    <nde-watchlist v-if="selectedTab == 1" />
    <nde-my-previous-search-queries v-if="selectedTab == 2" @searchFile="searchFile" />
    <nde-scan-upload-history v-if="selectedTab == 3" />
  </div>
</template>

<script>
import { mapState } from 'vuex';
import NdeKeySearch from './NdeKeySearch.vue';
import NdeWatchlist from './NdeWatchlist.vue';
import NdeDashboardTabItem from './NdeDashboardTabItem.vue';
import NdeMyPreviousSearchQueries from './NdeMyPreviousSearchQueries.vue';
import NdeScanUploadHistory from './NdeScanUploadHistory.vue';

export default {
  components: {
    NdeKeySearch,
    NdeWatchlist,
    NdeDashboardTabItem,
    NdeMyPreviousSearchQueries,
    NdeScanUploadHistory,
  },
  data() {
    return {
      selectedTab: 0,
      currentProfileId: 1,
    };
  },

  methods: {
    changeTab(num) {
      this.selectedTab = num;
    },
    searchFile(tagNumber) {
      this.selectedTab = tagNumber;
    },
  },
  computed: {
    ...mapState(['selectedProgramTree', 'Neusearch']),
    isRoleDashboardHistory() {
      let userLoginRoles = this.userLoginRoles();
      if (!userLoginRoles.length) {
        userLoginRoles = JSON.parse(atob(this.$cookie.get('roles'))).roles;
      }
      // History tab on Dashboard needs CREATERECORD, SCAN, or EUPLOAD
      return (
        (userLoginRoles.includes('CREATERECORD') && userLoginRoles.includes('SCAN')) ||
        userLoginRoles.includes('EUPLOAD')
      );
    },
    tabOptions() {
      const tabOptions = [
        {
          icon: 'mdi-file-search-outline',
          text:
            this.selectedProgramTree && this.selectedProgramTree[0]
              ? this.selectedProgramTree[0].name
              : '',
          value: 0,
        },
        {
          icon: 'mdi-sort-variant',
          text: 'WatchList',
          value: 1,
        },
        {
          icon: 'mdi-magnify-plus-outline',
          text: 'My Previous Search Queries',
          value: 2,
        },
      ];
      if (this.isRoleDashboardHistory) {
        tabOptions.push({
          icon: 'mdi-history',
          text: 'History',
          value: 3,
        });
      }
      return tabOptions;
    },
  },
  watch: {
    Neusearch: function (val) {
      if (this.Neusearch['profile'] !== this.currentProfileId) {
        this.selectedTab = 0;
        this.currentProfileId = this.Neusearch['profile'];
      }
    },
  },
  mounted() {
    this.currentProfileId = this.Neusearch['profile'];
  },
};
</script>

<style scoped lang="scss">
.nde-dashboard-tabs {
  padding: 10px;

  .nde-dashboard-tab-divider {
    height: 16px;
    width: 1px;
    background: #eae7e4;
    margin: 0 5px;
  }

  ul {
    li {
      list-style: none;
    }
  }

  .nde-dashboard-tab-item {
    padding: 10px;
    cursor: pointer;
    font-size: 1rem;

    &.active,
    &:hover {
      background: $primaryGreyColor;
      box-shadow: 0px 1px 3px rgba(95, 152, 193, 0.05), 0px 2px 1px rgba(95, 152, 193, 0.03),
        0px 1px 1px rgba(95, 152, 193, 0.35);
      border-radius: 4px;
      .nde-dashboard-tab-text {
        color: $primaryColor;
      }
    }
  }
}
</style>
