<template>
  <div>
    <v-list class="nde-progams-list">
      <v-list-item-group>
        <div v-for="(program, i) in programs" :key="i">
          <v-list-item
            v-if="(program.module == 'ESD' || program.module == 'Parent Profile') && program.subProfiles.length == 0 && !program.meta.has_key_search && program.name != 'Administration' && program.name != 'ESDQA' && program.name != 'Dashboard' && program.name != 'Report' && program.name != 'Custom On-Demand Report'"
            :link="isMultiTask" :href="isMultiTask ? '/search-profile?profileId=' + program.id : ''"
            @click.prevent="gotoPage('/search-profile?profileId=' + program.id)">
            <v-list-item-content>
              <v-list-item-title v-html="program.name" />
            </v-list-item-content>
          </v-list-item>

          <v-menu
            v-if="program.subProfiles.length == 0 && (program.module == 'ESD' || program.module == 'Parent Profile') && program.name != 'Report' && program.name != 'Custom On-Demand Report'"
            close-on-click :offset-x="!isMobile" :offset-y="isMobile">
            <template v-slot:activator="{ on, attrs }">
              <v-list-item v-bind="attrs" v-on="on" @click="selectSubProgram(program)">
                <v-list-item-title v-html="program.name" />
                <v-list-item-icon>
                  <v-icon v-if="!isMobile">mdi-chevron-right</v-icon>
                  <v-icon v-if="isMobile">mdi-chevron-down</v-icon>
                </v-list-item-icon>
              </v-list-item>
            </template>
            <v-list-item v-if="program.meta.has_key_search" class="nde-program-item" :link="isMultiTask"
              :href="isMultiTask ? '/search-profile?profileId=' + program.id : ''"
              @click.prevent="gotoPage('/search-profile?profileId=' + program.id)">
              <v-list-item-title v-html="program.meta.key_search_label" />
            </v-list-item>
          </v-menu>

          <v-menu
            v-if="(program.module == 'ESD' || program.module == 'Parent Profile') && program.subProfiles.length > 0 && program.name != 'Report' && program.name != 'Custom On-Demand Report'"
            close-on-click :offset-x="!isMobile" :offset-y="isMobile">
            <template v-slot:activator="{ on, attrs }">
              <v-list-item v-bind="attrs" v-on="on" @click="selectSubProgram(program)">
                <v-list-item-title v-html="program.name" />
                <v-list-item-icon>
                  <v-icon v-if="!isMobile">mdi-chevron-right</v-icon>
                  <v-icon v-if="isMobile">mdi-chevron-down</v-icon>
                </v-list-item-icon>
              </v-list-item>
            </template>
            <v-list-item v-if="program.module == 'ESD'" class="nde-program-item" :link="isMultiTask"
              :href="isMultiTask ? '/search-profile?profileId=' + program.id : ''"
              @click.prevent="gotoPage('/search-profile?profileId=' + program.id)">
              <v-list-item-title v-html="program.meta.key_search_label" />
            </v-list-item>
            <nde-dashboard-programs :programs="program.subProfiles" @onSelected="selectedItem">
            </nde-dashboard-programs>
          </v-menu>
        </div>
      </v-list-item-group>
    </v-list>

  </div>
</template>

<script>
export default {
  name: 'nde-dashboard-programs',
  data() {
    return {
      selectedProgram: null
    };
  },
  props: {
    programs: {
      type: Array,
      required: false,
      default: [],
    },
  },
  computed: {
    isMobile() {
      return window.screen.width < 768
    }
  },
  methods: {
    selectProgram(program) {
      this.$store.commit('setCurrentProgram', program);
      if (this.selectedProgram) {
        this.$emit('onSelected', [
          { id: program.meta.id, name: program.meta.key_search_label },
          { id: this.selectedProgram.id, name: this.selectedProgram.name },
        ]);
      } else {
        this.$emit('onSelected', [{ id: program.id, name: program.name }]);
      }
    },

    selectSubProgram(program) {
      this.selectedProgram = program;
    },

    selectedItem(data) {
      this.$emit('onSelected', [
        ...data,
        { id: this.selectedProgram.id, name: this.selectedProgram.name },
      ]);
    },
  },
};
</script>

<style scoped lang="scss">
.nde-program-item {
  background: white;
}

.v-list-item--link,
.v-list-item--active {
  &:hover {
    background-color: #e5e5e5 !important
  }
}
</style>

