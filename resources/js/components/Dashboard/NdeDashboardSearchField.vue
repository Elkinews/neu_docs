<template>
  <v-col
    cols="12"
    :md="smSize"
    v-if="searchFieldInfo && searchFieldInfo.search_display && searchFieldInfo.display"
    class="nde-dashboard-search-field"
    :id="'search_field-container-' + fieldName"
  >
    <label class="mb-2" :for="'search_field_' + fieldName"
      >{{ searchFieldInfo.label }}
      <!-- <span v-if="searchFieldInfo.allows_wildcard" class="allows-wildcard">*</span> -->
    </label>
    <v-combobox
      v-if="
        searchFieldInfo.autocomplete_ind &&
        (searchFieldInfo.type == 'TEXT' || searchFieldInfo.type == 'SEARCHINTRANGE')
      "
      solo
      dense
      attach
      hide-details
      :items="activeFieldsItems"
      v-model="inputValue"
      append-icon=""
      :search-input.sync="inputValue"
      :id="'search_field_' + fieldName"
      @keyup.enter="submit"
      :aria-label="searchFieldInfo.label"
    ></v-combobox>

    <v-text-field
      v-if="
        !searchFieldInfo.autocomplete_ind &&
        (searchFieldInfo.type == 'TEXT' || searchFieldInfo.type == 'SEARCHINTRANGE')
      "
      dense
      solo
      hide-details
      v-model="inputValue"
      :id="'search_field_' + fieldName"
      :aria-label="searchFieldInfo.label"
      aria-required="true"
    ></v-text-field>

    <nde-date-picker
      v-if="searchFieldInfo.type == 'DATE'"
      :value="inputValue"
      format="YYYY-MM-DD"
      @onInput="handleChangeDate"
      :ariaLabel="searchFieldInfo.label"
      role="combobox"
    />

    <nde-date-picker
      v-if="searchFieldInfo.type == 'DATERANGE'"
      range
      :value="dateRange"
      format="YYYY-MM-DD"
      @onInput="handleChangeDateRange"
      :ariaLabel="searchFieldInfo.label"
      role="combobox"
    />

    <v-select
      v-if="searchFieldInfo.type == 'DROPDOWN' || searchFieldInfo.type == 'ASSIGNTO'"
      :items="dropDownItems"
      item-text="name"
      item-value="value"
      dense
      solo
      attach
      hide-details
      v-model="inputValue"
      :id="'search_field_' + fieldName"
      :menu-props="{ offsetY: true }"
      :aria-label="searchFieldInfo.label"
      @blur="setSelectedDOMValue(inputValue, inputValueDOM)"
      @focus="setSelectedDOMValue(inputValue, inputValueDOM)"
      @click="setSelectedDOMValue(inputValue, inputValueDOM)"
    ></v-select>

    <v-row v-if="searchFieldInfo.type == 'INTRANGE'">
      <v-col>
        <v-text-field
          label="From"
          dense
          solo
          hide-details
          v-model="fromInt"
          :id="'search_field_' + fieldName"
          :aria-label="searchFieldInfo.label"
          type="number"
          min="1"
          onkeydown="return event.keyCode !== 69 
              && event.keyCode !== 187 
              && event.keyCode !== 189 
              && event.keyCode !== 190
              && event.keyCode !== 48"
        ></v-text-field>
      </v-col>
      <v-col>
        <v-text-field
          label="To"
          dense
          solo
          hide-details
          v-model="toInt"
          :id="'search_field_' + fieldName + '_1'"
          :aria-label="searchFieldInfo.label"
          type="number"
          min="1"
          onkeydown="return event.keyCode !== 69 
              && event.keyCode !== 187 
              && event.keyCode !== 189 
              && event.keyCode !== 190
              && event.keyCode !== 48"
        ></v-text-field>
      </v-col>
    </v-row>

    <v-row v-if="searchFieldInfo.type == 'SSN'">
      <v-col cols="12" md="3">
        <v-text-field
          label=""
          dense
          solo
          hide-details
          v-model="ssn1"
          id="search_field_1"
          maxlength="3"
          :aria-label="searchFieldInfo.label"
          aria-required="true"
        ></v-text-field>
      </v-col>

      <v-col cols="12" md="1" class="d-flex align-center justify-center">-</v-col>

      <v-col cols="12" md="2">
        <v-text-field
          label=""
          dense
          solo
          hide-details
          v-model="ssn2"
          id="search_field_2"
          maxlength="2"
          :aria-label="searchFieldInfo.label"
          aria-required="true"
        ></v-text-field>
      </v-col>

      <v-col cols="12" md="1" class="d-flex align-center justify-center">-</v-col>

      <v-col cols="12" md="4">
        <v-text-field
          label=""
          dense
          solo
          hide-details
          v-model="ssn3"
          id="search_field_2"
          maxlength="4"
          :aria-label="searchFieldInfo.label"
          aria-required="true"
        ></v-text-field>
      </v-col>
    </v-row>

    <v-row v-if="searchFieldInfo.type == 'TEXTAREA'">
      <v-col>
        <v-textarea
          solo
          name="input-7-4"
          dense
          hide-details
          v-model="inputValue"
          :id="'search_field_' + fieldName"
          :aria-label="searchFieldInfo.label"
        ></v-textarea>
      </v-col>
    </v-row>

    <v-row v-if="searchFieldInfo.type == 'MULTIDROPDOWN'">
      <v-col>
        <v-select
          chips
          multiple
          solo
          dense
          hide-details
          :items="dropDownItems"
          item-text="value"
          item-value="key"
          :id="'search_field_' + fieldName"
          :label="searchFieldInfo.label"
          :menu-props="{ offsetY: true }"
          v-model="multidropdown"
          :aria-label="searchFieldInfo.label"
        ></v-select>
      </v-col>
    </v-row>
  </v-col>
