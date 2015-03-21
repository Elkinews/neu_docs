<template>
  <v-container class="mt-5">
    <h5 class="mb-5">
      Double Entry Fields (Please re-enter the following field(s) to verify entered values are
      correct)
    </h5>
    <v-row v-if="dataDoubleEntry?.requiredDoubleEntry">
      <nde-form-double-entry-confirm-field
        v-for="(item, index) in dataDoubleEntry?.requiredDoubleEntry"
        :key="index"
        :controlName="item"
        @onChange="onChange"
      ></nde-form-double-entry-confirm-field>
    </v-row>
    <v-row v-if="dataDoubleEntry.doubleEntry">
      <nde-form-double-entry-confirm-field
        v-for="item in dataDoubleEntry.doubleEntry"
        :key="item"
        :controlName="item"
        @onChange="onChange"
      ></nde-form-double-entry-confirm-field>
    </v-row>
  </v-container>
</template>

<script>
import { mapState } from 'vuex';
import NdeFormDoubleEntryField from './NdeFormDoubleEntryField.vue';
import NdeFormDoubleEntryConfirmField from './NdeFormDoubleEntryConfirmField.vue';

export default {
  components: { NdeFormDoubleEntryField, NdeFormDoubleEntryConfirmField },
  name: 'NdeFormDoubleEntryConfirm',
  props: {
    item: {
      type: Object,
      default: () => ({}),
    },
  },

  computed: {
    ...mapState(['dataDoubleEntry']),
  },

  methods: {
    onChange(data) {
      this.$emit('onChange', data);
    },
  },
};
</script>
<style scoped lang="scss">
.v-form {
  background: $primaryGreyColor;
  border-radius: 6px;
  :v-deep .v-input__slot {
    min-height: 2.5rem;
    fieldset {
      background: #ffffff;
      border: 1px solid #ebebeb;
      border-radius: 8px;
    }
    input {
      font-weight: 500;
      font-size: 0.875rem;
      line-height: 1.5rem;
      color: #343c42;
    }
    .v-input__append-inner {
      margin-top: 0.313rem;
    }
  }
  :v-deep .col-12,
  .col-md-6 {
    padding: 3px 10px !important;
  }
}
</style>
