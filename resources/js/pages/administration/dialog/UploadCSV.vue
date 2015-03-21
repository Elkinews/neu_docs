<template>
    <v-dialog
        v-model="dialog"
        scrollable
        max-width="850px"
        content-class="dialog-w-custombar"
    >
        <v-card>
            <v-card-title class="d-flex justify-space-between">
                <p class="mb-0">
                    Upload CSV Data
                </p>
            </v-card-title>

            <div class="px-16 pb-8">
                <div class="nde-program mb-5">
                    <label for="">Program Name:</label>
                    <v-select
                        background-color="#fff"
                        outlined
                        dense
                        hide-details
                        class="nde-select mt-2"
                        v-model="currentTargetID"
                        :items="items"
                        @change="handleChange"
                    ></v-select>
                </div>

                <div class="nde-upload">
                    <label for="">CSV File Upload:</label>
                    <DragnDrop ref="dragnDrop" v-if="isDragUI" class="mt-2" @onGetDataFile="handleGetDataFile" />

                    <div class="upload-manual" v-else>
                        <v-container>
                            <v-row>
                                <v-col sm="9" class="pl-0">
                                    <v-text-field
                                        :value="file.name"
                                        background-color="#fff"
                                        outlined
                                        attach
                                        dense
                                        hide-details
                                        type="text"
                                        readonly
                                    ></v-text-field>
                                </v-col>
                                <v-col sm="3" class="pr-0">
                                    <nde-button-primary title="Add File" @click="addFile"></nde-button-primary>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-checkbox
                                    v-model="hasHeader"
                                    label="Has header"
                                    >
                                </v-checkbox>
                                <v-col sm="12" class="px-0" v-if="fileData">
                                    <NdeTableUploadFile
                                        :dataHeaders="fileData.headers"
                                        :selectedHeaders="headers"
                                        :dataRows="fileData.sample_rows"
                                        :hasHeader="hasHeader"
                                        @onChangeHeader="handleChangeHeader"
                                    />
                                </v-col>
                            </v-row>
                            <input v-show="false" ref="input" type="file" @change="onChange" accept=".csv">

                        </v-container>
                    </div>
                </div>

                <v-card-actions v-if="!isDragUI" class="d-flex justify-space-between px-0">
                    <nde-button-primary @click="submit" class="mt-5" title="Save" :loading="isLoading"></nde-button-primary>
                    <nde-button-cancel @click="close"  class="mt-5" title="Cancel"></nde-button-cancel>
                </v-card-actions>
            </div>
        </v-card>
    </v-dialog>
</template>

<script>
import {mapState} from "vuex"
import DragnDrop from "../shared/DragnDrop.vue"
import NdeButtonPrimary from "../../../components/Button/NdeButtonPrimary.vue";
import NdeButtonCancel from "../../../components/Button/NdeButtonCancel.vue";
import NdeTableUploadFile from "../../../components/Table/NdeTableUploadCSVFile.vue"

export default {
    components: {
        DragnDrop,
        NdeButtonPrimary,
        NdeButtonCancel,
        NdeTableUploadFile
    },

    data() {
        return {
            hasHeader: true,
            dialog: false,
            items: [],
            currentTargetID: {},
            isDragUI: true,
            fileData: {},
            file: null,
            headers: [],
            isLoading: false
        }
    },
    computed: {
        ...mapState(['programs', 'env']),
    },

    methods: {
        addFile() {
            this.$refs.input.click()
        },

        onChange(e) {
            let files = e.target.files || e.dataTransfer.files;

            if (!files.length) {
                return;
            }

            this.handleGetDataFile(files[0])
        },

        handleChange(data) {
            this.currentTargetID = {}
            this.currentTargetID.value = data
        },

        setHeadersData() {
            if(!this.headers.length) {
                this.fileData.sample_rows[0].forEach((val, idx) => {
                    this.headers[idx] = 'ignore'
                })
            }
        },

        handleChangeHeader(index, value) {

            this.setHeadersData()

            if(this.headers.includes(value)) {
                this.headers[index] = 'ignore'
                return this.$swal({
                    icon: 'error',
                    text: 'Header is existed',
                });
            }

            this.headers[index] = value
        },

        close() {
            this.fileData = {}
            this.file = null
            this.isDragUI = true
            this.dialog = !this.dialog
        },

        submit() {
            this.isLoading = true

            const data = new FormData()
            data.append('access_key', this.fileData.access_key);
            data.append('csv_file', this.fileData.csv_file);
            data.append('has_header', this.hasHeader);
            data.append('profile_id', this.currentTargetID.value);

            this.setHeadersData()

            this.headers.forEach((value, index) => {
                data.append(`columns[${index}]`, value || 'ignore')
            })

            this.$store.dispatch('increaseCallCount');

            axios.post('/addDatafeedEntriesOauth', data)
                .then( (response) => {
                if(response?.data?.message === 'success') {
                    this.isLoading = false
                    this.close()
                    this.$swal({
                        icon: 'success',
                        text: response?.data?.message,
                    });
                }
            })
                .catch((error) => {
                    this.isLoading = false
                    this.$swal({
                        icon: 'error',
                        text: error?.response?.data?.data?.data?.message,
                    });
            });
        },

        async handleGetDataFile(file) {
            this.file = file
            this.isDragUI = false

            let data = new FormData()
            data.append('profile_id', this.currentTargetID.value)
            data.append('file', file)


            try {
                this.$store.dispatch('increaseCallCount');
                const tokenData = await axios.get('/getAccessToken')

                const token = tokenData.data.data.token;

                let config = {
                    method: 'post',
                    url: this.env.NDE_URL + `/api.php?function=SaveTempFileOauth`,
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    data : data
                }

                this.$store.dispatch('increaseCallCount');

                axios(config).then(response => {
                    if(response && response.data && response.data.data) {
                        this.fileData = response.data.data
                        this.fileData.headers.unshift({name: 'ignore', desc: 'Ignore'})
                    }
                }).catch(error => {
                    console.error(error)
                })
            } catch(e) {
                console.log(e.response.data.data)
            }

        }
    },

    watch: {
        programs(newVal) {
            if(newVal && newVal.length)
            newVal.forEach(program => {
                if(program.meta && program.meta.has_key_search) {
                    this.items.push({text: program.name, value: program.id})
                }
            })

            this.currentTargetID = this.items[0]
        }
    }
}
</script>

<style lang="scss">
.nde-upload {
    .upload-manual {
        button {
            width: inherit !important;
        }
    }
}
</style>
