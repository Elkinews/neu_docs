<template>
  <nde-modal
    :showModal="isShow"
    title="Promote Record"
    persistent
    :max-width="withModal"
    class="nde-promote-record-modal"
    @update:closeModal="closeModal"
  >
    <template slot="ndeModalContent">
      <div class="nde-promote-record-modal__content">
        <nde-alert text type="error" :messages="errors" v-if="errors.length"></nde-alert>
        <nde-select-program-step
          class="nde-select-program-step"
          v-if="step === PROMOTION_RECORD_STEP.STEP_ONE"
          :programs="items"
          @onSelectProgram="onSelectProgram"
        />
        <nde-input-record-step
          class="nde-input-record-step"
          v-if="step === PROMOTION_RECORD_STEP.STEP_TWO"
          :currentControls="currentControls"
          @onUpdateFormFieldValue="onUpdateFormFieldValue"
        />

        <nde-promte-double-entry
          v-if="step === PROMOTION_RECORD_STEP.STEP_THREE"
          @onError="onErrorDoubleEntry"
          @onSuccess="onSuccessDoubleEntry"
        ></nde-promte-double-entry>

        <nde-promote-tab
          v-if="step === PROMOTION_RECORD_STEP.STEP_FOUR"
          @onChange="onChangePredefinedTab"
          :tabs="predefinedTabs"
        ></nde-promote-tab>
      </div>
    </template>

    <template slot="ndeModalAction">
      <v-row>
        <!-- <v-col cols="12" sm="6">
          <nde-button v-if="step !== PROMOTION_RECORD_STEP.STEP_ONE" @click="onBack">
            Back
          </nde-button>
        </v-col> -->

        <v-col cols="12" class="d-flex justify-end" align="right">
          <nde-button
            v-if="step == PROMOTION_RECORD_STEP.STEP_ONE || step == PROMOTION_RECORD_STEP.STEP_TWO"
            color="primary"
            @click="onNextStep"
            :disabled="getNextButtonStatus"
            :loading="isGoingNext"
          >
            Next
          </nde-button>

          <nde-button
            v-if="step === PROMOTION_RECORD_STEP.STEP_THREE"
            color="primary"
            @click="onCreateRecord"
            :disabled="!isCreateRecordAble"
            :loading="isCreatingRecord"
          >
            Create Record
          </nde-button>

          <nde-button
            v-if="step === PROMOTION_RECORD_STEP.STEP_FOUR"
            color="primary"
            @click="onPromote"
            :disabled="!selectedBoxType"
            :loading="isSubmittingPromote"
            >Promote</nde-button
          >
        </v-col>
      </v-row>
    </template>
  </nde-modal>
</template>

