<template>
	<div>
	  <nde-modal
		:showModal.sync="showModal"
		@update:closeModal="hideModal"
		title="Edit Indexing Fields"
	  >
		<template v-slot:ndeModalSubTitle>
		  <nde-button-primary
			class="mt-2"
			title="Pull From Data Feed"
			@click="pullFromDataFeed"
			:styles="{ width: '11.188rem' }"
		  >
		  </nde-button-primary>
		</template>
		<template v-slot:ndeModalContent>
		  <!-- <nde-form-indexing-fields :item="item"></nde-form-indexing-fields> -->
		  <v-row v-if="currentControls.length">
			<nde-input-controls
			  v-for="(inputField, i) in currentControls"
			  :key="i + 1"
			  :inputField="inputField"
			  @onChange="updatedFormFieldValue"
			  ref="inputField"
			  :isEditIndexFields="isEditIndexFields"
			/>
		  </v-row>
		</template>
		<template v-slot:ndeModalAction>
		  <v-row class="mb-3">
			<v-col cols="12" md="6">
			  <nde-button-primary title="Save Changes" @click="submitValidateForm"> </nde-button-primary>
			</v-col>
			<v-col cols="12" md="6" style="text-align: right">
			  <nde-button-cancel @click="hideModal"> </nde-button-cancel>
			</v-col>
		  </v-row>
		</template>
	  </nde-modal>
	  <nde-modal-data-feed v-if="isModalDataFeed" @hideModalDataFeed="hideModalDataFeed" :isCreateRecord="false" @setInputFields="setInputFields" />
    <nde-modal-edit-indexing-double-entry-confirm 
      v-if="showDoubleEntryConfirm" 
      @hideModal="showDoubleEntryConfirm = false"
      :formData="formData"
      @formDataValid="saveChange"
    />
	</div>
	
  </template>
  
  <script>
