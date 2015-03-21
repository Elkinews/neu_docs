<template>
  <v-simple-table>
    <template v-slot:default>
      <thead class="nde-table-import-header">
        <tr>
          <th></th>
          <th>Name</th>
          <th>Queued At</th>
          <th>Status</th>
          <th>Program</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in items" :key="item.id">
          <td>
            <v-checkbox v-model="item.checked" v-if="item.nuid"></v-checkbox>
          </td>
          <td>{{ item.name }}</td>
          <td>{{ item.queuedAt }}</td>
          <td>
            <v-chip :color="getStatusColor(item.status)" label text-color="white">
              <strong>{{ item.status }}</strong>
            </v-chip>
          </td>
          <td>{{ item.program }}</td>
        </tr>
      </tbody>
    </template>
  </v-simple-table>
</template>

<script>
export default {
  name: 'NdeTableImport',
  props: {
    items: {
      type: Array,
      default: () => [],
    },
  },
  methods: {
    getStatusColor(status) {
      if (status === 'done') return 'green';
      if (status === 'working') return 'orange';
      return 'blue';
    },
  },
};
</script>

<style scoped lang="scss">
.nde-table-import-header {
  background: #ebeced;
  th {
    color: #343c42 !important;
  }
}
tbody tr:nth-of-type(odd) {
  background-color: #f7f7f7;
}
th,
td {
  font-family: 'Poppins';
  font-style: normal;
  color: #343c42;
}
.v-chip {
  &.orange {
    background-color: #c3992c !important;
    border-color: #c3992c !important;
  }
  &.green {
    background-color: #547d36 !important;
    border-color: #547d36 !important;
  }
  &.blue {
    background-color: $primaryColor !important;
    border-color: $primaryColor !important;
  }
  strong {
    text-transform: capitalize;
  }
}
</style>
