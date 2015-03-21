<template>
  <v-menu
    v-model="isShow"
    :close-on-content-click="false"
    transition="scale-transition"
    offset-y
    max-width="290px"
    min-width="auto"
    attach
  >
    <template v-slot:activator="{ on, attrs }">
      <v-text-field
        v-model="dateValue"
        append-icon="mdi-calendar-text"
        v-bind="attrs"
        v-on="on"
        solo
        dense
        @blur="tmpDate = parseDate(dateValue)"
        :rules="[
          () => isValidDate(dateValue) || 'Please enter a valid date format (e.g. ' + format + ').',
          () =>
            isValidDateMin(dateValue) ||
            'Please enter a date greater than or equal ' + getFormatDate(min),
          () =>
            isValidDateMax(dateValue) ||
            'Please enter a date less than or equal ' + getFormatDate(max),
        ]"
        :aria-label="ariaLabel"
        @click="dateCLicked"
      />
    </template>
    <v-date-picker
      v-bind="$attrs"
      no-title
      v-model="tmpDate"
      :min="min"
      :max="max"
      @input="onInput"
      prev-icon="mdi-chevron-left datePrev"
      next-icon="mdi-chevron-right dateNext"
      next-month-aria-label="next month"
      prev-month-aria-label="previous month"
      :picker-date.sync="pickerDate"
    ></v-date-picker>
    <p
      v-text="alertText"
      style="height: 0px; width: 0px; overflow: hidden"
      role="alert"
      aria-atomic="true"
    ></p>
  </v-menu>
</template>

<script>
import moment from 'moment';
import { DEFAULT_ISO_DATE_FORMAT, DEFAULT_DATE_FORMAT } from '@utils/constants';

export default {
  name: 'NdeDatePickerItem',
  props: {
    min: {
      type: String,
      default: () => '',
    },
    max: {
      type: String,
      default: () => '',
    },
    format: {
      type: String,
      default: () => DEFAULT_DATE_FORMAT,
    },
    value: {
      type: String,
      default: () => '',
    },
    ariaLabel: {
      type: String,
      default: () => '',
    },
  },
  data() {
    return {
      isShow: false,
      tmpDate: '',
      dateValue: this.value,
      alertText: '',
      pickerDate: null,
    };
  },
  methods: {
    handleEnterDate() {
      console.log();
    },
    isValidDateMin(dateValue) {
      if (!this.min) {
        return true;
      }
      return moment(dateValue, this.format, true).isSameOrAfter(this.min, 'day');
    },
    isValidDateMax(dateValue) {
      if (!this.max) {
        return true;
      }
      return moment(dateValue, this.format, true).isSameOrBefore(this.max, 'day');
    },
    getFormatDate(date) {
      return moment(date).format(this.format);
    },
    isValidDate(dateValue) {
      let isValid = !dateValue ? false : moment(dateValue, this.format, true).isValid();
      if (isValid) {
        this.tmpDate = this.parseDate(dateValue);
        this.isShow = false;
      }
      return !dateValue || isValid;
    },
    parseDate(date) {
      if (!date) return null;
      const [month, day, year] = date.split('/');
      if (month && day && year) {
        return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
      }
      return '';
    },
    onInput(date) {
      const _date = moment(date, DEFAULT_ISO_DATE_FORMAT);
      this.dateValue = _date.format(this.format);
      this.isShow = false;
    },
    dateCLicked() {
      setTimeout(() => {
        // Remove Tooltip element to avoid duplicate element
        document.querySelectorAll('.tooltipPrev').forEach((el) => el.remove());
        document.querySelectorAll('.tooltipNext').forEach((el) => el.remove());
        // Create  tooltip for Prev
        Array.from(document.getElementsByClassName('datePrev')).forEach((prev) => {
          var tooltip = document.createElement('div');
          tooltip.className = 'tooltipPrev';
          tooltip.innerHTML = 'Previous';
          prev.appendChild(tooltip);
        });

        // Create  tooltip for Next
        Array.from(document.getElementsByClassName('dateNext')).forEach((prev) => {
          var tooltip = document.createElement('div');
          tooltip.className = 'tooltipNext';
          tooltip.innerHTML = 'Next';
          prev.appendChild(tooltip);
        });
      }, 1000);
    },
  },
  mounted() {
    if (this.dateValue) {
      this.$emit('onInput', this.dateValue);
    }
  },
  watch: {
    value(val) {
      if (val && val != '' && this.isValidDate(val)) {
        this.dateValue = this.value;
      }
    },
    dateValue(val) {
      if (this.isValidDate(val)) {
        this.$emit('onInput', this.dateValue);
      } else {
        this.$emit('onInput', 'error');
        // this.$emit('onError', null);
      }
    },
    pickerDate(val) {
      this.alertText = 'Month change ' + val;
    },
  },
};
</script>

<style lang="scss">
.v-date-picker-table__current {
  &.v-btn--active {
    background-color: #3030ff !important;
  }

  &.accent--text {
    color: #1a5297 !important;
    caret-color: #1a5297 !important;
  }
}

.v-picker__body {
  .v-date-picker-header {
    .accent--text {
      color: #4975c3 !important;
      caret-color: #4975c3 !important;
    }
  }
}

.datePrev .tooltipPrev,
.dateNext .tooltipNext {
  background: #484444c2;
  bottom: 100%;
  color: #fff;
  display: block;
  left: -20px;
  opacity: 0;
  padding: 10px;
  pointer-events: none;
  position: absolute;
  font-size: 12px;
  border-radius: 5px;
  z-index: 10;
  margin-left: 5px;
  -webkit-transform: translateY(0px);
  -moz-transform: translateY(0px);
  -ms-transform: translateY(0px);
  -o-transform: translateY(0px);
  transform: translateY(0px);
  -webkit-transition: all 0.25s ease-out;
  -moz-transition: all 0.25s ease-out;
  -ms-transition: all 0.25s ease-out;
  -o-transition: all 0.25s ease-out;
  transition: all 0.25s ease-out;
  -webkit-box-shadow: 2px 2px 6px rgba(0, 0, 0, 0.28);
  -moz-box-shadow: 2px 2px 6px rgba(0, 0, 0, 0.28);
  -ms-box-shadow: 2px 2px 6px rgba(0, 0, 0, 0.28);
  -o-box-shadow: 2px 2px 6px rgba(0, 0, 0, 0.28);
  box-shadow: 2px 2px 6px rgba(0, 0, 0, 0.28);
}

/* This bridges the gap so you can mouse into the tooltip without it disappearing */
.datePrev .tooltipPrev:before,
.dateNext .tooltipNext:before {
  bottom: -20px;
  content: ' ';
  display: block;
  height: 20px;
  left: 0;
  position: absolute;
  width: 100%;
}

.datePrev:hover .tooltipPrev,
.dateNext:hover .tooltipNext {
  opacity: 1;
  pointer-events: auto;
  -webkit-transform: translateY(0px);
  -moz-transform: translateY(0px);
  -ms-transform: translateY(0px);
  -o-transform: translateY(0px);
  transform: translateY(0px);
}

.v-picker__body {
  padding-top: 20px;
  .v-date-picker-header {
    .accent--text {
      color: #4975c3 !important;
      caret-color: #4975c3 !important;
    }
  }
}
</style>