import { mapState } from 'vuex';
  import NdeModal from './NdeModal.vue';
  import NdeFormIndexingFields from '../Form/NdeFormIndexingFields.vue';
  import NdeButtonPrimary from '../Button/NdeButtonPrimary.vue';
  import NdeButtonCancel from '../Button/NdeButtonCancel.vue';
  import NdeInputControls from '../Inputs/NdeInputControls.vue';
  import NdeModalDataFeed from './NdeModalDataFeed.vue';
  import NdeModalEditIndexingDoubleEntryConfirm from '../Modal/NdeModalEditIndexingDoubleEntryConfirm.vue';
  export default {
	name: 'NdeModalEditIndexingFields',
	components: {
	  NdeModal,
	  NdeFormIndexingFields,
	  NdeButtonPrimary,
	  NdeButtonCancel,
	  NdeInputControls,
	  NdeModalDataFeed,
	  NdeModalEditIndexingDoubleEntryConfirm,
	},
	data() {
	  return {
		docId: this.$store.state.currentImageId,
		showModal: false,
		currentControls: [],
		formData: [],
		isModalDataFeed: false,
		headers: {},
		isEditIndexFields: true,
		showDoubleEntryConfirm: false,
	  };
	},
	async created() {
	  this.$store.commit('setShowProgressAPI', true);
	  const resulGetControls = await this.$store.dispatch('callAPI', {
		url: '/getControls',
		method: 'post',
		data: {
		  profile_id: this.$store.state.currentProgram.id,
		  is_bulk_indexing: true,
		},
	  });
	  if (resulGetControls.message !== 'success') {
		this.$store.commit('setShowProgressAPI', false);
		return this.$swal({
		  icon: 'error',
		  text: this.getMessageResult(resulGetControls),
		});
	  }
	  const controls = (resulGetControls?.data?.controls || []).filter((dataControl) => {
		let result = false;
		for (const field in dataControl) {
		  if (dataControl[field]?.can_edit) {
			result = true;
			break;
		  }
		}
		return result;
	  });
	  this.showModal = true;
	  this.currentControls = controls;
	  const dataDoc = this.$store.state.dataDoc || {};
	  const formData = [];
	  setTimeout(() => {
		this.currentControls.forEach((dataControl) => {
		  const keys = Object.keys(dataControl);
		  const fieldName = keys[0];
		  const fieldValue = dataDoc[fieldName];
		  if (fieldValue) {
			formData.push({
			  key: fieldName,
			  value: fieldValue,
			  label: dataControl[fieldName].label,
			  type: dataControl[fieldName].type,
			});
		  }
			this.setInputField(fieldName, fieldValue);

		});
		this.formData = formData;
		this.$store.commit('setShowProgressAPI', false);
	  });
	},
	methods: {
	  hideModalDataFeed() {
		this.isModalDataFeed = false;
	  },
	  hideModal() {
		this.$store.commit('closeModalEditIndexingFields');
	  },
	  setInputField(fieldName, fieldValue) {
		const indexFindName = this.getFieldNameIndex(fieldName);
		if (indexFindName !== -1) {
		  this.$refs.inputField[indexFindName].setInputField(fieldValue);
		}
	  },
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
	  updatedFormFieldValue(data) {
		if (this.formData.length) {
		  this.formData = this.formData.filter((item) => item.key != data.key);
		}
		if (data.value) {
		  this.formData.push(data);
		}
		if (data.isDatafeedCompletion) {
		  this.setInputField(data.key, data.value);
		}
	  },
	  async submitValidateForm() {
      this.$store.commit('setShowProgressAPI', true);
      const resultGetDoubleEntryOauth = await this.$store.dispatch('callAPI', {
          url: '/getDoubleEntryOauth',
          method: 'post',
          data: {
          profile_id: this.$store.state.currentProgram.id,
          Searchitems: {
            item: this.formData,
          },
        },
      });
      this.$store.commit('setShowProgressAPI', false);
      const dataDoubleEntry = resultGetDoubleEntryOauth?.data?.data?.data || {};
      if (dataDoubleEntry?.doubleEntry?.length !== 0) {
          this.$store.commit('setDataDoubleEntry', dataDoubleEntry);
          this.showDoubleEntryConfirm = true
      } else {
        this.saveChange();
      }
    },

	  async saveChange() {
      this.$store.commit('setShowProgressAPI', true);
      const dataDoc = this.$store.state.dataDoc || {};
      const resultEditDocIndexingFieldsOauth = await this.$store.dispatch('callAPI', {
        url: '/editDocIndexingFieldsOauth',
        method: 'post',
        data: {
        profile_id: this.$store.state.currentProgram.id,
        document_ids: [this.docId],
        fields: this.formData.map((data) => {
          return {
          ...data,
          fromValue: dataDoc[data.key] || '',
          };
        }),
        },
      });
      this.$store.commit('setShowProgressAPI', false);
      this.$store.dispatch({ type: 'searchImages' });
      if (resultEditDocIndexingFieldsOauth.message !== 'success') {
        return this.$swal({
        icon: 'error',
        text: this.getMessageResult(resultEditDocIndexingFieldsOauth),
        });
      }
      await this.$swal({
        icon: 'success',
        text: this.getMessageResult(resultEditDocIndexingFieldsOauth),
      });
      return this.hideModal();
	  },
	  async pullFromDataFeed() {
		this.$store.commit('setShowProgressAPI', true);
		const fields = {};
		this.formData.forEach((data) => {
		  fields[data.key] = data.value;
		});
		const resulCheckDatafeedCompletionOauth = await this.$store.dispatch('callAPI', {
		  url: '/checkDatafeedCompletionOauth',
		  method: 'post',
		  data: {
			target_profile: this.$store.state.currentProgram.id,
			source_table: true,
			fields,
		  },
		});
		this.$store.commit('setShowProgressAPI', false);
		if (resulCheckDatafeedCompletionOauth.message !== 'success') {
		  return this.$swal({
			icon: 'error',
			text: this.getMessageResult(resulCheckDatafeedCompletionOauth),
		  });
		}
		const foundItems = resulCheckDatafeedCompletionOauth?.data?.data?.existing_values?.vals || [];
		this.headers = resulCheckDatafeedCompletionOauth?.data?.data?.existing_values?.headers || {};
		if (!foundItems.length) {
		  return this.$swal({
			icon: 'info',
			text: `Unable to pull from data feed. ${resulCheckDatafeedCompletionOauth?.data?.data?.existing_values?.message}`,
		  });
		}
		if (foundItems.length > 1) {
		  const columnsDataFeed = [
			{
			  text: '',
			  value: 'actions',
			  class: 'col-actions',
			  sortable: false
			},
		  ];
		  
		  const dataHeaders = resulCheckDatafeedCompletionOauth?.data?.data?.existing_values?.headers || {};
		  for (const headerKey in dataHeaders) {
			columnsDataFeed.push({
			  text: dataHeaders[headerKey],
			  value: headerKey,
			  class: 'col-' + headerKey,
			  sortable: false,
			});
		  }
		  this.$store.commit('setDataFeed', {
			columnsDataFeed,
			itemsDataFeed: foundItems,
		  });
		  this.isModalDataFeed = true;
		  return;
		}
		this.setInputFields(foundItems[0]);
	  },
	  setInputFields(dataItem = {}) {
		for (const header in this.headers) {
		  this.setInputField(header, dataItem[header]);
		}
	  }
	},
	beforeDestroy() {
	  this.$store.dispatch('callAPI', {
		url: '/unLockDocOauth',
		method: 'post',
		data: {
		  profile_id: this.$store.state.currentProgram.id,
		  document_ids: [this.docId],
		},
	  });
	},
  };
  </script>
  