<template>
  <v-dialog v-if="showModal" v-model="showModal" max-width="1200" :persistent="true">
    <v-form @submit="save">
      <v-card class="pa-5">
        <v-card-title class="text-sm-left font-weight-bold d-flex justify-space-between ml-2">
          <h3>Double Entry/Required Fields</h3>
          <v-icon aria-label="Close" size="24px" color="red" @click="hideModal"> mdi-close </v-icon>
        </v-card-title>
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
              :md="checkRequiredDoubleEntry ? 8 : 12"
            >
              <nde-form-double-entry @onChange="onChange"></nde-form-double-entry>
            </v-col>
            <v-col
              v-if="checkRequiredDoubleEntry"
              cols="12"
              md="4"
              class="border-left"
            >
              <nde-form-double-entry-confirm
                @onChange="onChangeConfirm"
              ></nde-form-double-entry-confirm>
            </v-col>
          </v-row>
        </v-card-text>
        <v-card-actions>
          <v-row>
            <v-col cols="12" md="4">
              <nde-button-primary
                title="Create"
                class="ml-3"
                type="submit"
                :loading="isLoading"
              ></nde-button-primary>
            </v-col>
            <v-col cols="12" md="4" offset-md="4" style="text-align: right">
              <nde-button-cancel @click="hideModal" class="mr-3"></nde-button-cancel>
            </v-col>
          </v-row>
        </v-card-actions>
      </v-card>
    </v-form>
  </v-dialog>
</template>

<script>
import { mapState } from 'vuex';
import moment from 'moment';
import NdeAlertDoubleEntry from '../Alert/NdeAlertDoubleEntry.vue';
import NdeFormDoubleEntry from '../Form/NdeFormDoubleEntry.vue';
import NdeFormDoubleEntryConfirm from '../Form/NdeFormDoubleEntryConfirm.vue';
import NdeButtonPrimary from '../Button/NdeButtonPrimary.vue';
import NdeButtonCancel from '../Button/NdeButtonCancel.vue';
import NdeAlert from '../Alert/NdeAlert.vue';

export default {
  name: 'NdeModalDoubleEntry',
  components: {
    NdeAlertDoubleEntry,
    NdeFormDoubleEntry,
    NdeFormDoubleEntryConfirm,
    NdeButtonPrimary,
    NdeButtonCancel,
    NdeAlert,
  },
  data() {
    return {
      showModal: true,
      formData: [],
      isLoading: false,
      isSuccess: false,
      error: '',
      errors: [],

      confirmFormData: [],
    };
  },
  async created() {
    this.showModal = true;
    // Todo: Will update in another task
  },
  methods: {
    hideModal() {
      this.$store.commit('closeModalDoubleEntry');
    },

    onChange(data) {
      if (this.formData.length) {
        this.formData = this.formData.filter((item) => item.key != data.key);
      }

      if (data.value) {
        this.formData.push(data);
      }
    },

    onChangeConfirm(data) {
      if (this.confirmFormData.length) {
        this.confirmFormData = this.confirmFormData.filter((item) => item.key != data.key);
      }

      if (data.value) {
        this.confirmFormData.push(data);
      }
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

      return !is_error;
    },

    async save(e) {
      if (e) e.preventDefault();
      this.error = '';
      this.errors = [];

      if (!this.validationForm()) {
        return;
      }

      this.isLoading = true;
      this.$store.commit('setShowProgressAPI', true);
      let dateRange = null;
      const updateItem = [...this.$store.state.Neusearch.Searchitems.item, ...this.formData].map(item => {
        if (item.type === 'SEARCHINTRANGE') {
          item.value = Number.isInteger(+item.value) ? +item.value :  item.value;
        }
        if (item.type === 'DATERANGE') {
          dateRange = item.value;
          var date = item.value.split('|');
          item.value = moment(date[0]).format('YYYY-MM-DD');
        }

        return item;
      });
      const resultCreateDocumentOauth = await this.$store.dispatch('callAPI', {
        url: '/createDocumentOauth',
        method: 'post',
        data: {
          profileId: this.$store.state.currentProgram.id,
          Searchitems: {
            item: updateItem,
          },
        },
      });
      // Retain original value for DATERANGE
      if (dateRange) {
        updateItem.map(item => {
          if (item.type === 'DATERANGE') {
            item.value = dateRange;
          }
          return item;
        });
      }
      this.$store.commit('setShowProgressAPI', false);
      this.isLoading = false;

      if (resultCreateDocumentOauth.message === 'success') {
        this.isSuccess = true;
        this.$store.commit('closeModalDoubleEntry');
        this.$store.dispatch({ type: 'searchImages' });
      } else {
        this.error = resultCreateDocumentOauth.data.error;
        const errors_tmp = resultCreateDocumentOauth.data.errors?.errors || resultCreateDocumentOauth.data.errors;
        const keys = Object.keys(errors_tmp);
        keys.map((key) => {
          this.errors.push(errors_tmp[key]);
        });
      }
    },
  },

  computed: {
    ...mapState(['dataDoubleEntry']),
    checkGroupRequiredGroups(){
      if(this.dataDoubleEntry.groupRequiredGroups.length || Object.values(this.dataDoubleEntry.groupRequiredGroups).length){
        return true
      }
      return false
    },
    checkRequiredDoubleEntry() {
      return this.dataDoubleEntry.requiredDoubleEntry.length || this.dataDoubleEntry.doubleEntry.length;
    },
    valueGroupRequiredGroups() {
      return Object.values(this.dataDoubleEntry.groupRequiredGroups) || [];
    },
    hasOneRequired(){
      return this.valueGroupRequiredGroups.includes('1');
    },
    hasTwoRequired(){
      return this.valueGroupRequiredGroups.includes('2');
    },
  },
};
</script>

<style scoped lang="scss">
.v-card__title {
  .v-icon:hover {
    &::after {
      @extend %afterIconModalClose;
    }
  }
}
.create-error {
  color: $errorColor;
}
.border-left{
  border-left: 1px solid;
  border-width: thin;
  border-color: rgba(0, 0, 0, 0.3) !important;
}

</style>
