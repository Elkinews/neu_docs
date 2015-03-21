<template>
  <div role="alert" class="cookie-text" v-if="!isEnabled">{{ message }}</div>
</template>

<script>
export default {
  data() {
    return {
      isEnabled: true,
      message: ''
    }
  },
  mounted() {
    if (navigator.cookieEnabled) {
      this.isEnabled = true;
      this.message = '';
    } else {
      this.isEnabled = false;
      this.message = 'PLEASE ENABLE COOKIES';
    }
  }
  
};
</script>

<style scoped lang="scss">
.cookie-text {
  color: $errorColor;
  padding: 10px 0px;
}
</style>
