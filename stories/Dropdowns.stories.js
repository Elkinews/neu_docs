
import DropdownsStory from './DropdownsStory.vue';

export default {
    title: 'Shared/Dropdowns',
};

const DropdownsStories = () => ({
    components: { DropdownsStory },
      template:
      '<dropdowns-story></dropdowns-story>',
  })
  
export const Dropdowns_Story = DropdownsStories.bind({});
  