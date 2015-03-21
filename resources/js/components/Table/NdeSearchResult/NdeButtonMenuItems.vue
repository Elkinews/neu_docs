<template>
  <v-menu offset-y bottom :close-on-content-click="closeOnContentClick" attach>
    <template v-slot:activator="{ on, attrs }">
      <v-btn color="grey" icon dark v-bind="attrs" v-on="on">
        <v-icon dense> {{ icon }} </v-icon>
      </v-btn>
    </template>
    <v-list dense outlined>
      <v-menu open-on-hover left offset-x v-for="item in optionItems" :key="item.id">
        <template v-slot:activator="{ on, attrs }">
          <v-list-item
            class="d-flex align-space-around"
            v-bind="attrs"
            v-on="isShowChildMenu(item) ? on : ''"
            link
            @click="runAction(item.content)"
          >
            <v-list-item-subtitle
              v-if="item.status === 'active'"
              class="blue-grey--text text--darken-1 fw-400"
              v-text="item.content"
            />
            <v-list-item-subtitle
              v-if="item.status === 'warning'"
              class="red--text text--darken-4 fw-400"
              v-text="item.content"
            />
            <v-list-item-icon>
              <v-avatar size="20"><img :src="'/images/' + `${item.icon}`" /></v-avatar>
            </v-list-item-icon>
          </v-list-item>
        </template>
        <v-list dense outlined>
          <v-list-item
            class="d-flex align-space-around"
            v-for="(subItem, index) in item.menuItems || menuItems"
            :key="index"
            link
            @click="runSubItemAction(subItem)"
          >
            <v-list-item-title
              v-if="item.content !== 'Show Last Activity'"
              class="blue-grey--text text--darken-1 fw-400"
              v-text="subItem.content"
            />
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
import { cloneDeep } from 'lodash';

