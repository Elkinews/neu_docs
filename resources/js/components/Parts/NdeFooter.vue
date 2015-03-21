<template>
  <v-footer color="#343c42" tabindex="999" role="contentinfo" aria-label="Footer"
    ><span class="white--text"
      >This site has been protected by copyright and trademark laws under US and international law.
      All Rights Reserved &copy; 2000-{{ year }}. Powered by Neubus, Inc.</span
    ></v-footer
  >
</template>

<style scoped lang="scss">
</style>

<script>
export default {
  data() {
    return {
      year: new Date().getFullYear(),
    };
  },
  computed: {},

  methods: {},
  props: {},
  components: {},
};
</script>
