<template>
  <v-card class="pa-5">
    <v-card-text>
      <nde-alert-double-entry
        v-if="checkGroupRequiredGroups"
        :hasOneRequired="hasOneRequired"
        :hasTwoRequired="hasTwoRequired"
      >
      </nde-alert-double-entry>

      <div v-if="error" v-html="error" class="create-error mt-2 mb-3" role="alert"></div>
      <nde-alert text type="error" :messages="errors" v-if="errors.length"></nde-alert>

      <v-row>
        <v-col
          cols="12"
          :md="dataDoubleEntry.requiredEntry && dataDoubleEntry.requiredEntry.length ? 8 : 12"
        >
          <nde-form-double-entry @onChange="onChange"></nde-form-double-entry>
        </v-col>
        <v-col
          v-if="dataDoubleEntry.requiredDoubleEntry || dataDoubleEntry.doubleEntry"
          cols="12"
          md="4"
        >
          <nde-form-double-entry-confirm
            @onChange="onChangeConfirm"
          ></nde-form-double-entry-confirm>
        </v-col>
      </v-row>
    </v-card-text>
  </v-card>
</template>

<script>
import { mapState } from 'vuex';
import NdeAlert from '@components/Alert/NdeAlert.vue';
import NdeAlertDoubleEntry from '@components/Alert/NdeAlertDoubleEntry.vue';
import NdeFormDoubleEntry from '@components/Form/NdeFormDoubleEntry.vue';
import NdeFormDoubleEntryConfirm from '@components/Form/NdeFormDoubleEntryConfirm.vue';

export default {
  components: {
    NdeAlertDoubleEntry,
    NdeFormDoubleEntry,
    NdeFormDoubleEntryConfirm,
    NdeAlert,
  },

  data() {
    return {
      error: '',
      errors: [],
      formData: [],
      confirmFormData: [],
    };
  },

  computed: {
    ...mapState(['dataDoubleEntry']),
    checkGroupRequiredGroups(){
      if(this.dataDoubleEntry.groupRequiredGroups.length || Object.values(this.dataDoubleEntry.groupRequiredGroups).length){
        return true
      }
      return false
    },
    valueGroupRequiredGroups() {
      return Object.values(this.dataDoubleEntry.groupRequiredGroups) || []
    },
    hasOneRequired(){
      return this.valueGroupRequiredGroups.includes('1');
    },
    hasTwoRequired(){
      return this.valueGroupRequiredGroups.includes('2');
    },
  },

  methods: {
    onChange(data) {
      if (this.formData.length) {
        this.formData = this.formData.filter((item) => item.key != data.key);
      }

      if (data.value) {
        this.formData.push(data);
      }

      console.log(this.formData);
      this.validationForm();
    },

    onChangeConfirm(data) {
      console.log(data);

      if (this.confirmFormData.length) {
        this.confirmFormData = this.confirmFormData.filter((item) => item.key != data.key);
      }

      if (data.value) {
        this.confirmFormData.push(data);
      }

      console.log(this.confirmFormData);
      this.validationForm();
    },

    validationForm() {
      let is_error = false;
      this.errors = [];

      let one_required = !this.hasOneRequired;
      let two_required = !this.hasTwoRequired;

      let isGroupRequired = false;
      for (const hasKey in this.dataDoubleEntry.groupRequiredGroups) {
        isGroupRequired = true;
        const formData_item = this.formData.find((o) => o.key == hasKey);
        if (formData_item?.value) {
          const valueGroupRequired = this.dataDoubleEntry?.groupRequiredGroups[hasKey];
          if (valueGroupRequired == '1') {
            one_required = true;
          }
          if (valueGroupRequired == '2') {
            two_required = true;
          }
        }
      }
      if (isGroupRequired && (!one_required || !two_required)) {
        this.errors.push(
          'Required Fields Error: At least one of the fields in the required group of fields cannot be empty.',
        );
        is_error = true;
      }

      let isFormConfirmValue = false;
      if (this.dataDoubleEntry.requiredDoubleEntry.length) {
        this.dataDoubleEntry.requiredDoubleEntry.forEach((filedName) => {
          const inputFormData = this.formData.find((o) => o.key == filedName);
          if (inputFormData?.value) {
            const inputConfirmData = this.confirmFormData.find((o) => o.key == filedName);
            if (inputConfirmData?.value !== inputFormData?.value) {
              if (!isFormConfirmValue) {
                isFormConfirmValue = true;
                this.errors.push('Please re-enter the double entry confirm fields to verify entered values are correct');
              }
              this.errors.push(inputFormData.label + ' field is not matched!');
              is_error = true;
            }
          }
        })
      }

      if (this.dataDoubleEntry.requiredEntry.length) {
        this.dataDoubleEntry.requiredEntry.map((item) => {
          const filtered_formdata = this.formData.filter((o) => o.key === item);
          if (filtered_formdata.length) {
          } else {
            this.errors.push(item + ' field is required to input!');
            is_error = true;
          }
        });
      }

      if (is_error) {
        this.$emit('onError', this.errors);
      } else {
        this.$emit('onSuccess', this.formData);
      }
    },
  },
};
</script>

<style>
</style>