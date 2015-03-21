<template>
  <div>
    <v-dialog v-if="showModal" v-model="showModal" max-width="1200" :persistent="true">
      <v-card class="pa-5" style="min-height: 30rem">
        <v-card-title class="text-sm-left font-weight-bold d-flex justify-space-between ml-2">
          <h3>Bulk</h3>
          <v-icon aria-label="Close" size="24px" color="red" @click="hideModal"> mdi-close </v-icon>
        </v-card-title>
        <v-card-text>
          <v-row class="pa-0 ma-0">
            <v-col cols="12" md="4" class="nde-dashboard-search-field">
              <label class="mb-2">Select Field To Change</label>
              <v-select
                :items="dropDownItems"
                item-text="label"
                item-value="key"
                dense
                solo
                attach
                hide-details
                v-model="selectedField"
                aria-label="Select Field To Change"
              ></v-select>
            </v-col>
          </v-row>
          <v-row class="pa-0 ma-0">
            <nde-input-controls
              v-if="inputField[selectedField]"
              :inputField="inputField"
              :hardSmSize="4"
              :isNotCheckAPI="true"
              @onChange="updatedFormFieldValue"
              ref="inputField"
            />
          </v-row>
          <v-row class="pa-2 ma-0 mb-5">
            <v-col cols="12" align="right" class="pa-0 ma-0">
              <nde-button-cancel
                @click="checkDoubleEntry()"
                title="View Changes"
				:styles="{ width: '10rem' }"
              ></nde-button-cancel>
            </v-col>
          </v-row>
          <v-divider></v-divider>
        </v-card-text>
        <v-card-text v-show="dataBulk.length">
          <v-row class="pa-0 ma-0 mb-3">
            <v-col cols="12" align="right" class="pa-0 ma-0 mt-2">
              <nde-button-cancel @click="clickEditFields()" title="Edit Fields" :styles="{ width: '10rem' }"></nde-button-cancel>
            </v-col>
          </v-row>
          <v-divider class="mb-5"></v-divider>
          <v-row class="pa-0 ma-0">
            <v-col cols="12" class="pa-0 ma-0">
              <nde-alert text class="error--text" :messages="messages"></nde-alert>
            </v-col>
          </v-row>
          <v-row class="pa-0 ma-0">
            <v-col cols="12" class="pa-0 ma-0">
              <nde-data-table
                :headers="headers"
                :items="dataBulk"
                class="nde-table-search-result"
              >
              </nde-data-table>
            </v-col>
          </v-row>
        </v-card-text>
        <v-card-actions>
          <nde-button-cancel
            @click="hideModal"
            title="Close"
            :styles="{ width: '10rem' }"
          ></nde-button-cancel>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <nde-modal-bulk-edit-double-entry
      v-if="showModalBulkEditDobuleEntry"
      :fields="dataSelectedField"
      :valueFields="{ [selectedField]: valueField }"
      @hideModal="hideModalBulkEditDobuleEntry"
      @afterCheckEditDobuleEntry="checkEditBulkFieldsOauth"
    />
  </div>
</template>

<script>
import NdeButtonCancel from '../Button/NdeButtonCancel.vue';
import NdeInputControls from '../Inputs/NdeInputControls.vue';
import NdeModalBulkEditDoubleEntry from './NdeModalBulkEditDoubleEntry.vue';
import NdeAlert from '../Alert/NdeAlert.vue';
import NdeDataTable from '@components/Table/NdeDataTable.vue';

