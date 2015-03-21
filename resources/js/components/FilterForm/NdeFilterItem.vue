<template>
  <div>
    <nde-report-select
      v-if="type === 'DROPDOWN'"
      :items="mappedDropdownItems"
      :v-model="mappedDropdownItems[0]"
      @onSelect="handleOnChange"
      :aria-label="label"
    />
    <nde-date-picker
      v-if="type === 'DATERANGE'"
      range
      :value="dateRange"
      format="YYYY-MM-DD"
      @onInput="handleOnChangeDateRange"
      :aria-label="label"
    />
    <nde-date-picker
      v-if="type == 'DATE'"
      @onInput="handleOnChangeDateRange"
      :aria-label="label"
    />
    <v-text-field
      v-if="type == 'TEXT' && id === 'retention_year'"
      dense
      solo
      v-model="inputValue"
      :rules="[
        () => isValidYear(inputValue) || 'Please enter a valid retention year format',
      ]"
      aria-label="Retention Year"
    ></v-text-field>
    <v-text-field
      v-if="type == 'TEXT' && id !== 'retention_year'"
      dense
      solo
      hide-details
      v-model="inputValue"
      aria-label="Retention Year"
    ></v-text-field>
    <v-checkbox
      v-if="type == 'CHECKBOX'"
      class="mt-0 pt-0"
      v-model="checkbox"
      hide-details
      :aria-label="label"
    ></v-checkbox>
</div>
</template>

<script>
import * as date from 'date.js';
import * as moment from 'moment';
import NdeReportSelect from '@components/Inputs/NdeReportSelect';
import NdeDatePicker from '@components/DatePicker/NdeDatePicker';
export default {
  data() {
    const start = date('7 days ago');
    const end = date();
    return {
      inputValue: '',
      checkbox: false,
      dateRange: {
        from: moment(start).format('YYYY-MM-DD'),
        to: moment(end).format('YYYY-MM-DD'),
      }
    };
  },
  props: {
    type: {
      type: String,
      default: () => 'DROPDOWN',
    },
    items: {
      type: Object|Array,
      default: () => {},
    },
    id: {
      type: String,
      default: () => '',
    },
    label: {
      type: String,
      default: () => '',
    },
  },
  computed: {
    mappedDropdownItems() {
      let result = [];
      for (const [key, value] of Object.entries(this.items)) {
        const mappedItem = {
          text: value?.label || value,
          value: value?.value || key,
        };
        result.push(mappedItem);
      }
      return result;
    },
  },
  methods: {
    isValidYear(inputValue) {
      return !!inputValue && moment(inputValue, 'YYYY', true).isValid()
    },
    handleOnChange(value) {
      let payload = value;
      if (this.type === 'DROPDOWN') {
        if (!isNaN(parseInt(value))){
          payload = parseInt(value)
        } else {
          payload = value
        }
        ;
      }
      this.$emit('onChange', this.id, payload);
    },
    handleOnChangeDateRange(value) {
      this.$emit('onChange', this.id, value);
    },
  },
  watch:{
    inputValue: function(val){
      this.$emit('onChange', this.id, val);
    },
    checkbox: function(val){
      this.$emit('onChange', this.id, val)
    }
  },
  components: {
    NdeDatePicker,
    NdeReportSelect,
  },
};
</script>

<style></style>
