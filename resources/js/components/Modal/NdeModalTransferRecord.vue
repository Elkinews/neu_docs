<template>
    <nde-modal :showModal.sync="showModal" @update:closeModal="hideModal" title="Transfer Record">
        <template v-slot:ndeModalSubTitle>
            <nde-alert-success v-if="messages.length" class="mt-3" :messages="messages"></nde-alert-success>
        </template>
        <template v-slot:ndeModalContent>
            <nde-form-transfer-record
				@change:chooseTargetProgram="callAPICheckProfilePageOrder"
				:item="item"
				:isShowIncludeDynamicText="isShowIncludeDynamicText">
			</nde-form-transfer-record>
        </template>
        <template v-slot:ndeModalAction>
			<v-container class="mr-0 ml-0">
				<v-row class="mb-3">
					<v-col cols="12" md="6">
						<nde-button-primary title="Transfer" @click="clickTransfer">
						</nde-button-primary>
					</v-col>
					<v-col cols="12" md="6">
						<nde-button-cancel @click="hideModal"> </nde-button-cancel>
					</v-col>
				</v-row>
			</v-container>
        </template>
    </nde-modal>
</template>

<script>
import NdeModal from "./NdeModal.vue";
import NdeAlertSuccess from "../Alert/NdeAlertSuccess.vue";
import NdeFormTransferRecord from "../Form/NdeFormTransferRecord.vue";
import NdeButtonPrimary from "../Button/NdeButtonPrimary.vue";
import NdeButtonCancel from "../Button/NdeButtonCancel.vue";

export default {
    name: "NdeModalTransferRecord",
    components: {
        NdeModal,
        NdeAlertSuccess,
        NdeFormTransferRecord,
        NdeButtonPrimary,
        NdeButtonCancel,
    },
    props: {
        showModal: {
            type: Boolean,
            default: true,
        },
    },
    data() {
		const currentProgramId = this.$store.state.currentProgram.id;
        return {
			messages: [],
            item: {
				targetProgramId: currentProgramId,
				includeDynamicText: true,
			},
			isShowIncludeDynamicText: false,
        };
    },
	async created() {
		const obj = JSON.parse(atob(this.$cookie.get('roles'))) || {};
		if (obj.settings.promote_view === 'ON') {
			this.messages = [
				'Your user settings are configured so that this will delete the original when it transfers to the new program.'
			];
		}
		const currentProgramId = this.$store.state.currentProgram.id;
		const program = this.$store.state.programs.find(dataProgram => dataProgram.meta.has_key_search && dataProgram.meta.id != currentProgramId);
		this.item.targetProgramId = program.meta.id;
		this.$store.commit('setShowProgressAPI', true);
		const resulGetProfileInfoOauth = await this.$store.dispatch('callAPI', {
			url: '/getProfileInfoOauth',
			method: 'post',
			data: {
				profileId: this.$store.state.currentProgram.id,
				getAll: false,
			},
		});
		this.$store.commit('setShowProgressAPI', false);
		if (resulGetProfileInfoOauth.message !== 'success') {
			return this.$swal({
				icon: 'error',
				text: this.getMessageResult(resulGetProfileInfoOauth),    
			});
		}
		const profileInfo = resulGetProfileInfoOauth?.data?.data?.data?.profile_info || [];
		this.isShowIncludeDynamicText = !!profileInfo.find(profile => profile.box_type === 'T' && profile.type === 'TEXT');
		await this.callAPICheckProfilePageOrder();
	},
    methods: {
        hideModal() {
			this.$emit("update:showModal", !this.showModal);
			this.$store.commit('closeModalTransferRecord');
        },
		clickTransfer() {
			this.$store.commit('setTargetProgramId', this.item.targetProgramId);
			this.$store.commit('openModalTransferRecordIndexingFields');
			this.hideModal();
		},
		async callAPICheckProfilePageOrder() {
			this.$store.commit('setShowProgressAPI', true);
			const resulCheckProfilePageOrderOauth = await this.$store.dispatch('callAPI', {
				url: '/checkProfilePageOrderOauth',
				method: 'post',
				data: {
				profileId: this.$store.state.currentProgram.id,
				targetProfileId: this.item.targetProgramId,
				},
			});
			this.$store.commit('setShowProgressAPI', false);
			if (resulCheckProfilePageOrderOauth.message !== 'success') {
				return this.$swal({
					icon: 'error',
					text: this.getMessageResult(resulCheckProfilePageOrderOauth),    
				});
			}
			if ( resulCheckProfilePageOrderOauth?.data?.data?.data?.same === false) {
				this.messages = [
					'This document might merge to the top or bottom of the target program, depending on the target program setting.'
				];
			} else {
				this.messages = [];
			}
		},
    },
};
</script>

