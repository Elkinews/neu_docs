<template>
  <div class="d-flex justify-space-between">
    <nde-button icon @click="clickAction(ACTIONS.DOWNLOAD)">
      <v-icon color="primary">mdi-download</v-icon>
    </nde-button>

    <nde-menu
      :menuItems="shareActions"
      :onClick="clickAction"
      className="nde-menu-share"
      offset-y
      @onClick="clickAction"
    >
      <template slot="activator" slot-scope="{ attrs, on }">
        <nde-button v-bind="attrs" v-on="on" icon>
          <v-icon color="primary">mdi-share-variant</v-icon>
        </nde-button>
      </template>
      <template slot="menu.item" slot-scope="menuItem">
        <v-list-item-subtitle class="text--darken-4 fw-400" v-text="menuItem.text" />
      </template>
    </nde-menu>

    <nde-button icon @click="clickAction(ACTIONS.DELETE)">
      <v-icon color="error">mdi-delete</v-icon>
    </nde-button>
  </div>
</template>

<script>
import NdeButton from '@components/Button/NdeButton.vue';
import NdeMenu from '@components/Menu/NdeMenu.vue';
import { ACTIONS } from '@utils/constants';
export default {
  data() {
    return {
      ACTIONS: ACTIONS,
      shareActions: [
        {
          text: 'Share with neuDocs Enterprise User',
          value: ACTIONS.SHARE,
        },
        {
          text: 'Temporary Link for External User',
          value: ACTIONS.GENERATE,
        },
        {
          text: 'Promote to Record',
          value: ACTIONS.PROMOTE,
        },
      ],
    };
  },
  props: {
    item: {
      type: Object,
    },
  },
  methods: {
    clickAction(action) {
      this.$emit('onClick', {action: action, item: this.item});
    },
  },
  components: {
    NdeButton,
    NdeMenu,
  },
};
</script>
