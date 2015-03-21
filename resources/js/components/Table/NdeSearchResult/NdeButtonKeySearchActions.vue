<template>
  <v-menu v-model="isMenuOpened" offset-y right attach :close-on-content-click="closeOnContentClick">
    <template v-slot:activator="{ on, attrs }">
      <v-btn @click.stop="getMenuItems()" color="#44343D" icon dark v-bind="attrs" v-on="on">
        <v-icon dense> {{ icon }} </v-icon>
      </v-btn>
    </template>
    <v-list style="min-width: 15rem;" dense outlined>
      <v-menu open-on-hover right offset-x v-for="item in items" :key="item.id">
        <template v-slot:activator="{ on, attrs }">
          <v-list-item class="d-flex align-space-around" v-bind="attrs" v-on="isShowChildMenu(item) ? on : ''" link
            @click.stop="runAction(item.actionName)">
            <v-list-item-subtitle v-if="item.status === 'active'" class="blue-grey--text text--darken-1 fw-400"
              v-text="item.content" />
            <v-list-item-subtitle v-if="item.status === 'warning'" class="red--text text--darken-4 fw-400"
              v-text="item.content" />
            <v-list-item-icon>
              <v-avatar size="20"><img :src="'images/' + `${item.icon}`" /></v-avatar>
            </v-list-item-icon>
          </v-list-item>
        </template>
        <v-list dense outlined>
          <v-list-item class="d-flex align-space-around" v-for="(subItem, index) in item.menuItems || menuItems"
            :key="index" link @click.stop="runSubItemAction(subItem)">
            <v-list-item-title v-if="item.content !== 'Show Last Activity'"
              class="blue-grey--text text--darken-1 fw-400" v-text="subItem.content" />
            <v-list-item-content v-else>
              <ul>
                <li class="blue-grey--text text--darken-1 fw-400 mt-2">
                  User: {{ subItem.content.user }}
                </li>
                <li class="blue-grey--text text--darken-1 fw-400 mt-2">
                  Modified date: {{ subItem.content.createdOn }}
                </li>
                <li class="blue-grey--text text--darken-1 fw-400 mt-2">
                  Activity Type: {{ subItem.content.activity }}
                </li>
              </ul>
            </v-list-item-content>
          </v-list-item>
        </v-list>
      </v-menu>
    </v-list>
  </v-menu>
</template>

<script>
import { mapState } from 'vuex';

