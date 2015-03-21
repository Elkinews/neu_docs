<template>
  <div>
    <v-row>
      <v-col cols="12" md="4">
        <label for="af_field_name" class="mb-1">Field Name</label>
        <v-select
          :items="fieldNames"
          item-text="label"
          item-value="field_name"
          dense
          solo
          attach
          hide-details
          v-model="selectedIEV"
          id="af_field_name"
          :menu-props="{ offsetY: true }"
          aria-label="Field Name"
          @blur="setSelectedDOMValue(selectedIEV, selectedIEVDOM)"
          @focus="setSelectedDOMValue(selectedIEV, selectedIEVDOM)"
          @click="setSelectedDOMValue(selectedIEV, selectedIEVDOM)"
        ></v-select>
      </v-col>
      <v-col cols="12" md="4">
        <v-checkbox
          v-model="isIncludeEmptyValue"
          label="Include Empty Value"
          hide-details
        ></v-checkbox>
      </v-col>
    </v-row>

    <v-row>
      <v-col cols="12" md="2">
        <label for="af_field_name_2" class="mb-1">Field Name</label>
        <v-select
          :items="fieldNames"
          item-text="label"
          item-value="field_name"
          dense
          solo
          attach
          hide-details
          v-model="selectedEV"
          id="af_field_name_2"
          :menu-props="{ offsetY: true }"
          aria-label="Field Name"
          @blur="setSelectedDOMValue(selectedEV, selectedEVDOM)"
          @focus="setSelectedDOMValue(selectedEV, selectedEVDOM)"
          @click="setSelectedDOMValue(selectedEV, selectedEVDOM)"
        >
        </v-select>
      </v-col>
      <nde-advanced-search-form-field-value
        v-if="search_field"
        :search_field="search_field"
        @onChange="updatedFormFieldValue"
      />
      <v-col cols="12" md="4" class="d-flex align-center">
        <v-checkbox v-model="isExcludeValue" hide-details>
          <template v-slot:label>
            <label class="v-label theme--light mr-1">Exclude Value</label>
          </template>
        </v-checkbox>
      </v-col>
    </v-row>

    <v-row>
      <v-col cols="12" md="2">
        <label for="af_from_date">Date</label>
        <nde-date-picker
          id="af_from_date"
          role="combobox"
          aria-label="From Date"
          @onInput="handleChangeDateFrom"
          :max="maxAdvancedDate"
        />
      </v-col>

      <v-col cols="12" md="2" class="d-flex align-end">
        <nde-date-picker
          aria-label="To Date"
          role="combobox"
          @onInput="handleChangeDateTo"
          :min="minAdvancedDate"
        />
      </v-col>

      <v-col cols="12" md="4">
        <v-checkbox
          v-model="isRecordCreationDate"
          label="Record Creation Date"
          hide-details
        ></v-checkbox>
      </v-col>
    </v-row>
  </div>
</template>

<script>
import moment from 'moment';
import { mapState } from 'vuex';
import NdeDatePicker from '../DatePicker/NdeDatePicker.vue';
import NdeAdvancedSearchFormFieldValue from './NdeAdvancedSearchFormFieldValue.vue';
import { DEFAULT_DATE_FORMAT } from '@utils/constants';

