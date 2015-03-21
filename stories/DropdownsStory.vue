<template>
  <div>
    <div>
      <v-menu offset-y content-class="nde-dropdown-mt">
        <template v-slot:activator="{ on, attrs, value }">
          <nde-button color="primary" v-bind="attrs" v-on="on" outlined>
            Action
            <v-icon right color="primary">{{
              value ? "mdi-menu-up" : "mdi-menu-down"
            }}</v-icon>
          </nde-button>
        </template>

        <v-list>
          <v-list-item-group>
            <v-list-item v-for="(item, index) in items" :key="index">
              <v-list-item-title>{{ item.title }}</v-list-item-title>
              <v-list-item-icon>
                <v-icon v-text="item.icon"></v-icon>
              </v-list-item-icon>
            </v-list-item>
          </v-list-item-group>
        </v-list>
      </v-menu>
    </div>

    <div class="mt-2">
      <v-menu offset-y content-class="nde-dropdown-mt">
        <template v-slot:activator="{ on, attrs, value }">
          <nde-button color="success" v-bind="attrs" v-on="on" outlined>
            Download
            <v-icon right color="success">{{
              value ? "mdi-menu-up" : "mdi-menu-down"
            }}</v-icon>
          </nde-button>
        </template>

        <v-list>
          <v-list-item-group>
            <v-list-item v-for="(item, index) in items" :key="index">
              <v-list-item-title>{{ item.title }}</v-list-item-title>
              <v-list-item-icon>
                <v-icon v-text="item.icon"></v-icon>
              </v-list-item-icon>
            </v-list-item>
          </v-list-item-group>
        </v-list>
      </v-menu>

      <v-menu offset-y content-class="nde-dropdown-mt">
        <template v-slot:activator="{ on, attrs, value }">
          <nde-button color="primary" v-bind="attrs" v-on="on" outlined>
            Save To View
            <v-icon right color="primary">{{
              value ? "mdi-menu-up" : "mdi-menu-down"
            }}</v-icon>
          </nde-button>
        </template>

        <v-list>
          <v-list-item-group>
            <v-list-item v-for="(item, index) in items" :key="index">
              <v-list-item-title>{{ item.title }}</v-list-item-title>
              <v-list-item-icon>
                <v-icon v-text="item.icon"></v-icon>
              </v-list-item-icon>
            </v-list-item>
          </v-list-item-group>
        </v-list>
      </v-menu>

      <v-menu offset-y content-class="nde-dropdown-mt">
        <template v-slot:activator="{ on, attrs, value }">
          <nde-button color="dark" v-bind="attrs" v-on="on">
            <v-icon left color="primary">mdi-pound</v-icon>
            Main
            <v-icon right color="dark">{{
              value ? "mdi-menu-up" : "mdi-menu-down"
            }}</v-icon>

            <v-icon right color="dark">mdi-pencil</v-icon>
          </nde-button>
        </template>

        <v-list>
          <v-list-item-group>
            <v-list-item v-for="(item, index) in items" :key="index">
              <v-list-item-title>{{ item.title }}</v-list-item-title>
              <v-list-item-icon>
                <v-icon v-text="item.icon"></v-icon>
              </v-list-item-icon>
            </v-list-item>
          </v-list-item-group>
        </v-list>
      </v-menu>

      <v-menu offset-y content-class="nde-dropdown-mt">
        <template v-slot:activator="{ on, attrs }">
          <nde-button color="primary" v-bind="attrs" v-on="on">
            <v-icon left>mdi-magnify</v-icon>
            Order search results via {{ selectedOrder }}
          </nde-button>
        </template>

        <v-list>
          <v-list-item-group>
            <v-list-item
              v-for="(item, index) in tableFields"
              :key="index"
              @click="selectOrderField(item)"
            >
              <v-list-item-title>{{ item }}</v-list-item-title>
            </v-list-item>
          </v-list-item-group>
        </v-list>
      </v-menu>
    </div>
  </div>
</template>

<script>
import NdeButton from "../resources/js/components/Button/NdeButton.vue";

export default {
  components: { NdeButton },
  data() {
    return {
      items: [
        { title: "Create Subtab", icon: "mdi-file-check-outline" },
        { title: "Delete Page", icon: "mdi-file-check-outline" },
        { title: "Cut Page", icon: "mdi-file-check-outline" },
        { title: "Copy Page", icon: "mdi-file-check-outline" },
      ],
      tableFields: [
        "File",
        "Street Number",
        "City",
        "Country Name",
        "State",
        "Date",
        "Deposition Date",
      ],
      selectedOrder: "Deposition Date",
    };
  },
  methods: {
    selectOrderField(value) {
      this.selectedOrder = value;
    },
  },
};
</script>
