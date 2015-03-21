<template>
    <div>
        <div>
            <nde-button>Default</nde-button>

            <nde-button color="primary">Primary</nde-button>

            <nde-button color="error">Error</nde-button>

            <nde-button color="warning">Warning</nde-button>

            <nde-button color="success">Success</nde-button>

            <nde-button color="white">White</nde-button>

            <nde-button color="dark">Dark</nde-button>
        </div>

        <div class="mt-3">
            
            <nde-button color="white"><v-icon left color="primary">mdi-cloud-upload</v-icon>Icon Left</nde-button>

             <nde-button color="white">Icon Right<v-icon right color="primary">mdi-cloud-upload</v-icon></nde-button>
        </div>

        <div class="mt-3">
            <nde-button outlined>Default</nde-button>

            <nde-button outlined color="primary">Primary</nde-button>

            <nde-button outlined color="error">Error</nde-button>

            <nde-button outlined color="warning">Warning</nde-button>

            <nde-button outlined color="success">Success</nde-button>

            <nde-button outlined color="white">White</nde-button>

            <nde-button outlined color="dark">Dark</nde-button>
        </div>

        <div class="mt-3">
            <nde-button outlined><v-icon left>mdi-cloud-upload</v-icon>Default</nde-button>

            <nde-button outlined color="primary"><v-icon left>mdi-cloud-upload</v-icon>Primary</nde-button>

            <nde-button outlined color="error"><v-icon left>mdi-cloud-upload</v-icon>Error</nde-button>

            <nde-button outlined color="warning"><v-icon left>mdi-cloud-upload</v-icon>Warning</nde-button>

            <nde-button outlined color="success"><v-icon left>mdi-cloud-upload</v-icon>Success</nde-button>

            <nde-button outlined color="white"><v-icon left>mdi-cloud-upload</v-icon>White</nde-button>

            <nde-button outlined color="dark"><v-icon left>mdi-cloud-upload</v-icon>Dark</nde-button>
        </div>

        <div class="mt-3">
            <nde-button><v-icon left>mdi-cloud-upload</v-icon>Default</nde-button>

            <nde-button color="primary"><v-icon left>mdi-cloud-upload</v-icon>Primary</nde-button>

            <nde-button color="error"><v-icon left>mdi-cloud-upload</v-icon>Error</nde-button>

            <nde-button color="warning"><v-icon left>mdi-cloud-upload</v-icon>Warning</nde-button>

            <nde-button color="success"><v-icon left>mdi-cloud-upload</v-icon>Success</nde-button>

            <nde-button color="white"><v-icon left>mdi-cloud-upload</v-icon>White</nde-button>

            <nde-button color="dark"><v-icon left>mdi-cloud-upload</v-icon>Dark</nde-button>
        </div>

        <div class="mt-3">
            <nde-button fab x-small><v-icon>mdi-cloud-upload</v-icon></nde-button>

            <nde-button fab small color="primary"><v-icon>mdi-cloud-upload</v-icon></nde-button>

            <nde-button fab color="error"><v-icon>mdi-cloud-upload</v-icon></nde-button>

            <nde-button fab large color="warning"><v-icon>mdi-cloud-upload</v-icon></nde-button>

            <nde-button fab x-large color="success"><v-icon>mdi-cloud-upload</v-icon></nde-button>

            <nde-button fab color="white"><v-icon>mdi-cloud-upload</v-icon></nde-button>

            <nde-button fab color="dark"><v-icon>mdi-cloud-upload</v-icon></nde-button>
        </div>

        <div class="mt-3">
            <nde-button fab outlined x-small><v-icon>mdi-cloud-upload</v-icon></nde-button>

            <nde-button fab outlined small color="primary"><v-icon>mdi-cloud-upload</v-icon></nde-button>

            <nde-button fab outlined color="error"><v-icon>mdi-cloud-upload</v-icon></nde-button>

            <nde-button fab outlined large color="warning"><v-icon>mdi-cloud-upload</v-icon></nde-button>

            <nde-button fab outlined x-large color="success"><v-icon>mdi-cloud-upload</v-icon></nde-button>

            <nde-button fab outlined color="white"><v-icon>mdi-cloud-upload</v-icon></nde-button>

            <nde-button fab outlined color="dark"><v-icon>mdi-cloud-upload</v-icon></nde-button>
        </div>
        
    </div>

   
</template>

<script>
import NdeButton from '../resources/js/components/Button/NdeButton.vue';

export default {
    name: 'nde-buttons-story',
    components: {NdeButton}    
}
</script>

