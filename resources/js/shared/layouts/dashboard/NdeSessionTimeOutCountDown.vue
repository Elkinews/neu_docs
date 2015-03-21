<template>
  <div class="nde-session-timeout-countdown">
    <div class="backdrop"></div>
    <div class="nde-session-container">
      <div class="nde-session-content">
        <h3>Session Timeout after {{ interval_min }}s</h3>

        <div class="d-flex mt-6 justify-space-between">
          <nde-button color="success" class="mr-6" @click="onContinue" :loading="isGettingRefreshToken">Continue</nde-button>
          <nde-button @click="logout" :disabled="isGettingRefreshToken">Logout</nde-button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import NdeButton from '../../../components/Button/NdeButton.vue';

export default {
  components: { NdeButton },
  data() {
    return {
      interval_min: 120,
      nIntervId: null,
      isGettingRefreshToken: false,
    };
  },

  props: {
    timeout: {
      required: true,
      type: Number,
      default: 120,
    }
  },

  mounted() {
    this.nIntervId = Date.now();
    this.interval_min = this.timeout;
    this.countDownTime();
  },

  methods: {
    countDownTime() {
      if (!this.nIntervId) {
        return;
      }
      if (this.interval_min <= 0) {
        return this.logout();
      }
      const countTime = Math.floor((Date.now() - this.nIntervId) / 1000);
      const nowCountTime = this.timeout - this.interval_min;
      if (nowCountTime < countTime) {
        this.interval_min = this.timeout - countTime;
      }
      setTimeout(() => {
        this.interval_min -= 1;
        this.countDownTime();
      }, 1000);
    },

    refresh: function () {;
      this.nIntervId = null;
      location.reload();
    },

    async logout() {
      this.nIntervId = null;
      this.$store.commit('setShowProgressAPI', true);
      const resultLogout = await this.$store.dispatch('callAPI', {
        url: '/endOauthSession',
        method: 'post',
        data: {},
      });
      this.$store.commit('setShowProgressAPI', false);
      this.$cookie.delete('roles');
      this.$cookie.delete('nde_frontend_session');
      this.$cookie.delete('XSRF-TOKEN');
      this.$cookie.delete('starrsProfiles');
      location.href = '/login';
      window.location.replace('/login');
      window.location.reload();
    },

    async onContinue() {
      this.nIntervId = null;
      this.isGettingRefreshToken = true;
      await this.$store.dispatch({ type: 'getAccesstokenRefresh' });
      this.isGettingRefreshToken = false;
      this.$emit("onContinue");
    }
  },

  watch: {
    timeout: function (val) {
      console.log(new Date(), 'this.interval_min = this.timeout:', this.timeout)
      this.interval_min = this.timeout;
    }
  }
};
</script>

<style lang="scss" scoped>
.nde-session-timeout-countdown {
  position: fixed;
  z-index: 1;
  width: 100vw;
  height: 100vh;

  .backdrop {
    position: fixed;
    width: 100vw;
    height: 100vh;
    background: #000;
    opacity: 0.5;
  }

  .nde-session-container {
    position: fixed;
    width: 100vw;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;

    .nde-session-content {
      padding: 30px;
      border-radius: 10px;
      background: white;
      width: 450px;
    }
  }
}
</style>
