<template>
  <div>
    <nde-modal
      :showModal.sync="showModal"
      @update:closeModal="hideModal"
      title="Transfer Record: Indexing Fields"
    >
      <template v-slot:ndeModalContent>
        <v-row>
          <nde-input-controls
            v-for="(inputField, i) in currentControls"
            :key="i + 1"
            :inputField="inputField"
            @onChange="updatedFormFieldValue"
            ref="inputField"
          />
        </v-row>
      </template>
      <template v-slot:ndeModalAction>
        <v-row>
          <v-col cols="12" md="6">
            <nde-button-primary title="Transfer" @click="clickTransfer"> </nde-button-primary>
          </v-col>
          <v-col cols="12" md="6" class="pr-12" style="text-align: right">
            <nde-button-cancel @click="hideModal"> </nde-button-cancel>
          </v-col>
        </v-row>
      </template>
    </nde-modal>
    <nde-modal-edit-indexing-double-entry-confirm 
      v-if="showDoubleEntryModal" 
      @hideModal="showDoubleEntryModal = false"
      :formData="formData"
      @formDataValid="transferDataAction"
    />
  </div>
</template>

<script>
import { cloneDeep } from 'lodash';
import NdeModal from './NdeModal.vue';
import NdeFormTransferRecordIndexingFields from '../Form/NdeFormTransferRecordIndexingFields.vue';
import NdeButtonPrimary from '../Button/NdeButtonPrimary.vue';
import NdeButtonCancel from '../Button/NdeButtonCancel.vue';
import NdeInputControls from '../Inputs/NdeInputControls.vue';
import NdeModalEditIndexingDoubleEntryConfirm from '../Modal/NdeModalEditIndexingDoubleEntryConfirm.vue';

export default {
  name: 'NdeModalTransferRecordIndexingFields',
  components: {
    NdeModal,
    NdeFormTransferRecordIndexingFields,
    NdeButtonPrimary,
    NdeButtonCancel,
    NdeInputControls,
    NdeModalEditIndexingDoubleEntryConfirm,
  },
  props: {
    showModal: {
      type: Boolean,
      default: true,
    },
  },
  data() {
    return {
      item: {},
      currentControls: [],
      formData: [],
      showDoubleEntryModal: false,
    };
  },
  async created() {
    this.$store.commit('setShowProgressAPI', true);
    const resulGetControls = await this.$store.dispatch('callAPI', {
      url: '/getControls',
      method: 'post',
      data: {
        profile_id: this.$store.state.targetProgramId,
        is_bulk_indexing: true,
      },
    });
    this.$store.commit('setShowProgressAPI', false);
    if (resulGetControls.message !== 'success') {
      return this.$swal({
        icon: 'error',
        text: this.getMessageResult(resulGetControls),
      });
    }
    const controls = (resulGetControls?.data?.controls || []).filter((dataControl) => {
      let result = false;
      for (const field in dataControl) {
        if (dataControl[field]?.can_edit && dataControl[field]?.display) {
          result = true;
          break;
        }
      }
      return result;
    });
    this.currentControls = controls;
  },
  methods: {
    getFieldNameIndex(filedName) {
      let result = false;
      let index = 0;
      for (const inputField of this.currentControls) {
        const keys = Object.keys(inputField);
        if (keys[0] === filedName) {
          result = true;
          break;
        }
        index++;
      }

      return result ? index : -1;
    },
    hideModal() {
      this.$emit('update:showModal', !this.showModal);
      this.$store.commit('closeModalTransferRecordIndexingFields');
    },
    updatedFormFieldValue(data) {
      if (this.formData.length) {
        this.formData = this.formData.filter((item) => item.key != data.key);
      }

      if (data.value) {
        this.formData.push(data);
      }

      if (data.isDatafeedCompletion) {
        const indexFindName = this.getFieldNameIndex(data.key);
        if (indexFindName !== -1) this.$refs.inputField[indexFindName].setInputField(data.value);
      }
    },
    async clickTransfer() {
      this.$store.commit('setShowProgressAPI', true);
      const resultGetDoubleEntryOauth = await this.$store.dispatch('callAPI', {
        url: '/getDoubleEntryOauth',
        method: 'post',
        data: {
          profile_id: this.$store.state.targetProgramId,
          Searchitems: {
            item: this.formData,
          },
        },
      });
      if (resultGetDoubleEntryOauth.message !== 'success') {
        this.$store.commit('setShowProgressAPI', false);
        return this.$swal({
          icon: 'error',
          text: this.getMessageResult(resulGetDoubleEntryOauth),
        });
      }
      this.$store.commit('setShowProgressAPI', false);
      const dataDoubleEntry = resultGetDoubleEntryOauth?.data?.data?.data || {};
      if (
        dataDoubleEntry?.doubleEntry?.length === 0 &&
        dataDoubleEntry?.requiredEntry?.length === 0 &&
        dataDoubleEntry?.groupRequiredEntry?.length === 0
      ) {
        this.transferDataAction();
      } else {
        this.$store.commit('setCurrentControls', this.currentControls);
        this.$store.commit('setDataDoubleEntry', dataDoubleEntry);
        this.$store.commit('setShowProgressAPI', false);
        this.showDoubleEntryModal = true;
      }
    },
    async transferDataAction() {
      this.$store.commit('setShowProgressAPI', true);
        const resultCheckImageFieldsOauth = await this.$store.dispatch('callAPI', {
          url: '/checkImageFieldsOauth',
          method: 'post',
          data: {
            profile_id: this.$store.state.targetProgramId,
            fields: this.formData,
            is_new_content: false,
          },
        });
        if (resultCheckImageFieldsOauth.message !== 'success') {
          this.$store.commit('setShowProgressAPI', false);
          return this.$swal({
            icon: 'error',
            text: this.getMessageResult(resultCheckImageFieldsOauth),
          });
        }
        this.$store.commit('setShowProgressAPI', false);
        let targetDocId = 0;
        const isFoundItem = resultCheckImageFieldsOauth?.data?.data?.data?.found;
        if (isFoundItem) {
          const confirmed = await this.$swal({
            text: 'A record with these fields already exists. Are you sure you want to merge to that record?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'OK',
            cancelButtonText: 'Cancel',
          });
          if (!confirmed.value) {
            return this.$swal({
              icon: 'error',
              text: 'Please change some fields and try again.',
            });
          }
          targetDocId = resultCheckImageFieldsOauth?.data?.data?.data?.ids[0]?.id || 0;
        }
        this.$store.commit('openModalTransferTabs', {
          formData: cloneDeep(this.formData),
          targetDocId,
        });
        this.hideModal();
    }
  },
};
</script>
