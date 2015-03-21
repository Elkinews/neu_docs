<template>
  <v-dialog v-if="showModal" v-model="showModal" max-width="1200" :persistent="true">
    <v-card class="pa-5" style="min-height: 10rem">
      <v-card-title class="text-sm-left font-weight-bold d-flex justify-space-between ml-2">
        <h3>Double Entry Fields</h3>
        <v-icon aria-label="Close" size="24px" color="red" @click="hideModal"> mdi-close </v-icon>
      </v-card-title>
      <v-card-text>
        <v-container class="mt-5">
          <h4 class="mb-5" style="font-weight: normal">
            <strong>Double Entry Fields</strong> (Please re-enter the following field(s) to verify
            entered values are correct)
          </h4>
          <v-row class="ma-0 pa-0" v-for="field in fields" :key="field.key">
            <v-col cols="12" md="4" class="mb-3">
              <div class="mb-2">
                <label>{{ field.label }}</label>
              </div>

              <v-text-field
                v-model="valueDoubleEntryFields[field.key]"
                :append-icon="showDoubleEntryFields[field.key] ? 'mdi-eye' : 'mdi-eye-off'"
                :type="showDoubleEntryFields[field.key] ? 'text' : 'password'"
                @click:append="clickShowDoubleEntryFields(field.key)"
                solo
                dense
                hide-details
              ></v-text-field>
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
      <v-card-actions>
        <nde-button-cancel
          @click="hideModal"
          title="Close"
          :styles="{ width: '8.25rem' }"
          class="mr-3"
        ></nde-button-cancel>
        <nde-button-cancel
          @click="checkDoubleEntryFields"
          title="View Changes"
          class="mr-3"
        ></nde-button-cancel>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import NdeButtonCancel from '../Button/NdeButtonCancel.vue';

export default {
  name: 'NdeModalBulkEditDoubleEntry',
  components: { NdeButtonCancel },
  props: {
    fields: {
      type: Array,
      default: () => [],
    },
    valueFields: {
      type: Object,
      default: () => {},
    },
  },
  data() {
    return {
      showModal: true,
      valueDoubleEntryFields: {},
      showDoubleEntryFields: {},
    };
  },
  methods: {
    clickShowDoubleEntryFields(key) {
      this.$set(this.showDoubleEntryFields, key, !this.showDoubleEntryFields[key]);
    },
    hideModal() {
      this.$emit('hideModal');
    },
    checkDoubleEntryFields() {
      let isShowError = false;
      for (const field of this.fields) {
        if (this.valueDoubleEntryFields[field.key] != this.valueFields[field.key]) {
          isShowError = true;
          break;
        }
      }
      if (isShowError) {
        return this.$swal({
          icon: 'error',
          text: 'Values do not match.',
        });
      }
      return this.$emit('afterCheckEditDobuleEntry');
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
.v-card__actions {
  justify-content: space-between;
}

.v-chip {
  &.red {
    background: #f9e1e1 !important;
    border: 1px solid #c32c39 !important;
    box-sizing: border-box;
    border-radius: 4px;
  }
  strong {
    text-transform: capitalize;
    color: #c32c39;
    font-size: 0.75rem;
    line-height: 1rem;
  }
}
</style>