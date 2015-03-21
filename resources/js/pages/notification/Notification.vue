<template>
    <div role="main">
      <v-container fluid>
        <h3 class="notification-header" role="heading">Notification</h3>
        <v-expansion-panels accordion v-bind="$attrs" class="document-table" v-model="panel" :disabled="false" multiple>
          <v-expansion-panel>
            <v-expansion-panel-header class="document-table-header d-flex justify-space-between">
              <template v-slot:actions>
                <v-icon class="document-table-icon"> mdi-chevron-down </v-icon>
              </template>
            </v-expansion-panel-header>
            <v-expansion-panel-content class="notification-table-content">
              <nde-data-table
                :headers="headers"
                :items="notificationList"
                :itemsPerPage="params.page_size"
                :page="params.page" dense
                :isLoading="isLoading"
                @updateSortBy="handleUpdateSortBy"
                @updateSortDesc="handleUpdateSortDesc"
                @onclickrow="onclickrow"
              >
                <template slot="item.body" slot-scope="{ item }">
                  <div v-html="item.body.substring(0, 100) + '...'"></div>
                </template>
                <template slot="item.created_date" slot-scope="{ item }">
                  {{formatDate(item.created_date)}}
                </template>
                <template slot="item.is_read" slot-scope="{ item }">
                  <v-tooltip bottom v-if="!item.is_read">
                    <template v-slot:activator="{ on, attrs }">
                      <v-icon
                        color="primary"
                        v-bind="attrs"
                        v-on="on"
                      >
                        mdi-circle-small
                      </v-icon>
                    </template>
                    <span>Read</span>
                  </v-tooltip>
                </template>
              </nde-data-table>
              <div class="d-flex justify-start">
              <nde-footer-table
                :page="params.page"
                :pageCount="totalPage"
                :isVisibleExportCSV="false"
                @paging:update="handlePagination"
              />
              </div>
            </v-expansion-panel-content>
          </v-expansion-panel>
        </v-expansion-panels>
      </v-container>
      <view-notification-modal
        :data="selectedNotification"
        :isShow="isShowViewModal"
        @closeModal="closeViewModal"
      />
    </div>
</template>

<script>
  import axios from 'axios';
  import NdeDataTable from '@components/Table/NdeDataTable.vue';
  import NdeDashboardLayout from '../../shared/layouts/dashboard/NdeDashboardLayout';
  import NdeFooterTable from '@components/Table/NdeSearchResult/NdeFooterTable.vue';
  import ViewNotificationModal from './components/ViewNotificationModal.vue';
  import moment from 'moment';

  export default {
    layout: NdeDashboardLayout,
    data() {
      return {
        isLoading: false,
        isShowViewModal: false,
        selectedNotification: {},
        panel: [0],
        notificationList: [],
        params: {
          page_size: 20,
          page: 1,
          sort_by: 'created_date desc'
        },
        totalPage: 1,
        isDesc: false,
        headers: [
          {
            text: 'Unread',
            value: 'is_read',
            class: 'col-status',
          },
          {
            text: 'Subject',
            value: 'subject',
            class: 'col-subject',
          },
          {
            text: 'Body',
            value: 'body',
            class: 'col-body',
          },
          {
            text: 'Type',
            value: 'type',
            class: 'col-type',
          },
          {
            text: 'Created Date',
            value: 'created_date',
            class: 'col-created-date',
          },
        ],
      };
    },

    mounted() {
       window.document.title = 'neuDocs Enterprise Notification';
    },

    components: {
      NdeDataTable,
      NdeFooterTable,
      ViewNotificationModal,
    },
    created() {
      this.getNotificationList();
    },
    methods: {
      formatDate(date) {
        const format = "YYYY-MM-DD hh:mm A";
        return moment(date).format(format)
      },
      onclickrow(value){
        this.selectedNotification = value;
        this.isShowViewModal = true;
        this.readNotification(value)
      },
      closeViewModal(){
        this.isShowViewModal = false;
      },
      getNotificationList() {
        this.isLoading = true;
        this.notificationList = [];
        this.$store.dispatch('increaseCallCount');
        axios
          .get('/getAllNotificationsOauth', {params: this.params })
          .then((response) => {
            this.isLoading = false;
            console.log('response', response)
            if (response.data.message == 'success') {
              this.notificationList = response.data.data.data.notifications;
              this.params.page_size = response.data.data.data.notifications.length;
              this.params.page = response.data.data.data.page;
              this.totalPage = response.data.data.data.no_of_pages;
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
      handlePagination(jumpToPage) {
        this.params.page = jumpToPage;
        this.params.page_size = 20;
        this.getNotificationList();
      },
      handleUpdateSortBy(column) {
        console.log('column', column)
        this.params.page = 1;
        if (column) {
          var sorting = this.isDesc ? ' desc' : ' asc';
          this.params.sort_by = column + sorting;
          this.$nextTick(() => {
            this.getNotificationList();
          });
        }
      },

      handleUpdateSortDesc(isDesc) {
        if(isDesc) {
          this.params.sort_by = this.params.sort_by.replace('asc', 'desc')
        } else {
          this.params.sort_by = this.params.sort_by.replace('desc', 'asc')
        }
        if (this.isDesc !== isDesc) {
          this.isDesc = isDesc;
          this.$nextTick(() => {
            this.getNotificationList();
          });
        }
      },
      async readNotification(item) {

        const postData = {
          notification_ids: [item.id],
        };
        this.isLoading = true;
        this.$store.dispatch('increaseCallCount');
        await axios
          .post('/readNotificationOauth', postData)
          .then(() => {
            this.isLoading = false;
            this.notificationList = this.notificationList.map(obj =>
                obj.id === item.id ? { ...obj, is_read: true } : obj
            );
          })
          .catch((error) => {
            this.isLoading = false;
            console.log(error.response);
          })
          .finally(() => {
            this.isLoading = false;
          });
        // Update unread notification count
        this.$store.dispatch({ type: 'getUnreadNotificationOauth' });
      }
    },
  };
</script>

<style lang="scss">
  .notification-header {
    margin: 10px;
    font-weight: bold;
  }
  .notification-table-header {
    padding-top: 0;
    padding-bottom: 0;
  }

  .notification-table {
    margin: 0 10px;
    width: auto;
  }

  .notification-table-content th {
    border-right-color: white;
    border-right-width: medium;
    background-color: #ebeced;
    color: $darkGreyColor;

    &.col-status {
      min-width: 100px;
    }
    &.col-subject {
      width: 20%;
    }
    &.col-body {
      width: 40%;
    }
    &.col-type {
      width: 15%;
    }
    &.col-created-date {
      width: 15%;
    }
  }

  .notification-table-content .theme--light.v-data-table>.v-data-table__wrapper>table>tbody>tr>td {
    cursor: pointer;
  }

  .notification-table-content .theme--light.v-data-table>.v-data-table__wrapper>table>tbody>tr:not(:last-child)>td:not(.v-data-table__mobile-row) {
    border-bottom: none;
  }

  .notification-table-content .theme--light.v-data-table>.v-data-table__wrapper>table>thead>tr:last-child>th {
    border-bottom: none;
  }
</style>
