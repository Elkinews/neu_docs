<template>
  <div role="main">
    <v-form class="nde-auth-form" v-if="!questions && !(reset_token && account_id)" @submit="reset">
      <h2 class="nde-auth-form-title">Reset password</h2>
      <p class="nde-auth-error mt-2" v-if="error">{{ error }}</p>
      <label for="username" class="mb-2">Username</label>
      <v-text-field name="username" id="username" label="Username" type="text" v-model="username" solo
        aria-label="Username"></v-text-field>
      <div class="mt-2 mb-3" v-if="(env && env.GOOGLE_RECAPTCHA_KEY && isNeedCaptcha)">
        <vue-recaptcha :sitekey="env.GOOGLE_RECAPTCHA_KEY" ref="recaptcha" @verify="verifiedCaptcha"
          @error="errorCaptcha"></vue-recaptcha>
      </div>

      <v-spacer></v-spacer>

      <v-row>
        <v-col class="d-flex justify-space-between">
          <nde-button class="nde-auth-default-btn pa-6" @click="backToLogin">Back </nde-button>
          <nde-button color="primary" :loading="isSubmitting" type="submit" class="nde-auth-primary-btn pa-6"
            @click="reset">Continue</nde-button>
        </v-col>
      </v-row>
    </v-form>

    <v-form class="nde-auth-form" v-if="questions && !(reset_token && account_id)" @submit="submitQA">
      <h2 class="nde-auth-form-title">Reset password</h2>
      <p class="nde-auth-error mb-2" v-if="error">{{ error }}</p>
      <label class="nde-auth-form-label mb-2" for="qa_1">{{ questions[0].question }}</label>
      <v-text-field aria-label="Quesion 1" :append-icon="showFirstAns ? 'mdi-eye' : 'mdi-eye-off'"
        @click:append="showFirstAns = !showFirstAns" :type="showFirstAns ? 'text' : 'password'" name="qa_1" label=""
        v-model="qa_1" solo id="qa_1"></v-text-field>

      <label class="nde-auth-form-label mb-2" for="qa_2">{{ questions[1].question }}</label>
      <v-text-field aria-label="Quesion 2" :append-icon="showSecondAns ? 'mdi-eye' : 'mdi-eye-off'"
        @click:append="showSecondAns = !showSecondAns" :type="showSecondAns ? 'text' : 'password'" name="qa_2" label=""
        v-model="qa_2" solo id="qa_2"></v-text-field>

      <v-spacer></v-spacer>

      <v-row>
        <v-col class="d-flex justify-space-between">
          <nde-button class="nde-auth-default-btn pa-6" @click="backToLogin">Back </nde-button>
          <nde-button color="primary" :loading="isSubmitting" type="submit" class="nde-auth-primary-btn pa-6"
            @click="submitQA">Submit</nde-button>
        </v-col>
      </v-row>
    </v-form>

    <v-form class="nde-auth-form" v-if="reset_token && account_id" @submit="resetNewPWD">
      <div v-if="errors.length > 0" class="nde-auth-error">
        <p class="nde-auth-error-title">Password policy:</p>
        <ul>
          <li v-for="(error, index) in errors" :key="index">
            {{ error }}
          </li>
        </ul>
      </div>

      <label class="nde-auth-form-label" for="new_pwd">New Password</label>
      <v-text-field name="password" label="" v-model="password" outlined id="new_pwd"
        :type="showPassword ? 'text' : 'password'" :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
        @click:append="showPassword = !showPassword" aria-label="New Password"></v-text-field>

      <label class="nde-auth-form-label" for="re_pwd">Re Enter New Password</label>
      <v-text-field name="repassword" label="" v-model="repassword" outlined id="re_pwd"
        :type="showConfirmPassword ? 'text' : 'password'" :append-icon="showConfirmPassword ? 'mdi-eye' : 'mdi-eye-off'"
        @click:append="showConfirmPassword = !showConfirmPassword" aria-label="Re Enter New Password"></v-text-field>

      <v-row>
        <v-col class="d-flex justify-space-between">
          <nde-button class="nde-auth-default-btn pa-6" @click="backToLogin">Back </nde-button>
          <nde-button color="primary" :loading="isSubmitting" type="submit" class="nde-auth-primary-btn pa-6"
            @click="resetNewPWD">Submit</nde-button>
        </v-col>
      </v-row>
    </v-form>
  </div>
</template>

<script>
import axios from 'axios';
import { mapState } from 'vuex';
import { VueRecaptcha } from 'vue-recaptcha';
import NdeAuthLayout from '../../shared/layouts/NdeAuthLayout.vue';
import NdeButton from '../../components/Button/NdeButton.vue';

