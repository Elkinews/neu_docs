<template>
  <div class="nde-filter-report-form">
    <nde-form class="nde-form">
      <template v-slot:ndeFormBody>
        <p v-text="alertText" style="height: 0px; width: 0px; overflow:hidden;" role="alert" aria-atomic="true"></p>
        <p style="color: rgb(199, 0, 0)">{{ textInfo }}</p>
        <v-row>
          <v-col
            :class="{
              'd-flex': item.type === 'CHECKBOX',
            }"
            cols="12"
            md="4"
            v-for="item in reportControls"
            :key="item.key"
          >
            <label
              class="mb-1"
              :class="{
                'mr-2': item.type === 'CHECKBOX',
              }"
            >
              {{ item.label }}
            </label>
            <nde-filter-item
              :type="item.type"
              :id="item.key || item.nql_param"
              :items="item.map ? item.map : []"
              @onChange="handleOnChangeInput"
              :label="item.label"
            />
          </v-col>
          <v-col cols="12" align="right">
            <nde-button-primary
              :disabled="enableButton"
              title="Generate Report"
              class="mr-2"
              @click="onGenerateReport"
            />
          </v-col>
        </v-row>
      </template>
    </nde-form>
  </div>
</template>

<script>
import NdeForm from '@components/Form/NdeForm.vue';
import NdeButtonPrimary from '@components/Button/NdeButtonPrimary.vue';
import NdeButtonWarning from '@components/Button/NdeButtonWarning.vue';

import NdeFilterItem from './NdeFilterItem.vue';
export default {
  name: 'NdeFilterForm',
  props: {
    reportControls: {
      type: Array,
      default: () => [],
    },
    textInfo: {
      type: String,
      default: '',
    },
    enableButton: {
      type: Boolean,
      default: false,
    },
  },
  computed: {
    alertText() {
      if (!this.enableButton) {
        return 'Loading data table';
      } else {
        // Get data Parent Component 
        const totalItems = this.$parent?.$options?.parent?.totalItems;
        if (totalItems == 0) {
          return 'No data available'
        }
        return 'Loaded data table';
      }
    }
  },
  methods: {
    handleOnChangeInput(id, value) {
      this.$emit('updateFilterData', id, value);
    },
    onGenerateReport() {
      this.$emit('onGenerateReport');
    },
    toArray(map) {
      return Object.values(map);
    },
  },
  components: {
    NdeFilterItem,
    NdeForm,
    NdeButtonPrimary,
    NdeButtonWarning,
  },
};
</script>

<style scoped lang="scss">
.nde-filter-report-form {
  .nde-form {
    background-color: $whiteColor;
  }
}
</style>
