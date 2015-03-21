<template>
  <v-treeview
    selectable
    :items="treeviewProfiles"
    item-text="label"
    v-model="selectedPrograms"
    selection-type="independent"
    open-all
    @input="checkboxProgram"
    class="row"
  >
  <template v-slot:label="{ item }">
    <div class="v-treeview-node__label">{{ item.label }}</div>
  </template>
</v-treeview>	
</template>

<script>
import { cloneDeep } from "lodash";

export default {
  name: 'NdeProgramTreeview',
  props:{
    programs: {
      type: Array,
      default: () => [],
    },
    value: {
      type: Array,
      default: () => [],
    }
  },
  data() {
    return {
      refProfiles: {},
      treeviewProfiles: [],
      selectedPrograms: [],
      oldSelectedPrograms: [],
    }
  },
  mounted() {
    this.getMappingDataTreeviewProfiles();
    this.selectedPrograms = [...this.value];
    this.makeProgram3Columns();
  },
  methods: {
    checkboxProgram() {
      this.makeProgram3Columns();
      let isChecked = true;
      let profileId = this.selectedPrograms[this.selectedPrograms.length -1];
      // Uncheck
      if (this.oldSelectedPrograms.length > this.selectedPrograms.length) {
        isChecked = false;
        const objectSelectedPrograms = {};
        this.selectedPrograms.forEach(id => { objectSelectedPrograms[id] = id; });
        for (const id of this.oldSelectedPrograms) {
          if (!objectSelectedPrograms[id]) {
            profileId = id;
            break;
          }
        }
      }
      this.checkboxProfile(profileId, isChecked, profileId);
      setTimeout(() => {
        this.oldSelectedPrograms = cloneDeep(this.selectedPrograms);
        this.$emit("input",  this.oldSelectedPrograms);
      });
    },
    getMappingDataTreeviewProfiles() {
      const refProfiles = {};
      const treeviewProfiles = [];
      for (const dataProgram of this.programs) {
        const { id, display_parent } = dataProgram;
        if (!refProfiles[id]) {
          refProfiles[id] = {
            ...dataProgram,
            children: [],
            disabled: false,
          }
        } else {
          Object.assign(refProfiles[id], {
            ...dataProgram,
          });
        }
        if (!display_parent) {
          refProfiles[id].disabled = false;
          treeviewProfiles.push(refProfiles[id]);
          continue;
        }
        if (!refProfiles[display_parent]) {
          refProfiles[display_parent] = {
            ...dataProgram,
            children: [],
            disabled: false,
          }
        }
        refProfiles[display_parent].children.push(refProfiles[id]);
      }
      this.refProfiles = refProfiles;
      (this.selectedPrograms || []).forEach(profileId => {
        this.checkboxProfile(profileId, true, profileId);
      });
      this.treeviewProfiles = treeviewProfiles;
      this.oldSelectedPrograms = cloneDeep(this.selectedPrograms);
    },
    checkboxProfile(id, isChecked = true, checkboxProfileId, isSetChildren = true) {
      if (!this.refProfiles[id]) {
        return;
      }
      const { display_parent } = this.refProfiles[id];
      if (isChecked && display_parent && display_parent !== checkboxProfileId) {
        this.checkboxProfile(display_parent, isChecked, checkboxProfileId, false);
      }
      if (isSetChildren) {
        this.refProfiles[id].children.forEach(dataProfile => {
          this.checkboxProfile(dataProfile.id, isChecked, checkboxProfileId, !isChecked);
        });
      }

      if (display_parent && isChecked && !this.selectedPrograms.includes(display_parent)) {
        this.selectedPrograms.push(display_parent);
      }

      if (!isChecked && this.selectedPrograms.includes(id)) {
        this.selectedPrograms = this.selectedPrograms.filter(profileId => profileId != id);
      }
      // this.refProfiles[id].disabled = !isChecked;
      if (
        (!display_parent) ||
        (display_parent && this.selectedPrograms.includes(display_parent))
        )
        {
          this.refProfiles[id].disabled = false;
        }
      return;
    },
    makeProgram3Columns() {
      this.$nextTick(function () {
        var treeviewItem = document.getElementsByClassName('v-treeview-node');
        for (let i = 0; i < treeviewItem.length; i++) {
          treeviewItem.item(i).classList.add('col-sm-6');
          treeviewItem.item(i).classList.add('col-md-4');
          treeviewItem.item(i).classList.add('col-12');
        }
      });
    }
  },
  watch: {
    value() {
      this.selectedPrograms = [...this.value];
    },
  },
};
</script>

<style>
button.v-icon.notranslate.v-treeview-node__checkbox.v-icon--link.mdi.mdi-checkbox-marked.theme--light.accent--text {
    color: #3D769E !important;
    caret-color: #3D769E !important;
}
</style>
