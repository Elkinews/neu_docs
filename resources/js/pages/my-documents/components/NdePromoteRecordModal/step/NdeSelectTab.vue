<template>
  <div class="nde-select-tab-step">
    <div class="nde-select-tab-step__sub_title">
      <h6 class="font-weight-black">Select Tab</h6>
    </div>

    <label>Record does not exits, will be created</label>

    <nde-select class="nde-select-tab-step__selectbox" :items="tabs" hide-details />
  </div>
</template>

<script>
import NdeSelect from '@components/Inputs/NdeSelect.vue';

export default {
  data() {
    return {};
  },

  props: {
    tabs: {
      type: Array,

      default: () => [],
    },
  },

  methods: {},

  components: {
    NdeSelect,
  },
};
</script>

<style lang="scss">
.nde-select-tab-step {
  .de-select-tab-step__sub_title {
    margin-bottom: 40px;
  }
}
</style>
