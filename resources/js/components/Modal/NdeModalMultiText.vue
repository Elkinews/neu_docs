<template>
    <v-dialog
        v-model="showModal"
        max-width="500px"
        persistent
        class="edit-multi-text"
    >
        <v-card>
            <v-card-title>Edit {{fieldLabel}} </v-card-title>
            <v-card-text>
                <v-row v-for="(value, index) in options" :key="index">
                    <v-col>
                        <v-text-field
                            dense
                            solo
                            hide-details
                            v-model="options[index]"
                            append-outer-icon="mdi-close-circle"
                            @click:append-outer="deleteRow(index)"
                        ></v-text-field>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col md="11">
                        <v-btn block @click="addRow" class="mt-2">
                            Add Row
                        </v-btn>
                    </v-col>
                </v-row>

            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                    depressed
                    @click="hideModal"
                >
                    Cancel
                </v-btn>
                <v-btn
                    color="primary"
                    @click="saveChanges"
                >
                    Save
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script>

import NdeButtonPrimary from '../Button/NdeButtonPrimary.vue';
import NdeButtonCancel from '../Button/NdeButtonCancel.vue';
export default {
    name: 'NdeModalMultiText',
	components: {
	  NdeButtonPrimary,
	  NdeButtonCancel,
	},
    data() {
        return {
            options: [],
        }
    },
    props: {
        items: {
            type: Array,
            default: [],
        },
        showModal: {
            type: Boolean,
            default: false,
        },
        fieldLabel: {
            type: String,
            default: '',
        }
    },
    methods: {
        hideModal() {
            this.$emit('onClose');
        },
        addRow() {
            this.options.push('');
        },
        deleteRow(key) {
            this.options.splice(key, 1);
        },
        async saveChanges() {
            if (JSON.stringify(this.items) !== JSON.stringify(this.options)) {
                const confirmed = await this.$swal({
                    text: 'These changes will not be save until "Save Changes" button is click on Edit Indexing Fields modal.',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Save',
                    cancelButtonText: 'Cancel',
                    cancelButtonColor: '#9a9ea1',
                    reverseButtons: true,
                });

                if (confirmed.value) {
                    this.$emit('onUpdate', this.options.join());
                }
            } else {
                this.$emit('onClose');
            }
        },
    },
    watch:{
        showModal(value) {
            if (value) {
                this.options = [];
                this.items.forEach(element => {
                    this.options.push(element)
                });
            }
        }
    }
}
</script>
<style scoped lang="scss">
.edit-multi-text {
    .v-input__icon--append .v-icon { 
        color: rgb(196, 13, 13);
    }
}
</style>