<script>
import { mapState } from 'vuex';
import NdeAlert from '@components/Alert/NdeAlert.vue';
import NdeModal from '@components/Modal/NdeModal.vue';
import NdeButton from '@components/Button/NdeButton.vue';
import NdeSelectTabStep from './step/NdeSelectTab.vue';
import NdeInputRecordStep from './step/NdeInputRecord.vue';
import NdeSelectProgramStep from './step/NdeSelectProgram.vue';
import { PROMOTION_RECORD_STEP } from '@utils/constants';
import NdePromteDoubleEntry from './step/NdePromteDoubleEntry.vue';
import NdePromoteTab from './step/NdePromoteTab.vue';
export default {
  created() {
    this.PROMOTION_RECORD_STEP = PROMOTION_RECORD_STEP;
  },

  data() {
    return {
      step: 1,
      lastStep: PROMOTION_RECORD_STEP.STEP_FOUR,
      withModal: 500,
      items: [],
      currentProgram: null,
      isGoingNext: false,
      currentControls: null,
      formData: [],
      isSubmittingPromote: false,
      isCreateRecordAble: false,
      isCreatingRecord: false,
      doubleEntryFormData: [],
      created_doc_id: null,
      predefinedTabs: null,
      selectedBoxType: null,
      errors: [],
      skipDoubleEntry: false,
    };
  },

  props: {
    isShow: {
      type: Boolean,

      default: () => false,
    },
    promoteItem: {
      type: Object,
      required: true,
    },
  },

  computed: {
    ...mapState(['programs', 'Neusearch']),
    getNextButtonStatus() {
      if (this.step == PROMOTION_RECORD_STEP.STEP_ONE) {
        if (!this.currentProgram) return true;
        return false;
      }

      if (this.step == PROMOTION_RECORD_STEP.STEP_TWO) {
        if (this.formData.length == 0) return true;
        return false;
      }
    },
  },

  methods: {
    closeModal() {
      this.resetModal();
      this.$emit('closeModal');
    },

    async onNextStep() {
      this.errors = [];
      this.isGoingNext = true;
      if (this.step == PROMOTION_RECORD_STEP.STEP_ONE) {
        const resulGetControls = await this.$store.dispatch('callAPI', {
          url: '/getControls',
          method: 'post',
          data: {
            profile_id: this.currentProgram,
            is_bulk_indexing: true,
          },
        });

        this.isGoingNext = false;

        if (resulGetControls.message !== 'success') {
          this.$store.commit('setShowProgressAPI', false);
          return this.$swal({
            icon: 'error',
            text: this.getMessageResult(resulGetControls),
          });
        }
        this.currentControls = resulGetControls?.data?.controls;
        this.step += 1;
        this.calcMaxWidth();
        return;
      }

      if (this.step == PROMOTION_RECORD_STEP.STEP_TWO) {
        const resultGetDoubleEntryOauth = await this.$store.dispatch('callAPI', {
          url: '/getDoubleEntryOauth',
          method: 'post',
          data: {
            profile_id: this.currentProgram,
            Searchitems: {
              item: this.formData,
            },
          },
        });

        this.isGoingNext = false;

        if (resultGetDoubleEntryOauth.message !== 'success') {
          this.$store.commit('setShowProgressAPI', false);
          return this.$swal({
            icon: 'error',
            text: this.getMessageResult(resultGetDoubleEntryOauth),
          });
        }

        const dataDoubleEntry = resultGetDoubleEntryOauth?.data?.data?.data || {};

        if(!dataDoubleEntry?.groupRequiredEntry.length){
          this.skipDoubleEntry = true;
          this.doubleEntryFormData = this.formData;
          await this.onCreateRecord();
          this.calcMaxWidth();
          return;
        }

        this.$store.commit('setDataDoubleEntry', dataDoubleEntry);
        this.step += 1;
        this.calcMaxWidth();
        return;
      }
    },

    async onCreateRecord() {
      // this.closeModal();
      this.errors = [];
      this.isCreatingRecord = true;
      const resultCreateDocumentOauth = await this.$store.dispatch('callAPI', {
        url: '/createDocumentOauth',
        method: 'post',
        data: {
          profileId: this.currentProgram,
          Searchitems: {
            item: [...this.Neusearch.Searchitems.item, ...this.doubleEntryFormData],
          },
        },
      });

      // this.isCreatingRecord = false;
      if (resultCreateDocumentOauth.message === 'success') {
        this.created_doc_id = resultCreateDocumentOauth.data.data.data.doc_id;

        const resultGetPredfinedTabs = await this.$store.dispatch('callAPI', {
          url: '/getPredefinedTabsOauth',
          method: 'post',
          data: {
            profile_id: this.currentProgram,
          },
        });

        if (resultGetPredfinedTabs.message == 'success') {
          this.predefinedTabs = resultGetPredfinedTabs?.data?.data;
          if(this.skipDoubleEntry){
            this.step += 2;
          } else {
            this.step++;
          }
        } else {
        }
      } else {
        if(resultCreateDocumentOauth?.data?.errors?.error_message){
          this.errors.push(resultCreateDocumentOauth?.data?.errors?.error_message || '');
        }

        const otherError = resultCreateDocumentOauth?.data?.errors?.errors
        if(otherError){
          for(const key in otherError){
            this.errors.push(otherError[key])
          }
        }
      }
      this.isCreatingRecord = false;
    },

    async onPromote() {
      this.errors = [];
      this.isSubmittingPromote = true;

      const resultUpload = await this.$store.dispatch('callAPI', {
        url: '/uploadFileMetadataOauth',
        method: 'post',
        data: {
          profileId: this.currentProgram,
          docId: this.created_doc_id,
          boxType: this.selectedBoxType.box_type,
          nuid: this.promoteItem.nuid,
          filePath: this.promoteItem.path,
          file_size: this.promoteItem.file_size,
          page_count: this.promoteItem.page_count
        },
      });

      this.isSubmittingPromote = false;

      if (resultUpload.message == 'success') {
        this.$swal({
          icon: 'success',
          text: 'Successfully promoted download',
        });
        this.closeModal();
      } else {
        this.$swal({
          icon: 'error',
          text: resultUpload?.data?.data?.message,
        });
      }
    },

    resetModal() {
      this.step = PROMOTION_RECORD_STEP.STEP_ONE;
    },

    calcMaxWidth() {
      switch (this.step) {
        case PROMOTION_RECORD_STEP.STEP_ONE:
          this.withModal = 500;
          break;
        case PROMOTION_RECORD_STEP.STEP_TWO:
          this.withModal = 700;
          break;
        case PROMOTION_RECORD_STEP.STEP_THREE:
          this.withModal = 1000;
          break;
        case PROMOTION_RECORD_STEP.STEP_FOUR:
          this.withModal = 500;
          break;
        default:
          return 0;
      }
    },

    programListDetail(programsMenu) {
      programsMenu.forEach((program) => {
        if (program.meta && program.meta.has_key_search) {
          this.items.push({
            value: program.id,
            text: program.name,
          });
        }
        if (program.meta.has_key_search && program.subProfiles.length > 0) {
          this.programListDetail(program.subProfiles);
        }
        if (!program.meta.has_key_search && program.subProfiles.length > 0) {
          this.programListDetail(program.subProfiles);
        }
      });
    },

    onSelectProgram(value) {
      this.currentProgram = value;
    },

    onUpdateFormFieldValue(data) {
      if (this.formData.length) {
        this.formData = this.formData.filter((item) => item.key != data.key);
      }

      if (data.value) {
        this.formData.push(data);
      }

      console.log(this.formData);
      this.$store.commit('setNeusearch', {
        key: 'Searchitems',
        data: { item: this.formData },
      });
    },

    onSuccessDoubleEntry(data) {
      this.isCreateRecordAble = true;
      this.doubleEntryFormData = data;
    },

    onErrorDoubleEntry(data) {
      this.isCreateRecordAble = false;
    },

    onChangePredefinedTab(data) {
      this.selectedBoxType = this.predefinedTabs.predefinedTabs.find(e => e.id === data);
    },
  },

  watch: {
    // programs(newVal) {
    //   if(newVal.length)
    //   this.programListDetail(newVal);
    // },
  },

  mounted() {
    this.programListDetail(this.programs);
  },
  components: {
    NdeAlert,
    NdeModal,
    NdeButton,
    NdeSelectTabStep,
    NdeInputRecordStep,
    NdeSelectProgramStep,
    NdePromteDoubleEntry,
    NdePromoteTab,
  },
};
</script>

<style lang="scss">
</style>
