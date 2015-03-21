<template>
	<v-data-table tabindex="1" class="nde-data-table" v-bind="$attrs" :items="items" :headers="headers"
		:items-per-page="itemsPerPage" :page.sync="page" :loading="isLoading" hide-default-footer
		:show-select="showSelect" :mobile-breakpoint="mobileBreakpoint" loading-text="Loading... Please wait"
		@update:sort-by="handleUpdateSortBy" @update:sort-desc="handleUpdateSortDesc" @click:row="handleClickRow"
		v-model="selected" :item-class="itemRowBackground">
		<template :slot="`item.${header.value}`" v-for="header in headers" slot-scope="props">
			<div :key="header.value" @click="hasSlot">
				{{ hasSlot(header.value) ? '' : props.item[header.value] || '' }}
				<slot :name="`item.${header.value}`" v-bind="props" />
			</div>
		</template>
	</v-data-table>
</template>
<script>
export default {
  name: 'NdeDataTable',
  props: {
    headers: {
      type: Array,
      default: () => [],
    },
    items: {
      type: Array,
      default: () => [],
    },
    itemsPerPage: {
      type: Number,
      default: 10
    },
    page: {
      type: Number,
      default: 1
    },
    isLoading: {
      type: Boolean,
      default: false
    },
    showSelect: {
      type: Boolean,
      default: false
    },
    selectAll: {
      type: Boolean,
      default: false
    },
    mobileBreakpoint: {
      type: Number,
      default: 600,
    },
  },
  data() {
    return {
      selected: [],
      currentClickedItem: ''
    };
  },
  methods: {
    hasSlot(header) {
      return !!this.$scopedSlots[`item.${header}`];
    },
    handleUpdateSortBy(column) {
      this.$emit('updateSortBy', column);
    },
    handleUpdateSortDesc(isDesc) {
      this.$emit('updateSortDesc', isDesc);
    },
    handleClickRow(value) {
      this.currentClickedItem = value.doc_id;
      this.$emit('onclickrow', value);
    },
    itemRowBackground(item) {
      if (item.doc_id && item.doc_id == this.currentClickedItem) {
        return 'nde-selected-item-row';
      } else {
        return '';
      }
    }
  },
  computed: {
  },
  components: {},
  watch: {
    selected(newVal, oldVal) {
      if (newVal !== oldVal && this.showSelect) {
        this.$emit('onUpdateSelected', newVal)
      }
    },
    showSelect(value) {
      if (value && this.selectAll) {
        this.selected = this.items
      }
    }
  }
};
</script>

<style scoped lang="scss">
.nde-data-table {
  table {
    tbody {
      td.text-start {
        div {
          overflow: hidden;
          text-overflow: ellipsis;

          &:hover {
            overflow: visible;
          }
        }

      }
    }
  }
}
</style>
