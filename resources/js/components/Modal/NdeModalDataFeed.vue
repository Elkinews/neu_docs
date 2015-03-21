<template>
    <v-dialog
		v-if="showModal" v-model="showModal" max-width="1200" :persistent="true"
    >
        <v-card class="pa-5">
            <v-card-title
                class="
                    text-sm-left
                    font-weight-bold
                    d-flex
                    justify-space-between
                    ml-2
                "
            >
                <h3>Data Feed</h3>
                <v-icon
                    aria-label="Close"
                    size="24px"
                    color="red"
                    @click="hideModal"
                >
                    mdi-close
                </v-icon>
            </v-card-title>
            <v-card-subtitle class="ml-2">
                <span>Results limited to {{ totalItems }}</span>
            </v-card-subtitle>
            <v-card-text>
                <div class="nde-table-search-result">
					<nde-data-table
						ref="ndeDataTable"
						class="nde-table-search-result"
						:headers="columnsDataFeed"
						:items="itemsDataFeed"
					>
						<template slot="item.actions" slot-scope="{ item }">
							<v-chip color="red" label @click="clickSelect(item)">
                  <strong>Select</strong>
              </v-chip>
						</template>
					</nde-data-table>
        </div>
      </v-card-text>
          <v-card-actions>
              <nde-button-cancel
                  v-if="isCreateRecord"
                  @click="callAPICreateDocumentOauth([])"
                  title="None of these options"
                  class="mr-3"
              ></nde-button-cancel>
              <nde-button-cancel
                  @click="hideModal"
                  title="Close"
                  :styles="{ width: '8.25rem' }"
                  class="mr-3"
              ></nde-button-cancel>
          </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import { mapState } from 'vuex';
import NdeDataTable from "../Table/NdeDataTable.vue";
import NdeButtonCancel from "../Button/NdeButtonCancel.vue";

export default {
    name: "NdeModalDataFeed",
    components: { NdeDataTable, NdeButtonCancel },
    props: {
      isCreateRecord: {
        type: Boolean,
        default: true,
      },
    },
    async created() {
      this.showModal = true;
    },
      data() {
          return {
              showModal: true,
              totalItems: 25,
          };
      },
    computed: {
      ...mapState([
        'columnsDataFeed',
        'itemsDataFeed',
      ]),
    },
    methods: {
      hideModal() {
        this.$store.commit('closeModalDataFeed');
        this.$emit("hideModalDataFeed");
      },
      clickSelect(dataItem = {}) {
        if (this.isCreateRecord) {
          let dataUpdate = [];
          for (const fieldKey in dataItem) {
            dataUpdate.push({
              key: fieldKey,
              value: dataItem[fieldKey]
            });
          }
          return this.callAPICreateDocumentOauth(dataUpdate)
        }
        this.$emit("setInputFields", dataItem);
        this.hideModal();
      },
      async callAPICreateDocumentOauth(dataUpdate = []) {
        this.$store.commit('setShowProgressAPI', true);
        const resultCreateDocumentOauth = await this.$store.dispatch('callAPI', {
          url: '/createDocumentOauth',
          method: 'post',
          data: {
            profileId: this.$store.state.currentProgram.id,
            Searchitems: dataUpdate.length ? { item: dataUpdate } : this.$store.state.Neusearch.Searchitems,
          },
        });
        if (resultCreateDocumentOauth.message !== 'success') {
          this.$store.commit('setShowProgressAPI', false);
          return this.$swal({
            icon: 'error',
            text: this.getMessageResult(resultCreateDocumentOauth),
          });
        }
        this.$store.commit('setShowProgressAPI', false);
        this.$store.dispatch({ type: 'searchImages' });
        this.hideModal();
      }
    },
};
</script>

<style scoped lang="scss">
.v-card__title {
    .v-icon:hover {
        &::after {
            @extend %afterIconModalClose;
        }
    }
}
.v-card__actions {
    justify-content: end;
}

.v-chip {
    &.red {
        background: #f9e1e1 !important;
        border: 1px solid #c32c39 !important;
        box-sizing: border-box;
        border-radius: 4px;
    }
    strong {
        text-transform: capitalize;
        color: #c32c39;
        font-size: 0.75rem;
        line-height: 1rem;
    }
}
</style>