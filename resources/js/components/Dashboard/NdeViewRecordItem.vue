<template>
  <v-card class="pa-3 mb-3 nde-view-record-item">
    <div class="d-flex justify-space-between align-center">
      <div class="d-flex nde-view-record-item-title">
        <div
          class="d-flex"
          v-if="!isEditTab"
          @click="isExpand = !isExpand"
          tabindex="0"
          :aria-label="title"
          :aria-expanded="isExpand ? 'true' : 'false'"
          role="button"
        >
          <v-icon>{{ isExpand ? 'mdi-menu-down' : 'mdi-menu-right' }}</v-icon>
          <div class="ml-3">
            <div class="text-h6">{{ title }} Tab</div>
          </div>
        </div>

        <v-tooltip
          right
          color="#547D36"
          bottom
          v-if="!isEditTab && authRoles.includes('MODIFYRECORD')"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-btn text icon class="mr-3" v-bind="attrs" v-on="on" @click="editTabName">
              <v-icon>mdi-folder-move-outline</v-icon>
            </v-btn>
          </template>
          <span>Edit</span>
        </v-tooltip>

        <v-row v-if="isEditTab">
          <v-col cols="6">
            <div class="d-flex">
              <v-combobox
                solo
                dense
                attach
                hide-details
                :items="[]"
                v-model="currentTabName"
                append-icon=""
                maxlength="256"
                item-text="name"
                item-value="tab_id"
                label="Please input the tab name"
                class="mr-3 input-edit-tab"
                ref="combobox"
              >
              </v-combobox>

              <v-tooltip right color="#547D36" bottom>
                <template v-slot:activator="{ on, attrs }">
                  <v-btn
                    text
                    icon
                    class="mr-3"
                    v-bind="attrs"
                    v-on="on"
                    @click="saveTabName"
                    :loading="isRenaming"
                  >
                    <v-icon>mdi-content-save</v-icon>
                  </v-btn>
                </template>
                <span>{{ isRenaming ? 'Saving...' : 'Save' }}</span>
              </v-tooltip>

              <v-tooltip right color="#547D36" bottom>
                <template v-slot:activator="{ on, attrs }">
                  <v-btn
                    text
                    icon
                    class="mr-3"
                    v-bind="attrs"
                    v-on="on"
                    @click="cancelTabEdit"
                    :loading="isRenaming"
                  >
                    <v-icon>mdi-close</v-icon>
                  </v-btn>
                </template>
                <span>Cancel</span>
              </v-tooltip>
            </div>
          </v-col>
        </v-row>
      </div>
    </div>

    <div class="view-record-table">
      <nde-view-record-item-table
        v-for="(item, index) in recordItems"
        :key="index"
        :tableData="item"
        :tabName.sync="title"
        @onRenameSuccess="onRenameSuccess"
        :isExpand="isExpand"
        :doc_id="doc_id"
        @onDeletedTab="onDeletedTab"
      />
    </div>
  </v-card>
</template>

<script>
import axios from 'axios';
import { mapState } from 'vuex';
import NdeViewRecordItemTable from './NdeViewRecordItemTable.vue';
import NdeButton from '../Button/NdeButton.vue';

export default {
  components: { NdeViewRecordItemTable, NdeButton },
  data() {
    return {
      isExpand: false,
      authRoles: [],
      isEditTab: false,
      currentTabName: '',
      isRenaming: false,
    };
  },

  props: {
    recordItems: {
      type: Array,
      required: true,
    },
    title: {
      type: String,
      required: true,
    },
    doc_id: {
      type: String,
      required: true,
    },
    isFirst: {
      type: Boolean,
      default: false,
    },
    openTab: {
      type: String,
      required: false,
      default: '',
    },
  },

  computed: {
    ...mapState(['currentProgram']),
  },

  methods: {
    onRenameSuccess(data) {
      this.title = data;
    },
    onDeletedTab() {
      this.$emit('onDeletedTab');
    },
    editTabName() {
      this.currentTabName = this.title;
      this.isEditTab = true;
    },
    cancelTabEdit() {
      this.isEditTab = false;
    },

    async saveTabName() {
      this.$refs.combobox.blur();
      this.$nextTick(async () => {
        if (!this.currentTabName) {
          this.$swal({
            icon: 'error',
            text: 'Please input the tab name!',
          });
          return;
        }

        this.isRenaming = true;
        const postData = {
          document_id: this.recordItems[0].doc_id,
          profile_id: this.currentProgram.id,
          new_name: this.currentTabName,
        };

        this.$store.dispatch('increaseCallCount');

        axios
          .post('changeTabNameOauth', postData)
          .then(() => {
            this.isRenaming = false;
            this.isEditTab = false;
            this.$emit('onSuccessEditTab');
          })
          .catch((error) => {
            this.isRenaming = false;
            this.isEditTab = false;
          });
      });
    },
  },

  mounted() {
    if (this.openTab) {
      if (this.openTab == this.title) {
        this.isExpand = true;
      }
    } else {
      if (this.isFirst) {
        this.isExpand = true;
      }
    }

    this.authRoles = this.userLoginRoles();
  },
  watch: {
    recordItems: {
      handler: function () {
        this.isExpand = false;
      },
      deep: true,
    },
  },
};
</script>

<style lang="scss" scoped>
.nde-view-record-item {
  &-title {
    cursor: pointer;
    width: 100%;
  }
}

button {
  text-transform: unset !important;
}

@media screen and (max-width: 48rem) {
  .view-record-table {
    overflow: hidden;
    overflow-x: scroll;
  }

  .nde-view-record-item {
    ::-webkit-scrollbar {
      -webkit-appearance: none;
      width: 0.625rem;
      height: 0.3125rem;
    }

    ::-webkit-scrollbar-thumb {
      border-radius: 4px;
      background-color: rgba(0, 0, 0, 0.2);
      -webkit-box-shadow: 0 0 1px rgba(255, 255, 255, 0.5);
    }
  }
}
</style>

<style lang="scss">
@media screen and (max-width: 48rem) {
  .input-edit-tab {
    .v-input__control {
      width: 12.5rem;
    }
  }
}
</style>
