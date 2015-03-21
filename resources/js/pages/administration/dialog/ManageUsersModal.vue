<template>
     <nde-modal
        :showModal.sync="showModal"
        @update:closeModal="hideModal"
        :title="isEditUser ? `Edit User: ${item.username}` : 'Create User'">
        <template v-slot:ndeModalContent>
            <!-- <nde-form-indexing-fields :item="item"></nde-form-indexing-fields> -->
            <NdeGeneralForm
                ref="generalForm"
                :dataProvinces="dataProvinces"
                :dataUserDetail="dataUserDetail"
                :isEditUser="isEditUser"
                :isLoading="isLoading"
                @onHideModal="hideModal"
                @onSaveData="saveData"
            />
        </template>
    </nde-modal>
</template>

<script>
// import NdeDataTable from "@components/Table/NdeDataTable.vue";
// import NdeButtonMenuItems from '@components/Table/NdeSearchResult/NdeButtonMenuItems.vue';
import NdeModal from "@components/Modal/NdeModal.vue";
import NdeButton from "@components/Button/NdeButton.vue";
import NdeGeneralForm from "../form/GeneralForm.vue";

// import NdeButtonCancel from "@components/Button/NdeButtonCancel.vue";
// import NdeButtonPrimary from "@components/Button/NdeButtonPrimary.vue";

import { mapState } from "vuex"

export default {
    components: {
        // NdeDataTable,
        // NdeButtonMenuItems,
        NdeModal,
        NdeButton,
        NdeGeneralForm,
        // NdeButtonCancel,
        // NdeButtonPrimary
    },

    props: {
        isEditUser: {
            type: Boolean,
            default: false
        },

        dataProvinces: {
            type: Array,
            default: () => []
        }
    },

    data() {
        return {
            showModal: false,
            isLoading: false,
            item: {},
            dataUserDetail: {},
        }
    },

    computed: {
        ...mapState(['permissionsControl']),
    },

    methods: {
        hideModal() {
            this.showModal = !this.showModal
            this.dataUserDetail = {}

            if(this.isEditUser) {
                this.$emit('onUpdateStatusModal', false)
            }
        },

        adminGetUserDetailsOauth() {
            this.isLoading = true
            this.$store.commit('setShowProgressAPI', true);
            this.$store.dispatch('increaseCallCount');
            axios
                .get('/adminGetUserDetailsOauth', {params: {user_id: this.item.account_id}})
                .then((response) => {
                    this.isLoading = false
                    this.$store.commit('setShowProgressAPI', false);
                    this.dataUserDetail = response?.data?.data?.data || {};
                    console.log("responding data: ", this.dataUserDetail)
                })
                .catch((error) => {
                    this.isLoading = false
                    this.$store.commit('setShowProgressAPI', false);
                    console.error(error);
                });
        },

        saveData() {
            let payload = {
                ...this.dataUserDetail,
                type: this.$refs.generalForm.type,
                programs: this.$refs.generalForm.selectedPrograms || this.dataUserDetail.programs,
                image_groups: this.$refs.generalForm.selectedImageGroups || this.dataUserDetail.image_groups,
                roles: this.$refs.generalForm.selectedRoles || this.dataUserDetail.roles,
                supervisors:this.$refs.generalForm.selectedSupervisors || this.dataUserDetail.supervisors,
                reports: this.$refs.generalForm.selectedReports || this.dataUserDetail.reports,
                provinces: this.$refs.generalForm.selectedProvinces || this.dataUserDetail.provinces,
                ondemandReports: this.$refs.generalForm.selectedOndemandReports || this.dataUserDetail.ondemand_reports,
            }
            console.log("this payload: ", payload)

            this.$emit('onUpdateUserInfo', payload)
        }
    },

    mounted() {
        this.$store.dispatch('getPermissionControlsOauth')
    },

    watch: {
        showModal(newVal, oldVal) {
            if(newVal !== oldVal && newVal && this.isEditUser) {
                this.adminGetUserDetailsOauth()
            }
        }
    }
}
</script>
