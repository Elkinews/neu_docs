<template>
  <!-- <nde-data-table
        class="nde-data-table"
        :headers="columns"
        :items="dataHistories"
        :loading="isDocHistoryLoading"
        @updateSortBy="handleUpdateSortBy"
        @updateSortDesc="handleUpdateSortDesc"
      >
  </nde-data-table> -->
  <v-simple-table>
    <template v-slot:default>
      <thead class="nde-table-previous-search-queries" v-if="!isDocHistoryLoading && docHistoryResult?.total != 0">
        <tr >
            <th v-for="(column, index) in columns" :key="index" class="text-center">{{column.text}}</th>
        </tr>
      </thead>
      <tbody v-if="!isDocHistoryLoading && docHistoryResult?.total != 0">
        <tr v-for="(item, index) in dataHistories" :key="index" >
          <td v-for="(column, index) in columns" :key="index">{{item[column.value]}}</td>
        </tr>
      </tbody>

      <div v-if="!isDocHistoryLoading && docHistoryResult?.total == 0" class="text-center">
        There is no Scan/Upload History activity.
      </div>

      <div class="d-flex justify-center" v-if="isDocHistoryLoading">
        <v-progress-circular indeterminate color="primary"></v-progress-circular>
      </div>
    </template>
  </v-simple-table>
</template>

<script>
import NdeDataTable from '@components/Table/NdeDataTable.vue';

import { mapState } from 'vuex';
export default {
  name: "NdeTableScanUploadHistory",
  components: {
    NdeDataTable,
  },
  props: {
    items: {
      type: Array,
      default: () => [],
    },
  },
  data: () => {
    return {
      columns: [
        { text: 'Activity', value: 'activity' },
        { text: 'When', value: 'activity_date' },
        { text: 'Date', value: 'app_date' },
        { text: 'Fruit', value: 'fruit' },
        { text: 'Animal(s)', value: 'sample_multitext' },
        { text: 'Trade Name', value: 'trade_name' },
        { text: 'License Number', value: 'license_number' },
        { text: 'File #', value: 'file_number' },
        { text: 'City', value: 'city' },
      ]
    };
  },
  computed: {
    ...mapState(['isDocHistoryLoading', 'docHistoryResult']),
    dataHistories() {
      return this.docHistoryResult?.activities || [];
    },
  },
  methods: {
    handleUpdateSortBy(column) {
      if (column) {
        this.$store.commit("setDocHistoryPostData", {key: 'page', data: 1});
        this.$store.commit("setDocHistoryPostData", {key: 'order_by', data: column});
        this.$store.commit("setDocHistoryPostData", {key: 'order', data: 'asc'});
      } else {
        /* Default:
          order_by: 'activity',
          order: 'asc',
        */
        this.$store.commit("setDocHistoryPostData", {key: 'page', data: 1});
        this.$store.commit("setDocHistoryPostData", {key: 'order_by', data: 'activity'});
        this.$store.commit("setDocHistoryPostData", {key: 'order', data: 'asc'});
      }
      this.$store.commit("getDocHistoryOauth");
    },
    handleUpdateSortDesc(column) {
      if (column) {
        this.$store.commit("setDocHistoryPostData", {key: 'order', data: 'desc'});
        this.$store.commit("getDocHistoryOauth");
      }
    }
  },
};
</script>

<style scoped lang="scss">
.nde-table-previous-search-queries {
  background: #ebeced;
  font-size: 0.75rem;
  th {
    color: #343c42 !important;
    text-align: center;
  }
}
tbody tr:nth-of-type(even) {
  background-color: #f7f7f7;
}
th,
td {
  font-family: "Poppins";
  font-style: normal;
  color: #343c42;
  text-align: center;
}

</style>
