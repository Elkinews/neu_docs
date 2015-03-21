
import NdeChipsStory from './NdeChipsStory.vue';

export default {
    title: 'Shared/Chips',
};

const NdeChips = () => ({
    components: { NdeChipsStory },
      template:
      '<nde-chips-story></nde-chips-story>',
  })
  
export const NdeChips_Story = NdeChips.bind({});
  