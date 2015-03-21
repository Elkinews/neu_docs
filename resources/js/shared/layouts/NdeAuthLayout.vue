<template>
  <v-app>
    <nde-overlay-progress-api />
    <div id="nde-auth-layout" class="nde-auth-layout d-flex">
      <div class="nde-auth-layout-left">
        <div class="d-flex justify-space-between align-center">
          <img alt="NeuDocs Enterprise Logo" src="images/neubus_logo_masthead.png" />
          <span class="nde-logo-title">neuDocs Enterprise <b>Version 5.0</b></span>
        </div>

        <div class="nde-auth-layout-container d-flex flex-column justify-center">
          <nde-cookie-enable-alert />
          <slot />
        </div>

        <div class="nde-auth-layout-allright-container">
          <p class="nde-auth-layout-allright">
            All Right Reserved © 2000 - 2022. Powered by Neubus Inc.
          </p>
          <div class="nde-auth-layout-right-contact-mobile">
            <div class="nde-auth-layout-right-contact-for">For General Inquries Contact</div>
            <div>
              <a class="contact-mail-addr" href="mailto:support@neubus.com">support@neubus.com</a>
            </div>
          </div>
        </div>
      </div>

      <div class="nde-auth-layout-right">
        <div class="nde-auth-layout-right-contact d-flex align-center">
          <div>
            <div class="nde-auth-layout-right-contact-for">For General Inquries Contact</div>
            <div>
              <a class="contact-mail-addr" href="mailto:support@neubus.com">support@neubus.com</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </v-app>
</template>

<script>
import NdeCookieEnableAlert from '../../components/Common/NdeCookieEnableAlert.vue';
import NdeOverlayProgressApi from '../../components/Overlay/NdeOverlayProgressAPI.vue';
export default {
  components: { NdeCookieEnableAlert, NdeOverlayProgressApi },

  mounted() {
    this.$store.dispatch({ type: 'getENV' });
  }
};
</script>

<style scoped lang="scss">
.nde-auth-layout {
  height: 100vh;
  width: 100vw;

  .nde-auth-layout-right-contact-for {
    font-size: 0.75rem;
  }

  .contact-mail-addr {
    font-size: 1.2rem;
    color: black;
  }

  &-left {
    width: 50%;
    // height: 100vh;
    padding: 65px 65px 30px;
    position: relative;
    overflow-y: auto;
    flex-direction: column;
    justify-content: space-between;
    align-items: stretch;
    display: flex;

    @media (max-width: 960px) {
      width: 100%;
      padding: 15px 15px 30px;
    }

    .nde-logo-title {
      font-size: 0.75rem;
    }

    .nde-auth-layout-allright-container {
      margin-top: 10px;
      @media (max-width: 960px) {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
      }
      .nde-auth-layout-allright {
        font-size: 0.75rem;
      }

      .nde-auth-layout-right-contact-mobile {
        display: none;

        @media (max-width: 960px) {
          display: block;
        }
      }
    }

    .nde-auth-layout-container {
      // width: 80%;

      @media (max-width: 960px) {
        width: 100%;
        padding-left: 0px;
      }
    }
  }

  &-right {
    width: 50%;
    height: 100vh;
    background: url(/images/loginBG1.png);
    background-size: cover;
    background-position: center;
    position: relative;

    @media (max-width: 960px) {
      display: none;
    }

    &-contact {
      position: absolute;
      left: 0;
      bottom: 0;
      right: 0;
      height: 112px;
      background: white;
      color: black;
    }
  }
}
</style>