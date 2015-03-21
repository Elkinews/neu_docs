<template>
    <v-simple-table
        v-bind="$attrs"
        class="nde-table"
    >
        <template v-slot:default>
            <slot name="ndeTableContent"></slot>
        </template>
    </v-simple-table>
</template>

<script>
export default {
    name: "NdeTable",
};
</script>

<style scoped lang="scss">
.nde-table {
    :v-deep thead {
        background-color: #ebeced;
        height: 3.5rem;
        th {
            color: #343c42 !important;
            font-family: "Poppins";
            font-style: normal;
            font-weight: 500;
            font-size: 0.75rem;
            line-height: 1.5rem;
        }
    }
    :v-deep tbody {
        tr:nth-of-type(even) {
            background-color: #f7f7f7;
        }
        td {
            height: 3.5rem !important;
            font-family: "Poppins";
            font-style: normal;
            color: #343c42;
            font-size: 0.75rem !important;
            line-height: 1rem;
        }
    }
}
</style>
