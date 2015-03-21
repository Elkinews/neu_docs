<template>
  <v-menu v-bind="$attrs" :class="`nde-menu ${className}`">
    <template v-slot:activator="props">
      <div>
        <v-btn v-if="!hasSlot('activator')" v-bind="props.attrs" v-on="props.on">
          {{ name }}
        </v-btn>
        <slot v-if="hasSlot('activator')" name="activator" v-bind="props" />
      </div>
    </template>
    <slot v-if="hasSlot('menu')" name="menu" />
    <v-list v-if="!hasSlot('menu')" :class="`nde-menu__list ${className}`">
      <v-list-item class="nde-menu__list-item" v-for="(item, index) in menuItems" :key="index" link>
        <v-list-item-title v-if="!hasSlot('menu')" @click="onClick(item.value)">
          {{ hasSlot('menu.item') ? '' : item.text }}
          <slot v-if="hasSlot('menu.item')" name="menu.item" v-bind="item" />
        </v-list-item-title>
      </v-list-item>
    </v-list>
  </v-menu>
</template>

<script>
export default {
  name: 'NdeMenu',

  props: {
    name: {
      type: String,
      default: () => '',
    },
    menuItems: {
      type: Array,
      default: () => [],
    },
    className: {
      type: String,
      default: () => '',
    },
  },
  methods: {
    hasSlot(slot) {
      return !!this.$scopedSlots[slot];
    },
    onClick(menu) {
      this.$emit('onClick', menu);
    },
  },
};
</script>

<style lang="scss"></style>
