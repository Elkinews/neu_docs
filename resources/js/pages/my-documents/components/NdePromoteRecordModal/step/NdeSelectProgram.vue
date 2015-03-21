<template>
  <div class="nde-select-program-step">
    <div class="select-program__section">
      <div class="nde-select-program-step__sub_title">
        <h6 class="font-weight-black">Select Program</h6>
      </div>
      <nde-select
        class="nde-select-program-step__selectbox"
        :items="programs"
        @onSelect="onSelect"
        v-model="selectedProgram"
      />
    </div>
  </div>
</template>

<script>
import NdeSelect from '@components/Inputs/NdeSelect.vue';
import NdeTextField from '@components/Inputs/NdeTextField.vue';
export default {
  data() {
    return {
      selectedProgram: '',
    };
  },
  props: {
    programs: {
      type: Array,
      default: () => [],
    },
  },
  watch: {
    programs: function(newVal){
      console.log("selected program: ", newVal[0])
      this.selectedProgram = newVal[0];
      this.$emit('onSelectProgram', newVal[0]?.value);
    }
  },
  methods: {
    onSelect(value) {
      console.log(value);
      this.$emit('onSelectProgram', value);
    },
  },
  components: {
    NdeSelect,
    NdeTextField,
  },
};
</script>
<style lang="scss" scoped>
.nde-select-program-step {
  label {
    margin-bottom: 5px;
  }

  .de-select-program-step__sub_title {
    margin-bottom: 40px;
  }
}
</style>
