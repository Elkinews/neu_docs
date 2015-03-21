<template>
  <div class="nde-predefined-tab" role="main">
    <nde-tab-admin />

    <v-card class="mt-5 pa-5">
      <v-card-title>
        <h3 class="font-weight-bold mb-0">Predefined Tab</h3>
      </v-card-title>

      <v-divider></v-divider>

      <v-card-text>
        <v-row>
          <v-col md="3">
            <v-select
              dense
              solo
              attach
              hide-details
              class="nde-select"
              v-model="currentTargetID"
              :items="items"
              label="Programs"
              aria-label="Programs"
            >
            </v-select>
          </v-col>

          <v-col md="3" offset-md="3" class="d-flex justify-end">
            <nde-button outlined color="primary" @click="addNew"
              ><v-icon left>mdi-plus</v-icon>Add Field</nde-button
            >
          </v-col>
        </v-row>

        <v-alert text type="error" v-if="error" class="mt-6 mb-6">
          {{ error }}
        </v-alert>

        <v-form ref="predefinedTabForm">
          <div v-if="predefinedTabsInfo && predefinedTabsInfo.predefinedTabs" class="mt-6">
            <div
              v-for="(item, index) in predefinedTabsInfo.predefinedTabs"
              :key="index"
              class="mb-5"
            >
              <predefined-tab-item
                :predefinedTab="item"
                :boxTypeInfo="predefinedTabsInfo.boxTypeInfo"
                :imageGroupInfo="predefinedTabsInfo.imageGroupInfo"
                :profileId="currentTargetID"
                @onRemove="onRemove"
                @onUpdate="onUpdate"
              />
            </div>
          </div>
        </v-form>

        <v-row v-if="isLoading">
          <v-col class="d-flex justify-center">
            <v-progress-circular :size="50" color="primary" indeterminate></v-progress-circular>
          </v-col>
        </v-row>
      </v-card-text>

      <v-card-actions>
        <v-row>
          <v-col class="pa-5">
            <nde-button outlined color="error" class="mr-6" @click="clear">Clear</nde-button>
            <nde-button outlined color="success" @click="submit" :loading="isSubmitting"
              ><v-icon left>mdi-send</v-icon>Submit</nde-button
            >
          </v-col>
        </v-row>
      </v-card-actions>
    </v-card>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import { v4 as uuidv4 } from 'uuid';
import NdeDashboardLayout from '../../shared/layouts/dashboard/NdeDashboardLayout.vue';
import NdeTabAdmin from '../../components/Tabs/NdeTabAdministration.vue';
import NdeButton from '../../components/Button/NdeButton.vue';
import PredefinedTabItem from './PredefinedTabItem.vue';

export default {
  layout: NdeDashboardLayout,
  components: {
    NdeTabAdmin,
    NdeButton,
    PredefinedTabItem,
  },

  data() {
    return {
      items: [],
      currentTargetID: null,
      isLoading: false,
      isSubmitting: false,
      predefinedTabsInfo: null,
      formData: [],
      error: '',
    };
  },

  computed: {
    ...mapState(['programs']),
  },

  methods: {
    async submit(e) {
      this.error = '';

      if (e) e.preventDefault();

      let emptyData = this.formData.filter(
        (item) => !item.name || !item.image_group || !item.box_type,
      );
      if (emptyData.length) {
        // alert('Please Input All Value!');
        await this.$swal({
          title: 'Error',
          text: 'Please Input All Value!',
          type: 'warning',
          showCancelButton: false,
          confirmButtonText: 'CLOSE',
        });
        return;
      }
      console.log(this.formData);

      let postData = [];
      let postTabs = this.formData.filter((tab) => tab.isnew);
      if (postTabs.length == 0) {
        await this.$swal({
          title: 'Error',
          text: 'There is not new tab to add! Please add new tab!',
          type: 'warning',
          showCancelButton: false,
          confirmButtonText: 'CLOSE',
        });
        return;
      }
      postTabs.map((item) => {
        postData.push({
          name: item.name,
          image_group: item.image_group,
          box_type: item.box_type,
        });
      });

      this.isSubmitting = true;
      this.$store.dispatch('increaseCallCount');
      axios
        .post('/savePredefinedTabsOauth', {
          profile_id: this.currentTargetID,
          tabs: postData,
        })
        .then((response) => {
          console.log(response);
          this.isSubmitting = false;
          this.loadPredefinedTabs();
        })
        .catch((error) => {
          console.log(error.response);
          this.isSubmitting = false;
          this.error = error.response?.data?.data?.data?.message || 'Something is wrong! Please try again!';
        });
    },

    clear() {
      this.error = '';
      this.$refs.predefinedTabForm.reset();
      this.loadPredefinedTabs();
    },

    loadPredefinedTabs() {
      console.log(this.currentTargetID);
      this.isLoading = true;
      this.predefinedTabsInfo = null;
      this.$store.dispatch('increaseCallCount');
      axios
        .post('/getPredefinedTabsOauth', {
          profile_id: this.currentTargetID,
        })
        .then((response) => {
          console.log(response);
          this.isLoading = false;
          this.predefinedTabsInfo = response.data.data.data;
          this.initFormData();
        })
        .catch((error) => {
          console.log(error);
          this.isLoading = false;
        });
    },

    initFormData() {
      this.formData = [];
      this.predefinedTabsInfo.predefinedTabs.map((item) => {
        this.formData.push({
          name: item.name,
          image_group: item.image_group_id,
          box_type: item.box_type,
          id: item.id,
          isnew: false,
        });
      });
    },

    onRemove(id) {
      this.error = '';
      this.formData = this.formData.filter((item) => item.id != id);
      this.predefinedTabsInfo.predefinedTabs = this.predefinedTabsInfo.predefinedTabs.filter(
        (item) => item.id != id,
      );
    },

    onUpdate(data) {
      this.error = '';
      console.log(data);
      this.formData = this.formData.filter((item) => item.id != data.id);
      this.formData = [data, ...this.formData];
    },

    addNew() {
      this.error = '';
      let id = uuidv4();
      this.predefinedTabsInfo.predefinedTabs = [
        {
          id,
          name: '',
          image_group_id: null,
          box_type: '',
          isnew: true,
        },
        ...this.predefinedTabsInfo.predefinedTabs,
      ];

      this.formData = [
        {
          name: '',
          image_group: null,
          box_type: '',
          id,
          isnew: true,
        },
        ...this.formData,
      ];
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
  },

  watch: {
    programs(newVal) {
      this.programListDetail(newVal);
    },
    currentTargetID(val) {
      this.error = '';
      this.loadPredefinedTabs();
    },
  },
};
</script>

<style>
</style>
