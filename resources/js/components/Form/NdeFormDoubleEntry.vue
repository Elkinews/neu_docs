<template>
  <v-container>
    <v-row>
      <nde-form-double-entry-field
        v-for="item in groupOneFields"
        :controlName="item"
        :key="item"
        @onChange="onChange"
      />
    </v-row>
    <v-row v-if="groupOneFields.length > 0 && groupTwoFields.length > 0">
      <v-col cols="12">
        <v-divider></v-divider>
      </v-col>
    </v-row>
    <v-row>
      <nde-form-double-entry-field
        v-for="item in groupTwoFields"
        :controlName="item"
        :key="item"
        @onChange="onChange"
      />
    </v-row>
  </v-container>
</template>

<script>
import { mapState } from 'vuex';
import NdeFormDoubleEntryField from './NdeFormDoubleEntryField.vue';
export default {
  components: { NdeFormDoubleEntryField },
  name: 'NdeFormDoubleEntry',
  // TODO: Will delete when there is data get from API
  props: {
    item: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {};
  },
  computed: {
    ...mapState(['dataDoubleEntry']),
    groupOneFields() {
      // Get Group 1 fields by doubleEntry, requiredDoubleEntry, and groupRequiredGroups with field value = 1
      let doubleEntry = this.dataDoubleEntry.requiredEntry.concat(this.dataDoubleEntry.requiredDoubleEntry);
      let fields = doubleEntry.concat(this.dataDoubleEntry.doubleEntry)
      let fieldsGroup = this.dataDoubleEntry.groupRequiredGroups;
      this.dataDoubleEntry.groupRequiredEntry.forEach(field => {
        if (fieldsGroup[field] != 2 && !fields.includes(field)) {
          fields.push(field);
        }
      });
      return fields;
    },
    groupTwoFields() {
      // Get Group 2 fields by groupRequiredGroups with field value = 2
      let fields = [];
      let fieldsGroup = this.dataDoubleEntry.groupRequiredGroups;
      this.dataDoubleEntry.groupRequiredEntry.forEach(field => {
        if (fieldsGroup[field] == 2 && !fields.includes(field)) {
          fields.push(field);
        }
      });
      return fields;
    },
  },
  mounted() {},
  methods: {
    onChange(data) {
      this.$emit('onChange', data);
    },
  },
};
</script>
<style scoped lang="scss">
:v-deep .v-text-field--outlined {
  .v-input__slot {
    min-height: 2.5rem;
    input {
      font-weight: 500;
      font-size: 0.875rem;
      line-height: 1.5rem;
      color: #343c42;
    }
    .v-input__append-inner {
      margin-top: 0.313rem;
    }
    .v-input__icon--clear {
      margin-right: 0.313rem;
      button {
        font-family: 'Poppins';
        font-style: normal;
        font-weight: 400;
        font-size: 0.875rem;
        line-height: 1.5rem;
        color: #c32c39 !important;
      }
    }
  }
  .v-input__append-outer {
    margin-top: 0.313rem;
    margin-left: 0.125rem;
    .v-input__icon--append-outer {
      i {
        font-weight: 400;
        font-size: 0.875rem;
        line-height: 1.5rem;
        color: #343c42;
      }
    }
  }
}
:v-deep .col-12,
.col-md-6 {
  padding: 0rem 0.625rem !important;
}
.theme--light.v-divider {
  border-color: rgba(0, 0, 0, 0.3) !important;
}
</style>
