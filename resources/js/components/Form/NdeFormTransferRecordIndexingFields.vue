<template>
    <nde-form>
        <template v-slot:ndeFormBody>
            <v-row>
                <v-col cols="12" md="4">
                    <label class="mb-1">Queue</label>
                    <v-autocomplete
                        v-model="item.queue"
                        :items="tempOptions"
                        outlined
                        item-text="text"
                        item-value="id"
                        placeholder="Text"
                        append-outer-icon=" "
                        aria-label="Queue"
                        
                    ></v-autocomplete>
                </v-col>
                <v-col cols="12" md="4">
                    <label class="mb-1">License Type</label>
                    <v-autocomplete
                        v-model="item.licenseType"
                        :items="tempOptions"
                        outlined
                        item-text="text"
                        item-value="id"
                        placeholder="Text"
                        append-outer-icon=" "
                        aria-label="License Type"

                    ></v-autocomplete>
                </v-col>
                <v-col cols="12" md="4">
                    <label class="mb-1">Assigned To</label>
                    <v-text-field
                        v-model="item.assignedTo"
                        outlined
                        clearable
                        clear-icon="Clear"
                        type="text"
                        append-outer-icon=" "
                        aria-label="Assigned To"

                    ></v-text-field>
                </v-col>
                <v-col cols="12" md="4">
                    <label class="mb-1">Applicant First Name</label>
                    <v-text-field
                        v-model="item.applicantFirstName"
                        outlined
                        clearable
                        clear-icon="Clear"
                        type="text"
                        append-outer-icon=" "
                        aria-label="Applicant First Name"

                    ></v-text-field>
                </v-col>
                <v-col cols="12" md="4">
                    <label class="mb-1">Payment Date</label>
                    <v-menu
                        v-model="expandPaymentDate"
                        :close-on-content-click="false"
                        transition="scale-transition"
                        offset-y
                        max-width="290px"
                        min-width="auto"
                    >
                        <template v-slot:activator="{ on, attrs }">
                            <v-text-field
                                v-model="item.paymentDate"
                                outlined
                                persistent-hint
                                append-icon="mdi-calendar-text"
                                readonly
                                v-bind="attrs"
                                v-on="on"
                                append-outer-icon=" "
                                aria-label="Payment Date"

                            ></v-text-field>
                        </template>
                        <v-date-picker
                            v-model="item.paymentDate"
                            no-title
                            @input="expandPaymentDate = false"
                        ></v-date-picker>
                    </v-menu>
                </v-col>
                <v-col cols="12" md="4">
                    <label class="mb-1">Date of Processing</label>
                    <v-menu
                        v-model="expandDateProcessing"
                        :close-on-content-click="false"
                        transition="scale-transition"
                        offset-y
                        max-width="290px"
                        min-width="auto"
                    >
                        <template v-slot:activator="{ on, attrs }">
                            <v-text-field
                                v-model="item.dateProcessing"
                                outlined
                                persistent-hint
                                append-icon="mdi-calendar-text"
                                readonly
                                v-bind="attrs"
                                v-on="on"
                                append-outer-icon=" "
                                aria-label="Date of Processing"

                            ></v-text-field>
                        </template>
                        <v-date-picker
                            v-model="item.dateProcessing"
                            no-title
                            @input="expandDateProcessing = false"
                        ></v-date-picker>
                    </v-menu>
                </v-col>
                <v-col cols="12" md="4">
                    <label class="mb-1">Applicant Middle Name</label>
                    <v-text-field
                        v-model="item.applicantMiddleName"
                        outlined
                        clearable
                        clear-icon="Clear"
                        type="text"
                        append-outer-icon=" "
                        aria-label="Applicant Middle Name"

                    ></v-text-field>
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
                    <label class="mb-1">Document Type</label>
                    <v-autocomplete
                        v-model="item.documentType"
                        :items="tempOptions"
                        outlined
                        item-text="text"
                        item-value="id"
                        placeholder="Text"
                        append-outer-icon=" "
                        aria-label="Document Type"

                    ></v-autocomplete>
                </v-col>
                <v-col cols="12" md="4">
                    <label class="mb-1">Applicant Last Name</label>
                    <v-text-field
                        v-model="item.applicantLastName"
                        outlined
                        clearable
                        clear-icon="Clear"
                        type="text"
                        append-outer-icon="Δ"
                        aria-label="Applicant Last Name"

                    ></v-text-field>
                </v-col>
                <v-col cols="12" md="4">
                    <label class="mb-1">Received Date and Time</label>
                    <v-menu
                        v-model="expandReceivedDate"
                        :close-on-content-click="false"
                        transition="scale-transition"
                        offset-y
                        max-width="290px"
                        min-width="auto"
                    >
                        <template v-slot:activator="{ on, attrs }">
                            <v-text-field
                                v-model="item.receivedDate"
                                outlined
                                persistent-hint
                                append-icon="mdi-calendar-text"
                                readonly
                                v-bind="attrs"
                                v-on="on"
                                append-outer-icon=" "
                        aria-label="Received Date and Time"

                            ></v-text-field>
                        </template>
                        <v-date-picker
                            v-model="item.receivedDate"
                            no-title
                            @input="expandReceivedDate = false"
                        ></v-date-picker>
                    </v-menu>
                </v-col>
                <v-col cols="12" md="4">
                    <label class="mb-1">Last Update On</label>
                    <v-text-field
                        v-model="item.lastUpdateOn"
                        outlined
                        type="text"
                        append-outer-icon=" "
                        aria-label="Last Update On"

                    ></v-text-field>
                </v-col>
                <v-col cols="12" md="4">
                    <label class="mb-1">Applicant Number</label>
                    <v-text-field
                        v-model="item.applicantNumber"
                        outlined
                        clearable
                        clear-icon="Clear"
                        type="text"
                        append-outer-icon=" "
                        aria-label="Applicant Number"

                    ></v-text-field>
                </v-col>
                <v-col cols="12" md="4">
                    <label class="mb-1">Is Supplemental</label>
                    <v-text-field
                        v-model="item.isSupplemental"
                        outlined
                        clearable
                        clear-icon="Clear"
                        type="text"
                        append-outer-icon=" "
                        aria-label="Is Supplemental"

                    ></v-text-field>
                </v-col>
                <v-col cols="12" md="4">
                    <label class="mb-1">Last Update by</label>
                    <v-text-field
                        v-model="item.lastUpdateBy"
                        outlined
                        clearable
                        clear-icon="Clear"
                        type="text"
                        append-outer-icon=" "
                        aria-label="Last Update by"

                    ></v-text-field>
                </v-col>
                <v-col cols="12" md="4">
                    <label class="mb-1">Entity ID</label>
                    <v-text-field
                        v-model="item.entityID"
                        outlined
                        clearable
                        clear-icon="Clear"
                        type="text"
                        append-outer-icon="Δ"
                        aria-label="Entity ID"

                    ></v-text-field>
                </v-col>
                <v-col cols="12" md="4">
                    <label class="mb-1">Application Type</label>
                    <v-text-field
                        v-model="item.applicationType"
                        outlined
                        clearable
                        clear-icon="Clear"
                        type="text"
                        append-outer-icon=" "
                        aria-label="Application Type"
                    ></v-text-field>
                </v-col>
            </v-row>
        </template>
    </nde-form>
</template>

<script>
import NdeForm from "./NdeForm.vue";

export default {
    name: "NdeFormTransferRecordIndexingFieldss",
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
            expandPaymentDate: false,
            expandDateProcessing: false,
            expandReceivedDate: false,
        };
    },
};
</script>
<style scoped lang="scss">
:v-deep .col-12,
.col-md-4 {
    padding: 0rem 0.625rem !important;
}
</style>
