<template>
  <div role="main">
    <v-form class="nde-auth-form" v-model="valid">
      <div v-if="errors.length > 0" class="nde-auth-error">
        <li v-for="(error, index) in errors" :key="index">
          {{ error }}
        </li>
      </div>

      <label class="nde-auth-form-label" for="old_pwd">Old Password</label>
      <v-text-field name="old_pwd" v-model="oldpwd" outlined tabindex="1" id="old_pwd"
        :type="showOldPwd ? 'text' : 'password'" :append-icon="showOldPwd ? 'mdi-eye' : 'mdi-eye-off'"
        @click:append="showOldPwd = !showOldPwd" aria-label="Old Password">
      </v-text-field>

      <label class="nde-auth-form-label" for="new_pwd">New Password</label>
      <v-text-field name="new_password" v-model="password" outlined tabindex="2" id="new_pwd"
        :rules="rules.passwordRules" :type="showPassword ? 'text' : 'password'"
        :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'" @click:append="showPassword = !showPassword"
        :messages="!passwordValid ? message : ''" aria-label="New Password">
        <template v-slot:message="{ message }" v-if="!passwordValid">
          <span v-html="message"></span>
        </template>
      </v-text-field>

      <label class="nde-auth-form-label" for="re_pwd">Re Enter New Password</label>
      <v-text-field name="repassword" v-model="repassword" outlined tabindex="3" id="re_pwd"
        :rules="[rules.requiredRule, rules.confirmPasswordRule]" :type="showConfirmPassword ? 'text' : 'password'"
        :append-icon="showConfirmPassword ? 'mdi-eye' : 'mdi-eye-off'"
        @click:append="showConfirmPassword = !showConfirmPassword" @update:error="checkPassword"
        aria-label="Re Enter New Password"></v-text-field>

      <v-spacer></v-spacer>

      <v-row>
        <v-col class="d-flex justify-space-between">
          <nde-button class="nde-auth-default-btn pa-6" @click="backToLogin">Back to Login</nde-button>
          <nde-button color="primary" :loading="loading" :disabled="!valid" @click="change"
            class="nde-auth-primary-btn pa-6">Change
          </nde-button>
        </v-col>
      </v-row>

    </v-form>
  </div>
</template>

<script>
import axios from 'axios';
import NdeAuthLayout from '../../shared/layouts/NdeAuthLayout.vue';
import NdeButton from '../../components/Button/NdeButton.vue';
import {
  getFormPasswordRules,
  getRequiredRules,
  isValidPassword,
  getPasswordMessageGuideList
} from '../../utils/passwordValidator';

export default {
  layout: NdeAuthLayout,
  data() {
    return {
      oldpwd: '',
      password: '',
      repassword: '',
      showOldPwd: false,
      showPassword: false,
      showConfirmPassword: false,
      errors: [],
      valid: true,
      loading: false,
      passwordValid: false,
      rules: {
        requiredRule: '',
        passwordRules: [],
        confirmPasswordRule: (value) =>
          value === this.password || 'The password confirmation does not match.',
      },
    };
  },

  components: {
    NdeButton,
  },

  computed: {
    message() {
      return getPasswordMessageGuideList();
    }
  },

  props: {
    changePWDUri: {
      type: String,
      required: true,
    },
  },

  mounted() {
    this.rules.passwordRules = getFormPasswordRules();
    this.rules.requiredRule = getRequiredRules();
  },
  methods: {
    async change() {
      let payload = {
        old: this.oldpwd,
        new: this.password,
        confirm: this.repassword
      };

      this.loading = true;

      const resUpdatePWD = await this.$store.dispatch('callAPI', {
        url: '/accountUpdateOldPasswordOauth',
        method: 'post',
        data: payload,
      });

      this.loading = false;
      console.log(resUpdatePWD);
      if (resUpdatePWD?.message == 'success') {
        location.href = '/dashboard';
      } else {
        this.errors = resUpdatePWD?.response?.data?.errors;
      }
    },
    backToLogin() {
      location.href = '/login';
    },
    checkPassword(invalid) {
      invalid ? (this.valid = false) : (this.valid = true);
    },
  },
  watch: {
    password(newVal) {
      if (
        !newVal ||
        newVal !== this.repassword ||
        !isValidPassword(newVal)
      ) {
        this.checkPassword(true);
        this.repassword = '';
      } else {
        this.checkPassword(false);
      }

      if (isValidPassword(newVal)) {
        this.passwordValid = true
      } else {
        this.passwordValid = false
      }
    },
  },
};
</script>

<style scoped lang="scss">
.nde-auth-button {
  color: white;
}

.nde-auth-form {
  min-width: 300px;
}

.nde-auth-form-label {
  margin-bottom: 5px;
}

.nde-auth-error {
  li {
    list-style: none;
  }
}
</style>
