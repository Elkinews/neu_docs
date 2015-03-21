<template>
  <div class="nde-view-record pa-6" transition="slide-y-reverse-transition">
    <v-row class="nde-view-record-acctions">
      <v-col cols="12" sm="12">
        <div class="d-flex justify-space-between align-center">
          <span
            @click="gotoSearchResults"
            class="nde-view-record-goback"
            tabindex="0"
            aria-label="Search Results"
            role="button"
          >
            <v-icon color="primary">mdi-menu-left</v-icon>
            <span>Search Results</span>
          </span>

          <nde-button
            color="primary"
            @click="onClickAddTab"
            :loading="isLoadingAllTabs"
            :disabled="!authRoles.includes('ADDTAB')"
          >
            <v-icon left>mdi-plus</v-icon>Add Tab
          </nde-button>
        </div>
      </v-col>
    </v-row>

    <v-row v-if="isLoading" class="mt-6">
      <v-col class="d-flex justify-center">
        <v-progress-circular :size="50" color="primary" indeterminate></v-progress-circular>
      </v-col>
    </v-row>

    <v-row class="mt-6" v-if="(!recordData || recordData.length == 0) && isLoaded">
      <v-col>
        <div class="text-center text-h4">There is not any Record Data!</div>
      </v-col>
    </v-row>

    <v-row v-for="(item, index) in titles" :key="index">
      <v-col>
        <nde-view-record-item
          :recordItems="recordData[item]"
          :title="item"
          :doc_id="record.doc_id"
          :isFirst="index == 0"
          @onDeletedTab="onDeletedTab"
          @onSuccessEditTab="onSuccessEditTab"
        />
      </v-col>
    </v-row>

    <nde-modal-add-new-tab
      :showModal="isAddTabModal"
      @onClose="isAddTabModal = false"
      :allTabs="allTabs"
      v-if="allTabs"
      :docId="record.doc_id"
      @onSuccessAdded="onSuccessAdded"
      :recordData="recordData"
    ></nde-modal-add-new-tab>
  </div>
</template>

<script>
import axios from 'axios';
import { mapState } from 'vuex';
import NdeViewRecordItem from './NdeViewRecordItem.vue';
import NdeModalAddNewTab from '../Modal/NdeModalAddNewTab.vue';
import NdeButton from '../Button/NdeButton.vue';

export default {
  components: { NdeViewRecordItem, NdeModalAddNewTab, NdeButton },

  data() {
    return {
      isLoading: true,
      isLoaded: false,
      recordData: null,
      titles: [],
      allTabs: null,
      isAddTabModal: false,
      isLoadingAllTabs: false,
      authRoles: [],
    };
  },

  props: {
    record: {
      type: Object,
      required: true,
    },
  },

  methods: {
    gotoSearchResults() {
      this.$emit('onClose');
    },

    onClickAddTab() {
      this.getAllTabs();
      this.isAddTabModal = true;
    },

    onSuccessAdded() {
      this.isAddTabModal = false;
      this.init();
    },

    onSuccessEditTab() {
      this.init();
    },

    async init() {
      const postData = {
        doc_id: this.record.doc_id,
        profile_id: this.currentProgram.id,
      };

      this.isLoading = true;

      this.$store.dispatch('increaseCallCount');

      axios
        .post('getViewRecordOauth', postData)
        .then((response) => {
          this.isLoading = false;
          this.isLoaded = true;
          if (response.data.message == 'success') {
            this.recordData = response.data.data.data;
            this.titles = Object.keys(this.recordData);
            console.log(this.recordData);
          }
        })
        .catch((error) => {
          this.isLoading = false;
          console.log(error.response);
        })
        .finally(() => {
          this.isLoading = false;
        });
    },

    onDeletedTab() {
      this.init();
    },

    async getAllTabs() {
      if (!this.allTabs) {
        this.isLoadingAllTabs = true;
        const tabs = await this.$store.dispatch('callAPI', {
          url: '/getAllTabs',
          method: 'post',
          data: {
            profile_id: this.$store.state.currentProgram.id,
            document_id: this.record.doc_id,
          },
        });
        this.isLoadingAllTabs = false;
        this.allTabs = tabs.data.data.data.tabs;
      }
    },
  },

  computed: {
    ...mapState(['currentProgram']),
  },

  async mounted() {
    this.init();
    this.authRoles = this.userLoginRoles();
  },

  watch: {
    record: function (val) {},
  },
};
</script>

<style scoped lang="scss">
.nde-view-record {
  position: fixed;
  left: 0;
  right: 0;
  top: 65px;
  bottom: 0;
  z-index: 1;
  background: $lightGreyColor;
  overflow: auto;

  &-goback {
    cursor: pointer;
    color: $primaryColor;
  }
}

.btn-add-tab {
  text-transform: unset !important;
  border-radius: 0.5rem;
}

@media screen and (max-width: 37.5rem) {
  .nde-view-record {
    top: 0;
    .nde-view-record-acctions {
      margin-top: 3.5rem;
    }
  }
}
</style>
