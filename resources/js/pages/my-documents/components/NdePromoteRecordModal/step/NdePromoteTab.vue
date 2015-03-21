<template>
  <v-row>
    <v-col cols="12" sm="12">

      <div>
        <p class="text-title">Select Tab</p>
        <br/>
        <h6 class="font-weight-black">Record does not exist, will be created.</h6>
      </div>
      <nde-select
        :items="allTabs"
        label="Select a predefined Tab"
        solo
        item-text="name"
        item-value="id"
        v-model="selectedTab"
        @onSelect="onSelect"
      ></nde-select>
    </v-col>
  </v-row>
</template>

<script>
import NdeSelect from '@components/Inputs/NdeSelect.vue';
export default {
  components: {
    NdeSelect
  },
  data() {
    return {
      selectedTab: '',
      //allTabs: [],
    };
  },
  props: {
    tabs: {
      type: Object,
      required: true,
    },
  },
  methods: {
    onSelect(value){
      this.$emit('onChange', value);
    },
    getAllTabs(){
      return this.tabs.predefinedTabs.map((tab) => {
        return tab
      })
    }
  },
  computed: {
    allTabs(){
      return this.getAllTabs()
    }
  },
  watch: {
    selectedTab: function (val) {
      if (val) {
        this.$emit('onChange', val);
      }
    },
  },
};
</script>

<style scoped lang="scss">
.text-title {
  font-size: 0.9rem;
  color: black;
  font-weight: bold;
}
</style>
