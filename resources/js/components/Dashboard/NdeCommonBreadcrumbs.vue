<template>
  <div class="align-end">
    <span class="text-h5">{{ programEdit }} / </span>
    <span class="text-subtitle-2">{{tabName}}</span>
  </div>
</template>

<script>
import { mapState } from "vuex";
export default {
  computed: {
    ...mapState(["selectedProgramTree"]),
    programEdit(){
        if(this.selectedProgramTree[1].name.length > 30){
            const shorterName = this.selectedProgramTree[1].name.split("");
            return (shorterName.splice( 0 , 29)).toString().replace(/,/g, '') + "..."
        }
        return this.selectedProgramTree[1].name
    },
  },
  props: {
      tabName: {
          type: String
      }
  }
};
</script>
