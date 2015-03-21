<template>
  <nde-button class="scan" outlined color="primary" @click="click"> Scan </nde-button>
</template>

<script>
import NdeButton from './NdeButton.vue';
export default {
  name: 'NdeScanButton',
  components: { NdeButton },
  methods: {
    click() {
      this.$emit('click');
    },
  },
};
</script>

<style lang="scss" scoped>
.v-btn:not(.v-btn--round).v-size--default.scan {
  text-transform: unset;
  min-width: 93px;
}
</style>
