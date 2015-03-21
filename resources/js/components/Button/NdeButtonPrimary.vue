<template>
  <v-btn :disabled="disabled" :style="styles" @click="click" v-bind="$attrs">
    <span>{{ title }}</span>
  </v-btn>
</template>

<script>
export default {
  name: 'NdeButtonPrimary',
  props: {
    title: {
      type: String,
      default: 'Upload',
    },
    styles: {
      type: Object,
      default: () => {},
    },
    disabled: {
      type: Boolean, 
      default: false
    }
  },
  methods: {
    click() {
      this.$emit('click');
    },
  },
};
</script>

<style scoped lang="scss">
.v-btn {
    text-transform: unset !important;

    &.btn-hover {
       box-shadow: rgba(0,0,0,0.35) 0px 5px 15px !important;
    }
}

button {
  background-color: $primaryColor !important;
  border-color: $primaryColor !important;
  border-radius: 8px;
  width: 17rem;
  height: 2.5rem !important;
  box-shadow: none;
  
  span {
    color: #ffffff;
    text-transform: capitalize;
    font-family: 'Poppins';
    font-style: normal;
    font-weight: 500;
    font-size: 0.875rem;
    line-height: 1.5rem;
  }
}
</style>
