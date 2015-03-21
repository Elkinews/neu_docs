<template>
  <v-col cols="12" md="2" class="nde-dashboard-search-field" v-if="searchFieldInfo">
    <label class="mb-2" for="asfv"
      >Value
      <!-- <span v-if="isRequired" class="allowed-wildcard error--text">*</span> -->
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
      id="asfv"
      role="combobox"
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
      id="asfv"
    ></v-text-field>

    <v-menu
      v-if="searchFieldInfo.type == 'DATE'"
      v-model="isDateMenu"
      :close-on-content-click="false"
      transition="scale-transition"
      max-width="290px"
      min-width="auto"
      attach
    >
      <template v-slot:activator="{ on, attrs }">
        <v-text-field
          v-model="inputValue"
          :label="searchFieldInfo.label"
          persistent-hint
          append-icon="mdi-calendar"
          readonly
          v-bind="attrs"
          v-on="on"
          solo
          dense
          hide-details
          id="asfv"
          :aria-label="searchFieldInfo.label"
        ></v-text-field>
      </template>
      <v-date-picker v-model="dateVal" no-title @input="isDateMenu = false"></v-date-picker>
    </v-menu>

    <v-menu
      v-if="searchFieldInfo.type == 'DATERANGE'"
      v-model="isDateMenu"
      :close-on-content-click="false"
      transition="scale-transition"
      max-width="290px"
      min-width="auto"
      attach
    >
      <template v-slot:activator="{ on, attrs }">
        <v-text-field
          v-model="inputValue"
          :label="searchFieldInfo.label"
          persistent-hint
          append-icon="mdi-calendar"
          readonly
          v-bind="attrs"
          v-on="on"
          solo
          dense
          hide-details
          id="asfv"
          :aria-label="searchFieldInfo.label"
        ></v-text-field>
      </template>
      <v-date-picker v-model="dateVal" no-title range></v-date-picker>
    </v-menu>

    <v-select
      v-if="searchFieldInfo.type == 'DROPDOWN' || this.searchFieldInfo.type == 'ASSIGNTO'"
      :items="dropDownItems"
      item-text="value"
      item-value="key"
      dense
      solo
      attach
      hide-details
      v-model="inputValue"
      id="asfv"
    ></v-select>

    <v-row v-if="searchFieldInfo.type == 'INTRANGE'">
      <v-col>
        <v-text-field
          label="From"
          dense
          solo
          hide-details
          v-model="fromInt"
          id="asfv"
        ></v-text-field>
      </v-col>
      <v-col>
        <v-text-field label="To" dense solo hide-details v-model="toInt" id="asfv"></v-text-field>
      </v-col>
    </v-row>
  </v-col>
</template>

<script>
import axios from 'axios';
import { mapState } from 'vuex';

export default {
  data() {
    return {
      searchFieldInfo: null,
      inputValue: '',
      activeFieldsItems: [],
      fieldName: '',
      _searchTimerId: null,
      isDateMenu: false,
      dateVal: null,
      dropDownItems: [],
      fromInt: null,
      toInt: null,
    };
  },
  props: {
    search_field: {
      type: Object,
      required: true,
      default: null,
    },
  },
  computed: {
    ...mapState(['Neusearch']),
    isRequired() {
      if (this.search_field) {
        const keys = Object.keys(this.search_field);
        const fieldName = keys[0];
        const searchFieldInfo = this.search_field[fieldName];
        return searchFieldInfo.allows_wildcard;
      }
      return false;
    },
  },
  mounted() {
    this.initValues();
  },
  methods: {
    initValues() {
      this.inputValue = '';
      const keys = Object.keys(this.search_field);
      this.fieldName = keys[0];
      this.searchFieldInfo = this.search_field[this.fieldName];

      if (this.searchFieldInfo.type == 'DROPDOWN' || this.searchFieldInfo.type == 'ASSIGNTO') {
        this.searchFieldInfo.map.map((item) => {
          this.dropDownItems.push(item);
        });
      }
    },
    getPossibleSearchData(value, fieldName) {
      this.activeFieldsItems = null;
      const postData = {
        profile_id: this.$store.state.currentProgram.id,
        search_key: fieldName,
        search_value: value,
      };

      this.$store.dispatch('increaseCallCount');

      axios
        .post('getPossibleSearchData', postData)
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
      clearTimeout(this._searchTimerId);
      this._searchTimerId = setTimeout(() => {
        this.$emit('onChange', {
          key: this.fieldName,
          value: val,
          label: this.searchFieldInfo.label,
        });

        if (!val) {
          return;
        }

        if (!this.searchFieldInfo.autocomplete_ind) {
          return;
        }

        this.getPossibleSearchData(val, this.fieldName);
      }, 500); /* 500ms throttle */
    },
    dateVal: function (val) {
      if (this.searchFieldInfo.type == 'DATERANGE') {
        this.inputValue = val.join('|');
        this.inputValue = this.inputValue.replaceAll('-', '/');
        return;
      }
      this.inputValue = val;
      this.inputValue = this.inputValue.replaceAll('-', '/');
    },
  },
};
</script>

<style scoped lang="scss">
.allows-wildcard {
  color: $errorColor;
}
</style>
