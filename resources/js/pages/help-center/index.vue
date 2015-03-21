<template>
  <div role="main">
  <v-container fluid>
    <v-card class="mt-2 help-center">
      <v-card-title>
        <div>
          <h2 class="help-title" role="heading">Help Center</h2>
        </div>
        <div style="width: 100%">
          <div class="d-flex align-center info-record pb-4">
            <p class="mt-3"><span class="help-choose"> Choose a topic to get Stated </span></p>
          </div>

          <v-row>
            <v-col cols="12" md="6" sm="12" v-for="item in helps" :key="item.title">
              <v-card class="help-item">
                <v-card-title class="help-title">
                  {{ item.title }}
                </v-card-title>
                <v-card-text
                  class="help-item-title w-100 px-4 pb-0 div-hover"
                  v-for="itemDetail in item.details"
                  :key="itemDetail.title"
                >
                  {{ itemDetail.title }}
                </v-card-text>
                <div class="d-flex px-4 py-4 mt-4">
                  <nde-button
                    text
                    class="
                      v-btn v-btn--is-elevated v-btn--has-bg
                      theme--light
                      v-size--default
                      primary
                    "
                    @click="onDetail(item)"
                  >
                    View All
                  </nde-button>
                </div>
              </v-card>
            </v-col>
          </v-row>
        </div>
      </v-card-title>
    </v-card>
  </v-container>
  </div>
</template>

<script>
import NdeDashboardLayout from '../../shared/layouts/dashboard/NdeDashboardLayout.vue';
import NdeButton from '../../components/Button/NdeButton.vue';

export default {
  layout: NdeDashboardLayout,
  data() {
    return {
      helps: [],
    };
  },
  components: {
    NdeButton,
  },
  async created() {
    window.document.title = 'neuDocs Enterprise Help';
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
      const helpDetails = [];
      if (typeof helpItems === 'string') {
        continue;
      }
      if (Array.isArray(helpItems)) {
        helpDetails.push(...helpItems);
      } else {
        helpDetails.push(
          ...Object.keys(helpItems).map((helpItemTitle) => ({
            title: helpItemTitle,
          })),
        );
      }
      helps.push({
        title: helpTitle,
        details: helpDetails,
      });
    }
    this.helps = helps;
  },
  methods: {
    onDetail(data) {
      if (data.title.includes('Issues')) {
        location.href = 'help-center/issues';
      } else {
        location.href = 'help-center/basics';
      }
    },
  },
  beforeDestroy() {
    this.$store.dispatch('handleVisibleProgramBtn', true);
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
  }
  .help-choose {
    @extend %fontNormal;
    font-size: 1rem;
    line-height: 1.5rem;
    color: $drakColor;
  }
  .help-item {
    border-left: 8px solid $primaryColor;
    .help-item-title {
      @extend %fontNormal;
      font-size: 0.875rem;
      line-height: 1.5rem;
      color: $primaryColor;
      padding-top: 0.5rem !important;
    }
  }
}
.div-hover:hover {
  text-decoration: underline;
  cursor: pointer;
}
</style>
