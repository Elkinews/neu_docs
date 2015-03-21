<template>
  <div>
    <v-select multiple v-model="multipleSelected" :items="items" solo>
      <template v-slot:selection="{ index }">
        <span v-if="index == 0">Visible Columns</span>
      </template>
    </v-select>

    <div class="mt-3">
      <v-select solo v-model="pageSize" :items="pageSizes">
        <template v-slot:prepend>
          <div style="white-space: nowrap">Load Size:</div>
        </template>
      </v-select>
    </div>

    <div class="mt-3">
      <v-select solo :items="bulkOptions" @change="clickBulkOption">
        <template v-slot:prepend-inner>
          <div style="white-space: nowrap">Bulk Options</div>
        </template>

        <template v-slot:selection="{}">
          <span v-if="true"></span>
        </template>
      </v-select>
    </div>

    <div class="mt-3">
      <v-select solo :items="exportItems" @change="clickBulkOption">
        <template v-slot:prepend-inner>
          <div style="white-space: nowrap" class="red--text text--darken-3">Export to CSV</div>
        </template>

        <template v-slot:selection="{}">
          <span v-if="true"></span>
        </template>
      </v-select>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      items: ["foo", "bar", "fizz", "buzz", "fizzbuzz", "foobar"],
      multipleSelected: ["foo", "bar", "fizz"],
      pageSizes: [10, 25, 50, 100],
      pageSize: 10,
      bulkOptions: [
        "Show records that have documents",
        "Show records with attachments",
      ],
      exportItems: ['All', 'Page']
    };
  },
  methods: {
    clickBulkOption(e) {
      console.log(e);
      alert(e);
    },
  },
};
</script>
