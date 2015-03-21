<template>
  <div class="footer-table d-flex justify-space-between pt-2">
    <div class="d-flex align-center">
      <nde-paging class="paging mr-3" :value="page" :length="pageCount" @input="onPaging"></nde-paging>
      <v-text-field class="jump-to-page mr-3" v-if="pageCount > 1" :value="page" v-model="jumpToPageNum" type="number"
        label="Jump to page" placeholder="Jump to page" hide-details solo dense @keydown.enter="jumpToPage">
        <v-icon slot="append" color="primary" style="cursor: pointer" @click="jumpToPage">mdi-debug-step-over</v-icon>
      </v-text-field>
    </div>
    <div class="d-flex align-center justify-space-between order-search" v-if="isVisibleExportCSV">
      <v-select
        :items="exportItems"
        v-model="exportType"
        dense
        solo
        hide-details
        @change="changeExport()"
        class="ml-2 export-to-csv">
      </v-select>
    </div>
  </div>
</template>

<script>
import NdePaging from '../../Common/NdePaging.vue';

export default {
  name: 'NdeFooterTable',
  data() {
    return {
      jumpToPageNum: 1,
      exportType: '',
      exportItems: [
        {
          text: 'Export To CSV',
          value: '',
          disabled: true,
        },
        {
          text: 'Page',
          value: 'page',
        },
        {
          text: 'All Pages',
          value: 'all'
        },
      ],
    };
  },
  props: {
    page: {
      type: Number,
      default: () => 1,
    },
    pageCount: {
      type: Number,
      default: () => 1,
    },
    isVisibleExportCSV: {
      type: Boolean,
      default: true,
    },
  },
  methods: {
    async changeExport() {
      if (this.exportType) {
        this.$emit('exportCSV:submit', this.exportType);
      }
      setTimeout(() => {
        this.exportType = '';
      }, 10);
    },
    async jumpToPage(event) {
      if (event) {
        event.preventDefault()
      }
      if (parseInt(this.jumpToPageNum) > this.pageCount || parseInt(this.jumpToPageNum) < 1) {
        await this.$swal({
          title: 'Warning',
          text: `Page number must be in range 1~` + this.pageCount,
          type: 'warning',
          showCancelButton: false,
          confirmButtonText: 'Ok',
          // cancelButtonText: 'Cancel',
        });

        this.jumpToPageNum = this.page;
        return;
      }

      const pageJump = this.jumpToPageNum ? parseInt(this.jumpToPageNum) : 1;
      this.$emit('paging:update', pageJump);
    },
    onPaging(pageNumber) {
      this.$emit('paging:update', pageNumber);
    },

  },

  watch: {
    jumpToPageNum(newVal) {
      if (newVal && (newVal > this.pageCount || newVal < 1)) {
        this.jumpToPageNum = this.page
        this.$swal({
          title: 'Warning',
          text: `Page number must be in range 1~` + this.pageCount,
          type: 'warning',
          showCancelButton: false,
          confirmButtonText: 'Ok',
        });
      }
    }
  },

  components: {
    NdePaging,
  },
};
</script>

<style scoped lang="scss">
.footer-table {
  width: 100%;

  .order-search {
    button {
      background: $primaryColor !important;
      color: white;
      text-transform: none;
    }

    .export-to-csv {
      max-width: 175px;

      label {
        color: $errorColor;
      }
      ::v-deep .v-select__selection--disabled {
        color: unset !important;
      }
    }
  }

  .jump-to-page {
    max-width: 250px;
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

<style lang="scss">
.footer-table {

  /* Chrome, Safari, Edge, Opera */
  input::-webkit-outer-spin-button,
  input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
  }

  /* Firefox */
  input[type=number] {
    -moz-appearance: textfield;
  }
}
</style>