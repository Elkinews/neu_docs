<template>
    <v-dialog v-if="showModal" v-model="showModal" max-width="1200" :persistent="true">
        <v-card class="pa-5">
            <v-card-title class="text-sm-left font-weight-bold d-flex justify-space-between ml-2">
                <h3>Double Entry/Required Fields</h3>
                <v-icon aria-label="Close" size="24px" color="red" @click="hideModal"> mdi-close </v-icon>
            </v-card-title>
            <v-card-text>
                <nde-alert text type="error" :messages="errors" v-if="errors.length"></nde-alert>
                <v-row>
                <v-col cols="12">
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
                        title="Save"
                        class="ml-3"
                        type="submit"
                        :loading="isLoading"
                        @click="save"
                        ></nde-button-primary>
                    </v-col>
                    <v-col cols="12" md="4" offset-md="4" style="text-align: right">
                        <nde-button-cancel @click="hideModal" class="mr-3"></nde-button-cancel>
                    </v-col>
                </v-row>
            </v-card-actions>
        </v-card>
    </v-dialog>
  </template>
  
  <script>
  import { mapState } from 'vuex';
  import NdeFormDoubleEntryConfirm from '../Form/NdeFormDoubleEntryConfirm.vue';
  import NdeButtonPrimary from '../Button/NdeButtonPrimary.vue';
  import NdeButtonCancel from '../Button/NdeButtonCancel.vue';
  import NdeAlert from '../Alert/NdeAlert.vue';
  
  export default {
    name: 'NdeModalEditIndexingDoubleEntryConfirm',
    components: {
      NdeFormDoubleEntryConfirm,
      NdeButtonPrimary,
      NdeButtonCancel,
      NdeAlert,
    },
    data() {
        return {
            showModal: false,
            confirmFormData: [],
            isLoading: false,
            errors: [],
        };
    },
    props: {
        formData: {
            type: Array,
            default: []
        },
    },
    async created() {
      this.showModal = true;
    },
    methods: {
        hideModal() {
            this.$emit('hideModal')
        },
        onChangeConfirm(data) {
            if (this.confirmFormData.length) {
                this.confirmFormData = this.confirmFormData.filter((item) => item.key != data.key);
            }

            if (data.value) {
                this.confirmFormData.push(data);
            }
        },
        async save() {
            if(this.validationForm()) {
                this.$emit('formDataValid');
                this.$emit('hideModal');
            }
        },
        validationForm() {
            this.errors = [];
            let isValid = true;

            if (this.dataDoubleEntry.doubleEntry.length) {
                this.dataDoubleEntry.doubleEntry.forEach((filedName) => {
                    const inputFormData = this.formData.find((o) => o.key == filedName);
                    if (inputFormData?.value) {
                        const inputConfirmData = this.confirmFormData.find((o) => o.key == filedName);
                        if (!inputConfirmData?.value) {
                            this.errors.push('Please re-enter ' + inputFormData.label + ' field to verify entered values are correct');
                            isValid = false;
                        } else if (inputConfirmData?.value !== inputFormData?.value) {
                            this.errors.push(inputFormData.label + ' field is not matched!');
                            isValid = false;
                        }
                    }
                })
            }
            return isValid;
        },
    },
  
    computed: {
      ...mapState(['dataDoubleEntry']),
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
  </style>
