<template>
    <div class="nde-general-user-form">
        <v-form ref="form" v-model="valid" lazy-validation>
            <v-row>
                <v-col md="10" sm="12">
                    <v-row>
                        <v-col md="4" sm="12">
                            <label>Name:</label>
                            <v-text-field :rules="nameRules" dense single-line outlined v-model="dataUserDetail.name"
                                required aria-label="Name"></v-text-field>
                        </v-col>
                        <v-col md="4" sm="12">
                            <label>Username:</label>
                            <v-text-field :rules="usernameRules" dense single-line outlined
                                v-model="dataUserDetail.username" required aria-label="Username"></v-text-field>
                        </v-col>
                        <v-col md="4" sm="12">
                            <label>Email:</label>
                            <v-text-field :rules="emailRules" single-line outlined dense v-model="dataUserDetail.email"
                                required aria-label="Email"></v-text-field>
                        </v-col>
                        <!-- <v-col md="4" sm="12">
                            <label>Email Intake:</label>
                            <v-select
                                v-model="dataUserDetail.email_intake_status"
                                :items="email_intake"
                                outlined
                                attach
                                dense
                                aria-label="Email Intake"
                            ></v-select>
                        </v-col> -->
                        <v-col md="4" sm="12">
                            <label>Display empty tabs:</label>
                            <v-select v-model="dataUserDetail.view_empty_tabs" :items="view_empty_tabs" outlined attach
                                dense aria-label="Display empty tabs"></v-select>

                        </v-col>
                        <v-col md="4" sm="12">
                            <label>Type:</label>
                            <v-radio-group @change="clickChangeType()" v-model="type" row>
                                <v-radio label="Manual" value="manual"></v-radio>
                                <v-radio label="Province" value="province"></v-radio>
                            </v-radio-group>
                        </v-col>
                        <template v-if="isEditUser">
                            <v-spacer class="col-12 pa-0"></v-spacer>
                            <v-col md="4" sm="12">
                                <label>Required MFA:</label>
                                <v-select v-model="selectedMfa" :items="mfaOption" outlined attach dense
                                    aria-label="Required MFA" @change="handleChangeMfa"></v-select>
                            </v-col>
                            <v-col v-show="selectedMfa === 'TOTP'" md="4" sm="12">
                                <nde-button-primary style="margin-top: 1.375rem" title="Generate QR Code"
                                    @click="generateQRCode"></nde-button-primary>
                            </v-col>
                        </template>
                    </v-row>
                </v-col>
            </v-row>

            <h3 class="d-flex align-center mb-5" v-if="isEditUser">
                Can be Assigned Records:

                <v-radio-group hide-details v-model="dataUserDetail.can_be_assigned" row class="mt-0 pt-0 ml-5">
                    <v-radio label="Yes" :value="true"></v-radio>
                    <v-radio label="No" :value="false"></v-radio>
                </v-radio-group>
            </h3>

            <p v-if="hasError" class="error--text">You have to choose at least 1 Program Access</p>
            <p v-if="hasErrorProfile" class="error--text">You have to choose at least 1 Program Access</p>
            <v-expansion-panels v-if="type === 'manual'">
                <v-expansion-panel>
                    <v-expansion-panel-header>
                        Program Access
                    </v-expansion-panel-header>

                    <v-expansion-panel-content>
                        <v-row>
                            <v-col cols="12">
                                <nde-program-treeview v-model="selectedPrograms" :programs="programAccess">
                                </nde-program-treeview>
                            </v-col>
                        </v-row>
                    </v-expansion-panel-content>
                </v-expansion-panel>

                <v-expansion-panel class="mt-3">
                    <v-expansion-panel-header>
                        Image Groups
                    </v-expansion-panel-header>

                    <v-expansion-panel-content>
                        <v-row>
                            <v-col cols="12" sm="6" md="4" v-for="image_group in imageGroups" :key="image_group.id">
                                <v-checkbox class="mt-0" hide-details v-model="selectedImageGroups"
                                    :value="`${image_group.id}`" :label="`${image_group.label}`">
                                </v-checkbox>
                            </v-col>
                        </v-row>
                    </v-expansion-panel-content>
                </v-expansion-panel>

                <v-expansion-panel class="mt-3">
                    <v-expansion-panel-header>
                        Supervisors
                    </v-expansion-panel-header>

                    <v-expansion-panel-content>
                        <v-row>
                            <v-col cols="12" sm="6" md="4" v-for="supervisor in supervisors" :key="supervisor.id">
                                <v-checkbox class="mt-0" hide-details v-model="selectedSupervisors"
                                    :value="`${supervisor.id}`" :label="`${supervisor.label}`">
                                </v-checkbox>
                            </v-col>
                        </v-row>
                    </v-expansion-panel-content>
                </v-expansion-panel>

                <v-expansion-panel class="mt-3">
                    <v-expansion-panel-header>
                        Roles
                    </v-expansion-panel-header>

                    <v-expansion-panel-content>
                        <div v-for="(value, key, index) in permissionsControl.roles" :key="index">
                            <div v-if="value.length !== 0" class="mb-5">
                                <v-row align="center" class="ma-0 mb-3">
                                    <h3 class="text-uppercase mr-2 pt-1">{{ key }}</h3>
                                    <v-checkbox class="ma-0" hide-details v-model="selectedGroupRoles[key]" readonly
                                        @click="clickSelectedGroupRoles(key)">
                                    </v-checkbox>
                                </v-row>
                                <v-row>
                                    <v-col cols="12" sm="6" md="4" v-for="role in value" :key="role.id">
                                        <v-checkbox class="mt-0 text-lowercase" hide-details v-model="selectedRoles"
                                            :value="`${role.id}`" :label="`${role.label}`">
                                        </v-checkbox>
                                    </v-col>
                                </v-row>
                            </div>
                        </div>
                    </v-expansion-panel-content>

                </v-expansion-panel>

                <v-expansion-panel class="mt-3">
                    <v-expansion-panel-header>
                        Reports
                    </v-expansion-panel-header>

                    <v-expansion-panel-content>
                        <v-row>
                            <v-col cols="12" sm="6" md="4" v-for="report in reports" :key="report.id">
                                <v-checkbox class="mt-0" hide-details v-model="selectedReports" :value="`${report.id}`"
                                    :label="`${report.label}`">
                                </v-checkbox>

                            </v-col>
                        </v-row>
                    </v-expansion-panel-content>
                </v-expansion-panel>

                <v-expansion-panel class="mt-3">
                    <v-expansion-panel-header>
                        Custom On-Demand Reports
                    </v-expansion-panel-header>

                    <v-expansion-panel-content>
                        <v-row>
                            <v-col cols="12" sm="6" md="4" v-for="report in ondemandReports" :key="report.id">
                                <v-checkbox class="mt-0" hide-details v-model="selectedOndemandReports"
                                    :value="`${report.id}`" :label="`${report.label}`">
                                </v-checkbox>

                            </v-col>
                        </v-row>
                    </v-expansion-panel-content>
                </v-expansion-panel>
            </v-expansion-panels>

            <v-expansion-panels v-else>
                <v-expansion-panel>
                    <v-expansion-panel-header>
                        Provinces
                    </v-expansion-panel-header>

                    <v-expansion-panel-content>
                        <v-row>
                            <v-col cols="12" sm="6" md="4" v-for="province in dataProvinces" :key="province.id">
                                <v-checkbox class="mt-0" hide-details v-model="selectedProvinces"
                                    :value="`${province.id}`" :label="`${province.name}`">
                                </v-checkbox>
                            </v-col>
                        </v-row>
                    </v-expansion-panel-content>
                </v-expansion-panel>
            </v-expansion-panels>

            <div class="mt-5">
                <nde-button-cancel @click="hideModal"> </nde-button-cancel>
                <nde-button-primary :disabled="!valid" :loading="isLoading" title="Save" @click="saveData">
                </nde-button-primary>
            </div>
        </v-form>
    </div>
