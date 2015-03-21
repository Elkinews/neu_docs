<template>
  <div class="nde-view-document pa-6">
    <v-row>
      <v-col>
        <span @click="gotoSearchResults" class="nde-view-document-goback">
          <v-icon color="primary">mdi-menu-left</v-icon>
          <span>Search Results</span>
        </span>
      </v-col>
    </v-row>

    <v-row class="nde-view-document-currentdata">
      <v-col>
        <table>
          <thead>
            <tr>
              <td>File#</td>
              <td>Street Number</td>
              <td>City</td>
              <td>County Name</td>
              <td>State</td>
              <td>Date</td>
              <td>Disposition Date</td>
              <td></td>
            </tr>
          </thead>

          <tbody>
            <tr>
              <th>{{ currentViewDocumentData.file_number }}</th>
              <th>{{ currentViewDocumentData.street_number }}</th>
              <th>{{ currentViewDocumentData.city }}</th>
              <th>{{ currentViewDocumentData.county_name }}</th>
              <th>{{ currentViewDocumentData.state }}</th>
              <th>{{ currentViewDocumentData.app_date }}</th>
              <th>{{ currentViewDocumentData.disposition_date }}</th>
              <th>
                <v-btn text icon class="mr-6"><v-icon>mdi-pencil-outline</v-icon></v-btn>
                <v-btn text icon><v-icon>mdi-dots-horizontal</v-icon></v-btn>
              </th>
            </tr>
          </tbody>
        </table>
      </v-col>
    </v-row>

    <v-row v-if="tabs.length">
      <v-col>
        <nde-button
          outlined
          color="dark"
          v-for="(item, index) in tabs"
          :key="index"
          class="mr-3"
          @click="changeTab(item)"
          ># {{ item }}
          <v-icon right>mdi-menu-down</v-icon>
          <v-icon right>mdi-pencil-outline</v-icon>
        </nde-button>
      </v-col>
    </v-row>

    <v-row v-if="selected_nuid">
      <v-col>
        <iframe class="nde-view-document-iframe" width="100%" :src="getSrc" allowfullscreen> </iframe>
      </v-col>
    </v-row>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import NdeButton from '../Button/NdeButton.vue';

export default {
  components: { NdeButton },
  computed: {
    ...mapState(['currentViewDocumentData', 'accesstoken', 'env']),
    getSrc() {
      return (
        this.env.DOCUVIEW_URL + '?nuid=' +
        this.selected_nuid +
        '&access_token=' +
        this.accesstoken +
        '&profile_id=' +
        this.$store.state.currentProgram.id +
        '&doc_id=' +
        this.selected_docid
      );
    },
  },

  data() {
    return {
      tabs: [],
      isLoading: false,
      recordData: null,
      selected_tab: null,
      selected_nuid: null,
      selected_docid: null,
      pieces: [],
    };
  },

  mounted() {
    // this.getPiecesByDocIdOauth();
    this.getViewRecordOauth();
  },

  methods: {
    changeTab(tabName) {
      this.selected_tab = tabName;
    },
    gotoSearchResults() {
      this.$store.commit('closeViewDocument');
    },

    getPiecesByDocIdOauth() {
      this.selected_nuid = null;
      
      this.$store.dispatch('increaseCallCount');

      axios
        .post('/getPiecesByDocIdOauth', {
          profileId: this.$store.state.currentProgram.id,
          docId: this.selected_docid, //this.currentViewDocumentData.doc_id,
        })
        .then((response) => {
          console.log(response);
          if (response.data.message == 'success') {
            this.pieces = response.data.data.data.image[0].piece;
            const versionFilter = this.pieces.filter((o) => o.version == '3');
            if (versionFilter.length) {
              this.selected_nuid = versionFilter[0].nuid;
            } else {
              this.selected_nuid = this.pieces[0].nuid;
            }
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },

    getViewRecordOauth() {
      const postData = {
        doc_id: this.currentViewDocumentData.doc_id,
        profile_id: this.$store.state.currentProgram.id,
      };
      
      this.$store.dispatch('increaseCallCount');

      axios
        .post('getViewRecordOauth', postData)
        .then((response) => {
          this.isLoading = false;
          this.isLoaded = true;
          if (response.data.message == 'success') {
            this.recordData = response.data.data.data;
            this.tabs = Object.keys(this.recordData);
            this.selected_tab = this.tabs[0];

            console.log(this.recordData);
          }
        })
        .catch((error) => {
          this.isLoading = false;
          console.log(error.response);
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
  },

  watch: {
    selected_tab: function (pre, cur) {
      this.selected_docid = this.recordData[this.selected_tab][0].doc_id;
      this.getPiecesByDocIdOauth();
    },
  },
};
</script>

<style scoped lang="scss">
.nde-view-document {
  position: fixed;
  left: 0;
  right: 0;
  top: 113px;
  bottom: 0;
  z-index: 1;
  background: $lightGreyColor;
  overflow: auto;

  &-goback {
    cursor: pointer;
    color: $primaryColor;
  }

  &-currentdata {
    table {
      border-collapse: collapse;
      width: 100%;

      td,
      th {
        border: 0px;
        padding: 8px;
        font-size: 0.75rem;
      }

      td {
        color: $greyColor;
      }

      thead tr {
        background-color: $defaultColor;
      }

      tbody tr {
        background-color: $whiteColor;
      }
    }
  }

  &-iframe {
    width: 100%;
    height: calc(100vh - 390px);
  }
}
</style>
