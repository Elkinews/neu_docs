<template>
    <v-dialog
        v-if="isModalSortRecordOrder"
        :value="showModal"
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
                    <nde-button  color="success" @click="sortRecordOrder"
                        ><v-icon left>mdi-sort</v-icon>Sort Record Order
                    </nde-button>
                </v-card-actions>
                <div class="nde-table-search-result">
                    <nde-data-table
                        ref="ndeDataTable"
                        class="nde-table-search-result"
                        :headers="headers"
                        :items="items"
                    >
                        <template slot="item.sort_order" slot-scope="{ item }">
                            <v-text-field
                                v-model="item.sort_order"
                                type="number"
                                dense
                                solo
                                hide-details
                                onkeydown="return event.keyCode !== 69 
                                    && event.keyCode !== 187 
                                    && event.keyCode !== 189 
                                    && event.keyCode !== 190
                                    && event.keyCode !== 48"
                                min="1"
                                class="input-sort-order"
                            ></v-text-field>
                        </template>
                    </nde-data-table>
                </div>
            </v-card-text>
            <v-card-actions style="justify-content: end;">
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
import { every } from 'lodash'
import { mapState } from 'vuex';

export default {
    name: "NdeModalSortRecordOrder",
    components: {
        NdeDataTable,
        NdeButtonCancel,
        NdeButton
    },
    data() {
        return {
            showModal: true,
        }
    },
    props: {
        headers: {
            type: Array,
            default: () => []
        },
		items: {
            type: Array,
            default: () => []
        }
    },
    computed: {
        ...mapState([
            'isModalSortRecordOrder',
        ]),
    },

    methods: {

        hideModal() {
            this.$store.commit('closeModalSortRecordOrder');
        },
        hasDuplicateOrder() {
            var items = this.items.map(function(item){ return item.sort_order });
            return (new Set(items)).size !== items.length;
        },
        async sortRecordOrder() {
			this.$store.commit('setShowProgressAPI', true);
            const isNotValidOrder = this.items.filter(person => person.sort_order < 0);
            const hasDuplicateOrder = this.hasDuplicateOrder();
            if (isNotValidOrder.length > 0 || hasDuplicateOrder) {
                this.$store.commit('setShowProgressAPI', false);
				return this.$swal({
                    icon: 'error',
                    text: hasDuplicateOrder ? "Order must be unique value" : "Order must be a positive number",
				});
            } else {
                const resultSortRecordOrderOauth = await this.$store.dispatch('callAPI', {
                    url: '/sortRecordOrderOauth',
                    method: 'post',
                    data: {
                        profile_id: this.$store.state.currentProgram.id,
                        document_ids: (this.items || [])?.map(item => {
                            return {
                                documentId: item.doc_id,
                                value: item.sort_order << 0,
                            };
                        }) || [],
                    },
                });
                this.$store.commit('setShowProgressAPI', false);
                if (resultSortRecordOrderOauth.message !== 'success') {
                    return this.$swal({
                    icon: 'error',
                    text: this.getMessageResult(resultSortRecordOrderOauth),
                    });
                }
                await this.$swal({
                    icon: 'success',
                    text: "Sort Order Changed!",
                });
                this.$store.commit('setAllowSortRecordOrder', false);
                this.$emit('onCancelSelect');
                this.$store.dispatch({ type: 'searchImages' });
                this.hideModal();

            }
        }
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
