<template>
  <v-col cols="12" md="12" class="mb-3" v-if="searchFieldInfo">
    <div class="mb-2">
      <label :for="'double_entry_field_' + fieldName"> Re-enter {{ searchFieldInfo.label }} </label>
    </div>
    <v-text-field
      v-model="inputValue"
      :append-icon="show ? 'mdi-eye' : 'mdi-eye-off'"
      :rules="[rules.required]"
      :type="show ? 'text' : 'password'"
      :name="'double-entry-confirm-field-' + controlName"
      :id="'double-entry-confirm-field-' + controlName"
      @click:append="show = !show"
      solo
      dense
      hide-details
      :aria-label="searchFieldInfo.label"
      :autocomplete="show ? 'off' : 'new-password'"
      @copy.prevent
      @paste.prevent
    ></v-text-field>
  </v-col>
</template>

<script>
import { mapState } from 'vuex';

export default {
  data() {
    return {
      inputValue: '',
      show: false,
      rules: {
        required: (value) => !!value || 'Required.',
      },
      searchFieldInfo: null,
      fieldName: '',
    };
  },
  computed: {
    ...mapState(['dataDoubleEntry', 'currentControls']),
  },

  props: {
    controlName: {
      type: String,
      required: true,
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
    },
  },

  watch: {
    inputValue: function (val) {
      this.$emit('onChange', {
        key: this.fieldName,
        value: val,
        label: this.searchFieldInfo.label,
        type: this.searchFieldInfo.type,
      });
    },
  },
};
</script>

<style>
</style>