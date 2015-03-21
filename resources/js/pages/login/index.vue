<template>
  <div role="main">
    <v-form class="nde-auth-form" @submit="login" ref="authform" autocomplete="false">

      <h2 class="nde-auth-form-title">Login</h2>

      <p role="alert" v-if="error" class="auth-error" v-html="error"></p>
      <p role="alert" v-if="successChangePwdMessage" class="auth-success" v-html="successChangePwdMessage"></p>

      <label for="username" class="mb-2">Username</label>
      <v-text-field
        solo
        type="text" 
        aria-label="Username"
        placeholder="Enter username" 
        id="username"
        v-model="username"
        :name="'username' + Math.random()"
        autocomplete="off"
      ></v-text-field>

      <label for="password" class="mb-2">Password</label>
      <v-text-field
        solo
        aria-label="Password"
        placeholder="Enter password"
        id="password"
        v-model="password"
        :name="'password' + Math.random()"
        autocomplete="new-password"
        :append-icon="showpwd ? 'mdi-eye' : 'mdi-eye-off'" :type="showpwd ? 'text' : 'password'"
        @click:append="showpwd = !showpwd"
      ></v-text-field>

      <v-row class="mb-5">
        <v-col cols="12" md="6">
          <!-- <v-checkbox
      label="Remember me"
    ></v-checkbox> -->
        </v-col>

        <v-col cols="12" md="6" class="d-flex align-center justify-end">
          <a class="nde-auth-resetpassword" href="/resetpassword">Reset password</a>
        </v-col>
      </v-row>

      <v-spacer></v-spacer>

      <v-row>
        <v-col class="d-flex justify-space-between">
          <nde-button class="nde-auth-default-btn pa-6" @click="clear">Clear</nde-button>
          <nde-button color="primary" :loading="isSubmitting" type="submit" class="nde-auth-primary-btn pa-6">Login
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

export default {
  layout: NdeAuthLayout,
  data() {
    return {
      username: '',
      password: '',
      error: '',
      isSubmitting: false,
      showpwd: false,
      successChangePwdMessage: "",
      errors: [],
      reset_token: '',
      account_id: '',
    };
  },

  computed: {
    hasMessageAfterChangePwd() {
      return this.$cookie.get('message')
    }
  },

  mounted() {
    window.document.title = 'neuDocs Enterprise Login';

    if (this.hasMessageAfterChangePwd) {
      this.successChangePwdMessage = this.$cookie.get('message')
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

      this.isSubmitting = true;
      this.error = '';
      this.$store.dispatch('increaseCallCount');
      axios
        .post(this.loginUri, {
          username: this.username,
          password: this.password,
        })
        .then((response) => {
          if (response?.data?.status === 'fail' && response?.data?.data?.reset_token != null) {

            if (response?.data?.data) {
              this.$cookie.set('account_id', response?.data?.data?.account_id);
              this.$cookie.set('reset_token', response?.data?.data?.reset_token);
            }

            this.isSubmitting = false;
            location.href = '/changepassword';
          } else if (response?.data?.message == "Password expired") {
            this.isSubmitting = false;
            location.href = '/changepassword';
          } else {

            if (this.hasMessageAfterChangePwd) {
              this.$cookie.delete('message');
              this.successChangePwdMessage = ""
            }
            this.$nextTick(() => {
              this.authRedirect();
            });
          }
        })
        .catch((error) => {
          this.isSubmitting = false;
          console.log(error.response);


          if (error.response.data?.data?.data?.data?.mfa_token != null) {
            this.error = '';
            location.href = '/mfa-both';
          } else {
            this.error = error.response.data.message;
          }
        });
    },

    clear() {
      this.$refs.authform.reset();
    },
    authRedirect() {
      if (window.location.toString().includes("redirect_url")) {
        let searchParams = new URLSearchParams(location.search)
        let param = searchParams.get('redirect_url')
        location.href = param;
      } else {
        location.href = '/dashboard';
      }
    }
  },

  props: {
    loginUri: {
      type: String,
      required: true,
    },
  },

  components: {
    NdeButton,
  },
};
</script>

<style scoped lang="scss">
.nde-auth-form {
  min-width: 300px;
}

.nde-auth-resetpassword {
  color: black;
}

.auth-error {
  color: $errorColor !important;

  a {
    color: $errorColor !important;
  }
}

.auth-success {
  color: $successColor !important;

  a {
    color: $successColor !important;
  }
}
</style>
