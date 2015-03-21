<template>
  <v-card width="fit-content" class="nde-tabs d-flex align-center" v-if="!isMobile">
    <template v-for="(setting, index) in settings">
      <nde-tab-item
        v-bind:key="index"
        :icon="setting.icon"
        :text="setting.text"
        :isActive="isActive(setting.path)"
        @click="goHref(setting.path)"
        class="mr-2"
      ></nde-tab-item>
      <span
        v-if="index !== settings.length - 1"
        v-bind:key="index + '_divider'"
        class="nde-tab-divider"
      ></span>
    </template>
  </v-card>
  <v-card width="fit-content" class="nde-tabs d-flex align-center" v-else>
    <v-select
      v-model="selectedTab"
      :items="settings"
      attach
    >
      <template v-slot:selection="{ item }">
        <v-icon left>{{ item.icon }}</v-icon>
        <span>{{item.text}}</span>
      </template>
      <template v-slot:item="{ item }">
        <nde-tab-item
          :icon="item.icon"
          :text="item.text"
          :isActive="isActive(item.path)"
          @click="goHref(item.path)"
        >
        </nde-tab-item>
      </template>
    </v-select>
  </v-card>

</template>

<script>
import NdeTabItem from './NdeTabItem.vue';
export default {
  name: 'NdeTabSetting',
  components: {
    NdeTabItem,
  },
  data() {
    return {
      selectedTab: {},
      settings: [
        {
          icon: 'mdi-key-variant',
          text: 'Change Password',
          path: '/setting/changePassword',
        },
        {
          icon: 'mdi-cog-outline',
          text: 'Default Settings',
          path: '/setting/defaultSetting',
        },
        {
          icon: 'mdi-account-edit',
          text: 'Edit your profile',
          path: '/setting/edityourprofile',
        },
      ],
    };
  },
  methods: {
    isActive(path) {
      return window.location.pathname.indexOf(path) === 0;
    },
    goHref(path) {
      location.href = path;
    },
  },

  mounted() {
    this.selectedTab = this.settings.find(e => e.path === window.location.pathname)
  }
};
</script>

<style scoped lang="scss">
.nde-tabs {
  padding: 0.625rem;
  .nde-tab-divider {
    height: 1rem;
    width: 0.063rem;
    background: #eae7e4;
    margin: 0 5px;
  }
}
</style>