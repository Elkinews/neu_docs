<template>
  <nde-table>
    <template v-slot:ndeTableContent>
      <thead>
        <tr>
          <th>Username</th>
          <th>Full Name</th>
          <th>Program</th>
          <th>Activity</th>
          <th>Date Record Added</th>
          <th>Date Record Last Deleted</th>
          <th>Deletion Information</th>
          <th>Records Information</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in items" :key="item.id">
          <td>{{ item.userName }}</td>
          <td>{{ item.fullName }}</td>
          <td>{{ item.program }}</td>
          <td>{{ item.activity }}</td>
          <td>{{ item.dateRecordAdded }}</td>
          <td>{{ item.dateRecordLastModified }}</td>
          <td style="max-width: 10rem; word-break: break-all">
            {{ item.deletionInformation }}
          </td>
          <td>{{ item.recordsInformation }}</td>
        </tr>
      </tbody>
    </template>
  </nde-table>
</template>

<script>
import NdeTable from "./NdeTable.vue";

export default {
  name: "NdeTableReportDeletion",
  components: {
    NdeTable,
  },
  props: {
    items: {
      type: Array,
      default: () => [],
    },
  },
};
</script>

<style scoped lang="scss"></style>