export default {
  name: 'NdeModalBulkEditDocFields',
  components: {
    NdeButtonCancel,
    NdeInputControls,
    NdeModalBulkEditDoubleEntry,
    NdeAlert,
    NdeDataTable,
  },
  props: {
    items: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      showModal: false,
      currentControls: [],
      dropDownItems: [],
      selectedField: '',
      valueField: '',
      showModalBulkEditDobuleEntry: false,
      newValue: '',
      messages: [
        'This will not edit indexing fields if the document or any of its tabs are processing.',
      ],
      headers: [
        {
          text: 'Old Value',
          value: 'oldvalue',
          class: 'col-oldvalue',
          sortable: false,
          width: '50%',
        },
        {
          text: 'New Value',
          value: 'newvalue',
          class: 'col-newvalue',
          sortable: false,
          width: '50%',
        },
      ],
      dataBulk: [],
    };
  },
  computed: {
    dataSelectedField() {
      return this.dropDownItems.filter((item) => item.key === this.selectedField) || [];
    },
    inputField() {
      this.dataBulk = [];
      this.newValue = '';
      this.$refs?.inputField?.setInputField('');

      if (!this.selectedField) return {};
      const findInputField = this.currentControls.find(
        (dataControl) => dataControl[this.selectedField],
      );
      return findInputField || {};
    },
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
    this.$store.commit('setShowProgressAPI', false);
    if (resulGetControls.message !== 'success') {
      return this.$swal({
        icon: 'error',
        text: this.getMessageResult(resulGetControls),
      });
    }
    const dropDownItems = [];
    const controls = (resulGetControls?.data?.controls || []).filter((dataControl) => {
      let result = false;
      for (const field in dataControl) {
        if (dataControl[field]?.can_edit && dataControl[field]?.display) {
          dropDownItems.push({
            label: dataControl[field]?.label,
            key: field,
          });
          result = true;
          break;
        }
      }
      return result;
    });
    this.currentControls = controls;

    this.dropDownItems = dropDownItems;
    this.selectedField = dropDownItems[0]?.key || '';
    this.showModal = true;
  },
  methods: {
    sleep(ms) {
      return new Promise(resolve => setTimeout(resolve, ms));
    },
    hideModalBulkEditDobuleEntry() {
      this.showModalBulkEditDobuleEntry = false;
    },
    hideModal() {
      this.$emit('hideModal');
    },
    updatedFormFieldValue(data) {
      this.valueField = data.value;
    },
    async checkDoubleEntry() {
      this.$store.commit('setShowProgressAPI', true);
      this.dataBulk = [];
      await this.sleep(100);
      if (!this.valueField) {
        return this.checkEditBulkFieldsOauth();
      }
      const resultGetDoubleEntryOauth = await this.$store.dispatch('callAPI', {
        url: '/getDoubleEntryOauth',
        method: 'post',
        data: {
          profile_id: this.$store.state.currentProgram.id,
          Searchitems: {
            item: [
              {
                key: this.selectedField,
                value: this.valueField,
              },
            ],
          },
        },
      });
      this.$store.commit('setShowProgressAPI', false);
      if (resultGetDoubleEntryOauth.message !== 'success') {
        return this.$swal({
          icon: 'error',
          text: this.getMessageResult(resultGetDoubleEntryOauth),
        });
      }
      const doubleEntry = resultGetDoubleEntryOauth?.data?.data?.data?.doubleEntry || [];
      if (doubleEntry.length && doubleEntry.includes(this.selectedField)) {
        this.showModalBulkEditDobuleEntry = true;
        return;
      }
      return this.checkEditBulkFieldsOauth();
    },
    async checkEditBulkFieldsOauth() {
      this.hideModalBulkEditDobuleEntry();
      this.$store.commit('setShowProgressAPI', true);
      const resultCheckEditBulkFieldsOauth = await this.$store.dispatch('callAPI', {
        url: '/checkEditBulkFieldsOauth',
        method: 'post',
        data: {
          profile_id: this.$store.state.currentProgram.id,
          document_ids: this.items.map((item) => item.doc_id),
          fieldKey: this.selectedField,
          fieldValue: this.valueField || '',
        },
      });
      this.$store.commit('setShowProgressAPI', false);
      if (resultCheckEditBulkFieldsOauth.message !== 'success') {
        return this.$swal({
          icon: 'error',
          text: this.getMessageResult(resultCheckEditBulkFieldsOauth),
        });
      }
      this.newValue = this.valueField;
      const dataBulk = [];
      const results = resultCheckEditBulkFieldsOauth?.data?.data?.data?.results || [];
      for (let result in results) {
        dataBulk.push(results[result]);
      }
      this.dataBulk = dataBulk;
    },
    async clickEditFields() {
      this.$store.commit('setShowProgressAPI', true);
      const resultBulkEditDocFields = await this.$store.dispatch('callAPI', {
        url: '/bulkEditDocFields',
        method: 'post',
        data: {
          profile_id: this.$store.state.currentProgram.id,
          document_ids: this.items.map((item) => item.doc_id),
          fields: [
            {
              field_key: this.selectedField,
              field_value: this.newValue || '',
            }
          ]
        },
      });
      this.$store.commit('setShowProgressAPI', false);
      if (resultBulkEditDocFields.message !== 'success') {
        return this.$swal({
          icon: 'error',
          text: this.getMessageResult(resultBulkEditDocFields),
        });
      }
      await this.$swal({
				icon: 'success',
				text: this.getMessageResult(resultBulkEditDocFields),
			});
			this.$store.dispatch({ type: 'searchImages' });
			this.hideModal();
    }
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
.v-card__actions {
  justify-content: end;
  button {
	margin-right: 1rem;
  }
}

@media screen and (max-width: 48rem) {
	.v-card__actions {

		button {
			margin-right: -0.5rem;
		}
	}
}

.v-chip {
  &.red {
    background: #f9e1e1 !important;
    border: 1px solid #c32c39 !important;
    box-sizing: border-box;
    border-radius: 4px;
  }
  strong {
    text-transform: capitalize;
    color: #c32c39;
    font-size: 0.75rem;
    line-height: 1rem;
  }
}

:v-deep .nde-table-search-result {
  thead {
    background: $primaryGreyColor !important;
  }

  tbody tr:nth-of-type(even) {
    background-color: $lightGreyColor !important;
  }
  tbody td,
  thead th {
    border-bottom: 0px !important;
  }

  tbody {
    vertical-align: top;
    td {
      font-size: 0.813rem;
      line-height: 1.5rem;
    }
  }
}
</style>
