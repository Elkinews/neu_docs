<template>
    <v-form>
        <v-container>
            <v-row>
                <v-col cols="12" md="6">
                    <label class="mb-3">File Number</label>
                    <v-text-field
                        v-model="item.fileNumber"
                        placeholder="File Number"
                        outlined
                        clearable
                        clear-icon="Clear"
                        type="text"
                        aria-label="File Number"
                    ></v-text-field>
                </v-col>
                <v-col cols="12" md="3">
                    <label class="mb-3">File Type</label>
                    <v-autocomplete
                        v-model="item.fileType"
                        :items="fileTypeOptions"
                        placeholder="File Type"
                        outlined
                        item-text="text"
                        item-value="id"
                        aria-label="File Type"

                    ></v-autocomplete>
                </v-col>
                <v-col cols="12" md="3">
                    <label class="mb-3">Status Type</label>
                    <v-autocomplete
                        v-model="item.statusType"
                        :items="statusTypeOptions"
                        placeholder="Status Type"
                        outlined
                        item-text="text"
                        item-value="id"
                        aria-label="Status Type"

                    ></v-autocomplete>
                </v-col>
                <v-col cols="12" md="6">
                    <label class="mb-3">Trade Name</label>
                    <v-text-field
                        v-model="item.tradeName"
                        placeholder="Trade Name"
                        outlined
                        clearable
                        clear-icon="Clear"
                        type="text"
                        aria-label="Trade Name"
                    ></v-text-field>
                </v-col>
                <v-col cols="12" md="3">
                    <label class="mb-3">Date</label>
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
                                v-model="item.date"
                                outlined
                                persistent-hint
                                append-icon="mdi-calendar-text"
                                readonly
                                v-bind="attrs"
                                v-on="on"
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
                <v-col cols="12" md="3">
                    <label class="mb-3">Count Date</label>
                    <v-menu
                        v-model="expandCountDate"
                        :close-on-content-click="false"
                        transition="scale-transition"
                        offset-y
                        max-width="290px"
                        min-width="auto"
                    >
                        <template v-slot:activator="{ on, attrs }">
                            <v-text-field
                                v-model="item.countDate"
                                outlined
                                persistent-hint
                                append-icon="mdi-calendar-text"
                                readonly
                                v-bind="attrs"
                                v-on="on"
                                aria-label="Count Date"

                            ></v-text-field>
                        </template>
                        <v-date-picker
                            v-model="item.countDate"
                            no-title
                            @input="expandCountDate = false"
                        ></v-date-picker>
                    </v-menu>
                </v-col>
                <v-col cols="12" md="3">
                    <label class="mb-3">Status</label>
                    <v-autocomplete
                        v-model="item.status"
                        :items="statusOptions"
                        placeholder="Status"
                        outlined
                        item-text="text"
                        item-value="id"
                        aria-label="Status"

                    ></v-autocomplete>
                </v-col>
                <v-col cols="12" md="3">
                    <label class="mb-3">Protest Status</label>
                    <v-autocomplete
                        v-model="item.protestStatus"
                        :items="statusTypeOptions"
                        placeholder="Protest Status"
                        outlined
                        item-text="text"
                        item-value="id"
                        aria-label="Protest Status"

                    ></v-autocomplete>
                </v-col>
                <v-col cols="12" md="6">
                    <label class="mb-3">Fruit</label>
                    <v-autocomplete
                        v-model="item.fruit"
                        :items="fileTypeOptions"
                        placeholder="Fruit"
                        outlined
                        item-text="text"
                        item-value="id"
                        aria-label="Fruit"

                    ></v-autocomplete>
                </v-col>
            </v-row>
        </v-container>
    </v-form>
</template>

<script>
export default {
    name: "NdeFormCopyToWorkQueue",
    // TODO: Will delete when there is data get from API
    props: {
        item: {
            type: Object,
            default: () => ({
                fileNumber: "5226",
                fileType: 1,
                statusType: 1,
                tradeName: "3911 regression A",
                date: "",
                countDate: "",
                status: 2,
                protestStatus: 1,
                fruit: 4,
            }),
        },
    },
    data() {
        return {
            fileTypeOptions: [
                {
                    id: 1,
                    text: "Original",
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
            statusTypeOptions: [
                {
                    id: 1,
                    text: "Protest",
                },
                {
                    id: 2,
                    text: "Cancel",
                },
                {
                    id: 3,
                    text: "Kick",
                },
            ],
            statusOptions: [
                {
                    id: 1,
                    text: "Old",
                },
                {
                    id: 2,
                    text: "New",
                },
            ],
            expandDate: false,
            expandCountDate: false,
        };
    },
};
</script>
<style scoped lang="scss">
:v-deep .v-text-field--outlined {
    .v-input__icon--clear {
        margin-right: 0.313rem;
        button {
            font-family: "Poppins";
            font-style: normal;
            font-weight: 400;
            font-size: 1rem;
            line-height: 1.5rem;
            color: #c32c39 !important;
        }
    }
}
:v-deep .col-12,
.col-md-6 {
    padding: 5px !important;
}
</style>