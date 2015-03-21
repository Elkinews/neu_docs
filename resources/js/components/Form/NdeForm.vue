<template>
  <v-form ref="form" v-model="valid" lazy-validation @submit.prevent="submit">
    <v-container>
      <slot name="ndeFormBody"></slot>
    </v-container>
  </v-form>
</template>

<script>
export default {
  name: 'NdeForm',
  data: () => ({
    valid: true,
  }),
  methods: {
    submit() {
      console.log('Will update later');
    },
  },
};
</script>
<style scoped lang="scss">
:v-deep .v-text-field--outlined {
  .v-input__slot {
    min-height: 2.5rem;
    input {
      font-weight: 500;
      font-size: 0.875rem;
      line-height: 1.5rem;
      color: #343c42;
    }
    .v-input__append-inner {
      margin-top: 0.313rem;
    }
    .v-input__icon--clear {
      margin-right: 0.313rem;
      button {
        font-family: 'Poppins';
        font-style: normal;
        font-weight: 400;
        font-size: 0.875rem;
        line-height: 1.5rem;
        color: #c32c39 !important;
      }
    }
  }
  .v-input__append-outer {
    margin-top: 0.313rem;
    margin-left: 0.125rem;
    .v-input__icon--append-outer {
      i {
        font-weight: 400;
        font-size: 0.875rem;
        line-height: 1.5rem;
        color: #343c42;
      }
    }
  }
}
:v-deep .v-messages__message {
  color: $errorColor !important;
}
</style>
