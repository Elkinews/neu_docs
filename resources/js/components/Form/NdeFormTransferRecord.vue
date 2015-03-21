<template>
    <nde-form class="mrl-container-0">
        <template v-slot:ndeFormBody>
            <v-row>
                <v-col cols="12" :md="isShowIncludeDynamicText ? 6 : 12">
                    <label class="mb-1">Target Program:</label>
                    <v-autocomplete
                        v-model="item.targetProgramId"
                        :items="targetProgramOptions"
                        outlined
                        item-text="name"
                        item-value="id"
						@change="changeTargetProgram"
                    ></v-autocomplete>
                </v-col>
                <v-col v-if="isShowIncludeDynamicText" cols="12" md="6">
                    <v-checkbox
                        class="mt-7"
                        v-model="item.includeDynamicText"
                        label="Include Dynamic Text"
                    ></v-checkbox>
                </v-col>
            </v-row>
        </template>
    </nde-form>
</template>

<script>
import NdeForm from "./NdeForm.vue";

export default {
    name: "NdeFormTransferRecord",
    components: {
        NdeForm,
    },
    props: {
        item: {
            type: Object,
            default: () => ({}),
        },
		isShowIncludeDynamicText: {
			type: Boolean,
			default: false,
		}
    },
    // data() {
    //     return {
    //         isShowIncludeDynamicText: false,
    //     };
    // },
	computed: {
		targetProgramOptions() {
			const currentProgramId = this.$store.state.currentProgram.id;
			const programs = this.$store.state.programs.map(dataProgram => dataProgram.meta).filter(dataProgram => dataProgram.has_key_search && dataProgram.id != currentProgramId);
			return programs;
		}
	},
	methods: {
		changeTargetProgram() {
			if (this.item.targetProgramId) {
				this.$emit("change:chooseTargetProgram");
			}
		}
	}
};
</script>
<style scoped lang="scss">
.mrl-container-0 {
	:v-deep .container {
		margin-left: 0px !important;
		margin-right: 0px !important;
	}
}
</style>
