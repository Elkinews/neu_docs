<template>
  <div>
    <v-card class="pa-6 mt-3">
      <v-form>
        <div class="d-flex justify-space-between align-center nde-dashboard-acc-item pa-4" @click="isExpandForm = !isExpandForm">
          <div class="d-flex">
            <v-icon>{{ isExpandForm ? "mdi-menu-down" : "mdi-menu-right" }}</v-icon>
            <nde-common-breadcrumbs :tabName="'Watchlist'"/>
          </div>
          <div class="d-flex text-description">
            <span>Records that I am watching</span>
            <span class="grey--text">(sort by activity last performed at column by clicking on the heading)</span>
          </div>
        </div>
        <v-divider></v-divider>

        <v-expand-transition>
          <div v-show="isExpandForm" class="mt-3">
            <nde-table-watchlist />
          </div>
        </v-expand-transition>
          
      </v-form>
    </v-card>
  </div>
</template>

<script>
import NdeTableWatchlist from "../Table/NdeTableWatchlist.vue";
import NdeCommonBreadcrumbs from "./NdeCommonBreadcrumbs.vue";

export default {
  name: "NdeWatchlist",
  components: { NdeTableWatchlist, NdeCommonBreadcrumbs },

  data() {
    return {
      isExpandForm: true,
      panel: [0],     
    };
  },
  methods: {},  
};
</script>

<style scoped lang="scss">
.nde-dashboard-acc-item {
  cursor: pointer;

  .text-description {
    .grey--text {
      color: #767676 !important;
    }
  }
}
span {
  font-size: 0.75rem !important;}


@media screen and (max-width: 48rem) {
  .nde-dashboard-acc-item {
    flex-direction: column;
    align-items: flex-start !important;

    > div {
      margin-bottom: .5rem;
    }

    .text-description {
      flex-direction: column;
    }

    
  }
}
</style>
