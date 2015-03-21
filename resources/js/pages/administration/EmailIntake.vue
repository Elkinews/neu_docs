<template>
    <div class="nde-hid-show-col" role="main">
        <nde-tab-admin />
        <v-card class="mt-5 pa-5">
            <v-form ref="emailIntakeForm">
                <v-card-title>
                    <h3 class="font-weight-bold mb-0">Email Intake</h3>
                </v-card-title>
                <hr class="mt-0">
                <v-card-text>
                    <v-row>
                        <v-col cols="12" sm="3" md="12">
                            <v-radio-group v-model="emailData.protocol" row>
                                <v-radio
                                    label="POP3"
                                    value="pop3"
                                ></v-radio>
                                <v-radio
                                    label="IMAP"
                                    value="imap"
                                ></v-radio>
                            </v-radio-group>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="12" md="3">
                            <label>Email Domain Name</label>
                            <v-text-field
                            dense
                            solo
                            hide-details
                            v-model="emailData.domain"    
                            aria-label="Email Domain Name"

                        ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="3">
                            <label>Email Account Username</label>
                            <v-text-field
                            dense
                            solo
                            hide-details
                            v-model="emailData.username"    
                            aria-label="Email Account Username"

                        ></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="12" md="3">
                            <label>Password</label>
                            <v-text-field
                            :append-icon="show ? 'mdi-eye' : 'mdi-eye-off'"
                            dense
                            solo
                            hide-details
                            v-model="emailData.password"
                            :type="show ? 'text' : 'password'"
                            @click:append="show = !show"
                            aria-label="Password"
                        ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="3">
                            <label>Port</label>
                            <v-text-field
                            dense
                            solo
                            hide-details
                            v-model="emailData.port"   
                            aria-label="Port"                         
                        ></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="12" md="3">
                            <v-checkbox
                                v-model="emailData.support"
                                :label="'PDF'"
                                value="PDF"
                            ></v-checkbox>
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-card-actions>
                    <nde-button color="primary" @click="handleSubmit" class="nde-auth-primary-btn pa-6">Save</nde-button>
                    <nde-button @click="clearForm" class="nde-clear-btn pa-6">Clear</nde-button>
                </v-card-actions>
            </v-form>    
		</v-card>
    </div>
</template>

<script>
import NdeTabAdmin from "../../components/Tabs/NdeTabAdministration.vue";
import NdeDashboardLayout from "../../shared/layouts/dashboard/NdeDashboardLayout.vue";
import NdeButton from "../../components/Button/NdeButton.vue";

export default {
    layout: NdeDashboardLayout,
    components: {
        NdeTabAdmin,
        NdeButton,
    },

    data() {
        return  {
            show: false,
            emailData: {},
        } 
    },

    methods: {
        emailInfo() {
            this.$store.dispatch('increaseCallCount');
            axios
            .post('/getEmailIntakeSettingsOauth', {})
            .then((response) => {
                this.emailData = response?.data?.data?.message?.data
                console.log(this.emailData)
            })
            .catch((error) => {
            console.log(error);
            });
        },

        handleSubmit() {
            this.$store.dispatch('increaseCallCount');
            axios
            .post('/saveEmailIntakeOauth', {
                "emailSetting": `${this.emailData.protocol}://${this.emailData.username}:${this.emailData.password}@${this.emailData.domain}:${this.emailData.port}`,
                "format": `${this.emailData.support}`
            })
            .then((response) => {
                alert("Successfully saved Email Intake.")
            })
            .catch((error) => {
                console.log(error);
            });
        },

       clearForm(){
           this.$refs.emailIntakeForm.reset()
       }  
    },

    mounted() {
        this.emailInfo()
    },
}
</script>

<style lang="scss">
.nde-auth-primary-btn{
    border-radius: 8px;
    width: 9.938rem;
    height: 2.5rem !important;
    border-style: solid;
}

.nde-clear-btn{
    color: #C32C39 !important;
    background-color: #F9E1E1 !important;
    border-color: #C32C39 !important;
    border-style: solid;
    border-radius: 8px;
    width: 7.188rem;
    height: 2.5rem !important;
    box-shadow: none;
}
.nde-hid-show-col {
    p {
        font-weight: bold;
    }

    .v-btn__loader {
        color: white
    }
}
</style>