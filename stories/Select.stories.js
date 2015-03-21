
import SelectStory from './SelectStory.vue';

export default {
    title: 'Shared/Select',
};

const SelectStories = () => ({
    components: { SelectStory },
      template:
      '<select-story></select-story>',
  })
  
export const Select_Story = SelectStories.bind({});
  