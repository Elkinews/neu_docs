<template>
  <div>
    <v-card class="pa-6 mt-3">
      
        <div class="d-md-flex justify-space-between" style="width: 100%">
          <div class="d-flex align-center info-record">
            <v-icon @click="isExpandForm = !isExpandForm">{{
              isExpandForm ? "mdi-menu-down" : "mdi-menu-right"
            }}</v-icon>
            <nde-common-breadcrumbs :tabName="'Scan/Upload History'"/>
          </div>
          <nde-table-scan-upload-history-date/>
        </div>

        <v-divider></v-divider>

        <v-expand-transition>
          <div v-show="isExpandForm" class="mt-3">
            <nde-table-scan-upload-history />
            <!-- <nde-button style="width: 100%" class="mt-5 btn-load-more black--text">Load More</nde-button> -->
            <nde-table-scan-upload-history-footer />
          </div>
        </v-expand-transition>
      
    </v-card>
  </div>
</template>

<script>
import NdeTableScanUploadHistory from "../Table/NdeTableScanUploadHistory.vue";
import NdeTableScanUploadHistoryDate from "../Table/NdeTableScanUploadHistoryDate.vue"
import NdeButton from "../Button/NdeButton.vue"
import NdeTableScanUploadHistoryFooter from "../Table/NdeTableScanUploadHistoryFooter.vue"
import NdeCommonBreadcrumbs from "./NdeCommonBreadcrumbs.vue";

export default {
  name: "NdeScanUploadHistory",
  components: {
	  NdeTableScanUploadHistory,
    NdeTableScanUploadHistoryDate,
    NdeButton,
    NdeTableScanUploadHistoryFooter,
    NdeCommonBreadcrumbs
  },
  data() {
    return {
      isExpandForm: true,  
    };
  },
  methods: {}
};
</script>

<style scoped lang="scss">
.btn-load-more {
  color: $primaryGreyColor;
  font-size: 0.875rem;
  font-weight: 500;
  font-family: "Poppins";
}
</style>
