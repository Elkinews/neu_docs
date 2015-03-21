<template>
  <div role="main">
  <v-form class="nde-auth-form">
    <h2 class="nde-auth-form-title">Reset password</h2>
    <label class="nde-auth-form-label" for="childhoodname">What was your childhood name?</label>
    <v-text-field
      name="childhoodname"
      label=""
      v-model="childhoodname"
      outlined
      id="childhoodname"
      :append-icon="showChildhoodnamed ? 'mdi-eye' : 'mdi-eye-off'"
      @click:append="showChildhoodnamed = !showChildhoodnamed"
      :type="showChildhoodnamed ? 'text' : 'password'"
      aria-label="What was your childhood name?" 
    ></v-text-field>

    <label class="nde-auth-form-label" for="favchildhoodname"
      >What is the name of your favorite childhood friend?</label
    >
    <v-text-field
      :append-icon="showFavChildhoodnamed ? 'mdi-eye' : 'mdi-eye-off'"
      @click:append="showFavChildhoodnamed = !showFavChildhoodnamed"
      :type="showFavChildhoodnamed ? 'text' : 'password'"
      name="favchildhoodname"
      label=""
      v-model="favchildhoodname"
      outlined
      id="favchildhoodname"
      aria-label="What is the name of your favorite childhood friend?" 

    ></v-text-field>

    <v-spacer></v-spacer>

    <!-- <nde-button color="primary" block @click="reset" class="mb-3">Continue</nde-button>

    <nde-button color="error" block @click="backToLogin">Back to Login</nde-button> -->

    <v-row>
      <v-col class="d-flex justify-space-between">
        <nde-button class="nde-auth-default-btn pa-6" @click="backToLogin">Back</nde-button>
        <nde-button 
          color="primary" 
          :loading="isSubmitting" 
          type="submit" 
          class="nde-auth-primary-btn pa-6" 
          @click="reset">Submit</nde-button
          >
      </v-col>
    </v-row>
  </v-form>
  </div>
</template>

<script>
import NdeAuthLayout from '../../shared/layouts/NdeAuthLayout.vue';
import NdeButton from '../../components/Button/NdeButton.vue';

export default {
  layout: NdeAuthLayout,
  data() {
    return {
      childhoodname: '',
      favchildhoodname: '',
      error: '',
      isSubmitting: false,

      showChildhoodnamed:false,
      showFavChildhoodnamed: false

    };
  },

  computed: {},

  methods: {
    reset() {},
    backToLogin() {
      location.href = '/login';
    },
  },

  props: {
    resetUri: {
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

.nde-auth-form-label {
  margin-bottom: 5px;
}
</style>