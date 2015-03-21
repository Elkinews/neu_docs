<template>
  <v-dialog
    v-if="showModal"
    :value="true"
    @click:outside="persistent ? '' : closeModal"
    v-bind="$attrs"
    :width="modalWith"
    :persistent="persistent"
    :content-class="classes"
  >
    <v-card class="pa-5">
      <v-card-title class="font-weight-bold d-flex justify-space-between">
        <h3>{{ title }}</h3>
        <v-icon
          v-if="!isNotShowClose"
          aria-label="Close"
          size="24px"
          color="red"
          @click="closeModal"
        >
          mdi-close
        </v-icon>
      </v-card-title>
      <v-card-subtitle class="ml-2">
        <slot name="ndeModalSubTitle"></slot>
      </v-card-subtitle>
      <v-card-text>
        <slot name="ndeModalContent"></slot>
      </v-card-text>
      <v-card-actions class="ml-3 mr-3">
        <slot name="ndeModalAction"></slot>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  name: 'NdeModal',
  props: {
    showModal: {
      type: Boolean,
      default: false,
    },
    title: {
      type: String,
      default: 'Modal Title',
    },
    isNotShowClose: {
      type: Boolean,
      default: false,
    },
    modalWith: {
      type: String,
      default: '',
    },
    persistent: {
      type: Boolean,
      default: true,
    },
    classes: {
      type: String,
      default: '',
    },
  },
  methods: {
    // hideModal() {
    //     this.$emit("update:showModal", !this.showModal);
    // },
    closeModal() {
      console.log('closeModal');
      this.$emit('update:closeModal');
    },
  },
};
</script>

<style scoped lang="scss">
.v-card__title {
  .v-icon:hover {
    &::after {
      @extend %afterIconModalClose;
    }
  }
}
</style>
