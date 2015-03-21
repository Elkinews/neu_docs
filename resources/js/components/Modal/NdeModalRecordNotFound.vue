<template>
  <nde-modal
    :showModal.sync="isModalRecordNotFound"
    title=""
    :persistent="true"
    @update:closeModal="closeModal"
  >
    <template v-slot:ndeModalContent>
      <v-row class="nde-modal-record-not-found-message mt-12 mb-12">
        <v-col cols="12" md="12" align="center">
          <h2 class="mb-8 font-weight-bold">No records found!</h2>
          <h4 v-if="isRoleCREATERECORD">
            Press tab then select actions and create record to create a new record from these
            criteria.
          </h4>
        </v-col>
      </v-row>
    </template>
    <template v-slot:ndeModalAction>
      <v-row class="mb-12">
        <v-col cols="12" md="12" align="center">
          <nde-button-primary title="OK!" @click="createRecord" style="width: 8.625rem">
          </nde-button-primary>
        </v-col>
      </v-row>
    </template>
  </nde-modal>
</template>

<script>
import NdeModal from './NdeModal.vue';
import NdeButtonPrimary from '../Button/NdeButtonPrimary.vue';
import { mapState } from 'vuex';

export default {
  name: 'NdeModalRecordNotFound',
  components: {
    NdeModal,
    NdeButtonPrimary,
  },
  computed: {
    ...mapState(['isModalRecordNotFound', 'Neusearch']),
  },
  methods: {
    createRecord() {
      if (this.Neusearch.page == 1) {
        // Todo Call API Create records
        const Searchitem = this.Neusearch?.Searchitems?.item;
        let imageFields = [];
        if (Array.isArray(Searchitem)) {
          imageFields = Searchitem.map((filedData) => ({
            field_name: filedData.key,
            field_value: filedData.key === 'app_date' ? filedData.value.slice(0, 10) || '' : filedData.value || '',
          }));
        }
        const dataResultDummy = {
          data: {
            meta: {
              view_empty_tabs: true,
              num_images: 1,
              num_loaded: 1,
            },
            search_results: {
              images: [
                {
                  doc_id: 'NDE_DUMMY_DATA',
                  image_fields: imageFields,
                  image_tabs: [],
                  image_is_processing: true,
                },
              ],
            },
          },
        };
        this.$store.commit('setSearchResult', dataResultDummy.data);
        this.$store.commit('setTotalResults', dataResultDummy.data?.meta?.num_images?.value);
      }

      this.closeModal();
    },
    closeModal() {
      this.$store.commit('setModalRecordNotFound', false);
    },
  },
};
</script>
<style scoped lang="scss">
.nde-modal-record-not-found-message {
  h2,
  h4 {
    color: $darkGreyColor;
  }
}
</style>
