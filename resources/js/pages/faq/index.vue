<template>
	<div role="main">
	<v-container fluid>
		<v-card>
			<v-card-title>
				<h3 role="heading">NeuDocs Enterprise FAQ</h3>
			</v-card-title>
			<v-card-text>
				<v-expansion-panels multiple focusable>
					<v-expansion-panel
            v-for="(item, i) in allFag"
            :key="i">
						<v-expansion-panel-header>
							{{ item.question }}
						</v-expansion-panel-header>
						<v-expansion-panel-content>
							<p v-html="item.answer"></p>
						</v-expansion-panel-content>
					</v-expansion-panel>
				</v-expansion-panels>
			</v-card-text>
		</v-card>
	</v-container>
	</div>
</template>

<script>
import NdeDashboardLayout from "../../shared/layouts/dashboard/NdeDashboardLayout.vue";

export default {
	layout: NdeDashboardLayout,
  data(){
    return {
      allFag : [],
    }
  },
	mounted() {
		this.$store.dispatch('handleVisibleProgramBtn', false)
		window.document.title = "neuDocs Enterprise FAQ";
    this.getFaqData()
	},
  methods: {
    async getFaqData(){
      const fagData = await this.$store.dispatch('callAPI', {
        url: '/getFAQContentsOauth',
        method: 'post',
        data: {},
      });

      if(fagData.message === 'success'){
        this.allFag = fagData?.data?.data || []
      }
    }
  },

	beforeDestroy() {
		this.$store.dispatch('handleVisibleProgramBtn', true)
	}
};
</script>

<style scoped lang="scss">
.v-expansion-panel-header {
	@extend %fontNormalBold;
}

.v-expansion-panel-content {
	margin-top: 1rem;

	.v-expansion-panel-content__wrap {
		@extend %fontNormal;

		a {
			color: $errorColor;
		}
	}
}
</style>
