<template>
  <v-simple-table>
    <template v-slot:default>
      <thead class="nde-table-previous-search-queries" v-if="isSearchHistory && localMPSQ?.length != 0">
        <tr >
            <th>Record Information</th>
            <th class="text-center">Date</th>
            <th class="text-center">Number of Times</th>
        </tr>
      </thead>
      <tbody v-if="isSearchHistory && localMPSQ?.length != 0">
        <tr v-for="item in NeuSearchHistoryResult.searches" :key="item.html" >
          <td class="text-left" style="padding: 0 30px 0 30px"><a href="#" class="red--text" @click="searchFile(item.html)" v-html="item.html"></a></td>
          <td>{{ item.updated_on }}</td>
          <td>{{ item.number_of_times }}</td>
        </tr>
      </tbody>
      <div v-if="isSearchHistory && localMPSQ?.length == 0" class="text-center">
        There are no searches saved.
      </div>

      <div class="d-flex justify-center" v-if="!isSearchHistory">
        <v-progress-circular indeterminate color="primary"></v-progress-circular>
      </div>
    </template>
  </v-simple-table>
</template>

<script>
import { mapState } from "vuex";

export default {
  name: "NdeTableMyPreviousSearchQueries",
  props: {
    items: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      searchData: [],
      localMPSQ: null
    }
  },

  computed: {
    ...mapState(["Neusearch", "NeuSearchHistoryResult", "isSearchHistory", 'currentControls', 'isSearchLoaded']),
  },
  watch: {
    NeuSearchHistoryResult: function(val){
      if(val.searches){
        this.localMPSQ = val.searches
      } else {
        this.localMPSQ = []
      }
    }
  },
  mounted() {
      this.$store.commit("setSearchHistoryData", {
        type: "getSearchHistoryOauth",
        data: {
          "profile_id": this.Neusearch['profile'],
        }
      });
      this.$store.dispatch({type: "generateSearchHistory"})
  },
  methods: {
    searchFile(searchDoc){
      let searchArry = searchDoc.split("<br />")
      searchArry.map((item) => {
        this.currentControls.map((item1) => {
        let keys = Object.keys(item1);
        let fieldName = keys[0];
        let searchFieldInfo = item1[fieldName];
        let searchFileLabel = searchFieldInfo.label
        if(searchFileLabel === item.split(" = ")[0]){
          this.searchData.push({
            key : fieldName,
            value:  item.split(" = ")[1],
            label:  item.split(" = ")[0]
          })
        }
      })
      })
      this.$store.commit('setNeusearch', {
        key: 'Searchitems',
        data: { item: this.searchData}
      });
      this.$store.dispatch({ type: 'searchImages'})
      this.$emit("searchFile", 0)
    }
  },
};
</script>

<style scoped lang="scss">
.nde-table-previous-search-queries {
  background: #ebeced;
  font-size: 0.75rem;
  th {
    color: #343c42 !important;
    text-align: center;
  }
}
tbody tr:nth-of-type(even) {
  background-color: #f7f7f7;
}
th,
td {
  font-family: "Poppins";
  font-style: normal;
  color: #343c42;
  text-align: center;

  a.red--text {
   color: #864744 !important
  }
}

</style>
