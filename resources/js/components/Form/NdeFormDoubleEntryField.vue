<template>
  <v-col cols="12" md="6" v-if="searchFieldInfo" class="mb-3">
    <div class="mb-2">
      <label :for="'double_entry_field_' + fieldName">{{ searchFieldInfo.label }} </label>
    </div>

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
      :id="'double_entry_field_' + fieldName"
      @keyup.enter="submit"
      :append-outer-icon="getRequiredCount"
      :clearable="searchFieldInfo.allow_clear"
      :aria-label="searchFieldInfo.label"
      autocomplete="off"
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
      :id="'double_entry_field_' + fieldName"
      :append-outer-icon="getRequiredCount"
      :clearable="searchFieldInfo.allow_clear"
      :aria-label="searchFieldInfo.label"
      autocomplete="off"
    ></v-text-field>

    <!-- Date field -->
    <v-row v-if="searchFieldInfo.type == 'DATE' || searchFieldInfo.type == 'DATERANGE'">
      <v-col cols="11">
        <nde-date-picker
          :value="inputValue"
          @onInput="handleChangeDate"
          :aria-label="searchFieldInfo.label"
          :append-outer-icon="getRequiredCount"
        />
      </v-col>
      <v-col cols="1" class="date-icon">
        <v-icon>{{ getRequiredCount }}</v-icon>
      </v-col>
    </v-row>
    <v-select
      v-if="searchFieldInfo.type == 'DROPDOWN' || searchFieldInfo.type == 'ASSIGNTO'"
      :items="dropDownItems"
      item-text="value"
      item-value="key"
      dense
      solo
      hide-details
      v-model="inputValue"
      :id="'double_entry_field_' + fieldName"
      :menu-props="{ offsetY: true }"
      :append-outer-icon="getRequiredCount"
      :clearable="searchFieldInfo.allow_clear"
      :aria-label="searchFieldInfo.label"
    ></v-select>

    <v-row v-if="searchFieldInfo.type == 'INTRANGE'">
      <v-col>
        <v-text-field
          label="From"
          dense
          solo
          hide-details
          v-model="fromInt"
          :id="'double_entry_field_' + fieldName"
          :clearable="searchFieldInfo.allow_clear"
          aria-label="From"
          
        ></v-text-field>
      </v-col>
      <v-col>
        <v-text-field
          label="To"
          dense
          solo
          hide-details
          v-model="toInt"
          :id="'double_entry_field_' + fieldName + '_1'"
          :append-outer-icon="getRequiredCount"
          :clearable="searchFieldInfo.allow_clear"
          aria-label="To"

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
          id="double_entry_field_1"
          maxlength="3"
          :append-outer-icon="getRequiredCount"
          :clearable="searchFieldInfo.allow_clear"
          :aria-label="searchFieldInfo.label"
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
          id="double_entry_field_2"
          maxlength="2"
          :append-outer-icon="getRequiredCount"
          :clearable="searchFieldInfo.allow_clear"
          :aria-label="searchFieldInfo.label"

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
          id="double_entry_field_3"
          maxlength="4"
          :append-outer-icon="getRequiredCount"
          :clearable="searchFieldInfo.allow_clear"
          :aria-label="searchFieldInfo.label"

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
          :id="'double_entry_field_' + fieldName"
          :append-outer-icon="getRequiredCount"
          :clearable="searchFieldInfo.allow_clear"
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
          :id="'double_entry_field_' + fieldName"
          :label="searchFieldInfo.label"
          :menu-props="{ offsetY: true }"
          v-model="multidropdown"
          :append-outer-icon="getRequiredCount"
          :clearable="searchFieldInfo.allow_clear"
          :aria-label="searchFieldInfo.label"

        ></v-select>
      </v-col>
    </v-row>
  </v-col>
</template>

<script>
import { mapState } from 'vuex';
import NdeDatePicker from '../DatePicker/NdeDatePicker.vue';

export default {
  data() {
    return {
      search_field: null,
      searchFieldInfo: null,
      inputValue: '',
      activeFieldsItems: [],
      fieldName: '',
      _searchTimerId: null,
      isDateMenu: false,
      dropDownItems: [],
      fromInt: null,
      toInt: null,
      ssn1: null,
      ssn2: null,
      ssn3: null,
      multidropdown: null,
    };
  },

  props: {
    controlName: {
      type: String,
      required: true,
    },
  },

  components: {
    NdeDatePicker,
  },

  computed: {
    ...mapState(['dataDoubleEntry', 'currentControls']),

    getRequiredCount() {
      if (this.dataDoubleEntry.requiredEntry?.includes(this.controlName)) {
        return '*';
      }

      if (!this.dataDoubleEntry.groupRequiredGroups[this.controlName]) {
        return '';
      }

      if (this.dataDoubleEntry.groupRequiredGroups[this.controlName] == '1') {
        return 'Δ';
      }

      if (this.dataDoubleEntry.groupRequiredGroups[this.controlName] == '2') {
        return 'ΔΔ';
      }
    },
  },

  mounted() {
    this.initValues();
  },

  methods: {
    initValues() {
      this.currentControls.map((item) => {
        const item_keys = Object.keys(item);
        const fieldName = item_keys[0];
        if (this.controlName == fieldName) {
          this.search_field = item;
        }
      });

      const keys = Object.keys(this.search_field);
      this.fieldName = keys[0];
      this.searchFieldInfo = this.search_field[this.controlName];
      if (
        this.searchFieldInfo.type == 'DROPDOWN' ||
        this.searchFieldInfo.type == 'ASSIGNTO' ||
        this.searchFieldInfo.type == 'MULTIDROPDOWN'
      ) {
        if (Array.isArray(this.searchFieldInfo.map)) {
          this.dropDownItems = this.searchFieldInfo.map.map(item => {
            return {
              key: item?.name,
              value: item?.value,
            };
          })
        } else {
          const d_keys = Object.keys(this.searchFieldInfo.map);
          d_keys.map((key_item) => {
            this.dropDownItems.push({
              key: key_item,
              value: this.searchFieldInfo.map[key_item],
            });
          });
        }
      }
      const items = this.$store.state.Neusearch.Searchitems.item;
      const foundItemFieldName = items.find(item => item.value && item.key === this.controlName);
      if (foundItemFieldName) {
        this.inputValue =  foundItemFieldName.value;
        if(foundItemFieldName.type === 'MULTIDROPDOWN') {
          this.multidropdown = this.inputValue.split(',');
        } else if(foundItemFieldName.type === 'SSN') {
          const ssnArray = this.inputValue.split('-');
          this.ssn1 = ssnArray[0] || null;
          this.ssn2 = ssnArray[1] || null;
          this.ssn3 = ssnArray[2] || null;
        } else if(foundItemFieldName.type === 'INTRANGE') {
          const valueArray = this.inputValue.split('|');
          this.fromInt = valueArray[0] || null;
          this.toInt = valueArray[1] || null;
        } 
      }
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
    controlName: function () {
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
  .date-icon{
    padding: 20px 0 0 0;
    margin-left: -7px;
  }
</style>
