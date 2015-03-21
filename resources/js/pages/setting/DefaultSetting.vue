<template>
  <div role="main">
    <nde-tab-setting />
    <v-app class="mt-5">
      <v-card class="pa-5">
        <v-card-title>
          <h3 role="heading">Default Settings</h3>
        </v-card-title>
        <v-divider></v-divider>
        <v-card-text>
          <nde-form-setting-default
            :item="settingInfo"
            class="mt-5"
            @updatedSetting="updatedSetting"
            ref="childRef"
          ></nde-form-setting-default>
        </v-card-text>
        
        <action-button @onSave="onSave" @onCancel="onCancel" />
      </v-card>
    </v-app>
  </div>
</template>

<script>
import NdeTabSetting from '../../components/Tabs/NdeTabSetting.vue';
import NdeDashboardLayout from '../../shared/layouts/dashboard/NdeDashboardLayout.vue';
import NdeFormSettingDefault from '../../components/Form/NdeFormSettingDefault.vue';
import ActionButton from "./ActionButton.vue"

export default {
  layout: NdeDashboardLayout,
  components: {
    NdeTabSetting,
    NdeFormSettingDefault,
    ActionButton
  },
  data() {
    return {
      settingUpdate: {},
    };
  },

  mounted() {
    this.$store.dispatch('handleVisibleProgramBtn', false);
    window.document.title = 'neuDocs Enterprise Settings';
  },

  beforeDestroy() {
    this.$store.dispatch('handleVisibleProgramBtn', true);
  },
  methods: {
    onCancel() {
      this.$refs.childRef.onCancelButton();
    },
    updatedSetting(val) {
      this.settingUpdate = val;
    },
    onSave() {
      this.$store.dispatch('increaseCallCount');
      axios
        .post('/saveDefaultSettingsOauth', {
          defaultSettings: this.settingUpdate,
        })
        .then((response) => {
          this.$swal({
            text: response?.data?.data?.data?.message,
            icon: response?.data?.data?.data?.status,
            confirmButtonText: 'OK',
          });
          this.resetCookie();
        })
        .catch((error) => {
          console.log(error);
        });
    },
    async resetCookie() {
      var obj = JSON.parse(atob(this.$cookie.get('roles')));
      obj.settings = this.settingUpdate;
      if (this.$cookie.get('roles')) {
        await this.$cookie.delete('roles');
        this.$cookie.set('roles', btoa(JSON.stringify(obj)));
      }
    },
  },
  watch: {},
};
</script>

<style scoped lang="scss">
.v-card__title {
  display: flex;
  justify-content: center;
}
</style>
