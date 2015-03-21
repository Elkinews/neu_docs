<template>
    <div class="nde-one-case-control" role="main">
        <nde-tab-admin />
        <v-card class="mt-5 pa-5">
            <v-form ref="emailIntakeForm">
                <v-card-title>
                    <h3 class="font-weight-bold mb-0">OneCase Control</h3>
                </v-card-title>
                <hr class="mt-0">
                <v-card-text>
                    <v-row>
                        <v-col md="6" sm="12">
                            <v-row>
                                <v-col md="6">
                                    <v-select
                                        dense
                                        solo
                                        attach
                                        hide-details
                                        class="nde-select"
                                        outlined
                                        :items="items"
                                        label="OneCase Training"
                                        aria-label="OneCase Training"
                                    >
                                    </v-select>

                                </v-col>

                                <v-col md="3" offset-md="3" class="d-flex justify-end" v-if="!isShowForm">
                                    <nde-button color="primary" @click="addTraining" class="nde-auth-primary-btn">
                                        <v-icon>mdi-plus</v-icon>
                                        Add Training
                                    </nde-button>
                                </v-col>
                            </v-row>

                            <v-form class="mt-8" v-if="isShowForm" ref="oneCaseControlForm">
                                <h3 class="mb-4">New Training</h3>

                                <v-text-field
                                    label="Title Training"
                                    placeholder="Title Training"
                                    outlined
                                    hide-details
                                    v-model="title"
                                    class="mb-3"
                                    aria-label="Title Training"
                                ></v-text-field>

                                
                                <v-textarea
                                    outlined
                                    label="Message"
                                    v-model="message"
                                    aria-label="Message"

                                    >
                                </v-textarea>
                                
                                <v-card-actions class="d-flex justify-space-between px-0">
                                    <nde-button outlined color="error" class="mr-6" @click="clear">Clear</nde-button>
                                    <nde-button outlined color="success" @click="submit"
                                    ><v-icon left>mdi-send</v-icon>Submit</nde-button>
                                </v-card-actions>
                            </v-form>

                            <div class="mt-15">
                                <h3 class="mb-4">History</h3>

                                <v-expansion-panels>
                                    <v-expansion-panel v-for="(item, index) in listOneCase" :key="index">
                                        <v-expansion-panel-header># {{index + 1}}</v-expansion-panel-header>
                                        <v-expansion-panel-content>
                                        {{item.message}}
                                        </v-expansion-panel-content>
                                    </v-expansion-panel>
                                </v-expansion-panels>
                            </div>
                        </v-col>
                    </v-row>
                </v-card-text>
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
            isShowForm: false,
            items: [
                {
                    text: 'OneCase Training',
                    value: 'OneCase Training'
                }
            ],
            listOneCase: [],
            title: "",
            message: ""
        
        } 
    },

    methods: {
        addTraining() {
            this.isShowForm = !this.isShowForm
        },

        getOneCaseTrainingOauth() {
            this.$store.dispatch('increaseCallCount');
            axios
            .post('/getOneCaseTrainingOauth', {})
            .then((response) => {
                this.listOneCase = response?.data?.data?.message?.oc_results
            })
            .catch((error) => {
                console.log(error);
            });
        },

        submit() {
            this.$store.dispatch('increaseCallCount');
            axios
            .post('/saveOneCaseTrainingOauth', {
                oc_training: [
                    {
                        message: this.message
                    }
                ],
            })
            .then((response) => {
                if(response && response.data && response.data.message === 'success') {
                    this.isShowForm = !this.isShowForm
                    alert('Add message successfully!')
                    this.getOneCaseTrainingOauth()
                }
            })
            .catch((error) => {
                console.log(error);
            });
        },

       clear(){
           this.$refs.oneCaseControlForm.reset()
       }  
    },

    mounted() {
        this.getOneCaseTrainingOauth()
    },
}
</script>

<style lang="scss">
.nde-one-case-control {
    .nde-select {
        width: 330px;
    }

    .nde-auth-primary-btn {
        border-radius: 8px;
        // width: 160px;
        height: 40px !important;
    }
}

</style>