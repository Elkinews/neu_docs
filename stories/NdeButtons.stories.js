import NdeButtonImport from '../resources/js/components/Button/NdeButtonImport.vue';
import NdeButtonImportCancel from '../resources/js/components/Button/NdeButtonCopyWorkQueueCancel.vue';
import NdeButtonCopyWorkQueueUpload from '../resources/js/components/Button/NdeButtonCopyWorkQueueUpload.vue';

import NdeButtonsStory from './NdeButtonsStory.vue';

export default {
    title: 'Shared/Buttons',
    component: NdeButtonImport,
};

const NdeButtons = () => ({
  components: { NdeButtonsStory },
    template:
    '<nde-buttons-story></nde-buttons-story>',
})

export const NdeButtons_Stories = NdeButtons.bind({});

const ImportButton = () => ({
    components: { NdeButtonImport },
    template:
    '<nde-button-import @click="click" v-bind="$props" />',
  });

  export const Import_Button = ImportButton.bind({});

const CancelButton = () => ({  
    components: { NdeButtonImportCancel },
    template: '<nde-button-import-cancel @click="click" v-bind="$props" />',
  });
  
  export const Cancel_Button = CancelButton.bind({});

const UploadButton = () => ({  
    components: { NdeButtonCopyWorkQueueUpload },
    template: '<nde-button-copy-work-queue-upload @click="click" v-bind="$props" />',
  });
  
  export const Upload_Button = UploadButton.bind({});  