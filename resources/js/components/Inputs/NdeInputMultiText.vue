<template>
    <v-row v-if="searchFieldInfo.input_data_type == 'MULTITEXT' && searchFieldInfo.type == 'TEXT'">
        <v-col>
            <v-select
                chips
                multiple
                solo
                dense
                hide-details
                readonly
                item-text="value"
                item-value="key"
                :id="'inputField_' + fieldName"
                :label="searchFieldInfo.label"
                :menu-props="{ offsetY: true }"
                :items="multiTextValue"
                v-model="multiTextValue"
                append-outer-icon="mdi-pencil"
                append-icon=""
                @click:append-outer="showEditModal = true"
            >
            <template v-slot:item="{ item }">
                {{item}}
            </template>
            </v-select>
        </v-col>
        <nde-modal-multi-text
            :items="multiTextValue"
            :show-modal="showEditModal"
            @onUpdate="updateChanges"
            @onClose="showEditModal = false"
            :fieldLabel="searchFieldInfo.label"
        ></nde-modal-multi-text>
    </v-row>
</template>
<script>

import NdeButtonPrimary from '../Button/NdeButtonPrimary.vue';
import NdeButtonCancel from '../Button/NdeButtonCancel.vue';
import NdeModalMultiText from '../Modal/NdeModalMultiText.vue';
export default {
    name: 'NdeInputMultitext',
	components: {
	  NdeButtonPrimary,
	  NdeButtonCancel,
      NdeModalMultiText
	},
    data() {
        return {
            multiTextValue: null,
            showEditModal: false,
        }
    },
    props: {
        searchFieldInfo: {
            type: Object,
            required: true,
            default: null,
        },
        value: {
            type: String,
            default: false,
        },
        fieldName: {
            type: String,
            default: false,
        },
    },
    methods: {
        updateChanges(value){
            this.showEditModal = false
            this.$emit('onUpdateValue', value);
        },
    }, 
    watch:{
        value(){
            this.multiTextValue = this.value ? this.value.split(',') : [];
        }
    }
}
</script>
