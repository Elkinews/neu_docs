<template>
  <div>
    <v-form class="nde-auth-form" v-if="!questions && !(reset_token && account_id)">
      <h2 class="nde-auth-form-title">Reset password</h2>

      <p role="alert" v-if="error" class="auth-error mb-3" v-html="error"></p>

      <nde-captcha ref="captchaForm" @onSuccess="onSuccess" @onError="onError"></nde-captcha>

      <!-- <label class="nde-auth-form-label mt-3 mb-2" for="code">Enter the code below and press continue:</label>
      <v-text-field aria-label="Enter the code below and press continue" name="code" id="code" label="" type="text" v-model="code" outlined></v-text-field> -->

      <label class="nde-auth-form-label mb-2" for="username">Enter your username then click continue:</label>
      <v-text-field aria-label="Enter your username then click continue" name="username" id="username" label=""
        type="text" v-model="username" outlined></v-text-field>

      <v-spacer></v-spacer>

      <!-- <nde-button color="primary" block @click="verifyCode" class="mb-3">Continue</nde-button>
  
      <nde-button color="error" block @click="backToLogin">Back to Login</nde-button> -->

      <v-row>
        <v-col class="d-flex justify-space-between">
          <nde-button class="nde-auth-default-btn pa-6" @click="backToLogin">Back</nde-button>
          <nde-button color="primary" :loading="isSubmitting" class="nde-auth-primary-btn pa-6" @click="verifyCode"
            :disabled="!username">Continue</nde-button>
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
import NdeAuthLayout from '../../shared/layouts/NdeAuthLayout.vue';
import NdeCaptcha from '../../components/Captcha/NdeCaptcha.vue';
import NdeButton from '../../components/Button/NdeButton.vue';

export default {
  layout: NdeAuthLayout,
  data() {
    return {
      code: '',
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
      showConfirmPassword: false
    };
  },

  computed: {},

  methods: {
    login() { },

    backToLogin() {
      location.href = '/login';
    },

    verifyCode() {
      if (!this.username) {
        this.error = "Please input the username";
        return;
      }

      this.isSubmitting = true;
      this.$refs.captchaForm.submitForm();
    },

    onSuccess() {
      this.reset(null);
    },

    reset(e) {
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
        .post('/accountGetPasswordResetQuestionsOauth', {
          username: this.username,
        })
        .then((response) => {
          console.log(response.data);
          this.isSubmitting = false;
          this.questions = response.data.data.questions;
          console.log(this.questions);
        })
        .catch((error) => {
          console.log(error.response);
          this.isSubmitting = false;
          this.error = error.response.data.data.error;
          location.href = '/captcha';
        });
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
          this.errors = error.response.data.data.data.errors;
        });
    },

    onError() {
      this.isSubmitting = false;
      this.error = "Captcha code is wrong. Please try again!";
    }
  },

  props: {},

  components: { NdeCaptcha, NdeButton },

  watch: {
    code: function (val) {
      this.$refs.captchaForm.setCode(val);
    }
  }
};
</script>

<style scoped lang="scss">
.nde-auth-form {
  min-width: 300px;
}

.nde-auth-form-label {
  margin-bottom: 5px;
}

.auth-error {
  color: $errorColor !important;
}
</style>