<template>
  <div class="nde-select-tab-step">
    <v-row>
      <nde-dashboard-search-field
        v-for="(search_field, i) in currentControls"
        :key="i + 1"
        :search_field="search_field"
        @onChange="updatedFormFieldValue"
        @onEnterKey="handleEnter"
        :isFullWidth="true"
      />
    </v-row>
  </div>
</template>

<script>
import NdeDashboardSearchField from '@components/Dashboard/NdeDashboardSearchField.vue';
export default {
  data() {
    return {};
  },
  props: {
    currentControls: {
      type: Array,
      required: true,
    },
  },
  methods: {
    updatedFormFieldValue(data) {
      this.$emit('onUpdateFormFieldValue', data);
    },
    handleEnter(data) {
      console.log(data);
    },
  },
  components: {
    NdeDashboardSearchField,
  },
};
</script>
<style lang="scss">
.nde-select-tab-step {
  .col {
    padding: unset;
  }
  &__required {
    color: red;
    margin-left: 2px;
  }
  .row {
    margin: 0 0 15px 0;
  }
  .text-end-center {
    display: flex;
    justify-content: flex-end;
    align-self: center;
  }
  .v-input__prepend-outer {
    .nde-select-tab-step__sublabel {
      color: rgba(0, 0, 0, 0.6);
      font-size: 0.875rem;
      font-weight: 400;
      line-height: 1.375rem;
      letter-spacing: 0.0071428571em;
    }
  }
}
</style>
