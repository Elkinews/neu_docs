<template>
  <v-container fluid>
    <v-card class="mt-2 help-center">
      <v-card-title class="help-title mb-2">NeuDou Enterprise Issues</v-card-title>
      <v-row>
        <v-col cols="12" v-for="help in helps" :key="help.title">
          <v-card class="pb-2">
            <div v-for="item in help.details" :key="item.title">
              <v-card-title class="help-item-title pb-0 mb-3">
                {{ item.title }}
              </v-card-title>
              <v-card-text class="w-100 px-4 pb-0">
                <p class="p-content" v-html="item.description"></p>
              </v-card-text>
            </div>
          </v-card>
        </v-col>
      </v-row>
    </v-card>
  </v-container>
</template>

<script>
import NdeDashboardLayout from '../../shared/layouts/dashboard/NdeDashboardLayout.vue';

export default {
  layout: NdeDashboardLayout,
  data() {
    return {
      helps: [],
    };
  },
  components: {},
  async created() {
    window.document.title = 'neuDocs Enterprise Issues';
    this.$store.dispatch('handleVisibleProgramBtn', false);
    this.$store.commit('setShowProgressAPI', true);
    const resultGetHelpContentsOauth = await this.$store.dispatch('callAPI', {
      url: '/getHelpContentsOauth',
      method: 'post',
      data: {},
    });
    this.$store.commit('setShowProgressAPI', false);
    if (resultGetHelpContentsOauth.message !== 'success') {
      return this.$swal({
        icon: 'error',
        text: this.getMessageResult(resultGetHelpContentsOauth),
      });
    }
    const helps = [];
    const reultHelps = resultGetHelpContentsOauth?.data?.data;
    for (const helpTitle in reultHelps) {
      const helpItems = reultHelps[helpTitle];
      if (typeof helpItems === 'string' || !helpTitle.includes('Issues')) {
        continue;
      }
      for (const itemTitle in helpItems) {
        helps.push({
          title: itemTitle,
          details: (helpItems[itemTitle] || []).map((item) => ({
            ...item,
            title: item.title || itemTitle,
          })),
        });
      }
    }
    this.helps = helps;
  },
};
</script>

<style lang="scss" scoped>
.help-center {
  background-color: unset;
  box-shadow: unset !important;
  .help-title {
    @extend %fontNormalBold;
    font-size: 1.5rem;
    line-height: 2rem;
    color: $darkGreyColor;
    padding: 0px !important;
  }
  .help-item-title {
    @extend %fontNormalBold;
    font-size: 1.25rem;
    line-height: 2rem;
    color: $darkGreyColor;
  }
  .p-content {
    @extend %fontNormal;
    white-space: pre-line;
    color: $darkGreyColor;
    font-size: 0.875rem;
    line-height: 1.5rem;
  }
}
.div-hover:hover {
  text-decoration: underline;
  cursor: pointer;
}
</style>