export default {
  name: 'NdeButtonMenuItemsTable',
  props: {
    text: {
      type: String,
      default: () => '',
    },
    icon: {
      type: String,
      default: () => 'mdi-dots-horizontal',
    },
    items: {
      type: Array,
      default: () => [
        {
          id: 1,
          content: 'WatchList',
          status: 'active',
          icon: 'watchList.png',
        },
        {
          id: 2,
          content: 'Show Last Activity',
          status: 'active',
          icon: 'activity.png',
        },
        {
          id: 3,
          content: 'Tranfer',
          status: 'active',
          icon: 'transfer.png',
        },
        {
          id: 4,
          content: 'Edit Indexing Fields',
          status: 'active',
          icon: 'edit.png',
        },
        {
          id: 5,
          content: 'Copy To Work Queue',
          status: 'active',
          icon: 'copy.png',
        },
        {
          id: 6,
          content: 'Merge From This Record',
          status: 'active',
          icon: 'merge.png',
        },
        {
          id: 7,
          content: 'Delete Record',
          status: 'warning',
          icon: 'delete.png',
        },
      ],
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
    menuItems: [],
  }),
  computed: {
    ...mapState(['mergeFromDocId']),
    optionItems() {
      if (!this.mergeFromDocId) {
        return this.items;
      }
      const tmpItems = cloneDeep(
        this.items.filter(
          (dataMenu) =>
            dataMenu.content !== 'Merge From This Record' && dataMenu.content !== 'Delete Record',
        ),
      );
      tmpItems.push(
        ...[
          {
            id: 6,
            content: 'Merge To This Record',
            status: 'active',
            icon: 'merge.png',
          },
          {
            id: 6.5,
            content: 'Cancel Merge Record',
            status: 'active',
            icon: 'merge.png',
          },
          {
            id: 7,
            content: 'Delete Record',
            status: 'warning',
            icon: 'delete.png',
          },
        ],
      );
      return tmpItems;
    },
    isMobile() {
      return window.screen.width < 768
    }
  },
  methods: {
    isShowChildMenu(item) {
      if (item.content === 'Show Last Activity') {
        return this.menuItems.length;
      }
      return (item.menuItems || []).length;
    },
    async runAction(actionName) {
      this.closeOnContentClick = false;
      console.log('actionName', actionName);
      if (actionName === 'Show Last Activity' && !this.menuItems.length) {
        await this.runActionShowLastActivity();
        this.closeOnContentClick = true;
        return;
      }
      this.$store.commit('setShowProgressAPI', true);
      this.closeOnContentClick = true;
      if (actionName === 'Delete Record') {
        await this.runActionDeleteRecord();
      } else if (actionName === 'Transfer') {
        await this.runActionTransfer();
      } else if (actionName === 'Edit Indexing Fields') {
        await this.runActionEditIndexingFields();
      } else if (actionName === 'Merge From This Record') {
        await this.runActionMergeFromThisRecord();
      } else if (actionName === 'Cancel Merge Record') {
        this.$store.commit('setMergeFromDocId', '');
      } else if (actionName === 'Merge To This Record') {
        await this.runActionMergeToThisRecord();
      } else if (actionName === 'Create This Record') {
        await this.runActionCreateThisRecord();
      } else if (actionName === 'View Document') {
        this.runActionViewDocurment();
      } else if (actionName === 'Edit This User') {
        this.$emit('onEditUser', this.document)
      } else if (actionName === 'Delete This User') {
        this.$emit('onDeleteUser', this.document)
      } else if (actionName === 'Reset Security Questions') {
        this.$emit('onRequestResetAccountQuestion', this.document)
      } else if (actionName === 'Reset Password') {
        this.$emit('onRequestResetPassword', this.document)
      } else if (actionName === 'Edit this Province') {
        this.$emit('onEditProvince', this.document)
      } else if (actionName === 'Delete this Province') {
        this.$emit('onDeleteProvince', this.document)
      }
      this.$store.commit('setShowProgressAPI', false);
      this.$emit('onSelectAction', actionName);
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
        action: subItem.action,
      };
      if(this.document.doc_id === 'NDE_DUMMY_DATA'){
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
    },
    async runSubItemAction(subItem) {
      this.closeOnContentClick = true;
      this.$store.commit('setShowProgressAPI', true);
      if (subItem.action) {
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
    },
    async runActionMergeToThisRecord() {
      const mergeFromDocId = this.$store.state.mergeFromDocId;
      if (mergeFromDocId === this.document.doc_id) {
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
      const confirmed = await this.$swal({
        title: 'Record Merge Confirmation',
        text: 'Are you sure you want to merge to this record?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
      });
      if (!confirmed.value) return false;
      const resulMergeRecordOauth = await this.$store.dispatch('callAPI', {
        url: '/mergeRecordOauth',
        method: 'post',
        data: {
          profile_id: this.$store.state.currentProgram.id,
          from_id: mergeFromDocId,
          to_id: this.document.doc_id,
        },
      });
      if (resulMergeRecordOauth.message !== 'success') {
        this.$store.commit('setShowProgressAPI', false);
        return this.$swal({
          icon: 'error',
          text: this.getMessageResult(resulMergeRecordOauth),
        });
      }
      this.$store.commit('setShowProgressAPI', false);
      this.$store.dispatch({ type: 'searchImages' });
    },
    async runActionCreateThisRecord() {
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
        dataDoubleEntry?.doubleEntry?.length === 0 &&
        dataDoubleEntry?.requiredEntry?.length === 0 &&
        dataDoubleEntry?.groupRequiredEntry?.length === 0
      ) {
        const resultCreateDocumentOauth = await this.$store.dispatch('callAPI', {
          url: '/createDocumentOauth',
          method: 'post',
          data: {
            profileId: this.$store.state.currentProgram.id,
            Searchitems: this.$store.state.Neusearch.Searchitems,
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
      }
      this.$store.commit('setShowProgressAPI', false);
      if (
        dataDoubleEntry?.doubleEntry?.length !== 0 ||
        dataDoubleEntry?.requiredEntry?.length !== 0 ||
        dataDoubleEntry?.groupRequiredEntry?.length !== 0
      ) {
        this.$store.commit('setDataDoubleEntry', dataDoubleEntry);
        this.$store.commit('openModalDoubleEntry', this.document.doc_id);
      }
    },
    runActionViewDocurment() {
      console.log(this.document);
      // this.$store.commit('openViewDocument', this.document);
      this.$emit('onViewDocument', this.document);
    },
  },
};
</script>

<style scoped>
.fw-400 {
  font-weight: 400 !important;
}
</style>
