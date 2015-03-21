<template>
    <div class="nde-table-upload-csv-admin">
        <v-simple-table>
            <template v-slot:default>
                <thead>
                    <tr>
                        <th class="py-2" v-for="(item, index) in dataRows[0]" :key="index" @click="handleSetHeader(index)">
                            <v-select
                                background-color="#fff"
                                outlined
                                dense
                                hide-details
                                class="nde-select mx-auto"
                                :items="dataHeaders"
                                item-text="desc"
                                item-value="name"
                                :value="dataHeaders[0]"
                                @input="handleChange"
                            ></v-select>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="dataRows[0] && !hasHeader">
                        <th class="text-center" v-for="(header, indexHeader) in dataRows[0]" :key="indexHeader">{{header}}</th>
                    </tr>
                    <tr v-for="(row, index) in contentRows" :key="index">
                        <td class="text-center" v-for="(content, idx) in row" :key="idx">
                            {{content}}
                        </td>
                    </tr>
                </tbody>
            </template>
        </v-simple-table>
    </div>
</template>

<script>
export default {

    data() {
        return  {
            currentColumnIndex: null
        }
    },

    props: {
        dataHeaders: {
            type: Array,
            default: () => []
        },

        dataRows: {
            type: Array,
            default: () => []
        },

        hasHeader: {
            type: Boolean,
            default: true
        },

        selectedHeaders: {
            type: Array, 
            default: () => []
        }
    },

    computed: {
        contentRows() {
            let data = []
            this.dataRows.forEach((row, index) => {
                if(index !== 0) {
                    data.push(row)
                }
            })
            return data
        }
    },

    methods: {
        handleChange(value) {
            console.log('value', value)
            console.log('dataHeaders', this.dataHeaders[0])
            console.log('dataRows', this.dataRows)
            this.$emit('onChangeHeader', this.currentColumnIndex, value)
        },

        handleSetHeader(index) {
            this.currentColumnIndex = index
        }
    }
}
</script>

<style lang="scss">
.nde-table-upload-csv-admin {
    overflow-x: auto;

    table {
        max-width: 75rem;
        overflow-x: auto;

        .nde-select {
            width: 9.375rem;
        }
    }

    thead tr {
        background-color: #EBECED;

        th {
            width: 12.5rem;
            position: relative;

            &::after {
                content:""; 
                background: #CCCECF; 
                position: absolute; 
                top: 1.2rem;
                right: 0;
                height: 1rem; 
                width: 0.0625rem;
            }
            
        }
    }

    tbody tr:nth-of-type(even) {
        background-color: #f7f7f7;
    }

    tbody {
        td, th {
            position: relative;
            &::after {
                content:""; 
                background: #CCCECF; 
                position: absolute; 
                top: 1.2rem;
                right: 0;
                height: 1rem; 
                width: 0.0625rem;
            }
        }
    }
}
</style>