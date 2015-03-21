<template>
  <div class="d-md-flex justify-space-around align-center options-record">
    <!-- Load size -->
    <v-select
      class="nde-list nde-load-size mr-2 d-flex align-center"
      :value="pageSize"
      :items="pageSizes"
      dense
      solo
      hide-details
      @change="onChangePageSize"
    >
      <template v-slot:prepend>
        <div class="nde-load-size__label nde-list__label">Load Size:</div>
      </template>
    </v-select>
    <!--  Visible Column-->
    <v-select
      class="nde-visible-columns mr-2"
      :value="selectedColumns"
      :items="columns"
      multiple
      solo
      dense
      hide-details
      left
      return-object
      :menu-props="{ top: true, offsetY: true, contentClass: 'nde-visible-columns-list' }"
      @change="onChangeVisibleColumns"
    >
      <template v-slot:prepend-inner>
        <div class="nde-list__label">Visible Columns</div>
      </template>
      <template v-slot:selection="{}"> </template>
    </v-select>
    <!-- Bulk Options -->
	<v-select
		v-if="showBulkOptions && bulkOptionsByRoles.length"
		class="nde-list nde-bulk-options"
		:items="bulkOptionsByRoles"
		item-text="text"
		item-value="endpoint"
		@change="onChangeBulkOption"
		solo
		dense
		hide-details
	>
		<template v-slot:prepend-inner>
			<div class="nde-list__label">Bulk Options</div>
		</template>

		<template v-slot:selection="{}">
			<span v-if="true"></span>
		</template>
	</v-select>

    <div class="d-flex" v-else>
      <nde-button color="default" class="mr-1" @click="reset">Cancel</nde-button>
      <nde-button  color="primary" @click="clickNext"
        >Next</nde-button>
     
    </div>
  </div>
</template>
<script>
import { mapState } from 'vuex';
import NdeButton from '../../Button/NdeButton';

export default {
  name: 'NdeTableAction',
  components: {
    NdeButton
  },
  props: {
    selectedColumns: {
      type: Array,
      default: () => [],
    },
    pageSize: {
      type: Number,
      default: () => 10,
    },
    columns: {
      type: Array,
      default: () => [],
    },
    showSelect: {
      type: Boolean,
      default: false
    },
  },
  data() {
    return {
      pageSizes: [10, 25, 50, 100],
    };
  },
  computed: {
    ...mapState(['bulkOptions', 'allowSortRecordOrder']),
    showBulkOptions() {
      return !(this.showSelect || this.allowSortRecordOrder)
    },
    bulkOptionsByRoles() {
      const bulkOptions = [];
      const userLoginRoles = this.userLoginRoles() || [];
      if (!userLoginRoles?.length) return [];
      if (!this.checkRoleByName('BULKACCESS')) return [];
      this.bulkOptions.forEach((dataOption) => {
        let checkRole = false;
        if (Array.isArray(dataOption.roles)) {
          if (!dataOption.roles.length) {
            checkRole = true;
          } else {
            dataOption.roles.forEach((role) => {
              if (userLoginRoles.includes(role) && (role !== 'COPYTOWORKQUEUE' && role !== 'ALLOWDOWNLOAD' && role !== 'DOWNLOADPDFA') ) {
                checkRole = true;
              }
            });
          }
        }
        
        if (checkRole) {
           bulkOptions.push(dataOption);
        }
      });
      return bulkOptions;
    },
  },
  methods: {
    onChangePageSize(pageSize) {
      console.log(pageSize);
      this.$emit('onChangePageSize', pageSize);
    },
    onChangeVisibleColumns(event) {
      this.$emit('onChangeVisibleColumn', event);
    },
    onChangeBulkOption(event) {
      this.$emit('onChangeBulkOption', event);
    },

    reset() {
      this.$emit('onCancelSelect')
    },

    clickNext() {
      this.$emit('onSubmitSelect')
    }
  },
};
</script>
<style lang="scss" scoped>
.options-record {
  margin-bottom: 0.75rem;
}
.nde-list {
  .v-select__selection {
    font-size: 0.813rem;
  }
  &__label {
    white-space: nowrap;
    font-size: 0.813rem;
  }
}
.nde-visible-columns {
  width: 11.25rem;
}
.nde-visible-columns-list.v-menu__content {
  overflow: visible !important;
  max-height: 100% !important;
  .v-list-item__action {
    margin-right: 0.625rem;
    margin-top: 0;
    margin-bottom: 0;
  }
}
.nde-bulk-options {
  width: 11.25rem;
}
.nde-load-size {
  width: 10rem;
  color: $greyColor;
}

.nde-visible-columns {
	margin: 1rem 0;
}

@media screen and (max-width: 48rem) {
	.options-record {
		display: flex;
		flex-wrap: wrap;
		margin-bottom: -0.5rem;
	}
	.nde-visible-columns,
	.nde-bulk-options {
		width: 8rem;
	}
	.nde-load-size {
		width: 100%;
	}
}
</style>