export default {
  components: { NdeAdvancedSearchFormFieldValue, NdeDatePicker },
  data() {
    return {
      fieldNames: [],
      selectedIEV: null,
      selectedEV: null,
      selectedEVV: null,
      selectedEVDOM: null,
      selectedIEVDOM: null,
      isIncludeEmptyValue: false,
      isExcludeValue: false,
      excludeVal: null,
      isRecordCreationDate: false,
      isRecordCreationDateFrom: false,
      isRecordCreationDateTo: false,
      recordCreationDateFrom: null,
      recordCreationDateTo: null,
      recordCreationDateFromVal: null,
      recordCreationDateToVal: null,
      search_field: null,
      format: DEFAULT_DATE_FORMAT,
    };
  },
  computed: {
    ...mapState(['currentProgram', 'currentControls']),
    minAdvancedDate() {
      const dateValue = this.recordCreationDateFromVal;
      let isValid = !dateValue ? false : moment(dateValue, this.format, true).isValid();
      return isValid ? moment(dateValue, this.format, true).format('YYYY-MM-DD') : '';
    },
    maxAdvancedDate() {
      const dateValue = this.recordCreationDateToVal;
      let isValid = !dateValue ? false : moment(dateValue, this.format, true).isValid();
      return isValid ? moment(dateValue, this.format, true).format('YYYY-MM-DD') : '';
    },
  },

  methods: {
    updatedFormFieldValue(val) {
      this.excludeVal = val;

      if (this.isExcludeValue) {
        this.$store.commit('setNeusearch', {
          key: 'excludeName',
          data: val.key,
        });

        this.$store.commit('setNeusearch', {
          key: 'excludeValue',
          data: val.value,
        });
      } else {
        this.$store.commit('setNeusearch', {
          key: 'excludeName',
          data: '',
        });

        this.$store.commit('setNeusearch', {
          key: 'excludeValue',
          data: '',
        });
      }
    },

    handleChangeDateFrom: function (val) {
      this.recordCreationDateFromVal = val;

      if (this.isRecordCreationDate) {
        this.$store.commit('setNeusearch', {
          key: 'recordFromDate',
          data: val,
        });
      } else {
        this.$store.commit('setNeusearch', {
          key: 'recordFromDate',
          data: '',
        });
      }
    },

    handleChangeDateTo: function (val) {
      this.recordCreationDateToVal = val;
      if (this.isRecordCreationDate) {
        this.$store.commit('setNeusearch', {
          key: 'recordToDate',
          data: val,
        });
      } else {
        this.$store.commit('setNeusearch', {
          key: 'recordToDate',
          data: '',
        });
      }
    },

    async setSelectedDOMValue(val, selectedDOM) {
      if (!val && !selectedDOM) return;
      if (selectedDOM) {
        setTimeout(() => {
          selectedDOM.value = val;
          selectedDOM.style.opacity = '0';
        });
        return;
      }
      await new Promise(r => setTimeout(r, 10));
      return this.setSelectedDOMValue(val, selectedDOM);
    }
  },

  mounted() {
    if (this.currentControls) {
      var val = this.currentControls;
      this.fieldNames = [];
      val.map((field) => {
        const keys = Object.keys(field);
        const fieldName = keys[0];

        if (field[fieldName].search_display && field[fieldName].label) {
          this.fieldNames.push({
            field_name: fieldName,
            label: field[fieldName].label,
            field: field,
          });
        }
      });

      if (!this.selectedIEV && this.fieldNames && this.fieldNames.length) {
        this.selectedIEV = this.fieldNames[0].field_name;
        this.selectedEV = this.fieldNames[0].field_name;
        this.search_field = this.fieldNames[0].field;
      }
    }
    this.selectedEVDOM = document.getElementById("af_field_name_2");
    this.selectedIEVDOM = document.getElementById("af_field_name");
  },

  watch: {
    currentControls: function (val) {
      if (!val) return;
      this.fieldNames = [];
      val.map((field) => {
        const keys = Object.keys(field);
        const fieldName = keys[0];

        if (field[fieldName].search_display && field[fieldName].label) {
          this.fieldNames.push({
            field_name: fieldName,
            label: field[fieldName].label,
            field: field,
          });
        }
      });

      if (!this.selectedIEV && this.fieldNames && this.fieldNames.length) {
        this.selectedIEV = this.fieldNames[0].field_name;
        this.selectedEV = this.fieldNames[0].field_name;
        this.search_field = this.fieldNames[0].field;
      }
    },

    selectedEV: function (val) {
      this.setSelectedDOMValue(val, this.selectedEVDOM);
      if (!val) return;
      this.search_field = null;
      this.search_field = this.fieldNames.filter((o) => o.field_name == this.selectedEV)[0].field;
      this.excludeVal = null;

      if (this.isExcludeValue) {
        this.$store.commit('setNeusearch', {
          key: 'excludeName',
          data: val,
        });

        this.$store.commit('setNeusearch', {
          key: 'excludeValue',
          data: null,
        });
      } else {
        this.$store.commit('setNeusearch', {
          key: 'excludeName',
          data: '',
        });

        this.$store.commit('setNeusearch', {
          key: 'excludeValue',
          data: null,
        });
      }
    },

    isExcludeValue: function (val) {
      if (val) {
        this.$store.commit('setNeusearch', {
          key: 'excludeValue',
          data: this.excludeVal.value,
        });

        this.$store.commit('setNeusearch', {
          key: 'excludeName',
          data: this.excludeVal.key,
        });
      } else {
        this.$store.commit('setNeusearch', {
          key: 'excludeName',
          data: '',
        });

        this.$store.commit('setNeusearch', {
          key: 'excludeValue',
          data: '',
        });
      }
    },

    isIncludeEmptyValue: function (val) {
      if (val) {
        this.$store.commit('setNeusearch', {
          key: 'includeName',
          data: this.selectedIEV,
        });
      } else {
        this.$store.commit('setNeusearch', {
          key: 'includeName',
          data: '',
        });
      }
    },

    selectedIEV: function (val) {
      this.setSelectedDOMValue(val, this.selectedIEVDOM);
      if (!val) return;
      if (this.isIncludeEmptyValue) {
        this.$store.commit('setNeusearch', {
          key: 'includeName',
          data: val,
        });
      } else {
        this.$store.commit('setNeusearch', {
          key: 'includeName',
          data: '',
        });
      }
    },

    isRecordCreationDate: function (val) {
      if (val) {
        this.$store.commit('setNeusearch', {
          key: 'recordFromDate',
          data: this.recordCreationDateFromVal,
        });

        this.$store.commit('setNeusearch', {
          key: 'recordToDate',
          data: this.recordCreationDateToVal,
        });
      } else {
        this.$store.commit('setNeusearch', {
          key: 'recordFromDate',
          data: null,
        });

        this.$store.commit('setNeusearch', {
          key: 'recordToDate',
          data: null,
        });
      }
    },
  },
};
</script>

<style scoped lang="scss">
.allowed-wildcard {
  color: $errorColor;
}
</style>
