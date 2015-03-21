export default {
  computed: {
    isRoleCREATERECORD() {
      return this.userLoginRoles().includes('CREATERECORD');
    },
    userLoginFullName() {
      return this.getDataCookieRole() ?.full_name;
    },
    userLoginEmail() {
      return this.getDataCookieRole() ?.email;
    },
    userLoginUserName() {
      return this.getDataCookieRole() ?.username;
    },
    settingInfo() {
      return this.getDataCookieRole() ?.settings;
    },
    isMultiTask() {
      if (this.getDataCookieRole() ?.settings ?.multitask == 'ON') return true;
      return false;
    }
  },
  methods: {
    getMessageResult(result) {
      let errors = result ?.data ?.data ?.response ?.data ?.errors || result ?.data ?.message ?.response ?.data ?.errors;
      if (Array.isArray(errors)) {
        errors = errors.join(', ');
      }
      return errors ||
        result ?.data ?.data ?.response ?.data ?.message ||
        result ?.data ?.data ?.response ?.data ?.msg ||
        result ?.data ?.data ?.message ||
        result ?.data ?.error;
    },
    userLoginRoles() {
      const cookieRoles = this.$cookie.get('roles');
      if (!cookieRoles) return [];
      const obj = JSON.parse(atob(cookieRoles)) || {};

      return Array.isArray(obj ?.roles) ? obj.roles : [];
    },
    getDataCookieRole() {
      try {
        return JSON.parse(atob(this.$cookie.get('roles'))) || {}
      } catch (e) {
        return {};
      }

    },
    checkRoleByName(roleName = '') {
      return this.userLoginRoles().includes(roleName);
    },
    gotoPage(url) {
      this.$store.commit('setShowProgressAPI', true);
      location.href = url;
    }
  },
}
