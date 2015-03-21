<template>
  <v-select v-bind="$attrs" class="nde-select" outlined dense @input="onSelect">
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
  methods: {
    onSelect(value) {
      this.$emit('onSelect', value);
    },
  },
};
</script>
