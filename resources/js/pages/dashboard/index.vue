<template>
  <nde-dashboard-tabs> </nde-dashboard-tabs>
</template>

<style scoped lang="scss">
</style>

<script>
import NdeDashboardLayout from '../../shared/layouts/dashboard/NdeDashboardLayout.vue';
import NdeDashboardTabs from '../../components/Dashboard/NdeDashboardTabs.vue';
export default {
  layout: NdeDashboardLayout,
  data() {
    return {};
  },

  computed: {},

  methods: {},

  mounted() {
    window.document.title = 'neuDocs Enterprise Record Search';
  },

  components: {
    NdeDashboardTabs,
  },
};
</script>