</template>

<script>
import { mapState } from "vuex"
import NdeButtonCancel from "@components/Button/NdeButtonCancel.vue";
import NdeButtonPrimary from "@components/Button/NdeButtonPrimary.vue";
import NdeProgramTreeview from "@components/Inputs/NdeProgramTreeview.vue";
import { intersection, uniq } from "lodash";

export default {
    components: {
        NdeButtonCancel,
        NdeButtonPrimary,
        NdeProgramTreeview,
    },

    props: {
        dataProvinces: {
            type: Array,
            default: () => []
        },

        dataUserDetail: {
            type: Object,
            default: () => { }
        },

        isEditUser: {
            type: Boolean,
            default: false
        },

        isLoading: {
            type: Boolean,
            default: false
        }
    },

    computed: {
        ...mapState(['permissionsControl']),
        programAccess() {
            return this.uniqueArr(this.permissionsControl.programs)
        },

        imageGroups() {
            return this.uniqueArr(this.permissionsControl.image_groups)
        },

        supervisors() {
            return this.uniqueArr(this.permissionsControl.supervisors)
        },

        reports() {
            return this.uniqueArr(this.permissionsControl.reports)
        },

        ondemandReports() {
            return this.uniqueArr(this.permissionsControl.ondemand_reports)
        },

        valuesOndemandReports() {
            return this.ondemandReports.filter(val => val.id).map(val => `${val.id}`) || [];
        },


        isManual() {
            return this.type === 'manual'
        }
    },

    data() {
        const selectedGroupRoles = {};
        for (const keyRole in this.permissionsControl?.roles) {
            selectedGroupRoles[keyRole] = false;
        }
        return {
            valid: true,
            hasError: false,
            hasErrorProfile: false,
            usernameRules: [
                v => !!v || 'Username is required',
                v => !!v && !(/\s/.test(v)) || 'Username may not contain any spaces',
                v => !!v && !(/\&|\<|\>|\\|\/|\'/.test(v)) || '& < > \\ / \' characters are not allowed in the username',
            ],
            nameRules: [
                v => !!v || 'Name is required',
            ],
            emailRules: [
                v => !!v || 'E-mail is required',
            ],
            email_intake: [
                {
                    text: 'ACTIVE',
                    value: "ON"
                },
                {
                    text: 'OFF',
                    value: "OFF"
                }
            ],
            mfaOption: ['OFF', 'TOTP', 'EMAIL'],
            selectedMfa: null,
            view_empty_tabs: [
                {
                    text: 'Yes',
                    value: true
                },
                {
                    text: 'No',
                    value: false
                }
            ],
            type: 'manual',
            selectedPrograms: [],
            selectedImageGroups: [],
            selectedSupervisors: [],
            selectedReports: [],
            selectedOndemandReports: [],
            selectedRoles: [],
            selectedProvinces: [],
            isGeneratedQRCode: false,
            selectedGroupRoles,
        }
    },

    methods: {
        async clickChangeType(isShowMessage = true) {

            if (this.isManual && this.selectedProvinces && this.selectedProvinces.length) {
                this.$store.commit('setShowProgressAPI', true);
                const resultPermissions = await this.$store.dispatch('callAPI', {
                    url: '/getCollectedProvincePermissions',
                    method: 'post',
                    data: {
                        provinces_ids: this.selectedProvinces
                    },
                });

                console.log(resultPermissions);
                if (resultPermissions.message == 'success') {
                    const permissions = resultPermissions?.data?.data?.permissions;

                    const image_groups = permissions?.image_groups;
                    this.selectedImageGroups = [];
                    image_groups.map(image_group => {
                        this.selectedImageGroups.push(image_group.id.toString());
                    });


                    const profiles = permissions?.profiles;
                    this.selectedPrograms = [];
                    this.selectedReports = [];
                    this.selectedOndemandReports = [];
                    profiles.map(profile => {
                        this.programAccess.map(pr => {
                            if (pr?.id.toString() == profile.id.toString()) {
                                this.selectedPrograms.push(profile.id.toString());
                            }
                        });

                        this.reports.map(report => {
                            if (report?.id.toString() == profile.id.toString()) {
                                this.selectedReports.push(profile.id.toString());
                            }
                        });

                    })

                    const roles = permissions?.roles;
                    this.selectedRoles = [];
                    roles.map(role => {
                        this.selectedRoles.push(role.id.toString());
                    })

                    this.selectedSupervisors = [];

                } else {
                    this.selectedPrograms = [];
                    this.selectedImageGroups = [];
                    this.selectedSupervisors = [];
                    this.selectedReports = [];
                    this.selectedRoles = [];
                    this.selectedOndemandReports = [];
                }
                this.$store.commit('setShowProgressAPI', false);
            } else {
                //   this.selectedProvinces = [];
                this.selectedPrograms = [];
                this.selectedImageGroups = [];
                this.selectedSupervisors = [];
                this.selectedReports = [];
                this.selectedRoles = [];
                this.selectedOndemandReports = [];
            }


        },
        clickSelectedGroupRoles(keyRole) {
            const oldSelectedGroupRoles = this.selectedGroupRoles[keyRole];
            const roleIds = this.permissionsControl.roles[keyRole].map(dataRole => dataRole.id) || [];
            if (oldSelectedGroupRoles) {
                this.selectedRoles = this.selectedRoles.filter(roleId => !roleIds.includes(roleId));
            } else {
                this.selectedRoles = uniq([
                    ...roleIds,
                    ...this.selectedRoles,
                ]);
            }
        },
        updateSelectedGroupRoles() {
            for (const keyRole in this.permissionsControl.roles) {
                const roleIds = this.permissionsControl.roles[keyRole].map(dataRole => dataRole.id) || [];
                this.selectedGroupRoles[keyRole] = intersection(this.selectedRoles, roleIds).length === roleIds.length;
            }
        },
        saveData() {
            if (this.$refs.form.validate()) {
                this.hasError = false
                this.hasErrorProfile = false;
                if (this.isManual) {
                    if (!(this.selectedPrograms.length
                        || this.selectedReports.length
                        || this.selectedOndemandReports.length)) {
                        this.$store.commit('scrollToTop', '.nde-general-user-form');
                        return this.hasError = true
                    }

                    const moduleOnePrograms = this.programAccess.filter(dataProgram => dataProgram.module_id === '1').map(dataProgram => Number(dataProgram.id));

                    this.hasErrorProfile = !intersection(moduleOnePrograms, this.selectedPrograms.map(Number)).length;
                    if (this.hasErrorProfile) {
                        this.$store.commit('scrollToTop', '.nde-general-user-form');
                        return;
                    }
                } else {
                    if (!this.selectedProvinces.length) {
                        this.$store.commit('scrollToTop', '.nde-general-user-form');
                        return this.hasError = true
                    }
                }

                return this.$emit('onSaveData')
            } else {
                this.hasError = true
                this.$store.commit('scrollToTop', '.nde-general-user-form');
            }

        },

        hideModal() {
            this.$emit('onHideModal')
        },

        uniqueArr(arr) {
            return arr.filter((v, i, a) => a.findIndex(e => (e.id === v.id)) === i)
        },

        mappingData(data) {
            this.selectedPrograms = data && data.programs && data.programs.length && data.programs.filter(val => val.id).map(val => { return val.id.toString() }) || []
            this.selectedImageGroups = data && data.image_groups && data.image_groups.length && data.image_groups.filter(val => val.id).map(val => { return val.id.toString() }) || []
            this.selectedSupervisors = data && data.supervisors && data.supervisors.length && data.supervisors.filter(val => val.id).map(val => { return val.id.toString() }) || []
            this.selectedReports = data && data.reports && data.reports.length && data.reports.filter(val => val.id).map(val => { return val.id.toString() }) || []
            this.selectedRoles = data && data.roles && data.roles.length && data.roles.filter(val => val.id).map(val => { return val.id.toString() }) || []
            this.selectedProvinces = data && data.provinces && data.provinces.length && data.provinces.filter(val => val.id).map(val => { return val.id.toString() }) || []
            const selectedOndemandReports = (data?.ondemand_reports || data?.reports || []).filter(val => val.id).map(val => `${val.id}`) || [];
            this.selectedOndemandReports = selectedOndemandReports.filter(id => this.valuesOndemandReports.includes(id));
            if (this.selectedProvinces.length > 0) {
                this.type = 'province';
            }
        },

        async generateQRCode() {
            if (this.isGeneratedQRCode || this.dataUserDetail['is_mfa_setup']) {
                const confirmed = await this.$swal({
                    text: `Warning: If this user already added an account in their mobile MFA app (e.g. Duo, or Google Authenticator, or Microsoft Authenticator), generating a new QR code for this user will cause that account to become invalid.
						They will need to set up a new account in their mobile app, using this new QR code. Proceed?`,
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ok',
                    cancelButtonText: 'Cancel',
                });
                if (!confirmed.value) return;
            }

            this.$store.commit('setShowProgressAPI', true);
            const resultGetMfaQROauth = await this.$store.dispatch('callAPI', {
                url: '/getMfaQROauth',
                method: 'post',
                data: {
                    user_id: this.dataUserDetail['account_id'],
                },
            });
            this.$store.commit('setShowProgressAPI', false);
            if (resultGetMfaQROauth.message !== 'success') {
                return this.$swal({
                    icon: 'error',
                    text: this.getMessageResult(resultGetMfaQROauth),
                });
            }
            this.isGeneratedQRCode = true;
            await this.showQRcode(resultGetMfaQROauth?.data?.data?.qr_image);
        },
        async showQRcode(qrImage) {
            await this.$swal({
                html: `
					<p style="font-size: 1rem; margin-top: 1rem; margin-bottom: 0.5rem;">Please copy and paste this image into an email to be sent to this user. "Copy Image" is available
                        in the menu when you right-click an image in Chrome, Firefox, and Edge. In IE11, it's labelled
                        "Copy". Please right-click the barcode below and select either "Copy Image" or "Copy", in order
                        to have the image in your clipboard such that you can paste it into an email or another
                        document.</p>
                    <p style="font-size: 1rem; margin-bottom: 0.5rem;">The user will need to have a Multi-Factor Authentication app on their mobile device. This
                        includes Duo, Google Authenticator, and Microsoft Authenticator. The user will need to begin the
                        process to add a new account in their MFA app, and as part of that process, use their mobile
                        device to scan this barcode.</p>
                    <p style="font-size: 1rem; margin-bottom: 0.5rem;">Having done that, the user will be able to use their mobile MFA app to retrieve 6-digit MFA
                        codes, which will be necessary to enter in order to successfully log in.</p>
					<img id="qr-code-mfa" src="data:image/png;base64,${qrImage}" alt="MFA Setup QR Code">
				`,
                type: 'warning',
                showCloseButton: true,
                showDenyButton: true,
                confirmButtonText: 'Done',
                denyButtonText: 'Copy to Clipboard',
                allowOutsideClick: false,
                preDeny: async () => {
                    let copyImage = document.getElementById("qr-code-mfa");
                    const response = await fetch(copyImage.src);
                    const blob = await response.blob();
                    const image = [new ClipboardItem({
                        [blob.type]: blob,
                        "text/plain": new Blob([copyImage.alt], {
                            type: "text/plain",
                        }),
                        "text/html": new Blob([`
						    <p>${copyImage.alt}</p>
							<p>${copyImage.outerHTML}</p>
						`], {
                            type: "text/html",
                        }),
                    })];
                    navigator.clipboard.write(image);
                    await this.$swal({
                        type: 'success',
                        text: 'Successfully copied barcode to clipboard',
                    });
                    return this.showQRcode(qrImage);
                },
            });
        },
        handleChangeMfa(value) {
            if (value === 'TOTP') {
                this.dataUserDetail.is_mfa_setup = true;
                this.dataUserDetail.has_mfa = true;
                this.dataUserDetail.email_mfa = false;
            } else if (value === 'EMAIL') {
                this.dataUserDetail.is_mfa_setup = true;
                this.dataUserDetail.has_mfa = true;
                this.dataUserDetail.email_mfa = true;
            } else {
                this.dataUserDetail.is_mfa_setup = false;
                this.dataUserDetail.email_mfa = false;
                this.dataUserDetail.has_mfa = false;
            }
        },
        setSelectedMfa(data) {
            if (data.has_mfa && data.is_mfa_setup && !data.email_mfa) {
                this.selectedMfa = 'TOTP';
            } else if (!data.has_mfa && data.is_mfa_setup && data.email_mfa) {
                this.selectedMfa = 'EMAIL';
            } else if (!data.has_mfa && !data.is_mfa_setup && !this.dataUserDetail.email_mfa) {
                this.selectedMfa = 'OFF';
            } else {
                this.selectedMfa = null;
            }
        }
    },

    mounted() {
        if (this.isEditUser) {
            this.mappingData(this.dataUserDetail);
            this.isGeneratedQRCode = false;
            this.updateSelectedGroupRoles();
        }
    },

    watch: {
        dataUserDetail: {
            handler(newVal) {
                if (this.isEditUser) {
                    this.mappingData(newVal)
                    this.setSelectedMfa(newVal);
                }
            },
            deep: true
        },
        selectedRoles: {
            handler(newVal) {
                this.updateSelectedGroupRoles();
            },
            deep: true
        }

    }
}
</script>

<style lang="scss" scoped>
.nde-general-user-form {
    h3 {
        color: #9A9EA1;
    }
}
</style>
