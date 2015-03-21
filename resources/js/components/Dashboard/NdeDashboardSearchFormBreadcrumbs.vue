<template>
  <button
    class="align-end"
    aria-controls="breadcrumbs"
    :aria-expanded="ariaExpanded ? `true` : `false`"
    type="button"
  >
    <span
      v-for="(item, i) in selectedProgramTree.slice().reverse()"
      :key="i"
	  :id="'breadcrumb_dashboard_' + i"
      v-bind:class="{ 'text-h5': i == 0, 'text-subtitle-2': i > 0 }"
      v-html="item.name + (i == selectedProgramTree.length - 1 ? '' : ' / ')"
    ></span>
  </button>
</template>

<script>
import { mapState } from 'vuex';
export default {
  props: {
    ariaExpanded: {
      type: Boolean,
      default: false,
    },
  },

  computed: {
    ...mapState(['selectedProgramTree']),
  },
};
</script>
