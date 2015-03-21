<template>
  <v-menu offset-y>
    <template v-slot:activator="{ on, attrs }">
      <v-btn
        icon
        v-bind="attrs"
        v-on="on"
      >
        <v-icon>mdi-dots-horizontal</v-icon>
      </v-btn>
    </template>
    <v-list>
      <v-list-item
        v-for="(action, index) in actions"
        :key="index"
        link
        @click="onClick(action.title)"
      >
        <v-list-item-title>
          <v-icon>{{ action.icon }}</v-icon> {{ action.title }}
        </v-list-item-title>
      </v-list-item>

      <v-list-item
        link
        @click="onClick('Copy File')"
        v-if="((!currentCopyFileOrg ||( currentCopyFileOrg && currentCopyFileOrg?.nuid != item.nuid)) && copyFile)"
      >
        <v-list-item-title>
          <v-icon>mdi-clipboard-check-outline</v-icon> Copy File
        </v-list-item-title>
      </v-list-item>

      <v-list-item
        link
        @click="onClick('Cancel Copy File')"
        v-if="currentCopyFileOrg && currentCopyFileOrg?.nuid == item.nuid"
      >
        <v-list-item-title>
          <v-icon>mdi-clipboard-off-outline</v-icon> Cancel Copy File
        </v-list-item-title>
      </v-list-item>
    </v-list>
  </v-menu>
</template>
<script>
import { mapState } from 'vuex';

export default {
  data: () => ({
    authRoles: [],
    options: [
      {
        title: 'Copy To My Documents',
        icon: 'mdi-content-copy'
      },
      {
        title: 'Replace' ,
        icon: 'mdi-file-replace-outline'
      },
      {
        title: 'Download' ,
        icon: 'mdi-file-download-outline'
      },
      {
        title: 'Delete' ,
        icon: 'mdi-delete'
      },
      {
        title: 'Rename File',
        icon: 'mdi-pencil'
      }
    ],
  }),

  props: {
    item: {
      type: Object,
      required: true
    }
  },

  computed: {
    ...mapState(['currentCopyFileOrg']),
    actions: function () {
      let actions = this.options.filter((option) => {
        // Check if auth user has DELETETAB role else remove delete button from action
        if (
          option.title !== 'Delete' ||
          (option.title === 'Delete' && this.authRoles.includes('DELETETAB'))
        ) {
          return option;
        }
      })

      actions = actions.filter((option) => {
        if (
          option.title !== 'Replace' ||
          (option.title === 'Replace' && this.authRoles.includes('EUPLOAD'))
        ) {
          return option;
        }
      })

      actions = actions.filter((option) => {
        if (
          option.title !== 'Copy To My Documents' ||
          (option.title === 'Copy To My Documents' && this.authRoles.includes('EUPLOAD'))
        ) {
          return option;
        }
      })

      actions = actions.filter((option) => {
        if (
          option.title !== 'Download' ||
          (option.title === 'Download' && this.authRoles.includes('ALLOWDOWNLOAD'))
        ) {
          return option;
        }
      })
      return actions;
    },
    copyFile: function () {
      return this.authRoles.includes('EUPLOAD')
    }
  },

  mounted() {
    this.authRoles = this.userLoginRoles();
  },

  methods: {
    onClick(action) {
      this.$emit('onClick', action);
    },
  },
}
</script>
