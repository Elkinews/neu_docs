<template>
  <nde-button class="upload" outlined color="primary" @click="click"> Upload </nde-button>
</template>

<script>
import NdeButton from './NdeButton.vue';

export default {
  name: 'NdeUploadButton',
  components: { NdeButton },
  methods: {
    click() {
      this.$emit('click');
    },
  },
};
</script>

<style lang="scss" scoped>
.upload {
  text-transform: unset;
}
</style>
