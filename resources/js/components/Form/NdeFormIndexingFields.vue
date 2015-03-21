<template>
    <nde-form class="mrl-container-0">
        <template v-slot:ndeFormBody>
            <v-row>
                <v-col cols="12" md="4">
                    <label class="mb-1">File #</label>
                    <v-text-field
                        v-model="item.fileNumber"
                        outlined
                        clearable
                        clear-icon="Clear"
                        type="text"
                        append-outer-icon=" "
                        aria-label="File #"

                    ></v-text-field>
                </v-col>
                <v-col cols="12" md="4">
                    <label class="mb-1">County Name</label>
                    <v-text-field
                        v-model="item.countyName"
                        outlined
                        clearable
                        clear-icon="Clear"
                        type="text"
                        append-outer-icon=" "
                        aria-label="County Name"

                    ></v-text-field>
                </v-col>
                <v-col cols="12" md="4">
                    <label class="mb-1">Date</label>
                    <v-menu
                        v-model="expandDate"
                        :close-on-content-click="false"
                        transition="scale-transition"
                        offset-y
                        max-width="290px"
                        min-width="auto"
                    >
                        <template v-slot:activator="{ on, attrs }">
                            <v-text-field
                                :value="formatDate(item.date)"
                                outlined
                                append-icon="mdi-calendar-text"
                                readonly
                                v-bind="attrs"
                                v-on="on"
                                append-outer-icon="ΔΔ"
                                aria-label="Date"

                            ></v-text-field>
                        </template>
                        <v-date-picker
                            v-model="item.date"
                            no-title
                            @input="expandDate = false"
                        ></v-date-picker>
                    </v-menu>
                </v-col>
                <v-col cols="12" md="4">
                    <label class="mb-1">License Type</label>
                    <v-text-field
                        v-model="item.licenseType"
                        outlined
                        clearable
                        clear-icon="Clear"
                        type="text"
                        append-outer-icon=" "
                        aria-label="License Type"

                    ></v-text-field>
                </v-col>
                <v-col cols="12" md="4">
                    <label class="mb-1">State</label>
                    <v-text-field
                        v-model="item.state"
                        outlined
                        clearable
                        clear-icon="Clear"
                        type="text"
                        append-outer-icon=" "
                        aria-label="State"

                    ></v-text-field>
                </v-col>
                <v-col cols="12" md="4">
                    <label class="mb-1">Fruit</label>
                    <v-autocomplete
                        v-model="item.fruit"
                        :items="tempOptions"
                        outlined
                        item-text="text"
                        item-value="id"
                        append-outer-icon="ΔΔ"
                        aria-label="Fruit"

                    ></v-autocomplete>
                </v-col>
                <v-col cols="12" md="4">
                    <label class="mb-1">License Number</label>
                    <v-text-field
                        v-model="item.licenseNumber"
                        outlined
                        clearable
                        clear-icon="Clear"
                        type="text"
                        append-outer-icon=" "
                        aria-label="License Number"

                    ></v-text-field>
                </v-col>
                <v-col cols="12" md="4">
                    <label class="mb-1">Zip</label>
                    <v-text-field
                        v-model="item.zip"
                        outlined
                        clearable
                        clear-icon="Clear"
                        type="text"
                        append-outer-icon="Δ"
                        aria-label="Zip"

                    ></v-text-field>
                </v-col>
                <v-col cols="12" md="4">
                    <label class="mb-1">Integer Range Search</label>
                    <v-row class="mt-0">
                        <v-col cols="12" md="6">
                            <v-text-field
                                v-model="item.fromInteger"
                                outlined
                                placeholder="From:"
                                type="text"
                                append-outer-icon=" "
                                aria-label="Integer Range Search"

                            ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field
                                v-model="item.toInteger"
                                outlined
                                placeholder="To:"
                                type="text"
                                append-outer-icon="ΔΔ"
                                aria-label="Integer Range Search"

                            ></v-text-field>
                        </v-col>
                    </v-row>
                </v-col>
                <v-col cols="12" md="4">
                    <label class="mb-1">Street Number</label>
                    <v-text-field
                        v-model="item.streetNumber"
                        outlined
                        clearable
                        clear-icon="Clear"
                        type="text"
                        append-outer-icon="Δ"
                        aria-label="Street Number"

                    ></v-text-field>
                </v-col>
                <v-col cols="12" md="4">
                    <label class="mb-1">Trade Name</label>
                    <v-text-field
                        v-model="item.tradeName"
                        outlined
                        clearable
                        clear-icon="Clear"
                        type="text"
                        append-outer-icon="ΔΔ"
                        aria-label="Trade Name"

                    ></v-text-field>
                </v-col>
                <v-col cols="12" md="4">
                    <label class="mb-1"> </label>
                    <v-row class="mt-1">
                        <v-col cols="12" md="6">
                            <v-text-field
                                v-model="item.fromItem"
                                outlined
                                clearable
                                clear-icon="Edit"
                                type="text"
                                append-outer-icon="Δ"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field
                                v-model="item.toItem"
                                outlined
                                clearable
                                clear-icon="Edit"
                                type="text"
                                append-outer-icon="ΔΔ"
                            ></v-text-field>
                        </v-col>
                    </v-row>
                </v-col>
                <v-col cols="12" md="4">
                    <label class="mb-1">Address</label>
                    <v-text-field
                        v-model="item.address"
                        outlined
                        clearable
                        clear-icon="Clear"
                        type="text"
                        append-outer-icon=" "
                        aria-label="Address"

                    ></v-text-field>
                </v-col>
                <v-col cols="12" md="4">
                    <label class="mb-1">Court Date</label>
                    <v-menu
                        v-model="expandCourtDate"
                        :close-on-content-click="false"
                        transition="scale-transition"
                        offset-y
                        max-width="290px"
                        min-width="auto"
                    >
                        <template v-slot:activator="{ on, attrs }">
                            <v-text-field
                                :value="formatDate(item.courtDate)"
                                outlined
                                append-icon="mdi-calendar-text"
                                readonly
                                v-bind="attrs"
                                v-on="on"
                                append-outer-icon="ΔΔ"
                                aria-label="Court Date"

                            ></v-text-field>
                        </template>
                        <v-date-picker
                            v-model="item.courtDate"
                            no-title
                            @input="expandCourtDate = false"
                        ></v-date-picker>
                    </v-menu>
                </v-col>
                <v-col cols="12" md="4">
                    <label class="mb-1">Disposition Date</label>
                    <v-text-field
                        v-model="item.dispositionDate"
                        outlined
                        type="text"
                        append-outer-icon=" "
                        aria-label="Disposition Date"

                    ></v-text-field>
                </v-col>
                <v-col cols="12" md="4">
                    <label class="mb-1">City</label>
                    <v-text-field
                        v-model="item.city"
                        outlined
                        clearable
                        clear-icon="Clear"
                        type="text"
                        append-outer-icon="Δ"
                        aria-label="City"

                    ></v-text-field>
                </v-col>
                <v-col cols="12" md="4">
                    <label class="mb-1">Owner Name</label>
                    <v-text-field
                        v-model="item.ownerName"
                        outlined
                        clearable
                        clear-icon="Clear"
                        type="text"
                        append-outer-icon="ΔΔ"
                        aria-label="Owner Name"

                    ></v-text-field>
                </v-col>
                <v-col cols="12" md="4">
                    <label class="mb-1">Arbitrary Number</label>
                    <v-text-field
                        v-model="item.arbitraryNumber"
                        outlined
                        type="text"
                        append-outer-icon=" "
                        aria-label="Arbitrary Number"

                    ></v-text-field>
                </v-col>
            </v-row>
        </template>
    </nde-form>
</template>

<script>
import NdeForm from "./NdeForm.vue";

export default {
    name: "NdeFormIndexingFields",
    components: {
        NdeForm,
    },
    props: {
        item: {
            type: Object,
            default: () => ({}),
        },
    },
    data() {
        // TODO: Will delete when there is data get from API
        return {
            tempOptions: [
                {
                    id: 1,
                    text: "Finance",
                },
                {
                    id: 2,
                    text: "Red",
                },
                {
                    id: 3,
                    text: "Green",
                },
                {
                    id: 4,
                    text: "Grapes",
                },
            ],
            expandDate: false,
            expandCourtDate: false,
        };
    },
    methods: {
        formatDate(date) {
            if (!date) return null;

            const [year, month, day] = date.split("-");
            return `${month}/${day}/${year}`;
        },
    },
};
</script>
<style scoped lang="scss">
:v-deep .col-12,
.col-md-4 {
    padding: 0rem 0.625rem !important;
}

.mrl-container-0 {
	:v-deep .container {
		margin-left: 0px !important;
		margin-right: 0px !important;
	}
}

</style>
