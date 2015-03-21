<template>
  <v-alert v-bind="$attrs" v-bind:class="{ 'nde-default-alert': isDefault }">
    <slot v-if="messages.length == 0" />

    <ul v-if="!messages.length == 0">
      <li v-for="(message, index) in messages" :key="index">
        <span>{{ message }}</span>
      </li>
    </ul>
  </v-alert>
</template>

<script>
export default {
  props: {
    messages: {
      type: Array,
      default: () => [],
    },

    isDefault: {
      type: Boolean,
      default: () => false,
    },
  },
};
</script>
