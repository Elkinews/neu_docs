<template>
  <nde-modal :showModal.sync="showModal" title="Create New Tab" modalWith="645px" @update:closeModal="hideModal"
    :classes="'create-new-tab-modal'">
    <template v-slot:ndeModalContent>
      <v-row>
        <v-col cols="12" sm="12">
          <v-select :items="currentDocMimeTypes" label="Select Box Type" solo attach hide-details
            v-model="currentBoxType" :item-text="(item) => `${item.description}`" item-value="box_type"></v-select>
        </v-col>
        <v-col cols="12" sm="12">
          <v-combobox solo attach hide-details :items="getCurrentTab" v-model="currentTabName" maxlength="256"
            :search-input.sync="search" item-text="name" item-value="tab_id" label="Please select a new tab name"
            @keyup.enter="createNewTab">
            <template v-slot:no-data>
              <v-list-item>
                <v-list-item-content>
                  <v-list-item-title>
                    <p>
                      No results matching "<strong>{{ search }}</strong>".
                    </p>
                    <p>Press <kbd>enter</kbd> to create a new one</p>
                  </v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </template>
          </v-combobox>
        </v-col>
      </v-row>
    </template>

    <template v-slot:ndeModalAction>
      <v-row>
        <v-col>
          <nde-button-primary :class="{ 'mt-3': isMobile }" title="Create" @click="createNewTab" :loading="isAdding"
            :disabled="
            !currentTabName
            || (!currentTabName?.tab_id && currentTabName.trim().length === 0)">
          </nde-button-primary>
        </v-col>
      </v-row>
    </template>
  </nde-modal>
</template>

<script>
import { mapState } from 'vuex';
import NdeModal from './NdeModal.vue';
import NdeButtonPrimary from '../Button/NdeButtonPrimary.vue';
export default {
  components: {
    NdeModal,
    NdeButtonPrimary,
  },

  data() {
    return {
      currentBoxType: 'B',
      currentTabName: '',
      isAdding: false,
      search: null,
    };
  },

  props: {
    showModal: {
      type: Boolean,
      required: true,
    },
    allTabs: {
      type: Object,
      required: true,
    },
    docId: {
      type: String,
      requried: true,
    },
    recordData: {
      type: Object,
    },
  },
  methods: {
    hideModal() {
      this.$emit('onClose');
    },

    getBoxTypeLabel() {
      const filtered = this.currentDocMimeTypes.filter((o) => o.box_type === this.currentBoxType)[0]
        .description;
      if (filtered) {
        return filtered;
      } else {
        return 'Document';
      }
    },

    async createNewTab() {
      
      if (this.recordData) {
        const keys = Object.keys(this.recordData);
        let tabname = this.currentTabName;

        if (typeof this.currentTabName != 'object') {
          tabname = tabname.trim();
        }

        if(!tabname) {
          await this.$swal({
              icon: 'error',
              text: 'Cannot create empty tab name',
            });
            return;
        }

        const filterkey = keys.filter((o) => o == tabname);
        if (filterkey.length) {
          const filtereditem = this.recordData[tabname];
          let flag = false;
          filtereditem.map((item) => {
            if (item.box_type == this.currentBoxType) {
              flag = true;
            }
          });

          if (flag) {
            await this.$swal({
              icon: 'error',
              text: 'This tab is already existing. Please try to add another tab.',
            });
            return;
          }
        }
      }

      this.isAdding = true;
      console.log(typeof this.currentTabName);
      const param = {
        profile_id: this.$store.state.currentProgram.id,
        document_id: this.docId,
        name:
          typeof this.currentTabName == 'object' ? this.currentTabName.name : this.currentTabName,
        box_type: this.currentBoxType,
        predefined: typeof this.currentTabName == 'object' ? 'true' : 'false',
      };

      console.log(param);
      const addtab_res = await this.$store.dispatch('callAPI', {
        url: '/addTabOauth',
        method: 'post',
        data: param,
      });

      console.log(addtab_res);
      this.isAdding = false;
      if (addtab_res.message == 'success') {
        this.$swal({
          icon: 'success',
          text: this.getMessageResult(addtab_res),
        });
        this.$emit('onSuccessAdded');
      } else {
        this.$swal({
          icon: 'error',
          text: addtab_res?.data?.message?.message,
        });
      }
    },
  },

  computed: {
    ...mapState(['currentDocMimeTypes']),
    getCurrentTab() {
      let preTabs = this.allTabs[this.getBoxTypeLabel()]?.predefined;
      let existingtabs = [];
      this.allTabs[this.getBoxTypeLabel()]?.tabs.map(tab => {
        existingtabs.push({
          column_name: null,
          column_value: null,
          image_group_id: tab?.group_id,
          name: tab?.tabname,
          tab_id: tab?.tab_id,
          tags_required: tab?.tags_required,
        })
      });

      return preTabs.concat(existingtabs);
    },
  },

  watch: {
    showModal: function () {
      this.currentTabName = '';
    }
  }
};
</script>

<style lang="scss">
@media screen and (max-width: 48rem) {
  .v-dialog>.v-card>.v-card__text {
    padding: 0 !important
  }
}
</style>
