<template>
  <v-container fluid>
    <v-card class="mt-2 help-center">
      <v-card-title class="help-title mb-2">NeuDou Enterprise Basics</v-card-title>
      <v-row>
        <v-col cols="12" md="4">
          <v-card class="mx-auto" max-width="600">
            <v-list dense class="pa-3">
              <v-list-item-group v-model="selectedItem" mandatory>
                <v-list-item v-for="(item, i) in menuItems" :key="i">
                  <v-list-item-icon>
                    <v-icon v-text="item.icon"></v-icon>
                  </v-list-item-icon>
                  <v-list-item-content>
                    <v-list-item-title v-text="item.title"></v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list-item-group>
            </v-list>
          </v-card>
        </v-col>
        <v-col cols="12" md="8">
          <v-card class="pb-2">
            <v-card-title class="help-item-title div-hover pb-0 mb-3">
              {{ menuContent.title }}
            </v-card-title>
            <v-card-text v-show="menuContent.title_description" class="mb-3">
              <v-alert text color="success">
                <span
                  class="help-item-title-description"
                  v-html="menuContent.title_description"
                ></span>
              </v-alert>
            </v-card-text>
            <v-card-text v-show="menuContent.help_fields.length">
              <nde-data-table
                :headers="headers"
                :items="menuContent.help_fields"
                class="nde-table-search-result"
              >
                <template slot="item.field" slot-scope="{ item }">
                  <p class="field-name mb-2 mt-5">{{ item.name }}</p>
                  <p class="field-description" v-html="item.description"></p>
                </template>
                <template slot="item.explanation" slot-scope="{ item }">
                  <iframe
                    class="mt-5"
                    width="364px"
                    height="253px"
                    src="https://www.youtube.com/embed/xcJtL7QggTI"
                    title="YouTube video player"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                  ></iframe>
                </template>
              </nde-data-table>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-card>
  </v-container>
</template>

<script>
import NdeDashboardLayout from '../../shared/layouts/dashboard/NdeDashboardLayout.vue';
import NdeDataTable from '@components/Table/NdeDataTable.vue';

export default {
  layout: NdeDashboardLayout,
  components: {
    NdeDataTable,
  },
  data() {
    return {
      menuItems: [],
      selectedItem: 0,
      headers: [
        {
          text: 'Field',
          value: 'field',
          class: 'col-field',
          sortable: false,
          width: '50%',
        },
        {
          text: 'Explanation',
          value: 'explanation',
          class: 'col-explanation',
          sortable: false,
          width: '50%',
        },
      ],
    };
  },
  computed: {
    menuContent() {
      return (
        this.menuItems[this.selectedItem] ||
        this.menuItems[0] || {
          help_fields: [],
        }
      );
    },
  },
  async created() {
    window.document.title = 'neuDocs Enterprise Issues';
    this.$store.dispatch('handleVisibleProgramBtn', false);
    this.$store.commit('setShowProgressAPI', true);
    const resultGetHelpContentsOauth = await this.$store.dispatch('callAPI', {
      url: '/getHelpContentsOauth',
      method: 'post',
      data: {},
    });
    this.$store.commit('setShowProgressAPI', false);
    if (resultGetHelpContentsOauth.message !== 'success') {
      return this.$swal({
        icon: 'error',
        text: this.getMessageResult(resultGetHelpContentsOauth),
      });
    }
    const reultHelps = resultGetHelpContentsOauth?.data?.data;
    const menuItems = [];
    for (const helpTitle in reultHelps) {
      const helpItems = reultHelps[helpTitle];
      if (typeof helpItems === 'string' || !helpTitle.includes('Basics')) {
        continue;
      }
      menuItems.push(
        ...helpItems.map((item, index) => ({
          ...item,
          icon: this.getIconMenu(item.title),
          key: index,
        })),
      );
    }
    this.menuItems = menuItems;
  },
  methods: {
    getIconMenu(title) {
      let icon = 'mdi-flag';
      if (title === 'View Document') {
        icon = 'mdi-eye';
      } else if (title === 'Key Search Results') {
        icon = 'mdi-account-search';
      } else if (title === 'Edit Document without Bookmark') {
        icon = 'mdi-pencil';
      } else if (title === 'Administration') {
        icon = 'mdi-cog-outline';
      } else if (title === 'Full Text Search') {
        icon = 'mdi-magnify';
      } else if (title === 'Create New User') {
        icon = 'mdi-account-plus-outline';
      } else if (title === 'Change Password') {
        icon = 'mdi-lock-question';
      } else if (title === 'Logout') {
        icon = 'mdi-logout';
      } else if (title === 'Edit Document with Bookmark') {
        icon = 'mdi-bookmark';
      } else if (title === 'Actions') {
        icon = 'mdi-cursor-default-click-outline';
      }
      return icon;
    },
  },
};
</script>

<style lang="scss" scoped>
.help-center {
  background-color: unset;
  box-shadow: unset !important;

  .v-list-item {
    @extend %fontNormal;
    font-size: 1rem;
    line-height: 1.5rem;
    color: $darkGreyColor;
    border-radius: 8px;
  }
  .v-list-item--active {
    color: $errorColor;
    background: $errorGreyColor;
    &:before {
      border-radius: 8px;
    }
  }

  .help-title {
    @extend %fontNormalBold;
    font-size: 1.5rem;
    line-height: 2rem;
    color: $darkGreyColor;
    padding: 0px !important;
  }
  .help-item-title {
    @extend %fontNormalBold;
    font-size: 1.25rem;
    line-height: 2rem;
    color: $darkGreyColor;
  }
  .help-item-title-description {
    @extend %fontNormal;
    font-size: 0.813rem;
    line-height: 1.5rem;
    color: $darkGreyColor;
  }
  .p-content {
    @extend %fontNormal;
    white-space: pre-line;
    color: $darkGreyColor;
    font-size: 0.875rem;
    line-height: 1.5rem;
  }
}
.div-hover:hover {
  text-decoration: underline;
  cursor: pointer;
}

:v-deep .nde-table-search-result {
  thead {
    background: $primaryGreyColor !important;
  }

  tbody tr:nth-of-type(even) {
    background-color: $lightGreyColor !important;
  }
  tbody td,
  thead th {
    border-bottom: 0px !important;
  }

  tbody {
    vertical-align: top;
    td {
      font-size: 0.813rem;
      line-height: 1.5rem;
      .field-name {
        @extend %fontNormalBold;

        color: $darkGreyColor;
      }
      .field-description {
        @extend %fontNormal;
        color: $drakColor;
      }
    }
  }
}
</style>
