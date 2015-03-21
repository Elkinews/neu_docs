import NdeAuthLayout from '../resources/js/shared/layouts/NdeAuthLayout.vue';
import NdeDashboardLayout from '../resources/js/shared/layouts/dashboard/NdeDashboardLayout.vue';

export default {
  title: 'Shared/Layout',
  component: NdeAuthLayout,
};

const AuthLayoutTemplate = () => ({
  components: { NdeAuthLayout },
  template:
    '<nde-auth-layout></nde-auth-layout>',
});

export const AuthLayout = AuthLayoutTemplate.bind();

const DashboardTemplate = () => ({
    components: { NdeDashboardLayout },
    template:
      '<nde-dashboard-layout programsJson="{}"></nde-dashboard-layout>',
  });
  
  export const DashboardLayout = DashboardTemplate.bind();