export default {
  layout: NdeAuthLayout,
  data() {
    return {
      username: '',
      error: '',
      isSubmitting: false,
      questions: null,
      qa_1: '',
      qa_2: '',

      password: '',
      repassword: '',
      reset_token: '',
      account_id: '',
      errors: [],

      showFirstAns: false,
      showSecondAns: false,

      showPassword: false,
      showConfirmPassword: false,
      isNeedCaptcha: false,
      isCaptchaVerified: false,
    };
  },

  computed: {
    ...mapState(['env'])
  },

  methods: {
    reset(e) {
      if (e) e.preventDefault();

      if (this.isNeedCaptcha) {
        if (!this.isCaptchaVerified) {
          this.$refs.recaptcha.reset();
          this.error = "Please verifiy the captcha.";
          return;
        }
      }

      if (!navigator.cookieEnabled) {
        this.isEnabled = false;
        this.error = 'Please refresh the page and try again.';
        return;
      }

      this.error = '';
      this.isSubmitting = true;
      this.$store.dispatch('increaseCallCount');
      axios
        .post('/accountGetPasswordResetQuestionsOauth', {
          username: this.username,
        })
        .then((response) => {
          console.log(response.data);
          this.isSubmitting = false;
          this.questions = response.data.data.questions;
          console.log(this.questions);
          this.isNeedCaptcha = false;
          this.isCaptchaVerified = false;
          this.$refs.recaptcha.reset();
        })
        .catch((error) => {
          console.log(error.response);
          this.isSubmitting = false;
          this.error = error.response.data.data.error;
          if (this.isNeedCaptcha) {
            this.$refs.recaptcha.reset();
          }
          this.isNeedCaptcha = true;
          this.isCaptchaVerified = false;
        });
    },

    backToLogin() {
      location.href = '/login';
    },

    submitQA(e) {
      if (e) e.preventDefault();
      if (!navigator.cookieEnabled) {
        this.isEnabled = false;
        this.error = 'Please refresh the page and try again.';
        return;
      }

      const postData = {
        username: this.username,
        questions: [
          {
            question_id: this.questions[0].question_id,
            question_answer: this.qa_1,
          },
          {
            question_id: this.questions[1].question_id,
            question_answer: this.qa_2,
          },
        ],
      };

      console.log(postData);

      this.error = '';
      this.isSubmitting = true;
      this.$store.dispatch('increaseCallCount');
      axios
        .post('/accountConfirmSecurityAnswersOauth', postData)
        .then((response) => {
          console.log(response.data);
          this.isSubmitting = false;
          // location.href = '/setting/changePassword';
          this.reset_token = response.data.data.data.data.reset_token;
          this.account_id = response.data.data.data.data.account_id;
        })
        .catch((error) => {
          console.log(error.response);
          this.isSubmitting = false;
          this.error = error.response.data.data.error.data.message;
        });
    },

    resetNewPWD(e) {
      if (e) e.preventDefault();
      if (this.password !== this.repassword) {
        this.errors = ['Passwords should be matched!'];
        return;
      }

      this.errors = [];
      this.isSubmitting = true;
      const postData = {
        new: this.password,
        confirm: this.repassword,
        reset_token: this.reset_token,
        account_id: this.account_id,
      };
      this.$store.dispatch('increaseCallCount');
      axios
        .post('/accountSetNewPasswordOauth', postData)
        .then((response) => {
          console.log(response.data);
          this.isSubmitting = false;
          location.href = '/login';
        })
        .catch((error) => {
          console.log(error.response);
          this.isSubmitting = false;
          // this.errors = error.response.data.data.data.errors;
          this.errors = [
            "Minimum length of twelve (12) alphanumeric characters.",
            "Password must contain a mix of upper and lower case characters.",
            "Password must have at least 2 numeric characters.",
            "Numeric characters must not be at the beginning or the end of Password.",
            'Password must include at least one special character from the following list: !@#$%^&*()_+-={}|[]~`:"'+";'<>?,./",
            "Password cannot have been used within the last four (4) years."
          ];
        });
    },

    verifiedCaptcha(e) {
      console.log(e)
      this.isCaptchaVerified = true;
    },

    errorCaptcha(e) {
      console.log(e);
      this.isCaptchaVerified = false;
    },

    onCaptchaLoaded() {
      this.$refs.recaptcha.execute();
    }
  },

  props: {
    resetUri: {
      type: String,
      required: true,
    },
  },

  components: {
    NdeButton, VueRecaptcha
  },
};
</script>

<style scoped lang="scss">
.nde-auth-form {
  min-width: 300px;
}
</style>