</template>

<script>
import axios from 'axios';
import { mapState } from 'vuex';
import NdeDatePicker from '../DatePicker/NdeDatePicker.vue';
import debounce from "lodash/debounce";

export default {
  components: {
    NdeDatePicker,
  },
  data() {
    return {
      searchFieldInfo: null,
      inputValue: '',
      inputValueDOM: null,
      activeFieldsItems: [],
      fieldName: '',
      _searchTimerId: null,
      isDateMenu: false,
      dropDownItems: [],
      fromInt: null,
      toInt: null,
      dateRange: {
        from: '',
        to: '',
      },
      ssn1: null,
      ssn2: null,
      ssn3: null,
      multidropdown: null,
    };
  },
  props: {
    search_field: {
      type: Object,
      required: true,
      default: null,
    },
    isFullWidth: {
      type: Boolean,
      required: false,
      default: false,
    },
  },
  computed: {
    ...mapState(['Neusearch']),
    smSize: function () {
      if (this.isFullWidth) {
        return 12;
      } else {
        if (this.searchFieldInfo.display_size < 10) return 3;
        if (this.searchFieldInfo.display_size > 10 && this.searchFieldInfo.display_size <= 20)
          return 4;
        if (this.searchFieldInfo.display_size > 20) return 5;

        return 2;
      }
    },
  },
  mounted() {
    this.initValues();
    setTimeout(() => {
      this.inputValueDOM = document.getElementById(`search_field_${this.fieldName}`);
    }, 100);
  },
  methods: {
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
    },
    initValues() {
      const keys = Object.keys(this.search_field);
      this.fieldName = keys[0];
      this.searchFieldInfo = this.search_field[this.fieldName];
      if (
        this.searchFieldInfo.type == 'DROPDOWN' ||
        this.searchFieldInfo.type == 'ASSIGNTO' ||
        this.searchFieldInfo.type == 'MULTIDROPDOWN'
      ) {
        this.searchFieldInfo.map.map((item) => {
          if (this.searchFieldInfo.type == 'DROPDOWN' && item.value == 'none') {
            item.name = 'ALL';
            item.value = '';
          }
          this.dropDownItems.push(item);
        });
      }
      if (this.Neusearch.Searchitems && this.Neusearch.Searchitems.item) {
        this.Neusearch.Searchitems.item.map((item) => {
          if (item.key === this.fieldName) {
            this.inputValue = item.value;
          }
        });
      }
    },
    getPossibleSearchData: debounce(function(value, fieldName) {
      console.log(this.Neusearch.Searchitems);
      this.activeFieldsItems = null;
      const fields = this.Neusearch.Searchitems.item.filter((o) => o.key !== fieldName);
      const postData = {
        profile_id: this.$store.state.currentProgram.id,
        search_key: fieldName,
        search_value: value,
        fields,
      };

      this.$store.dispatch('increaseCallCount');

      axios
        .post('/getPossibleSearchData', postData)
        .then((response) => {
          if (response.data.message == 'success') {
            this.activeFieldsItems = response.data.data.possible_values;
          } else {
            console.log(response);
          }
        })
        .catch((error) => {
          console.log(error.response);
        });
    }, 300),
    handleChangeDateRange(range) {
      this.dateRange = range;
      const inputValue = `${this.dateRange.from || ''}|${this.dateRange.to || ''}`;
      this.inputValue = inputValue === '|' ? '' : inputValue;
    },
    handleChangeDate(date) {
      this.inputValue = date;
    },
    submit() {
      this.$emit('onEnterKey');
    },
    updateSSN() {
      var ssn = '';
      if (!this.ssn1 && !this.ssn2 && !this.ssn3) {
        this.inputValue = '';
        return;
      }
      if (this.ssn1) {
        ssn += this.ssn1;
      } else {
        ssn += '*';
      }
      if (this.ssn2) {
        ssn += '-' + this.ssn2;
      } else {
        ssn += '-*';
      }
      if (this.ssn3) {
        ssn += '-' + this.ssn3;
      } else {
        ssn += '-*';
      }
      this.inputValue = ssn;
    },
  },
  watch: {
    search_field: function () {
      this.initValues();
    },
    fromInt: function (val) {
      if (!val && !this.toInt) {
        this.inputValue = null;
      } else {
        this.inputValue = (val ? val : '') + '|' + (this.toInt ? this.toInt : '');
      }
    },
    toInt: function (val) {
      if (!val && !this.fromInt) {
        this.inputValue = null;
      } else {
        this.inputValue = (this.fromInt ? this.fromInt : '') + '|' + (val ? val : '');
      }
    },
    inputValue: function (val) {
      if (this.searchFieldInfo.type == 'DROPDOWN' || this.searchFieldInfo.type == 'ASSIGNTO') {
        this.setSelectedDOMValue(val ? val : 'All', this.inputValueDOM);
      }
      if (!val) {
        this.activeFieldsItems = null;
      }
      this.$emit('onChange', {
        key: this.fieldName,
        value: val,
        label: this.searchFieldInfo.label,
        type: this.searchFieldInfo.type,
      });
      clearTimeout(this._searchTimerId);
      this._searchTimerId = setTimeout(() => {
        if (!val) {
          return;
        }
        if (!this.searchFieldInfo.autocomplete_ind) {
          return;
        }
        this.getPossibleSearchData(val, this.fieldName);
      }, 500); /* 500ms throttle */
    },
    ssn1: function (val) {
      this.updateSSN();
    },
    ssn2: function (val) {
      this.updateSSN();
    },
    ssn3: function (val) {
      this.updateSSN();
    },
    multidropdown: function (val) {
      if (val) {
        this.inputValue = val.join(',');
      } else {
        this.inputValue = '';
      }
    },
  },
};
</script>

<style scoped lang="scss">
.allows-wildcard {
  color: $errorColor;
}
</style>
