<template>
    <v-simple-table>
        <template v-slot:default>
            <thead class="nde-table-data-feed-header">
                <tr>
                    <th></th>
                    <th>File #</th>
                    <th>Street Number</th>
                    <th>City</th>
                    <th>County Name</th>
                    <th>State</th>
                    <th>Date</th>
                    <th>Disposition Date</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in items" :key="item.id">
                    <td>
                        <v-chip color="red" label>
                            <strong>Select</strong>
                        </v-chip>
                    </td>
                    <td>{{ item.fileNumber }}</td>
                    <td>{{ item.streetNumber }}</td>
                    <td>{{ item.city }}</td>
                    <td>{{ item.countryName }}</td>
                    <td>{{ item.state }}</td>
                    <td>{{ item.date }}</td>
                    <td>{{ item.dispositionDate }}</td>
                </tr>
            </tbody>
        </template>
    </v-simple-table>
</template>

<script>
export default {
    name: "NdeTableDataFeed",
    props: {
        items: {
            type: Array,
            default: () => [],
        },
    },
};
</script>

<style scoped lang="scss">
.nde-table-data-feed-header {
    background: #ebeced;
    th {
        color: #343c42 !important;
    }
}
tbody tr:nth-of-type(odd) {
    background-color: #f7f7f7;
}
th,
td {
    font-family: "Poppins";
    font-style: normal;
    color: #343c42;
}
.v-chip {
    &.red {
        background: #f9e1e1 !important;
        border: 1px solid #c32c39 !important;
        box-sizing: border-box;
        border-radius: 4px;
    }
    strong {
        text-transform: capitalize;
        color: #c32c39;
        font-size: 0.75rem;
        line-height: 1rem;
    }
}
</style>
