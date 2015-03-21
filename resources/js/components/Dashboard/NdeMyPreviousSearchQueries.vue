<template>
  <div>
    <v-card class="pa-6 mt-3">
      <v-form>
        <div class="d-flex justify-space-between align-center nde-dashboard-acc-item" @click="isExpandForm = !isExpandForm">
          <div class="d-flex">
            <v-icon>{{
              isExpandForm ? "mdi-menu-down" : "mdi-menu-right"
            }}</v-icon>
              <nde-common-breadcrumbs :tabName="'My Previous Search Queries'"/>   
          </div>
        </div>
        <v-divider></v-divider>

        <v-expand-transition>
          <div v-show="isExpandForm" class="mt-3">
            <nde-table-my-previous-search-queries v-on="$listeners" />
          </div>
        </v-expand-transition>
          
      </v-form>
    </v-card>
  </div>
</template>

<script>
import NdeTableMyPreviousSearchQueries from "../Table/NdeTableMyPreviousSearchQueries.vue";
import NdeCommonBreadcrumbs from "./NdeCommonBreadcrumbs.vue";

export default {
  name: "NdeMyPreviousSearchQueries",
  components: { NdeTableMyPreviousSearchQueries, NdeCommonBreadcrumbs },

  data() {
    return {
      // TODO: Will delete when there is data get from API
      isExpandForm: true,
      panel: [0],
      newItem: '',
    };
  },
  methods: {
    
  }, 
};
</script>

<style scoped lang="scss">
.nde-dashboard-acc-item {
  cursor: pointer;
}
</style>
