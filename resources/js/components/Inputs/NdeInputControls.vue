<template>
  <v-col cols="12" :md="smSize" v-if="searchFieldInfo" class="nde-dashboard-search-field">
    <label class="mb-2" :for="'inputField_' + fieldName"
      >{{ searchFieldInfo.label }}
      <!-- <span v-if="searchFieldInfo.allows_wildcard" class="allows-wildcard">*</span> -->
    </label>
    <nde-input-multi-text v-if="searchFieldInfo.input_data_type == 'MULTITEXT' && searchFieldInfo.type == 'TEXT'"
      :searchFieldInfo="searchFieldInfo"
      :value="inputValue"
      :fieldName="fieldName"
      @onUpdateValue="updateInputValue"
    ></nde-input-multi-text>
    <v-combobox
      v-else-if="
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
      :id="'inputField_' + fieldName"
      @keyup.enter="submit"
      :clearable="searchFieldInfo.allow_clear"
      @change="comboboxSelected"
      :aria-label="searchFieldInfo.label"
    ></v-combobox>

    <v-text-field
      v-else-if="
        !searchFieldInfo.autocomplete_ind &&
        (searchFieldInfo.type == 'TEXT' || searchFieldInfo.type == 'SEARCHINTRANGE')
      "
      dense
      solo
      hide-details
      v-model="inputValue"
      :id="'inputField_' + fieldName"
      :label="searchFieldInfo.label"
      :clearable="searchFieldInfo.allow_clear"
      :aria-label="searchFieldInfo.label"
      aria-required="true"
    ></v-text-field>

    <nde-date-picker
      v-if="searchFieldInfo.type == 'DATE' || searchFieldInfo.type == 'DATERANGE'"
      :value="inputValue"
      format="YYYY-MM-DD"
      @onInput="handleChangeDate"
    />

    <v-select
      v-if="searchFieldInfo.type == 'DROPDOWN' || searchFieldInfo.type == 'ASSIGNTO'"
      :items="dropDownItems"
      item-text="value"
      item-value="key"
      dense
      solo
      attach
      hide-details
      v-model="inputValue"
      :id="'inputField_' + fieldName"
      :label="searchFieldInfo.label"
      :menu-props="{ offsetY: true }"
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
          v-model="inputValue"
          :id="'inputField_' + fieldName"
          :aria-label="searchFieldInfo.label"
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
          id="inputField_1"
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
          id="inputField_2"
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
          id="inputField_2"
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
          :id="'inputField_' + fieldName"
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
          :id="'inputField_' + fieldName"
          :label="searchFieldInfo.label"
          :menu-props="{ offsetY: true }"
          v-model="multidropdown"
          :clearable="searchFieldInfo.allow_clear"
      :aria-label="searchFieldInfo.label"

        ></v-select>
      </v-col>
    </v-row>
  </v-col>
</template>

<script>
import NdeDatePicker from '../DatePicker/NdeDatePicker.vue';
import NdeInputMultiText from './NdeInputMultiText.vue';
export default {
  name: 'NdeInputControls',
  components: {
    NdeDatePicker,
    NdeInputMultiText,
  },
  data() {
    return {
      searchFieldInfo: null,
      inputValue: '',
      activeFieldsItems: [],
      fieldName: '',
      _searchTimerId: null,
      isDateMenu: false,
      dropDownItems: [],
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
    inputField: {
      type: Object,
      required: true,
      default: null,
    },

    isEditIndexFields: {
      type: Boolean,
      default: false,
    },
    hardSmSize: {
      type: Number,
      default: 0,
    },

    isNotCheckAPI: {
      type: Boolean,
      default: false,
    },
  },
  computed: {
    // ...mapState(['Neusearch']),
    smSize: function () {
      if (this.hardSmSize) return this.hardSmSize;
      if (this.searchFieldInfo.type === 'SPACER') return 12;
      if (this.searchFieldInfo.display_size < 10) return 3;
      if (this.searchFieldInfo.display_size > 10 && this.searchFieldInfo.display_size <= 20)
        return 4;
      if (this.searchFieldInfo.display_size > 20) return 5;

      return 2;
    },
  },
  mounted() {
    this.initValues();
  },
  methods: {
    setInputField(val) {
      this.inputValue = val;
    },
    initValues() {
      const keys = Object.keys(this.inputField);
      this.fieldName = keys[0];
      this.searchFieldInfo = this.inputField[this.fieldName];
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

      if (this.isEditIndexFields && this.inputField['app_date']) {
        this.inputField['app_date'].type = 'DATE';

        this.searchFieldInfo = {
          ...this.searchFieldInfo,
          ...this.inputField['app_date'],
        };
      }
    },
    async getCheckFieldHint(value, fieldName) {
      this.activeFieldsItems = null;
      const resulGetCheckFieldHint = await this.$store.dispatch('callAPI', {
        url: '/getCheckFieldHint',
        method: 'post',
        data: {
          targetProfileId: this.$store.state.targetProgramId || this.$store.state.currentProgram.id,
          prefix: value,
          sourceTable: false,
          fieldName,
          fields: [
            {
              key: fieldName,
              value,
            },
          ],
        },
      });
      if (resulGetCheckFieldHint.message !== 'success') {
        this.$store.commit('setShowProgressAPI', false);
        return this.$swal({
          icon: 'error',
          text: this.getMessageResult(resulGetCheckFieldHint),
        });
      }
      this.activeFieldsItems = resulGetCheckFieldHint?.data?.data?.data?.suggestions?.vals || [];
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
    async comboboxSelected() {
      if (!this.inputValue) {
        return;
      }
      if (this.isNotCheckAPI) {
        return;
      }
      this.$store.commit('setShowProgressAPI', true);
      const resulCheckDatafeedCompletionOauth = await this.$store.dispatch('callAPI', {
        url: '/checkDatafeedCompletionOauth',
        method: 'post',
        data: {
          target_profile: this.$store.state.targetProgramId || this.$store.state.currentProgram.id,
          source_table: false,
          fields: {
            [this.fieldName]: this.inputValue,
          },
        },
      });
      if (resulCheckDatafeedCompletionOauth.message !== 'success') {
        this.$store.commit('setShowProgressAPI', false);
        return this.$swal({
          icon: 'error',
          text: this.getMessageResult(resulCheckDatafeedCompletionOauth),
        });
      }
      this.$store.commit('setShowProgressAPI', false);
      this.$emit('onChange', {
        key: this.fieldName,
        value:this.inputValue,
        label: this.searchFieldInfo.label,
        isDatafeedCompletion: true,
      });
    },
    updateInputValue(value){
      this.inputValue = value;
    }
  },
  watch: {
    inputField: function () {
      this.initValues();
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
        if (!this.isNotCheckAPI) {
          this.getCheckFieldHint(val, this.fieldName);
        }
      }, 50); /* 500ms throttle */
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