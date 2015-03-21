<template>
	<nde-modal :showModal="showModal" @update:closeModal="hideModal"
		:title="isEditProvince ? 'Edit Province' : 'Create Province'">
		<template v-slot:ndeModalContent>
			<nde-province-form ref="provinceForm" :dataProvinceDetail="dataProvinceDetail"
				:isEditProvince="isEditProvince" />
		</template>
		<template v-slot:ndeModalAction>
			<nde-button-cancel @click="hideModal"> </nde-button-cancel>
			<nde-button-primary :loading="isLoading" title="Save" @click="saveData"> </nde-button-primary>
		</template>
	</nde-modal>
</template>

<script>
import NdeModal from '@components/Modal/NdeModal.vue';
import NdeButton from '@components/Button/NdeButton.vue';
import NdeProvinceForm from '../form/NdeProvinceForm.vue';

import NdeButtonCancel from '@components/Button/NdeButtonCancel.vue';
import NdeButtonPrimary from '@components/Button/NdeButtonPrimary.vue';
import { uniq } from 'lodash'

import { mapState } from 'vuex';

export default {
	components: {
		NdeModal,
		NdeButton,
		NdeProvinceForm,
		NdeButtonCancel,
		NdeButtonPrimary,
	},

	data() {
		return {
			showModal: true,
			isLoading: false,
			dataProvinceDetail: {},
		};
	},

	computed: {
		...mapState(['permissionsControl']),
		isEditProvince() {
			return !!this.dataProvinceDetail.id;
		},
		reports() {
			return this.uniqueArr(this.permissionsControl.reports)
		},
		ondemandReports() {
			return this.uniqueArr(this.permissionsControl.ondemand_reports)
		},
	},

	async created() {
		await this.$store.dispatch('getPermissionControlsOauth');
		this.dataProvinceDetail = this.$store.state.dataProvinceDetail || {};
	},

	methods: {
		uniqueArr(arr) {
			return arr.filter((v, i, a) => a.findIndex(e => (e.id === v.id)) === i)
		},
		hideModal() {
			this.$store.commit('closeModalProvince');
		},

		async saveData() {
			this.$refs.provinceForm.hasError = false;

			let profiles = (this.$refs.provinceForm.selectedPrograms || this.dataProvinceDetail.permissions.programs).map((id) => +id);
			let reports = (this.$refs.provinceForm.selectedReports || this.dataProvinceDetail.permissions.reports).map((id) => +id);
			let ondemandReports = (this.$refs.provinceForm.selectedOndemandReports || this.dataProvinceDetail.permissions.ondemand_reports).map((id) => +id);

			// Merge reports into profiles
			const reportProfiles = [];
			reports.forEach(profileId => {
				const dataReport = this.reports.find(report => +report.id === profileId);
				const parentId = +dataReport?.display_parent;
				reportProfiles.push(profileId);
				if (parentId && !reportProfiles.includes(parentId)) {
					reportProfiles.push(parentId)
				}
			});

			profiles.push(...reportProfiles);


			// Merge ondemandReports into profiles
			const ondemandReportsProfiles = [];
			ondemandReports.forEach(profileId => {
				const dataReport = this.ondemandReports.find(report => +report.id === profileId);
				const parentId = +dataReport?.display_parent;
				ondemandReportsProfiles.push(profileId);
				if (parentId && !ondemandReportsProfiles.includes(parentId)) {
					ondemandReportsProfiles.push(parentId)
				}
			});

			profiles.push(...ondemandReportsProfiles);

			profiles = uniq(profiles);


			const provinceInfo = {
				province_name: this.dataProvinceDetail.name,
				profiles,
				roles: (
					this.$refs.provinceForm.selectedRoles || this.dataProvinceDetail.permissions.roles
				).map((id) => +id),
				image_groups: (
					this.$refs.provinceForm.selectedImageGroups ||
					this.dataProvinceDetail.permissions.image_groups
				).map((id) => +id),
				supervisors: (
					this.$refs.provinceForm.selectedSupervisors ||
					this.dataProvinceDetail.permissions.supervisors
				).map((id) => +id),
			};
			const formValidate = this.$refs.provinceForm.$refs.form.validate();
			if (!formValidate) {
				this.$store.commit('scrollToTop', '.nde-province-form');
				return;
			}

			if (!(provinceInfo.profiles.length
				&& provinceInfo.image_groups.length)) {
				this.$store.commit('scrollToTop', '.nde-province-form');
				return this.$refs.provinceForm.hasError = true;
			}

			this.$store.commit('setShowProgressAPI', true);
			const resultUpdateProvinceOauth = await this.$store.dispatch('callAPI', {
				url: this.isEditProvince ? '/editProvinceInfoOauth' : '/createNewProvinceOauth',
				method: 'post',
				data: {
					provinceInfo,
					provinceId: this.dataProvinceDetail.id,
				},
			});
			this.$store.commit('setShowProgressAPI', false);
			if (resultUpdateProvinceOauth.message !== 'success') {
				return this.$swal({
					icon: 'error',
					text: this.getMessageResult(resultUpdateProvinceOauth),
				});
			}
			await this.$swal({
				icon: 'success',
				text: this.getMessageResult(resultUpdateProvinceOauth),
			});
			this.$emit('onGetProvinces');
			this.hideModal();
		},
	},
};
</script>