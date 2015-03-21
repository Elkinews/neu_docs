<template>
    <v-hover v-slot="{ hover }">
        <v-btn :class="{'btn-hover' : hover}" v-bind="$attrs" @click="$emit('click', $event)">
            <slot />
        </v-btn>
    </v-hover>
</template>

<script>
export default {
    emits: ['click'],

    props: {
        
    }
}
</script>

<style scoped lang="scss">
.v-btn {
    text-transform: unset !important;

    &.btn-hover {
       box-shadow: rgba(0,0,0,0.35) 0px 5px 15px !important;
    }
}
</style>
