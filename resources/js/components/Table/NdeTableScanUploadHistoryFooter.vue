<template>
  <div class="footer-table d-flex justify-space-between pt-2 ">
    <div class="d-flex align-center">
      <nde-paging class="paging mr-3" v-model="page" :value="page" :length="totalCount" :totalVisible="itemsPerPage">
      </nde-paging>
      <v-text-field v-if="totalCount > 1" class="jump-to-page mr-3" :value="page" v-model="jumpToPageNum"
        label="Jump to page" placeholder="Jump to page" hide-details solo dense type="number"
        @keydown.enter="jumpToPage">
        <v-icon slot="append" color="primary" style="cursor: pointer" @click="jumpToPage">mdi-debug-step-over</v-icon>
      </v-text-field>
    </div>
    <div class="d-flex align-center justify-space-between order-search">
      <v-select :items="exportItems" label="Export To CSV" dense solo hide-details class="ml-2 export-to-csv">
      </v-select>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import NdePaging from "../Common/NdePaging.vue";

export default {
  name: "NdeTableScanUploadHistoryFooter",
  data() {
    return {
      jumpToPageNum: 1,
      page: 1,
      totalCount: 0,
      itemsPerPage: 10,
      exportItems: ["Page", "All Pages"],
    };
  },
  methods: {
    async jumpToPage(event) {
      if(event) {
        event.preventDefault()
      }
      if (parseInt(this.jumpToPageNum) > this.totalCount || parseInt(this.jumpToPageNum) < 1) {
        await this.$swal({
          title: 'Warning',
          text: `Page number must be in range 1~` + this.totalCount,
          type: 'warning',
          showCancelButton: false,
          confirmButtonText: 'Ok',
          // cancelButtonText: 'Cancel',
        });

        this.jumpToPageNum = this.page;
        return;
      }
      this.$store.commit("setDocHistoryPostData", { key: 'page', data: this.jumpToPageNum });
      this.$store.commit("getDocHistoryOauth");
      this.page = this.jumpToPageNum
    },
  },

  components: {
    NdePaging,
  },
  computed: {
    ...mapState(['isDocHistoryLoading', 'docHistoryResult']),
  },
  watch: {
    docHistoryResult: function (val) {
      if (val && val.total) {
        this.totalCount = Math.ceil(val.total / this.itemsPerPage);
      } else {
        this.totalCount = 0;
      }
    },
    page: function (newPage) {
      this.$store.commit("setDocHistoryPostData", { key: 'page', data: newPage });
      this.$store.commit("getDocHistoryOauth");
    },

    jumpToPageNum(newVal) {
      if (newVal && (newVal > this.totalCount || newVal < 1)) {
        this.jumpToPageNum = this.page
        this.$swal({
          title: 'Warning',
          text: `Page number must be in range 1~` + this.totalCount,
          type: 'warning',
          showCancelButton: false,
          confirmButtonText: 'Ok',
        });
      }
    }
  }
};
</script>

<style scoped lang="scss">
.footer-table {
  width: 100%;

  .order-search {
    .export-to-csv {
      max-width: 175px;

      label {
        color: #C32C39 !important;
      }
    }
  }

  .jump-to-page {
    max-width: 15.625rem;
  }
}

@media screen and (max-width: 48rem) {
  .d-flex {
    flex-direction: column;

    .jump-to-page {
      margin: 1rem 0;
    }
  }
}
</style>
