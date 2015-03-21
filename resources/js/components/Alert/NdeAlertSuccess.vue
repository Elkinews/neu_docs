<template>
    <v-alert text color="#1B3B1E">
        <ul v-show="messages.length">
            <li v-for="(item, index) in messages" :key="index">
                <span>{{ item }}</span>
            </li>
        </ul>
		<span v-show="message">{{ message }}</span>
    </v-alert>
</template>

<script>
export default {
    name: "NdeAlertSuccess",
    props: {
        messages: {
            type: Array,
            default: () => [],
        },
		message: {
			type: String,
			default: '',
		},
    },
};
</script>

<style scoped lang="scss">
.v-alert {
    padding: 0.5rem;
    background: $successGreyColor !important;
    border-radius: 4px;
	:v-deep .v-alert__wrapper {
		align-items: start;
		.v-alert__content {
			span {
				color: $successColor;
				font-family: "Poppins";
				font-style: normal;
				font-weight: 600;
				font-size: 0.813rem;
				line-height: 1.5rem;
			}
		}
	}
}
</style>
