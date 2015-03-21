<template>
    <v-dialog
        v-if="showModal"
        v-model="showModal"
        @click:outside="hideModal"
    >
        <v-card class="pa-5">
            <v-card-title
                class="
                    text-sm-left
                    font-weight-bold
                    d-flex
                    justify-space-between
                    ml-2
                "
            >
                <h3>Bulk</h3>
                <v-icon
                    aria-label="Close"
                    size="24px"
                    color="red"
                    @click="hideModal"
                >
                    mdi-close
                </v-icon>
            </v-card-title>
            
            <v-card-text>
                <v-card-actions>
                    <nde-button  color="success" @click="downloadAsPDF"
                        ><v-icon left>mdi-download</v-icon>Download
                    </nde-button>
                </v-card-actions>

                <i class="mb-2">You can drag each row to rearrange order</i>
                <div class="nde-table-search-result">
                    <v-data-table
                        class="nde-data-table"
                        v-bind="$attrs"
                        :items="data"
                        :headers="visibleColumns"
                        v-sortable-data-table
                        @sorted="saveOrder"
                    >
                    </v-data-table>
                </div>
            </v-card-text>
            <v-card-actions>
                <nde-button-cancel
                    @click="hideModal"
                    title="Close"
                    :styles="{ width: '8.25rem' }"
                    class="mr-3"
                ></nde-button-cancel>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import NdeDataTable from "../Table/NdeDataTable.vue";
import NdeButtonCancel from "../Button/NdeButtonCancel.vue";
import NdeButton from '../Button/NdeButton.vue';

import { mapState } from 'vuex';
import Sortable from 'sortablejs';

export default {
    name: "NdeModalDownloadPdf",
    components: {
        NdeDataTable,
        NdeButtonCancel,
        NdeButton
    },
    data() {
        return {
            showModal: false,
            visibleColumns: [],
            page: 1,
            itemPerPage: 10,
        }
    },
    props: {
        data: {
            type: Array,
            default: () => []
        }
    },
    computed: {
        ...mapState([
            'currentControls',
            'totalResults'
        ]),
    },
    directives: {
    sortableDataTable: {
        bind (el, binding, vnode) {
            const options = {
                animation: 150,
                onUpdate: function (event) {
                    vnode.child.$emit('sorted', event)
                }
                }
                Sortable.create(el.getElementsByTagName('tbody')[0], options)
            }
        }
    },

    methods: {
        saveOrder(event) {
            const movedItem = this.data.splice(event.oldIndex, 1)[0];
            this.data.splice(event.newIndex, 0, movedItem);
        },

        hideModal() {
            this.showModal = !this.showModal
        },

        downloadAsPDF() {
            let imageIDs = []
        
            this.data.forEach(value => {
                imageIDs.push(value.image_id)
            })

            let payload = {
                profile: this.$store.state.currentProgram.id,
                imageIDs: imageIDs,
                status: "PROCESSING",
                group: "true"
            }

            this.$store.dispatch('increaseCallCount');

            axios
            .post('/queueDownloadOauth', payload)
            .then((response) => {
                console.log('QueueDownloadOauth response', response)
                const filename = response?.data?.data?.data?.filename

                const message = `Download ${filename} is processing. Go to the My Documents link to download the file.`
                alert(message)
                this.hideModal()
                this.$emit('onClearData')
            })
            .catch((error) => {
                console.error(error);
            });
        }
    },

    watch: {
        currentControls: function (val) {
            if (val) {
                this.visibleColumns = [];

                val.map((field) => {
                    const keys = Object.keys(field);
                    if (field[keys[0]].display) {
                        this.visibleColumns.push({
                            text: field[keys[0]].label,
                            value: keys[0],
                        });
                        this.$store.commit('setVisibleColumns', this.visibleColumns);
                    }
                });
            }
        },
    }
};
</script>

<style scoped lang="scss">
.v-card__title {
    .v-icon:hover {
        &::after {
            background-color: currentColor;
            border-radius: 50%;
            content: "";
            display: inline-block;
            height: 100%;
            left: 0;
            pointer-events: none;
            position: absolute;
            top: 0;
            transform: scale(1.3);
            width: 100%;
            transition: opacity 0.1s cubic-bezier(0.4, 0, 0.6, 1);
            opacity: 0.12;
        }
    }
}
.v-card__actions {
    justify-content: end;
}
</style>