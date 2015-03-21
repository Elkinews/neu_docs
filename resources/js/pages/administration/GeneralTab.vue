<template>
  <div>
    <nde-tab-admin />
    <v-card class="mt-5 pa-5">
    <v-card-title>
        <h3 class="font-weight-bold mb-0">General Tab</h3>
      </v-card-title>
  </v-card>
  </div>
</template>

<script>

import NdeDashboardLayout from '../../shared/layouts/dashboard/NdeDashboardLayout.vue';
import NdeTabAdmin from '../../components/Tabs/NdeTabAdministration.vue';

export default{
  layout: NdeDashboardLayout,
  components: {
    NdeTabAdmin
  }
}
</script>

<style scoped>

</style>
