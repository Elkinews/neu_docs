<template>
  <v-pagination
    class="nde-paging"
    v-model="activePage"
    :length="length || 0"
    :total-visible="totalVisible"
    prev-icon="mdi-menu-left"
    next-icon="mdi-menu-right"
  ></v-pagination>
</template>

<script>
export default {
  name: 'NdePaging',
  props: {
    value: {
      type: Number,
      default: 1,
    },
    length: {
      type: Number,
      default: 0,
    },
    totalVisible: {
      type: Number,
      default: 8,
    },
  },
  computed: {
    activePage: {
      get() {
        return this.value;
      },
      set(value) {
        this.$emit('input', value);
      },
    },
  },
  mounted() {
    this.removeAriaCurrent();
  },
  updated() {
    this.removeAriaCurrent();
  },
  methods: {
    removeAriaCurrent() {
      const element = document.querySelector('.nde-paging > ul > li > [aria-current="true"]');
      if (element) {
        element.ariaCurrent = "false";
        element.ariaLabel = element.ariaLabel.replace('Current Page, Page', 'Current Page');
      }
    }
  },
};
</script>

<style scoped lang="scss">
:v-deep .v-pagination {
  justify-content: start !important;
  button {
    &.v-pagination__item {
      box-shadow: none;
    }
    &.v-pagination__item--active {
      color: $primaryColor !important;
      background-color: #f4f7fb !important;
      border-color: unset !important;
    }
  }
}
</style>

<style lang="scss">
.nde-table-userProfile-report {
  .v-pagination {
    li:first-child,
    li:last-child {
      button {
        border: 1px solid #000;
        box-shadow: none;
      }
      
    }
  }
  button {
    &.v-pagination__item {
      border: 1px solid #000;
      box-shadow: none;
    }
  }
}
</style>
