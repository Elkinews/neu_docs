<template>
    <nde-modal :showModal.sync="showModal" @update:closeModal="hideModal" title="Transfer Tabs">
        <template v-slot:ndeModalContent>
            <nde-table-transfer-tabs :items="items"></nde-table-transfer-tabs>
        </template>
        <template v-slot:ndeModalAction>
            <v-row class="mb-15">
                <v-col cols="12" md="6">
                    <nde-button-primary title="Commit" @click="clickCommit">
                    </nde-button-primary>
                </v-col>
                <v-col cols="12" md="6" style="text-align: right">
                    <nde-button-cancel @click="hideModal"> </nde-button-cancel>
                </v-col>
            </v-row>
        </template>
    </nde-modal>
</template>

<script>
import NdeModal from "./NdeModal.vue";
import NdeTableTransferTabs from "../Table/NdeTableTransferTabs.vue";
import NdeButtonPrimary from "../Button/NdeButtonPrimary.vue";
import NdeButtonCancel from "../Button/NdeButtonCancel.vue";

export default {
    name: "NdeModalTransferRecordIndexingFields",
    components: {
        NdeModal,
        NdeTableTransferTabs,
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
        return {
            items: [],
        };
    },
	async created() {
		this.$store.commit('setShowProgressAPI', true);
		const [resulGetAllTabs, resultGetDocTypeOauth, resulGetProfileInfoOauth] = await Promise.all([
			this.$store.dispatch('callAPI', {
				url: '/getAllTabs',
				method: 'post',
				data: {
					profile_id: this.$store.state.currentProgram.id,
					document_id: this.$store.state.currentImageId,
				},
			}),
			this.$store.dispatch('callAPI', {
				url: '/getDocTypeOauth',
				method: 'post',
				data: {
					profile_id: this.$store.state.currentProgram.id,
					document_id: this.$store.state.currentImageId,
				},
			}),
			this.$store.dispatch('callAPI', {
				url: '/getProfileInfoOauth',
				method: 'post',
				data: {
					profileId: this.$store.state.targetProgramId,
					getAll: true,
				},
			})
		]);

		this.$store.commit('setShowProgressAPI', false);
		if (resulGetAllTabs.message !== 'success') {
			return this.$swal({
				icon: 'error',
				text: this.getMessageResult(resulGetAllTabs),
			});
		}
		if (resultGetDocTypeOauth.message !== 'success') {
			return this.$swal({
				icon: 'error',
				text: this.getMessageResult(resultGetDocTypeOauth),
			});
		}
		if (resulGetProfileInfoOauth.message !== 'success') {
			return this.$swal({
				icon: 'error',
				text: this.getMessageResult(resulGetProfileInfoOauth),
			});
		}
		const docTypes = resultGetDocTypeOauth?.data?.data?.data?.docTypes;
		const predefinedTabs = resulGetProfileInfoOauth?.data?.data?.data?.predefined_tabs || [];
		const tabs = resulGetAllTabs?.data?.data?.data?.tabs;
		const items = [];
		for (const docType in docTypes) {
			const dataDocType = docTypes[docType][0];
			const dataTab = tabs[docType]?.tabs[0];
			dataTab.name = dataTab.tabname ;
			const item = {
				sourceId: dataTab.id,
				fromTab: dataDocType.name,
				type: docType,
				boxType: dataDocType.boxType,
				isTransfer: true,
				toTabOptions: predefinedTabs.filter(dataTab => dataTab.box_type === dataDocType.boxType) || [],
				tabId:  dataTab.id,
			};
			item.toTabOptions.unshift({
				disabled: true,
				divider: true,
				header: '  -- Unused Tabs --  ',
			});
			item.toTabOptions.unshift(dataTab);
			item.toTabOptions.unshift({
				disabled: true,
				divider: true,
				header: ' -- Keep Original(Move) -- ',
			});
			items.push(item);
		}
		this.items = items;
	},
    methods: {
       hideModal() {
           	this.$emit("update:showModal", !this.showModal);
			this.$store.commit('closeModalTransferTabs');
        },
		async clickCommit() {
			this.$store.commit('setShowProgressAPI', true);
			const resulTargetGetAllTabs = this.$store.state.targetDocId ? await this.$store.dispatch('callAPI', {
				url: '/getAllTabs',
				method: 'post',
				data: {
					profile_id: this.$store.state.targetProgramId,
					document_id: this.$store.state.targetDocId,
				},
			}) : null;
			this.$store.commit('setShowProgressAPI', false);
			if (resulTargetGetAllTabs && resulTargetGetAllTabs.message !== 'success') {
				return this.$swal({
					icon: 'error',
					text: this.getMessageResult(resulTargetGetAllTabs),
				});
			};
			const targetTabs = resulTargetGetAllTabs?.data?.data?.data?.tabs || {};
			this.$store.commit('setShowProgressAPI', true);
			let tabs = [];
			let transfer_all = true;
			tabs = this.items.map(item => {
				let tarID = targetTabs[item.type]?.tabs[0]?.id || -1;
				const dataTabSelected = item.toTabOptions.find(dataTab =>dataTab.id === item.tabId);
				if (!item.isTransfer) {
					transfer_all = false;
				}
				let type = 'none';
				if (item.isTransfer) {
					type = this.$store.state.targetDocId ? 'merge' : 'move';
				}
				return {
					type,
					sourceID: item.sourceId,
					tarID,
					name2: dataTabSelected.name,
					boxType: item.boxType,
					tabs: []
				}
			});
			const resultTransferTabOauth = await this.$store.dispatch('callAPI', {
				url: '/transferTabOauth',
				method: 'post',
				data: {
					profile_id: this.$store.state.currentProgram.id,
					target_profile_id: this.$store.state.targetProgramId,
					document_id: this.$store.state.currentImageId,
					target_document_id: !this.$store.state.targetDocId ? "-1" : this.$store.state.targetDocId,
					transfer_all,
					fields: this.$store.state.dataTransferRecordIndexingFields,
					tabs,
				}
			});
			this.$store.commit('setShowProgressAPI', false);
			if (resultTransferTabOauth.message !== 'success') {
				return this.$swal({
					icon: 'error',
					text: this.getMessageResult(resultTransferTabOauth),
				});
			};

			await this.$swal({
				icon: 'success',
				text: this.getMessageResult(resultTransferTabOauth),
			});
			this.$store.dispatch({ type: 'searchImages' });

			return this.hideModal();
		},
    },
};
</script>
<style scoped lang="scss">
:v-deep .v-text-field--outlined {
    height: 2.5rem;
    .v-input__control {
        .v-input__slot {
            fieldset {
                background: #ffffff !important;
                border: 1px solid #ebeced !important;
                border-radius: 8px !important;
                height: 2.5rem;
                top: 0px;
            }
            min-height: 2.5rem;
            input {
                font-weight: 400;
                font-size: 0.813rem;
                line-height: 1.5rem;
                color: #343c42;
            }
            .v-input__append-inner {
                margin-top: 0.5rem;
            }
        }
    }
}
</style>
