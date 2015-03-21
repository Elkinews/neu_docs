<template>
  <div class="nde-captcha d-flex justify-spance-between align-center">
    <iframe :src="`/getcaptchaform`" frameborder="0" id="captchaframe" height="100%" width="100%"></iframe>

    <div class="ml-2 mt-4">
      <nde-button fab x-small @click="reset" class="mb-2"><v-icon>mdi-reload</v-icon></nde-button>

      <nde-button fab x-small @click="listen"><v-icon>mdi-volume-high</v-icon></nde-button>
    </div>
  </div>
</template>

<script>
import NdeButton from "../Button/NdeButton.vue";

export default {
  components: { NdeButton },
  data() {
    return {
      captchaContent: "",
    };
  },

  mounted() {
    const iframe = document.getElementById("captchaframe");
    console.log(iframe);
    iframe.onload = function () {
      const iWindow = iframe.contentWindow;
      const iDocument = iWindow.document;

      // accessing the element
      const element = iDocument.getElementById("NdeCaptcha_CaptchaIconsDiv");
      element.style.setProperty("display", "none", "important");

      const elementInput = iDocument.getElementById("CaptchaCode");
      // elementInput.style.setProperty("display", "none", "important");
    };
  },

  methods: {
    getCode() {
      const iframe = document.getElementById("captchaframe");
      const iWindow = iframe.contentWindow;
      const iDocument = iWindow.document;

      // accessing the element
      const element = iDocument.getElementById("CaptchaCode");
      console.log(element);
      console.log(element.value);
    },

    reset() {
      // this.$cookie.delete('nde_frontend_session');
      // this.$cookie.delete('XSRF-TOKEN');
      var cookies = document.cookie.split(";");

      for (var i = 0; i < cookies.length; i++) {
          var cookie = cookies[i];
          var eqPos = cookie.indexOf("=");
          var name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
          document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
      }

      const iframe = document.getElementById("captchaframe");
      const iWindow = iframe.contentWindow;
      // const iDocument = iWindow.document;

      // // accessing the element
      // const element = iDocument.getElementById("NdeCaptcha_ReloadLink");
      // element.click();

      // iWindow.location.reload();

      window.location.reload();
    },

    listen() {
      const iframe = document.getElementById("captchaframe");
      const iWindow = iframe.contentWindow;
      const iDocument = iWindow.document;

      // accessing the element
      const element = iDocument.getElementById("NdeCaptcha_SoundLink");
      element.click();
    },
    getValues() {
      const iframe = document.getElementById("captchaframe");
      const iWindow = iframe.contentWindow;
      const iDocument = iWindow.document;

      // accessing the element
      const BDC_UserSpecifiedCaptchaId = iDocument.getElementById(
        "BDC_UserSpecifiedCaptchaId"
      ).value;
      const BDC_VCID_NdeCaptcha = iDocument.getElementById(
        "BDC_VCID_NdeCaptcha"
      ).value;
      const BDC_BackWorkaround_NdeCaptcha = iDocument.getElementById(
        "BDC_BackWorkaround_NdeCaptcha"
      ).value;
      const BDC_Hs_NdeCaptcha =
        iDocument.getElementById("BDC_Hs_NdeCaptcha").value;
      const BDC_SP_NdeCaptcha =
        iDocument.getElementById("BDC_SP_NdeCaptcha").value;

      const _token =
        iDocument.getElementsByName("_token")[0].value;

      const CaptchaCode =
        iDocument.getElementById("CaptchaCode").value;

      return {
        BDC_UserSpecifiedCaptchaId,
        BDC_VCID_NdeCaptcha,
        BDC_BackWorkaround_NdeCaptcha,
        BDC_Hs_NdeCaptcha,
        BDC_SP_NdeCaptcha,
        _token,
        CaptchaCode
      };
    },

    submitForm() {
      var xhr = new XMLHttpRequest();
      xhr.open("POST", "/doverifycaptcha");
      let me = this;
      xhr.onload = function (event) {
        console.log(event)
        console.log(event.target.response)
        const resdata = JSON.parse(event.target.response);
        console.log(resdata);
        // alert("Success, server responded with: " + event.target.response); // raw response
        if (resdata.data.verify == true) {
          me.$emit('onSuccess');
        } else {
          me.reset();
          me.$emit('onError');
        }
      };
      // or onerror, onabort

      const iframe = document.getElementById("captchaframe");
      const iWindow = iframe.contentWindow;
      const iDocument = iWindow.document;

      var formData = new FormData(iDocument.getElementById("captcha_form"));
      xhr.send(formData);
    },

    setCode(code) {
      const iframe = document.getElementById("captchaframe");
      const iWindow = iframe.contentWindow;
      const iDocument = iWindow.document;
      const element = iDocument.getElementById("CaptchaCode");
      element.value = code;
      element.blur();
    }
  },
};
</script>

<style scoped lang="scss">
.nde-captcha {}
</style>