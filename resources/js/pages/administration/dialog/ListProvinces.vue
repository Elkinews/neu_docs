<template>
     <v-dialog
        v-model="dialog"
        scrollable
        max-width="1208px"
        content-class="dialog-w-custombar"
    >

        <v-card>
            <v-card-title class="d-flex justify-space-between">
                <p class="mb-0">
                    Manage Province
                </p>
            </v-card-title>

            <v-card-text>
                <v-btn
                    depressed outlined primary light color="success"
                    width="180px"
                    class="btn-create-province my-5"
                >
                Create New Province
                </v-btn>

                <nde-data-table :headers="headers" :items="data" class="nde-table-search-result">
                    <template slot="item.neubus_predefined" slot-scope="{ item }">
                        {{ item.neubus_predefined === 'f' ? 'No' : 'Yes' }}
                    </template>
                    <template slot="item.actions">
                    <nde-button-menu-items :items="actions" />
                    </template>
                </nde-data-table>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
import NdeDataTable from "@components/Table/NdeDataTable.vue";
import NdeButtonMenuItems from '@components/Table/NdeSearchResult/NdeButtonMenuItems.vue';

export default {
    components: {
        NdeDataTable,
        NdeButtonMenuItems
    },

    data() {
        return {
            dialog: false,
            headers: [
                {
                    text: 'Name',
                    value: 'name',
                    class: 'col-name'
                },
                {
                    text: 'Neubus Predefined',
                    value: 'neubus_predefined',
                    class: 'col-neubus-predefined'
                },
                {
                    text: 'Date Created',
                    value: 'created_on',
                    class: 'col-created-created'
                },
                {
                    text: '',
                    value: 'actions',
                    class: 'col-actions',
                    sortable: false
                }
            ],
            data: [],
            actions: [
               {
                    id: 1,
                    content: 'Edit this Province',
                    status: 'active',
                    icon: 'edit.png',
                },


                {
                    id: 2,
                    content: 'Delete this Province',
                    status: 'warning',
                    icon: 'delete.png',
                },
            ]
        }
    },

    methods: {
        getProvinces() {
            this.$store.dispatch('getProvinces', {page: 1, page_size: 10}).then(response => {
                if(response && response.data && response.data.provinces && response.data.provinces.length) {
                    this.data = response.data.provinces
                }
            })
        }
    },

    watch: {
        dialog(newVal, oldVal) {
            if(newVal !== oldVal && newVal) {
                this.getProvinces()
            }
        }
    }
}
</script>

<style lang="scss" scoped>
.btn-create-province {
    text-transform: unset !important;
    font-size: 14px
}

.nde-table-search-result {
    thead {
        background: #ebeced;
    }

    tbody tr:nth-of-type(even) {
        background-color: $lightGreyColor;
    }
}

.dialog-w-custombar {
    /* width */
    ::-webkit-scrollbar {
    width: 4px;
    }

    /* Track */
    ::-webkit-scrollbar-track {
    background: #f1f1f1;
    }

    /* Handle */
    ::-webkit-scrollbar-thumb {
    background: $primaryColor;
        border-radius: 8px

    }

    /* Handle on hover */
    ::-webkit-scrollbar-thumb:hover {
    background: $primaryColor;
    }
}
</style>
