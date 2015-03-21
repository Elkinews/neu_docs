<template>
    <v-card width="fit-content" class="nde-tabs d-flex align-center">
		<template v-for="(administration, index) in administrations">
			<span v-if="index !== 0 && administration.show && !isMobile" v-bind:key="index + '_divider'" class="nde-tab-divider"></span>
			<nde-tab-item
        v-if="administration.show"
				v-bind:key="index"
				:icon="administration.icon"
				:text="administration.text"
				:isActive="isActive(administration.path)"
				@click="goHref(administration.path)"
        :ariaLevel="index+1"
				class="mr-2"
			></nde-tab-item>

		</template>
    </v-card>
</template>

<script>
import NdeTabItem from "./NdeTabItem.vue";
export default {
  name: 'NdeTabSetting',
  components: {
    NdeTabItem,
  },
  data() {
    return {
        administrations: [
        {
          icon: 'mdi-shield-outline',
          text: 'General',
          path: '/administration/general',
          show: true,
        },
        {
          icon: 'mdi-account-check-outline',
          text: 'Manage Users',
          path: '/administration/manageUsers',
          show: false,
        },
        {
          icon: 'mdi-cog-outline',
          text: 'OneCase Control',
          path: '/administration/oneCaseControl',
          show: false,
        },
        {
          icon: 'mdi-view-agenda-outline',
          text: 'Predefined Tab',
          path: '/administration/predefined-tab',
          show: false,
        },
        // {
        //   icon: 'mdi-email-outline',
        //   text: 'Email Intake',
        //   path: '/administration/emaiIntake',
        //   show: false,
        // },
      ]};
  },

  methods: {
    isActive(path) {
      return window.location.pathname.indexOf(path) === 0;
    },
    goHref(path){
      location.href= path;
    }
  },
  mounted(){
    let userLoginRoles = this.userLoginRoles();
    this.administrations.map((tab) =>{
      if(tab.text === 'Manage Users' && userLoginRoles.includes('USERMANAGEMENT')){
        tab.show = true
      };
      if(tab.text === 'OneCase Control' && userLoginRoles.includes('ONECASENOTES') && !this.isMobile){
        tab.show = true
      };
      if(tab.text === 'Predefined Tab' && userLoginRoles.includes('MODIFYPREDTAB') && !this.isMobile){
        tab.show = true
      };
      if(tab.text === 'Email Intake' && userLoginRoles.includes('GLOBALSETTINGS') && !this.isMobile){
        tab.show = true
      }
      if(tab.text === 'Hide Show Column' && userLoginRoles.includes('GLOBALSETTINGS') && !this.isMobile){
        tab.show = true
      }

      if(tab.text === 'General' && this.isMobile) {
        tab.show = false
      }
    })
  }
};
</script>

<style scoped lang="scss">
.nde-tabs {
  padding: 0.625rem;
  .nde-tab-divider {
    height: 1rem;
    width: 0.063rem;
    background: #eae7e4;
    margin: 0 5px;
  }
}
</style>
