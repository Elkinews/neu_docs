<template>
  <v-dialog v-if="showModal" v-model="showModal" max-width="1200" @click:outside="hideModal">
    <v-card class="pa-5" :class="{'p-0' : isMobile}">
      <v-card-title class="text-sm-left font-weight-bold d-flex justify-space-between mb-3" :class="{'pa-0' : isMobile}">
        <h3 v-if="!isMobile">Import From My Documents</h3>
        <h5 v-else>Import From My Documents</h5>
        <v-icon aria-label="Close" size="24px" color="red" @click="hideModal"> mdi-close </v-icon>
      </v-card-title>
      <v-card-text class="mb-3">
        <nde-table-import :items="pagedItems"></nde-table-import>
      </v-card-text>
      <v-card-actions>
        <v-row :class="{'w-100': isMobile}">
          <v-col cols="12" md="6" class="paging-import">
            <nde-paging :length="maxPage" v-model="page"></nde-paging>
          </v-col>
          <v-col cols="12" md="6" :class="{'text-align-right': !isMobile, 'text-center': isMobile}">
            <nde-button-import
              :class="{'w-100': isMobile}"
              @click="clickImport"
              :disabled="!isImportAble"
              :loading="isUploading"
            ></nde-button-import>
            <nde-button-import-cancel @click="hideModal"
              :class="{'w-100 mt-3': isMobile, 'ml-3 mr-1': !isMobile}"
              :disabled="isUploading">
            </nde-button-import-cancel>
          </v-col>
        </v-row>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import { mapState } from 'vuex';
import NdeTableImport from '../Table/NdeTableImport.vue';
import NdeButtonImport from '../Button/NdeButtonImport.vue';
import NdePaging from '../Common/NdePaging.vue';
import NdeButtonImportCancel from '../Button/NdeButtonImportCancel.vue';

export default {
  name: 'NdeModalImport',
  components: {
    NdeTableImport,
    NdeButtonImport,
    NdePaging,
    NdeButtonImportCancel,
  },
  props: {
    showModal: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      page: 1,
      items: [],
      totalItems: 0,
      limit: 10,
      isUploading: false,
      uploadCount: 0,
      pagedItems: [],
    };
  },
  computed: {
    ...mapState(['myDocumentData', 'currentUploadBoxType', 'currentImageId', 'currentProgram']),
    maxPage() {
      return Math.ceil(this.totalItems / this.limit);
    },

    isImportAble() {
      let importable = false;
      this.items.map((item) => {
        if (item.checked) importable = true;
      });

      return importable;
    },
  },

  methods: {
    hideModal() {
      this.$emit('onClose', !this.showModal);
    },

    formatData() {
      this.totalItems = this.myDocumentData.total;
      this.items = [];
      this.myDocumentData.download_requests.map((item) => {
        this.items.push({
          id: item.id,
          name: item.filename,
          queuedAt: item.queued_at,
          status: item.status,
          program: item.name,
          checked: false,
          nuid: item.nuid,
          file_size: item.file_size,
          page_count: item.page_count
        });
      });
    },

    async getItems() {
      this.formatData();
      this.getPagedItems();
    },

    clickImport() {
      console.log('action click import');
      const filteredItems = this.items.filter((o) => o.checked);

      this.$emit('onImport', filteredItems);
      this.hideModal();
    },

    async calcUploadTotalStatus() {
      const checkedItems = this.items.filter((o) => o.checked);
      if (checkedItems.length == this.uploadCount) {
        this.isUploading = false;

        const confirmed = await this.$swal({
          title: 'Success',
          text: 'All files are imported successfully!',
          type: 'success',
          showCancelButton: false,
          confirmButtonText: 'Ok',
        });

        if (confirmed.value) {
          this.$emit('onImported');
        }
      }
    },

    getPagedItems() {
      this.pagedItems = this.items.slice((this.page - 1) * this.limit, this.page * this.limit);
    },
  },

  watch: {
    page: {
      handler() {
        this.getPagedItems();
      },
    },
    showModal: function (val) {
      this.pagedItems.forEach((item) => {
        item.checked = false;
      });
      if(val) {
        this.getItems();
      }
    },
  },
};
</script>

<style scoped lang="scss">
.v-card__title {
  .v-icon:hover {
    &::after {
      @extend %afterIconModalClose;
    }
  }
}
:v-deep .paging-import {
  min-width: 20.625rem;
  min-height: 2rem;
  .v-pagination {
    justify-content: left;
  }
  .v-pagination__item {
    font-size: 0.875rem;
    min-width: 2rem;
    height: 2rem;
  }
}
.text-align-right {
  text-align: right;
}
</style>
