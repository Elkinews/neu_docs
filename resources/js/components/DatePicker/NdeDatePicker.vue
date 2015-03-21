<template>
  <div>
    <nde-date-picker-item
      v-if="!range"
      :min="min"
      :max="max"
      :format="format"
      @onInput="onInput"
      :value="range ? dateValue : value"
      :ariaLabel="ariaLabel"
    />
    <v-row v-else>
      <v-col cols="12" md="6" v-if="showDateRange">
        <nde-date-picker-item
          :format="format"
          :value="dateRange.from"
          :max="maxDate"
          @onInput="handleChangeDateFrom"
          :ariaLabel="ariaLabel"
        />
      </v-col>

      <v-col cols="12" md="6" v-if="showDateRange">
        <nde-date-picker-item
          :format="format"
          :value="dateRange.to"
          :min="minDate"
          @onInput="handleChangeDateTo"
          :ariaLabel="ariaLabel"
        />
      </v-col>
    </v-row>
  </div>
</template>

<script>
import moment from 'moment';
import { DEFAULT_DATE_FORMAT } from '@utils/constants';
import NdeDatePickerItem from './NdeDatePickerItem.vue';

export default {
  name: 'NdeDatePicker',
  components: {
    NdeDatePickerItem,
  },
  props: {
    min: {
      type: String,
      default: () => '',
    },
    max: {
      type: String,
      default: () => '',
    },
    range: {
      type: Boolean,
      default: () => false,
    },
    characterRange: {
      type: String,
      default: () => '~',
    },
    format: {
      type: String,
      default: () => DEFAULT_DATE_FORMAT,
    },
    value: {
      type: [String, Object],
      default: () => '',
    },
    ariaLabel: {
      type: String,
      default: () => ""
    }
  },
  data() {
    return {
      isShow: false,
      tmpDate: [],
      dateRange: this.range ? { from: this.value.from || '', to: this.value.to || '' } : {},
      dateValue: !this.range ? this.value : '',
      showDateRange: true,
    };
  },
  computed: {
    minDate() {
      const dateValue = this.dateRange.from;
      let isValid = !dateValue ? false : moment(dateValue, this.format, true).isValid();
      return isValid ? moment(dateValue,  this.format, true).format('YYYY-MM-DD') : this.min;
    },
    maxDate() {
      const dateValue = this.dateRange.to;
      let isValid = !dateValue ? false : moment(dateValue, this.format, true).isValid();
      return isValid ? moment(dateValue,  this.format, true).format('YYYY-MM-DD') : this.max;
    },
    dateDisplay() {
      if (this.range) {
        if (this.dateRange.from || this.dateRange.from) {
          return `${this.dateRange.from} ${this.characterRange} ${this.dateRange.to}`;
        }
        return '';
      } else {
        return this.dateValue;
      }
    },
  },
  methods: {
    parseDate(date) {
      if (!date) return null;

      const [month, day, year] = date.split('/');
      return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
    },
    onInput(date) {
      this.$emit('onInput', date);
    },
    handleChangeDateRange() {
      if (
        this.dateRange.from &&
        this.dateRange.to &&
        moment(this.dateRange.from).isAfter(moment(this.dateRange.to))
      ) {
        this.showDateRange = false;
        let temp = this.dateRange.from;
        this.dateRange.from = this.dateRange.to;
        this.dateRange.to = temp;
      }
      setTimeout(() => {
        this.showDateRange = true;
      }, 50);
      this.$emit('onInput', this.dateRange);
    },
    handleChangeDateFrom(date) {
      this.dateRange.from = date;
      this.handleChangeDateRange();
    },
    handleChangeDateTo(date) {
      this.dateRange.to = date;
      this.handleChangeDateRange();
    },
  },
  mounted() {
    if(this.range) {
      this.dateRange = this.value;
    }
    
  }
};
</script>