export default {
  name: 'NdeButtonKeySearchActions',
  props: {
    text: {
      type: String,
      default: () => '',
    },
    icon: {
      type: String,
      default: () => 'mdi-dots-horizontal',
    },
    name: {
      type: String,
      default: () => '',
    },
    isChangeDisplayValue: {
      type: Boolean,
      default: () => false,
    },
    render: {
      type: Function,
      default: (item) => {
        return item.name;
      },
    },
    document: {
      type: Object,
    },
  },
  data: () => ({
    closeOnContentClick: false,
    isMenuOpened: false,
    menuItems: [],
    items: [],
  }),
  computed: {
    ...mapState(['mergeFromDocId', 'mergeFromId', 'currentProgram']),
  },
  methods: {
    makeId(length = 6) {
      var result = '';
      var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
      var charactersLength = characters.length;
      for (var i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
      }
      return result;
    },
    async getMenuItems() {
      this.$store.commit('setShowProgressAPI', true);
      const resulGetActionsOauth = await this.$store.dispatch('callAPI', {
        url: '/getActionsOauth',
        method: 'post',
        data: {
          profile_id: this.$store.state.currentProgram.id,
          document_id: this.document.doc_id === 'NDE_DUMMY_DATA' ? -1 : this.document.doc_id,
        },
      });
      this.$store.commit('setShowProgressAPI', false);
      if (resulGetActionsOauth.message !== 'success') {
        return this.$swal({
          icon: 'error',
          text: this.getMessageResult(resulGetActionsOauth),
        });
      }

      let actions = resulGetActionsOauth?.data?.data?.data?.actions || {};
      if (this.document.doc_id !== 'NDE_DUMMY_DATA') {
        actions = actions['Keysearch'];
      }

      const userLoginRoles = this.userLoginRoles() || [];
      const menuItems = [];

      for (const actionName in actions) {
        const dataAction = actions[actionName];
        const roles = dataAction?.roles || [];
        let permission = !roles.length;
        if (!permission) {
          for (let role of roles) {
            if (userLoginRoles.includes(role)) {
              permission = true;
              break;
            }
          }
        }

        if (!permission) {
          continue;
        }

        let content = dataAction?.text;
        if (actionName === 'Merge') {
          content = `Merge ${this.mergeFromDocId ? 'To' : 'From'} This Record`;
        }
        if (actionName === 'Last Activity') {
          content = 'Show Last Activity';
        }
        const subMenuItems = [];
        let subOptions = dataAction?.subOptions || {};
        for (const keySubOption in subOptions) {
          const subOption = subOptions[keySubOption];
          subMenuItems.push({
            id: this.makeId(),
            content: subOption?.text,
            actionName: keySubOption,
          });
        }
        menuItems.push({
          id: this.makeId(),
          status: actionName === 'Delete' ? 'warning' : 'active',
          content,
          icon: this.getMenuIcon(actionName),
          menuItems: subMenuItems.length ? subMenuItems : null,
          actionName,
        });
        if (actionName === 'Merge' && this.mergeFromId) {
          menuItems.push({
            id: this.makeId(),
            status: 'active',
            content: 'Cancel Merge Record',
            icon: this.getMenuIcon(actionName),
            actionName: 'MergeCancel',
          });
        }
      }
      this.items = menuItems;
      if (this.document.doc_id !== 'NDE_DUMMY_DATA') {
          this.items.push({
          actionName: "View Record in New Tab",
          content: "View Record in New Tab",
          icon: "new-tab-2.jpg",
          id: "12345",
          menuItems: null,
          status: "active",
        })
      }

    },
    getMenuIcon(actionName) {
      let icon = 'watchList.png';
      switch (actionName) {
        case 'Last Activity':
          icon = 'activity.png';
          break;
        case 'Transfer':
          icon = 'transfer.png';
          break;
        case 'Edit':
          icon = 'edit.png';
          break;
        case 'Merge':
          icon = 'merge.png';
          break;
        case 'Delete':
          icon = 'delete.png';
          break;
        case 'Copy':
          icon = 'copy.png';
          break;
        case 'Advance':
          icon = 'adv_status.png';
          break;
        case 'Undo':
          icon = 'undo_status.png';
          break;
        default:
          break;
      }
      return icon;
    },
    isShowChildMenu(item) {
      if (item.actionName === 'Last Activity') {
        return this.menuItems.length;
      }
      return (item.menuItems || []).length;
    },
    async runAction(actionName) {
      this.closeOnContentClick = false;
      console.log('actionName', actionName);
      if (actionName === 'Last Activity' && !this.menuItems.length) {
        await this.runActionShowLastActivity();
        this.closeOnContentClick = true;
        return;
      }
      this.$store.commit('setShowProgressAPI', true);
      this.closeOnContentClick = true;
      if (actionName === 'Delete') {
        await this.runActionDeleteRecord();
      } else if (actionName === 'Transfer') {
        await this.runActionTransfer();
      } else if (actionName === 'Edit') {
        await this.runActionEditIndexingFields();
      } else if (actionName === 'Merge' && !this.mergeFromDocId) {
        await this.runActionMergeFromThisRecord();
      } else if (actionName === 'MergeCancel') {
        this.runActionMergeCancel();
      } else if (actionName === 'Merge' && this.mergeFromDocId) {
        await this.runActionMergeToThisRecord();
      } else if (actionName === 'Create') {
        await this.runActionCreateThisRecord();
      } else if (actionName === 'View Document') {
        this.runActionViewDocurment();
      } else if (actionName === 'Advance' || actionName === 'Undo') {
        await this.runActionUpdateStatus(actionName)
      } else if (actionName === 'View Record in New Tab') {
        window.open('/viewrecord?profile_id=' + this.currentProgram?.id + '&doc_id=' + this.document?.doc_id, '_blank');
      }
      this.$store.commit('setShowProgressAPI', false);
      this.$emit('onSelectAction', actionName);
    },
    runActionMergeCancel() {
      this.$store.commit('setMergeFromDocId', '');
      this.$store.commit('setMergeFromId', '');
      this.isMenuOpened = false
    },
    async runActionDeleteRecord() {
      const resultAllowDeleteIndOauth = await this.$store.dispatch('callAPI', {
        url: '/getAllowDeleteIndOauth',
        method: 'post',
        data: {
          profile_id: this.$store.state.currentProgram.id,
          image_id: this.document.doc_id,
        },
      });
      this.$store.commit('setShowProgressAPI', false);
      if (resultAllowDeleteIndOauth?.data?.data?.allow_delete_ind === 't') {
        const confirmed = await this.$swal({
          title: 'Record Delete Confirmation',
          text: 'Are you sure you want to delete this record?',
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

        const resultDeleteDocumentOauth = await this.$store.dispatch('callAPI', {
          url: '/deleteDocumentOauth',
          method: 'post',
          data: {
            profile_id: this.$store.state.currentProgram.id,
            document_id: this.document.doc_id,
          },
        });

        this.$store.commit('setShowProgressAPI', false);
        if (resultDeleteDocumentOauth.message !== 'success') {
          return this.$swal({
            icon: 'error',
            text: this.getMessageResult(resultDeleteDocumentOauth),
          });
        }
        const mergeFromId = this.$store.state.mergeFromId;
        if (mergeFromId === this.document.id) {
          this.runActionMergeCancel();
        }
        this.$store.dispatch({ type: 'searchImages' });
      } else {
        this.$swal({
          title: 'This record is flagged to not allow deletion.',
          type: 'warning',
        });
      }
    },
    async runActionShowLastActivity() {
      this.$store.commit('setShowProgressAPI', true);
      const resultGetLastActivityOauth = await this.$store.dispatch('callAPI', {
        url: '/getLastActivityOauth',
        method: 'post',
        data: {
          profile_id: this.$store.state.currentProgram.id,
          document_id: this.document.doc_id,
        },
      });
      this.$store.commit('setShowProgressAPI', false);
      this.menuItems = [
        {
          content: resultGetLastActivityOauth?.data?.data || {},
        },
      ];
    },
    async runActionWatchList(subItem) {
      let url = '';
      let params = {
        profile_id: this.$store.state.currentProgram.id,
        action: subItem.actionName === 'Files Uploaded' ? 'DOC_UPLOAD' : 'RECORD_CREATED',
      };

      if (this.document.doc_id === 'NDE_DUMMY_DATA') {
        url = 'createUserWatchlistOauth';
        params.Searchitems = this.$store.state.Neusearch.Searchitems;
      } else {
        url = '/addUserWatchlistOauth';
        params.doc_id = this.document.doc_id
      }

      const resulWatchlistOauth = await this.$store.dispatch('callAPI', {
        url,
        method: 'post',
        data: params,
      });
      this.$swal({
        icon: resulWatchlistOauth.message !== 'success' ? 'error' : 'success',
        text: this.getMessageResult(resulWatchlistOauth),
      });
      this.$store.dispatch({ type: 'searchImages' });
    },
    async runSubItemAction(subItem) {
      this.closeOnContentClick = true;
      this.$store.commit('setShowProgressAPI', true);
      if (subItem.actionName) {
        await this.runActionWatchList(subItem);
      }
      this.$store.commit('setShowProgressAPI', false);
    },
    async runActionTransfer() {
      const resulRequestDocFromStorageOauth = await this.$store.dispatch('callAPI', {
        url: '/requestDocFromStorageOauth',
        method: 'post',
        data: {
          profile_id: this.$store.state.currentProgram.id,
          document_id: this.document.doc_id,
        },
      });
      if (resulRequestDocFromStorageOauth.message !== 'success') {
        this.$store.commit('setShowProgressAPI', false);
        return this.$swal({
          icon: 'error',
          text: this.getMessageResult(resulRequestDocFromStorageOauth),
        });
      }
      const resulCheckProfilePageOrderOauth = await this.$store.dispatch('callAPI', {
        url: '/checkProfilePageOrderOauth',
        method: 'post',
        data: {
          profileId: this.$store.state.currentProgram.id,
          targetProfileId: this.$store.state.currentProgram.id,
        },
      });
      if (resulCheckProfilePageOrderOauth.message !== 'success') {
        this.$store.commit('setShowProgressAPI', false);
        return this.$swal({
          icon: 'error',
          text: this.getMessageResult(resulCheckProfilePageOrderOauth),
        });
      }
      this.$store.commit('setShowProgressAPI', false);
      this.$store.commit('openModalTransferRecord', this.document.doc_id);
    },
    async runActionEditIndexingFields() {
      const resulLockDocOauth = await this.$store.dispatch('callAPI', {
        url: '/lockDocOauth',
        method: 'post',
        data: {
          profile_id: this.$store.state.currentProgram.id,
          document_ids: this.document.doc_id,
        },
      });
      if (resulLockDocOauth.message !== 'success') {
        this.$store.commit('setShowProgressAPI', false);
        return this.$swal({
          icon: 'error',
          text: this.getMessageResult(resulLockDocOauth),
        });
      }
      const resulGetAllowEditIndOauth = await this.$store.dispatch('callAPI', {
        url: '/getAllowEditIndOauth',
        method: 'post',
        data: {
          profile_id: this.$store.state.currentProgram.id,
          image_id: this.document.doc_id,
        },
      });
      if (resulGetAllowEditIndOauth.message !== 'success') {
        this.$store.commit('setShowProgressAPI', false);
        return this.$swal({
          icon: 'error',
          text: this.getMessageResult(resulGetAllowEditIndOauth),
        });
      }
      this.$store.commit('setShowProgressAPI', false);
      this.$store.commit('openModalEditIndexingFields', {
        docId: this.document.doc_id,
        dataDoc: this.document,
      });
    },
    async runActionMergeFromThisRecord() {
      const resultAllowDeleteIndOauth = await this.$store.dispatch('callAPI', {
        url: '/getAllowDeleteIndOauth',
        method: 'post',
        data: {
          profile_id: this.$store.state.currentProgram.id,
          image_id: this.document.doc_id,
        },
      });
      this.$store.commit('setShowProgressAPI', false);
      if (resultAllowDeleteIndOauth.message !== 'success') {
        return this.$swal({
          icon: 'error',
          text: this.getMessageResult(resultAllowDeleteIndOauth),
        });
      }
      if (resultAllowDeleteIndOauth?.data?.data.allow_delete_ind === 'f') {
        return this.$swal({
          icon: 'error',
          text: 'Cannot merge from this record since it has been flagged to not allow deletion.',
        });
      }
      this.$store.commit('setMergeFromDocId', this.document.doc_id);
      this.$store.commit('setMergeFromId', this.document.id);
      this.$store.dispatch({ type: 'searchImages' });
      this.isMenuOpened = false
    },
    async runActionMergeToThisRecord() {
      const mergeFromDocId = this.$store.state.mergeFromDocId;
      const mergeFromId = this.$store.state.mergeFromId;
      if (mergeFromId === this.document.id) {
        this.$store.commit('setShowProgressAPI', false);
        return this.$swal({
          icon: 'error',
          text: 'Cannot merge to same record.',
        });
      }
      const resulGetAllowNewContentIndOauth = await this.$store.dispatch('callAPI', {
        url: '/getAllowNewContentIndOauth',
        method: 'post',
        data: {
          profile_id: this.$store.state.currentProgram.id,
          image_id: this.document.doc_id,
        },
      });
      if (resulGetAllowNewContentIndOauth.message !== 'success') {
        this.$store.commit('setShowProgressAPI', false);
        return this.$swal({
          icon: 'error',
          text: this.getMessageResult(resulGetAllowNewContentIndOauth),
        });
      }
      if (resulGetAllowNewContentIndOauth?.data?.data?.data?.allow_new_content_ind === 'f') {
        this.$store.commit('setShowProgressAPI', false);
        return this.$swal({
          icon: 'error',
          text: 'Cannot merge to this record since it has been flagged to not allow new content',
        });
      }
      this.$store.commit('setShowProgressAPI', false);
      const confirmed = await this.$swal({
        title: 'Record Merge Confirmation',
        text: 'Are you sure you want to merge to this record?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
      });
      if (!confirmed.value) return false;
      this.$store.commit('setShowProgressAPI', true);
      const resulMergeRecordOauth = await this.$store.dispatch('callAPI', {
        url: '/mergeRecordOauth',
        method: 'post',
        data: {
          profile_id: this.$store.state.currentProgram.id,
          from_id: mergeFromDocId,
          to_id: this.document.doc_id,
        },
      });
      this.$store.commit('setShowProgressAPI', false);
      if (resulMergeRecordOauth.message !== 'success') {
        return this.$swal({
          icon: 'error',
          text: this.getMessageResult(resulMergeRecordOauth),
        });
      }
      await this.$swal({
        icon: 'success',
        text: this.getMessageResult(resulMergeRecordOauth),
      });
      this.runActionMergeCancel();
      this.$store.dispatch({ type: 'searchImages' });
    },

    async runActionCreateThisRecord() {
      const fields = {};
      this.$store.state.Neusearch.Searchitems?.item.forEach(dataItem => {
        fields[dataItem.key] = dataItem.value;
      });
      const resulCheckDatafeedCompletionOauth = await this.$store.dispatch('callAPI', {
        url: '/checkDatafeedCompletionOauth',
        method: 'post',
        data: {
          target_profile: this.$store.state.currentProgram.id,
          source_table: "1",
          fields,
        },
      });

      if (resulCheckDatafeedCompletionOauth.message !== 'success') {
        this.$store.commit('setShowProgressAPI', false);
        return this.$swal({
          icon: 'error',
          text: this.getMessageResult(resulCheckDatafeedCompletionOauth),
        });
      }

      const foundItems = resulCheckDatafeedCompletionOauth?.data?.data?.existing_values?.vals || [];
      if (foundItems.length > 0) {
        await this.dataFeedCompletionAction(resulCheckDatafeedCompletionOauth);
      } else {
        await this.doubleEntryOauthAction();
      }
    },

    async dataFeedCompletionAction(dataResult) {
      let dataUpdate = [];
      const foundItems = dataResult?.data?.data?.existing_values?.vals || [];
      if (foundItems.length > 1) {
        // Show modal data feed
        const columnsDataFeed = [
          {
            text: '',
            value: 'actions',
            class: 'col-actions',
            sortable: false
          },
        ];

        const dataHeaders = dataResult?.data?.data?.existing_values?.headers || {};
        for (const headerKey in dataHeaders) {
          columnsDataFeed.push({
            text: dataHeaders[headerKey],
            value: headerKey,
            class: 'col-' + headerKey,
            sortable: false,
          });
        }
        this.$store.commit('setShowProgressAPI', false);
        this.$store.commit('openModalDataFeed', {
          columnsDataFeed,
          itemsDataFeed: foundItems,
        });
      } else if (foundItems.length === 1) {
        // Create data if single data is found
        await this.createDocumentAction(foundItems);
      }
    },

    async doubleEntryOauthAction() {
      const resultGetDoubleEntryOauth = await this.$store.dispatch('callAPI', {
        url: '/getDoubleEntryOauth',
        method: 'post',
        data: {
          profile_id: this.$store.state.currentProgram.id,
          Searchitems: this.$store.state.Neusearch.Searchitems,
        },
      });

      if (resultGetDoubleEntryOauth.message !== 'success') {
        this.$store.commit('setShowProgressAPI', false);
        return this.$swal({
          icon: 'error',
          text: this.getMessageResult(resultGetDoubleEntryOauth),
        });
      }

      const dataDoubleEntry = resultGetDoubleEntryOauth?.data?.data?.data || {};
      if (
        dataDoubleEntry?.doubleEntry?.length !== 0 ||
        dataDoubleEntry?.requiredEntry?.length !== 0 ||
        dataDoubleEntry?.groupRequiredEntry?.length !== 0
      ) {
        this.$store.commit('setDataDoubleEntry', dataDoubleEntry);
        this.$store.commit('openModalDoubleEntry', this.document.doc_id);
        this.$store.commit('setShowProgressAPI', false);
      } else {
        // Create document if NO double entry
        await this.createDocumentAction();
      }
    },
    async createDocumentAction(foundItems) {
        let dataUpdate = [];
        const dataItem = foundItems ? foundItems[0] : {};
        for (const fieldKey in dataItem) {
          dataUpdate.push({
            key: fieldKey,
            value: dataItem[fieldKey]
          });
        }

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

    },
    runActionViewDocurment() {
      this.$store.commit('openViewDocument', this.document);
    },

    async runActionUpdateStatus(actionName) {
      let payload = {
        profile_id: this.$store.state.currentProgram.id,
        document_id: this.document.doc_id,
        cmd: actionName === 'Advance' ? 'ADV' : 'UNDO'
      }

      const resultUpdateImageStatusOauth = await this.$store.dispatch('callAPI', {
        url: '/updateImageStatusOauth',
        method: 'post',
        data: payload,
      });

      if (resultUpdateImageStatusOauth?.data?.data?.status === 'success') {
        this.$store.commit('setShowProgressAPI', false);
        this.$emit('onUpdateNewData')
        return this.$swal({
          icon: 'success',
          text: resultUpdateImageStatusOauth?.data?.data?.message,
        });
      }

      this.$store.commit('setShowProgressAPI', false);
      return this.$swal({
        icon: 'error',
        text: this.getMessageResult(resultUpdateImageStatusOauth),
      });
    }
  },
  watch: {
    isMenuOpened: function () {
      this.items = [];
    },
  },
};
</script>

<style scoped lang="scss">
.fw-400 {
  font-weight: 400 !important;
}

@media screen and (max-width: 48rem) {
  .v-menu--attached {
    .v-menu__content {
      text-align: left !important;
    }
  }
}
</style>
