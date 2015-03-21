
import NdeAlertsStory from './NdeAlertsStory.vue';

export default {
    title: 'Shared/Alerts',
};

const NdeAlerts = () => ({
    
    components: { NdeAlertsStory },
      template:
      '<nde-alerts-story></nde-alerts-story>',
  })
  
export const NdeAlerts_Story = NdeAlerts.bind({});
  