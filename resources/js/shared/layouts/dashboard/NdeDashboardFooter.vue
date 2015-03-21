<template>
  <v-footer class="nde-dashboard-footer" color="#343c42" tabindex="99999">
    <div class="white--text mr-2 ml-2" v-if="isHelpUrl" role="contentinfo" aria-label="Footer">
      <span
        >Trouble using NeuDocs Enterprise? <a href="#">Contact Neubus</a> to submit a ticket.</span
      >
    </div>
    <div role="contentinfo" aria-label="Footer">
      <span class="white--text mr-2 ml-2"
        >This site has been protected by copyright and trademark laws under US and international
        law. All Rights Reserved &copy; 2000-{{ year }}. Powered by Neubus, Inc.</span
      >
    </div>
  </v-footer>
</template>

<script>
export default {
  data() {
    return {
      year: new Date().getFullYear(),
      isHelpUrl: false,
    };
  },
  computed: {},

  methods: {},
  props: {},
  components: {},

  created() {
    const url = window.location.href;
    if (url.includes('/help-center')) {
      this.isHelpUrl = true;
    } else {
      this.isHelpUrl = false;
    }
  },
};
</script>

<style scoped lang="scss">
.nde-dashboard-footer {
  // position: fixed;
  bottom: 0;
  width: 100%;
  left: 0;
  padding: 9px 0;
  display: flex;
  justify-content: center;
  flex-direction: column;
}
</style>