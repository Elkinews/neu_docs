<template>
  <nde-data-table
    ref="ndeDataTable"
    class="nde-table-search-result"
    :headers="columns"
    :items="localWatchlist"
    @updateSortBy="handleUpdateSortBy"
    @updateSortDesc="handleUpdateSortDesc"
    :mobileBreakpoint="0"
  >
    <template slot="item.fields" slot-scope="{ item }">
      <span style="line-height: 1rem" v-html="item.fields"></span>
    </template>
    <template slot="item.group_hash" slot-scope="{ item }">
      <span class="pa-3">
        <v-icon aria-label="Close" size="18px" color="red" @click="deleteWatchlistOauth(item.group_hash)">mdi-close</v-icon>
      </span>
    </template>
  </nde-data-table>
</template>

<script>
import NdeDataTable from './NdeDataTable.vue';
export default {
  name: "NdeTableWatchlist",
  components: {
    NdeDataTable,
  },
  data(){
    return {
      localWatchlist: [],
      columns: [
        {
          text: 'Record Information',
          value: 'fields',
          class: 'col-fields'
        },
        {
          text: 'Watching for',
          value: 'category',
          class: 'col-category'
        },
        {
          text: 'Started watching at',
          value: 'created_on',
          class: 'col-created_on'
        },
        {
          text: 'Activity last performed at',
          value: 'updated_on',
          class: 'col-updated_on'
        },
        {
          text: 'Unwatch',
          value: 'group_hash',
          class: 'col-group_hash'
        },
      ],
      orderBy: 'created_on',
      orderDir: 'desc',
    }
  },
  async created() {
    await this.getWatchlistOauth();
  },
  methods: {
    async getWatchlistOauth() {
      this.$store.commit('setShowProgressAPI', true);
      const resultGetWatchlistOauth = await this.$store.dispatch('callAPI', {
        url: '/getWatchlistOauth',
        method: 'post',
        data: {
          profileId: this.$store.state.currentProgram.id,
          orderBy: this.orderBy,
          orderDir: this.orderDir,
        }
      });
      this.$store.commit('setShowProgressAPI', false);
      if (resultGetWatchlistOauth.message !== 'success') {
        return this.$swal({
          icon: 'error',
          text: this.getMessageResult(resultGetWatchlistOauth),
        });
      }
      this.localWatchlist = resultGetWatchlistOauth?.data?.data?.watchlists;
    },
    async deleteWatchlistOauth(itemID) {
      const confirmed = await this.$swal({
        title: 'Watchlist Delete Confirmation',
        text: 'Are you sure you want to delete this watchlist?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Delete',
        confirmButtonColor: '#c32c39',
        cancelButtonText: 'Cancel',
        cancelButtonColor: '#9a9ea1',
        reverseButtons: true
      });
      if (!confirmed.value) return false;
      this.$store.commit('setShowProgressAPI', true);
      const resultDeleteUserWatchlistOauth = await this.$store.dispatch('callAPI', {
        url: '/deleteUserWatchlistOauth',
        method: 'post',
        data: {
          profileId: this.$store.state.currentProgram.id,
          groupHash: itemID,
        }
      });
      this.$store.commit('setShowProgressAPI', false);
      if (resultDeleteUserWatchlistOauth.message !== 'success') {
        return this.$swal({
          icon: 'error',
          text: this.getMessageResult(resultDeleteUserWatchlistOauth),
        });
      }
      await this.$swal({
        icon: 'success',
        text: this.getMessageResult(resultDeleteUserWatchlistOauth) || 'Delete successfully',
      });
      this.getWatchlistOauth();
    },
    // TODO: Since the system is getting all the Watchlist of ProfileId, there is no need to call the API to sort
    handleUpdateSortBy(column) {
      if (column) {
        this.orderBy = column;
      } else {
        this.orderBy = 'created_on';
        this.orderDir = 'desc';
      }
      // this.getWatchlistOauth();
    },

    handleUpdateSortDesc(isDesc) {
      if (isDesc === undefined) {
        return;
      }
      this.orderDir = isDesc ? 'desc' : 'asc';
      // this.getWatchlistOauth();
    },
  },
};
</script>
