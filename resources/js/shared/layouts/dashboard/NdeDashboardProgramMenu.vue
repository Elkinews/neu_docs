<template>
  <v-menu offset-y close-on-click v-model="isOpen" max-width="fit-content" v-if="!standalone">
    <template v-slot:activator="{ on, attrs }" v-if="visibleProgramBtn">
      <a tabindex="0" v-bind="attrs" v-on="on" role="" class="ml-6 programs-button primary white--text d-flex align-center" aria-label="Program navigation Landmark" aria-haspopup="false">
        <v-icon color="white" class="mr-2">mdi-view-dashboard-outline</v-icon>
          Programs
          <v-icon color="white">mdi-menu-down</v-icon>
      </a>
    </template>
    <nde-dashboard-programs
      :programs="programs"
      style="max-height: 80vh"
      @onSelected="selectedItem"
    >
    </nde-dashboard-programs>
  </v-menu>
</template>
<script>
import { mapState } from 'vuex';
import NdeDashboardPrograms from './NdeDashboardPrograms.vue';

export default {
  name: 'NdeDashboardProgramMenu',
  components: {
    NdeDashboardPrograms,
  },
  props: {
    type: {
      type: String,
      required: false,
      default: 'dashboard',
    },
    programsJson: {
      type: String,
      required: false,
      default: '',
    },
    profileId: {
      type: String,
    },
    standalone: {
      type: Boolean,
      required: false,
      default: false
    }
  },
  data() {
    return {
      programs: [],
      isOpen: false,

      isLoadedPrograms: false,
      isLoadingPrograms: false,
    };
  },

  methods: {
    selectedItem(data) {
      this.$store.commit('setSelectedProgramTree', data);
      this.handleExpand();
    },
    handleExpand() {
      this.isOpen = false;
    },
    getCurrentProfile(profiles, prev) {
      for (let i = 0; i < profiles.length; i++) {
        if (profiles[i].id === this.profileId) {
          this.$store.commit('setCurrentProgram', profiles[i]);

          if (profiles[i].meta.has_key_search) {
            prev.push(
              { id: profiles[i].id, name: profiles[i].name },
              { id: profiles[i].meta.id, name: profiles[i].meta.key_search_label },
            );
          } else {
            prev.push({ id: profiles[i].id, name: profiles[i].name });
          }
          prev = prev.filter((o) => o);
          this.$store.commit('setSelectedProgramTree', prev.reverse());
        } else {
          if (profiles[i].subProfiles) {
            let tempPrev = [...prev];
            tempPrev.push({ id: profiles[i].id, name: profiles[i].name });
            this.getCurrentProfile(profiles[i].subProfiles, tempPrev);
          }
        }
      }
    }
  },

  async mounted() {
    if (this.type === 'dashboard') {
      this.programs = JSON.parse(this.programsJson);
      this.$store.commit('setPrograms', this.programs);
      this.getCurrentProfile(this.programs, []);
    }
  },

  computed: {
    ...mapState(['visibleProgramBtn']),
  },
}
</script>

<style lang="scss" scoped>
.programs-button {
  text-decoration: none;
  min-width: 4rem;
  height: 2.25rem;
  line-height: 2.25rem;
  border-radius: 0.25rem;
  padding: 0 1rem;
  font-size: .875rem;
  font-weight: 500;
}
</style>
