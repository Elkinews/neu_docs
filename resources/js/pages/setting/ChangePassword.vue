<template>
	<div class="nde-change-password" role="main">
		<nde-tab-setting />
			<v-card class="mt-5">
				<v-card-title>
					<h3 role="heading">Change Password</h3>
				</v-card-title>
				<v-card-subtitle>
					<h5>Password policy:</h5>
					<nde-alert :messages="messages"></nde-alert>
				</v-card-subtitle>
				<v-card-text>
					<nde-form-setting-change-password ref="changePasswordForm" @onUpdateValidPassword="handleValidPassword" :item="item">
					</nde-form-setting-change-password>
				</v-card-text>
                <action-button @onSave="changePassword" @onCancel="handleRedirect" :disabled="!isValid" :loading="isLoading" />
			</v-card>
	</div>
</template>

<script>
import NdeDashboardLayout from "../../shared/layouts/dashboard/NdeDashboardLayout.vue";
import NdeTabSetting from "../../components/Tabs/NdeTabSetting.vue";
import NdeAlert from "../../components/Alert/NdeAlert.vue";
import NdeFormSettingChangePassword from "../../components/Form/NdeFormSettingChangePassword.vue";
import ActionButton from "./ActionButton.vue"
import { PASSWORD_GUIDE } from '../../utils/passwordValidator';

export default {
	layout: NdeDashboardLayout,
    components: {
		NdeTabSetting,
        NdeAlert,
        NdeFormSettingChangePassword,
        ActionButton
    },
    data() {
        // TODO: Will delete when there is data get from API
        return {
            item: {},
            isLoading: false,
            isValid: false,
        };
    },
    
    computed: {
        messages() {
            return PASSWORD_GUIDE;
        }
    },
    methods: {
        handleRedirect() {
            location.href = '/dashboard';
        },

        handleValidPassword(value) {
            value ? this.isValid = true : this.isValid = false
        },

        changePassword() {
            this.isLoading = true

            let payload = {
                old : this.$refs.changePasswordForm.old_pwd,
                new : this.$refs.changePasswordForm.new_pwd,
                confirm: this.$refs.changePasswordForm.confirm_pwd
            }
            this.$store.dispatch('increaseCallCount');
            axios
            .post('/accountUpdateOldPasswordOauth', payload)
            .then((response) => {
                this.isLoading = false
                if(response.data && response.data.message === 'success') {
                    this.$refs.changePasswordForm.old_pwd = ''
                    this.$refs.changePasswordForm.new_pwd = ''
                    this.$refs.changePasswordForm.confirm_pwd = ''

                    const message = response?.data?.data?.data?.message
                    this.$swal({
						icon: 'success',
						text: message,
					}).then(result => {
                        if(result.isConfirmed) {
                            this.handleRedirect()
                        }
                    });
                } else {
                    const message = response?.data?.response?.data?.errors[0]
                    return this.$swal({
						icon: 'error',
						text: message,
					});
                }
            })
            .catch((error) => {
                this.isLoading = false
                console.error(error);
                this.$swal({
                    icon: 'error',
                    text: "Something went wrong! Please try again!",
                });
            });
        }
    },
    mounted() {
        this.$store.dispatch('handleVisibleProgramBtn', false)
        window.document.title = "neuDocs Enterprise Settings";
    },
    beforeDestroy() {
   	 	this.$store.dispatch('handleVisibleProgramBtn', true)
  	}
};
</script>

<style scoped lang="scss">
.v-card__title {
    display: flex;
    justify-content: center;
}
.v-btn__loader {
    color: white
}
.v-card__subtitle {
    * {
        color: $errorColor;
    }
    .v-alert {
        background: $errorGreyColor;
        :v-deep .v-alert__wrapper {
            align-items: flex-start;
        }
    }
}
.nde-change-password {
    .v-btn {
        width: 10rem;
    }
}

@media screen and (max-width: 48rem) {
    .nde-change-password {
        .v-btn {
            width: 100%;
        }
    }
}
</style>
