<template>
  <div class="nde-hid-show-col" role="main">
    <nde-tab-admin />
    <v-card class="mt-5 pa-5">
      <v-card-title>
        <h3 class="font-weight-bold mb-0">Hide Show Column</h3>
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
              class="nde-select mt-5"
              v-model="currentTargetID"
              :items="items"
              @change="handleChange"
              outlined
              label="Programs"
              aria-label="Programs"
            >
            </v-select>
          </v-col>
        </v-row>
        <p v-if="columns.length" class="mt-3 mb-0">
          Check which columns should appear on the Upload screen
        </p>
        <v-checkbox
          class="mt-0"
          hide-details
          v-for="(column, index) in columns"
          :key="index"
          v-model="selected"
          :value="column"
          :label="column.name"
          @change="handleHideShow(column)"
        >
        </v-checkbox>

        <nde-button-primary
          class="mt-5"
          title="Save"
          @click="handleSubmit"
          :loading="!isSearchLoaded"
        ></nde-button-primary>
      </v-card-text>
    </v-card>
  </div>
</template>

<script>
import NdeTabAdmin from '../../components/Tabs/NdeTabAdministration.vue';
import NdeDashboardLayout from '../../shared/layouts/dashboard/NdeDashboardLayout.vue';
import NdeButtonPrimary from '../../components/Button/NdeButtonPrimary.vue';
import { mapState } from 'vuex';

export default {
  layout: NdeDashboardLayout,
  components: {
    NdeTabAdmin,
    NdeButtonPrimary,
  },

  computed: {
    ...mapState(['programs', 'currentProgram', 'hideShowColsData', 'isSearchLoaded']),
  },

  data() {
    return {
      items: [],
      currentTargetID: {},
      columns: [],
      selected: null,
    };
  },

  methods: {
    handleChange(data) {
      this.currentTargetID = {};
      this.currentTargetID.value = data;

      let payload = {
        targetProfile: parseFloat(this.currentTargetID.value),
      };

      this.getHideShowColumnData(payload);
    },

    handleSubmit() {
      let hideShowColsData = [];
      this.columns.forEach((column) => {
        hideShowColsData.push({
          name: column.name,
          isShowed: column.ishidden === 't' ? 'off' : 'on',
        });
      });

      if (!this.currentTargetID.value || hideShowColsData.length === 0) return;

      let payload = {
        profile_id: parseFloat(this.currentTargetID.value),
        columns: hideShowColsData,
      };

      this.$store.dispatch('saveHideShowColumnData', payload);
    },

    handleHideShow(data) {
      this.columns.forEach((column) => {
        if (column.name === data.name) {
          data.ishidden === 't' ? (data.ishidden = 'f') : (data.ishidden = 't');
        }
      });
    },

    getHideShowColumnData(payload) {
      this.$store.dispatch('getHideShowColumnData', payload).then((response) => {
        if (response && response.hideShowCols && response.hideShowCols.length) {
          this.columns = response.hideShowCols;
          this.selected = this.columns.filter((e) => e.ishidden === 'f');
        }
      });
    },
  },

  watch: {
    programs(newVal) {
      if (newVal && newVal.length)
        newVal.forEach((program) => {
          if (program.meta && program.meta.has_key_search) {
            this.items.push({ text: program.name, value: program.id });
          }
        });
    },
  },
};
</script>

<style lang="scss">
.nde-hid-show-col {
  p {
    font-weight: bold;
  }

  .v-btn__loader {
    color: white;
  }
}
</style>
