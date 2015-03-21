<template>
  <v-card elevation="2" class="mx-auto mt-3 pa-6" style="width: 100%">
    <!-- <validation-observer
    ref="observer"
    v-slot="{ invalid }"
> -->
    <p v-if="!questions">You don't have any questions.</p>
    <v-form @submit="save" ref="authform" v-if="questions">
      <v-row>
        <v-col>
          <!-- <validation-provider
              v-slot="{ errors }"
              name="Name"
              rules="required"
          > -->
          <label for="question_1" class="mb-2">Question 1</label>
          <v-select
            :items="questions"
            label="Question 1"
            solo
            dense
            attach
            item-text="question"
            item-value="id"
            name="question_1"
            id="question_1"
            :rules="questionRules1"
            v-model="q_1"
          ></v-select>
          <v-text-field
            name="qa_1"
            dense
            solo
            id="qa_1"
            required
            :rules="requiredRules"
            v-model="qa_1"
            :append-icon="showAws1 ? 'mdi-eye' : 'mdi-eye-off'" 
            :type="showAws1 ? 'text' : 'password'"
            @click:append="showAws1 = !showAws1"
          ></v-text-field>
          <!-- </validation-provider> -->
        </v-col>
      </v-row>

      <v-row>
        <v-col>
          <label for="question_2" class="mb-2">Question 2</label>
          <v-select
            :items="questions"
            label="Question 2"
            solo
            dense
            attach
            item-text="question"
            item-value="id"
            name="question_2"
            id="question_2"
            :rules="questionRules2"
            v-model="q_2"
          ></v-select>
          <v-text-field
            name="qa_2"
            dense
            solo
            id="qa_2"
            required
            :rules="requiredRules"
            v-model="qa_2"
            :append-icon="showAws2 ? 'mdi-eye' : 'mdi-eye-off'" 
            :type="showAws2 ? 'text' : 'password'"
            @click:append="showAws2 = !showAws2"
          ></v-text-field>
        </v-col>
      </v-row>

      <v-row>
        <v-col>
          <label for="question_3" class="mb-2">Question 3</label>
          <v-select
            :items="questions"
            label="Question 3"
            solo
            dense
            attach
            item-text="question"
            item-value="id"
            name="question_3"
            id="question_3"
            :rules="questionRules3"
            v-model="q_3"
          ></v-select>
          <v-text-field
            name="qa_3"
            dense
            solo
            id="qa_3"
            required
            :rules="requiredRules"
            v-model="qa_3"
            :append-icon="showAws3 ? 'mdi-eye' : 'mdi-eye-off'" 
            :type="showAws3 ? 'text' : 'password'"
            @click:append="showAws3 = !showAws3"
          ></v-text-field>
        </v-col>
      </v-row>

      <nde-button color="primary" :loading="isSubmitting" type="submit" block class="mb-3 mt-3"
        >Save</nde-button
      >

      <nde-button color="error" block @click="clear">Clear</nde-button>
    </v-form>
    <!-- </validation-observer> -->
  </v-card>
</template>

<script>
import { extend, ValidationObserver, ValidationProvider, setInteractionMode } from 'vee-validate';
import { required, digits, email, max, regex } from 'vee-validate/dist/rules';

import NdeAuthLayout from '../../shared/layouts/NdeAuthLayout.vue';
import NdeButton from '../../components/Button/NdeButton.vue';
import NdeDashboardLayout from '../../shared/layouts/dashboard/NdeDashboardLayout.vue';

extend('required', {
  ...required,
  message: 'Answers cannot be empty.',
});

export default {
  layout: NdeAuthLayout,
  data() {
    return {
      error: '',
      isSubmitting: false,
      q_1: '',
      q_2: '',
      q_3: '',
      qa_1: '',
      qa_2: '',
      qa_3: '',
      requiredRules: [(v) => !!v || 'Answers cannot be empty.'],
      questionRules1: [
        (v) => !!v || 'Select a question',
        (v) => !(v == this.q_2 || v == this.q_3) || 'Questions must be different.',
      ],
      questionRules2: [
        (v) => !!v || 'Select a question',
        (v) => !(v == this.q_1 || v == this.q_3) || 'Questions must be different.',
      ],
      questionRules3: [
        (v) => !!v || 'Select a question',
        (v) => !(v == this.q_2 || v == this.q_1) || 'Questions must be different.',
      ],

      showAws1: false,
      showAws2: false,
      showAws3: false,
    };
  },

  computed: {},

  mounted() {
    window.document.title = 'NeuDocs Enterprise Set Questions';
  },

  methods: {
    async save(e) {
      e.preventDefault();
      var validate = this.$refs.authform.validate();
      // this.$refs.observer.validate();
      console.log(validate);
      if (!validate) return;

      this.isSubmitting = true;
      this.error = '';

      const postData = [
        {
          id: this.q_1,
          answer: this.qa_1,
        },
        {
          id: this.q_2,
          answer: this.qa_2,
        },
        {
          id: this.q_3,
          answer: this.qa_3,
        },
      ];

      console.log(postData);

      const resultSave = await this.$store.dispatch('callAPI', {
        url: '/accountReplaceSecurityAnswersOauth',
        method: 'post',
        data: {
          answers: postData,
        },
      });

      console.log(resultSave);

      if (resultSave.message == 'success') {
        this.$cookie.delete('roles');
        this.$cookie.delete('nde_frontend_session');
        this.$cookie.delete('XSRF-TOKEN');
        this.$cookie.delete('starrsProfiles');
        location.href = '/dashboard';
      } else {
        this.isSubmitting = false;
      }
    },

    clear() {
      this.$refs.authform.reset();
    },
  },

  props: {
    questions: {
      type: Array,
      required: true,
    },
  },

  components: {
    NdeButton,
    ValidationObserver,
    ValidationProvider,
    NdeDashboardLayout,
  },
};
</script>

<style scoped lang="scss">
.nde-auth-form {
  min-width: 300px;
}

.nde-auth-resetpassword {
  display: flex;
  justify-content: flex-end;

  a {
    color: #a80000;
  }
}

.auth-error {
  color: $errorColor !important;

  a {
    color: $errorColor !important;
  }
}
</style>
