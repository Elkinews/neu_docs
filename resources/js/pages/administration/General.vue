<template>
    <div class="nde-general" role="main">
        <nde-tab-admin />
        <v-card class="mt-5 pa-5">
            <h3>General</h3>
            <v-btn
                depressed outlined light color="success"
                class="mt-5"
                @click="openUploadCSVModal"
            >
                <v-icon left color="primary">mdi-file-download-outline</v-icon>
                Upload CSV Data Feed
            </v-btn>

            <UploadCSV ref="uploadCSVModal" />
		</v-card>
    </div>
</template>

<script>
import NdeTabAdmin from "../../components/Tabs/NdeTabAdministration.vue";
import NdeDashboardLayout from "../../shared/layouts/dashboard/NdeDashboardLayout.vue";
import UploadCSV from "./dialog/UploadCSV"


export default {
    layout: NdeDashboardLayout,
    components: {
        NdeTabAdmin,
        UploadCSV
    },

    methods: {
        openUploadCSVModal() {
            this.$refs.uploadCSVModal.dialog = true
        }
    },

    created() {
        if(this.isMobile) {
            location.href = '/administration/manageUsers';
        }
    }

   
}
</script>

<style lang="scss">

button.v-btn {
    text-transform: unset !important;
}
</style>