<template>
  <div
    class="nde-tab-item d-flex"
    v-bind:class="{ active: isActive }"
    @click="$emit('click')"
  >
    <v-icon left :color="isActive && 'primary'">{{ icon }}</v-icon>
    <span class="nde-tab-text" v-html="text"></span>
  </div>
</template>

<script>
export default {
  name: 'NdeTabItem',
  props: {
    icon: {
      type: String,
      required: true,
    },
    text: {
      type: String,
      required: true,
    },
    isActive: {
      type: Boolean,
      required: true,
    },
  },
};
</script>

<style scoped lang="scss">
.nde-tab-item {
  padding: 0.625rem;
  cursor: pointer;
  font-size: 1rem;

  &.active,
  &:hover {
    background: $primaryGreyColor;
    box-shadow: 0px 1px 3px rgba(95, 152, 193, 0.05),
      0px 2px 1px rgba(95, 152, 193, 0.03), 0px 1px 1px rgba(95, 152, 193, 0.35);
    border-radius: 4px;
    .nde-tab-text {
      color: $primaryColor;
    }
  }
}
</style>
