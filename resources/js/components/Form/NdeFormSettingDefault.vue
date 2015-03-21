<template>
    <nde-form>
        <template v-slot:ndeFormBody>
            <v-row>
                <v-col cols="12" md="5" class="d-flex">
                    <v-subheader v-text="'View document resolution'"></v-subheader>
                </v-col>
                <v-col cols="12" md="2" class="d-flex">
                    <v-select
                        v-model="itemsList.view_doc"
                        :items="documentResolutionOptions"
                        solo
                        attach
                        dense
                        aria-label="View document resolution"
                    ></v-select>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="12" md="5" class="d-flex">
                    <v-subheader v-text="'Pull from data feed when creating a record'"></v-subheader>
                </v-col>
                <v-col cols="12" md="2" class="d-flex">
                    <v-select
                        v-model="itemsList.create_doc_data_feed"
                        :items="statusOptions"
                        solo
                        attach
                        dense
                        aria-label="Pull from data feed when creating a record"
                    ></v-select>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="12" md="5" class="d-flex">
                    <v-subheader v-text="'Home Program'"></v-subheader>
                </v-col>
                <v-col cols="12" md="5" class="d-flex">
                    <v-select
                        v-model="itemsList.defaultprofile_id"
                        :items="homeProgramOptions"
                        solo
                        attach
                        dense
                        aria-label="Home Program"

                    ></v-select>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="12" md="5" class="d-flex">
                    <v-subheader v-text="'Delete from source when transferring record'"></v-subheader>
                </v-col>
                <v-col cols="12" md="2" class="d-flex">
                    <v-select
                        v-model="itemsList.promote_view"
                        :items="statusOptions"
                        solo
                        attach
                        dense
                        aria-label="Delete from source when transferring record"

                    ></v-select>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="12" md="5" class="d-flex">
                    <v-subheader v-text="'Always include annotations on downloaded documents'"></v-subheader>
                </v-col>
                <v-col cols="12" md="2" class="d-flex">
                    <v-select
                        v-model="itemsList.download_batestamp"
                        :items="statusOptions"
                        solo
                        attach
                        dense
                        aria-label="Always include annotations on downloaded documents"

                    ></v-select>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="12" md="5" class="d-flex">
                    <v-subheader v-text="'Send email when creating accounts / resetting others password'"></v-subheader>
                </v-col>
                <v-col cols="12" md="2" class="d-flex">
                    <v-select
                        v-model="itemsList.send_email"
                        :items="statusOptions"
                        solo
                        attach
                        dense
                        aria-label="Send email when creating accounts / resetting others password"

                    ></v-select>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="12" md="5" class="d-flex">
                    <v-subheader v-text="'Multitask'"></v-subheader>
                </v-col>
                <v-col cols="12" md="2" class="d-flex">
                    <v-select
                        v-model="itemsList.multitask"
                        :items="statusOptions"
                        solo
                        attach
                        dense
                        aria-label="Multitask"

                    ></v-select>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="12" md="5" class="d-flex">
                    <v-subheader v-text="'View document size'"></v-subheader>
                </v-col>
                <v-col cols="12" md="2" class="d-flex">
                    <v-select
                        v-model="itemsList.view_doc_auto_zoom"
                        :items="documentSizeOptions"
                        solo
                        attach
                        dense
                        aria-label="View document size"

                    ></v-select>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="12" md="5" class="d-flex">
                    <v-subheader v-text="'Default Image Group'"></v-subheader>
                </v-col>
                <v-col cols="12" md="2" class="d-flex">
                    <v-select
                        v-model="itemsList.default_image_group_id"
                        :items="imageOptions"
                        solo
                        attach
                        dense
                        aria-label="Default Image Group"

                    ></v-select>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="12" md="5" class="d-flex">
                    <v-subheader v-text="'Continuous Scrolling'"></v-subheader>
                </v-col>
                <v-col cols="12" md="2" class="d-flex">
                    <v-select
                        v-model="itemsList.view_scrolling"
                        :items="statusOptions"
                        solo
                        attach
                        dense
                        aria-label="Continuous Scrolling"

                    ></v-select>
                </v-col>
            </v-row>
        </template>
    </nde-form>
</template>

<script>
import NdeForm from "./NdeForm.vue";
import { mapState } from 'vuex';
export default {
    name: "NdeFormSettingDefault",
    components: {
        NdeForm,
    },
    props: {
        item: {
            type: Object,
        },
    },
    data() {
        return {
            itemsList: {},
            statusOptions: [
                {
                    value: "ON",
                    text: "ON",
                },
                {
                    value: "OFF",
                    text: "OFF",
                },
            ],
            documentResolutionOptions: [
                {
                    value: "HIGH",
                    text: "HIGH",
                },
                {
                    value: "LOW",
                    text: "LOW",
                },
            ],
            documentSizeOptions: [
                {
                    value: "Normal",
                    text: "Normal",
                },
                {
                    value: "Fit Width",
                    text: "Fit Width",
                },
            ],
            imageOptions: [],
            homeProgramOptions: [
                {
                    value: null,
                    text: "Use system default",
                },
            ],
        };
    },
    computed: {
        ...mapState(['programs']),
    },
    mounted() {
        this.itemsList = this.item
        if(this.itemsList.defaultprofile_id !== null){
            this.itemsList.defaultprofile_id = parseInt(this.itemsList.defaultprofile_id)
        }

        const cookieObj = JSON.parse(atob(this.$cookie.get('roles')));
        this.imageOptions = [];
        if(cookieObj?.image_groups) {
            cookieObj?.image_groups.map(image_group => {
                this.imageOptions.push({
                    value: image_group?.id,
                    text: image_group?.name
                })
            })
        }
    },
    methods: {
        onCancelButton(){
            const initialSetting = {};
            Object.assign(initialSetting, this.settingInfo)
            this.itemsList = {};
            this.itemsList = initialSetting
            if(this.itemsList.defaultprofile_id !== null){
                this.itemsList.defaultprofile_id = parseInt(this.itemsList.defaultprofile_id)
            }
        },
        programListDetail(programsMenu){
            programsMenu.forEach((program) => {
                    if(program.meta.has_key_search){
                        this.homeProgramOptions.push({
                            value: parseInt(program.id),
                            text: program.name
                        })
                    };
                    if(program.meta.has_key_search && program.subProfiles.length > 0){
                        this.programListDetail(program.subProfiles)
                    }
                    if(!program.meta.has_key_search && program.subProfiles.length > 0){
                        this.programListDetail(program.subProfiles)
                    }
                })
        }
    },
    watch: {
      programs(newVal){
        this.programListDetail(newVal)
      },
      itemsList: {
          handler(val){
              if(val.defaultprofile_id === null){
                  val.defaultprofile_id = this.homeProgramOptions[1].value
              }
              //console.log("this is watching: ", val)
              this.$emit("updatedSetting", val)
          },
          deep: true
      }
    }
};
</script>
<style scoped lang="scss">
label v-subheader {
    @extend %fontNormalBold;
}
:v-deep .v-input {
    .v-input__append-inner {
        margin-top: 0.625rem !important;
    }
}
.col {
    padding: 0 !important;
}
</style>
