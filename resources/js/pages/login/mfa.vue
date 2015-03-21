<template>
  <div role="main">
    <v-form class="nde-auth-form" @submit="login">
      <!-- <div v-text="mfaType == 'email' ? des_email : mfaType == 'mobile' ? des_mobile : ''"></div>
    <div v-if="mfaType == 'both'" v-text="bothToggle == 'email' ? des_email : des_mobile"></div> -->

      <h2 class="nde-auth-form-title">MFA</h2>

      <template v-if="data.is_mfa_setup">

        <div class="mfa-des">
          This account requires Multi-Factor Authentication(MFA) to log in, but has not been set up
          with an MFA method yet. Please contact your administrator.
        </div>
      </template>

      <template v-if="data.email_mfa">
        <div class="mfa-des">
          Please enter the 6-digit authentication code that was just sent to the email address
          associated with your account: {{ data.masked_email }}. Please check your Junk Mail folder if
          you don't see the email. If you don't see an email even in your junk folder, please check
          with your administrator.
        </div>
      </template>

      <template v-if="!data.email_mfa">
        <div class="mfa-des">
          Please enter the 6-digit authentication code that was just sent to the email address
          associated with your account: {{ data.masked_email }}. Please check your Junk Mail folder if
          you don't see the email. If you don't see an email even in your junk folder, please check
          with your administrator.
        </div>
      </template>

      <p class="nde-auth-error mt-2">{{ error }}</p>
      <label for="mfaToken">MFA Token</label>
      <!-- v-if="data.otp_secret" -->
      <v-text-field name="mfaToken" id="mfaToken" label="MFA Token" type="text" v-model="mfaToken" solo class="mt-2"
        aria-label="MFA Token"></v-text-field>

      <v-spacer></v-spacer>

      <v-row class="mb-3">
        <v-col class="d-flex justify-space-between">
          <nde-button class="nde-auth-default-btn pa-6" @click="backToLogin">Back</nde-button>
          <!--  v-if="data.otp_secret" -->
          <nde-button color="primary" :loading="isSubmitting" type="submit" class="nde-auth-primary-btn pa-6"
            @click="login">Login</nde-button>
        </v-col>
      </v-row>

      <nde-button block @click="resendMfaEmail" class="mb-3 pa-6"
        v-if="(!data.is_mfa_setup && data.email_mfa) || data.is_mfa_setup && data.email_mfa && !bothToggle">Resend
        email</nde-button>

      <!-- <label class="switch" v-if="data.is_mfa_setup && data.email_mfa">
        <input type="checkbox" id="toggle_vat" class="toggle_vat" v-model="bothToggle">
        <div class="slider round">
          <span class="slider_text"><span class="off">Email</span><span class="on">Mobile App MFA</span></span>
        </div>
      </label> -->

    </v-form>
  </div>
</template>
<script>
import axios from 'axios';
import NdeAuthLayout from '../../shared/layouts/NdeAuthLayout.vue';
import NdeButton from '../../components/Button/NdeButton.vue';

export default {
  layout: NdeAuthLayout,
  data() {
    return {
      mfaToken: '',
      des_email: `Please enter the 6-digit authentication code that was just sent to the email address associated with your account: slo...s.com. Please check your Junk Mail folder if you don't see the email. If you don't see an email even in your junk folder, please check with your administrator.`,
      des_mobile: `Please enter the 6-digit code from your authenticator app (Duo, Google Authenticator, or Microsoft Authenticator). Each code will expire after 30 seconds. If you haven't ever received an activation barcode for this site, or you no longer possess the mobile device to which you added the MFA account, please contact your supervisor.`,
      bothToggle: false,
      error: '',
      isSubmitting: false,
      isFristSendEmail: true,
    };
  },
  computed: {
    isEmail() {
      if (!this.data.email_mfa) {
        return false;
      }
      return !this.bothToggle;
    },
  },
  async created() {
    if (this.data.email_mfa) {
      // await this.sendEmailMFA(false);
    }
  },

  methods: {
    login(e) {
      if (e) e.preventDefault();
      if (!navigator.cookieEnabled) {
        this.isEnabled = false;
        this.error = 'Please refresh the page and try again.';
        return;
      }

      this.error = '';
      this.isSubmitting = true;
      this.$store.dispatch('increaseCallCount');
      axios
        .post('oauthMfa', {
          mfa_token: this.token,
          otp: this.mfaToken,
          is_email: this.isEmail,
        })
        .then((response) => {
          this.isSubmitting = false;
          console.log(response);
          location.href = '/dashboard';
        })
        .catch((error) => {
          this.isSubmitting = false;
          console.log(error.response);
          this.error = 'Invalid MFA Token, please try again';
          location.href = '/login';
        });
    },

    backToLogin() {
      location.href = '/login';
    },

    async resendMfaEmail() {
      this.$store.commit('setShowProgressAPI', true);
      const resultGetMfaEmailOauth = await this.$store.dispatch('callAPI', {
        url: '/resendMfaEmailOauth',
        method: 'post',
        data: {
          mfa_token: this.token,
        },
      });
      this.$store.commit('setShowProgressAPI', false);
      if (resultGetMfaEmailOauth.message !== 'success') {
        await this.$swal({
          icon: 'error',
          text: resultGetMfaEmailOauth?.data?.error?.data?.data?.errors[0] || 'Please try again!'
        });

        location.href = '/login';
        return;
      }

      this.token = resultGetMfaEmailOauth?.data?.data?.data?.mfa_token;

      return this.$swal({
        icon: 'success',
        text: `The authentication code has been sent again to ${this.data.masked_email}.`,
      });

    },

    toggle() {
      this.error = '';
      this.bothToggle = !this.bothToggle
    },
  },

  props: {
    mfaType: {
      type: String,
      required: true,
      default: 'mobile',
    },
    data: {
      type: Object,
    },
    token: {
      type: String
    },
  },
  components: {
    NdeButton,
  },
};
</script>

<style scoped lang="scss">
.mfa-des {
  color: #747474;
  font-size: 16px;
}

.switch {
  position: relative;
  width: 100%;
  height: 56px;
  display: block;
  margin: auto;
  margin-bottom: auto;

}

.switch input {
  display: none;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  -webkit-transition: .4s;
  transition: .4s;

  background: #F9F9F9;
  border: 1px solid #E9E9E9;
  border-radius: 5px;
}

/*Moving SLider Section*/

.slider::before {
  position: absolute;
  content: "";
  height: 44px;
  width: calc(50% - 7px);
  left: 7px;
  bottom: 6px;
  // background-color: #de6570;
  -webkit-transition: .4s;
  transition: .4s;

  background-color: #FFFFFF;
  box-shadow: 0px 3px 5px rgba(0, 0, 0, 0.12);
  border-radius: 8px;
}

/*Slider Text*/

.slider_text {
  position: absolute;
  transform: translate(-50%, -50%);
  top: 50%;
  left: 50%;
  font-size: 10px;
  width: 100%;
  text-align: center;
}

.slider_text>span {
  color: #000;
  font-size: 18px;
  width: 50%;
  display: block;
  float: left;
  -webkit-transition: .4s;
  transition: .4s;
}


/*Changes on Slide*/

input:checked+.slider::before {
  -webkit-transform: translateX(100%);
  -ms-transform: translateX(100%);
  transform: translateX(100%);
}

input:checked+.slider .off {
  color: #747474;
}


input:checked+.slider .on {
  color: #000;
}

input+.slider .on {
  color: #747474;
}
</style>
