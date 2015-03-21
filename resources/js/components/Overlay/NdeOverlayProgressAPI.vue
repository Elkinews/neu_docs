<template>
  <v-overlay z-index="99999" :value="isShowProgressAPI">
    <v-progress-circular style="z-index: 99999" indeterminate size="64"></v-progress-circular>
  </v-overlay>
</template>

<script>
import { mapState } from 'vuex';
export default {
  name: 'NdeOverlayProgressApi',
  computed: {
    ...mapState(['isShowProgressAPI']),
  },
};
</script>
