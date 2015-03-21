<template>
    <div>
        <nde-view-record-page v-if="doc_id && profileId" :doc_id="doc_id"
        :tab_name="tab_name" :profileId="profileId"></nde-view-record-page>
        <nde-modal-upload-document v-if="isModalUploadDocument" />
        <nde-scan-modal v-if="isScanModal" />
    </div>
    
</template>
  
<script>
import { mapState } from 'vuex';
import NdeDashboardLayout from '../../shared/layouts/dashboard/NdeDashboardLayout.vue';
import NdeViewRecordPage from '../../components/Dashboard/NdeViewRecordPage.vue';
import NdeModalUploadDocument from '../../components/Modal/NdeModalUploadDocument.vue';
import NdeScanModal from '../../components/Modal/NdeScanModal.vue';
export default {
    layout: NdeDashboardLayout,
    data() {
        return {};
    },
    props: {
        doc_id: {
            required: true,
            type: String
        },
        tab_name: {
            required: false,
            type: String,
            default: ''
        },
        profileId: {
            required: true,
            type: String
        }
    },
    computed: {
        ...mapState(['currentProgram', 'isModalUploadDocument', 'isScanModal']),
    },
    methods: {},
    mounted() {
        window.document.title = 'NDE View Record';
    },
    components: {
        NdeViewRecordPage, NdeModalUploadDocument, NdeScanModal
    },
};
</script>
  
<style scoped lang="scss">

</style>