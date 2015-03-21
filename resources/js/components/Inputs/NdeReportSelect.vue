<template>
  <v-select
    v-bind="$attrs"
    class="nde-select"
    solo
    dense
    @input="onSelect"
    :items="items"
    v-model="items[0]"
  >
    <template v-slot:prepend>
      <slot name="prepend" />
    </template>

    <template v-slot:append-outer>
      <slot name="append-outer" />
    </template>
  </v-select>
</template>

<script>
export default {
  name: 'NdeSelect',
  props: {
    items: {
      type: Array,
      default: () => [],
    },
  },
  methods: {
    onSelect(value) {
      this.$emit('onSelect', value);
    },
  },
  mounted() {
    this.$emit('onSelect', this.items[0].value);
  },
};
</script>
