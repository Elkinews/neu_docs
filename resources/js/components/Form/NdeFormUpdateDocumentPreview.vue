<template>
  <nde-form>
    <template v-slot:ndeFormBody>
      <v-row>
        <v-col cols="12" class="d-flex justify-end">
          <slot name="ndeButtonClose"></slot>
        </v-col>
        <v-col cols="12" v-show="fileSelected && !isImport">
          <v-row>
            <v-col cols="12" class="preview-data" v-if="isFilePDF">
              <label>Preview</label>
              <v-divider></v-divider>
              <div>
                <iframe :src="urlFilePDF" width="100%" height="370"> </iframe>
              </div>
              <v-alert v-if="error" text color="#F9E1E1">
                <v-icon size="24" color="#C32C39"> mdi-alert-outline </v-icon>
                <ul>
                  <li>
                    <span>{{ error }}</span>
                  </li>
                </ul>
              </v-alert>
            </v-col>
            <v-col cols="12" class="setting-data" v-if="isAnnotationShow">
              <v-expansion-panels v-model="settingPanel" multiple>
                <v-expansion-panel>
                  <v-expansion-panel-header>
                    <label>Settings</label>
                  </v-expansion-panel-header>
                  <v-expansion-panel-content>
                    <v-row>
                      <v-col cols="12" md="12" class="mt-2">
                        <label class="mb-1">Annotation Type</label>
                        <v-autocomplete v-model="dataSetting.annotationType" :items="annotations" outlined
                          item-text="label" item-value="code" placeholder="Annotation Type"
                          aria-label="Annotation Type"></v-autocomplete>
                        <label class="mb-1">Annotation</label>
                        <v-text-field v-model="dataSetting.annotationText" placeholder="Annotation" outlined type="text"
                          aria-label="Annotation" :error="annotationValidation" :error-messages="annotationError"
                          v-if="!isAnnotionDate"></v-text-field>

                        <v-menu v-if="isAnnotionDate" v-model="isAnnotationDatePicker" :close-on-content-click="false"
                          transition="scale-transition" max-width="290px" min-width="auto">
                          <template v-slot:activator="{ on, attrs }">
                            <v-text-field v-model="dataSetting.annotationText"
                              @blur="annotationDateVal = parseDate(dataSetting.annotationText)" hint="MM/DD/YYYY"
                              append-icon="mdi-calendar" v-bind="attrs" v-on="on" outlined type="text"
                              class="annotation-date-stamp-input"></v-text-field>
                          </template>
                          <v-date-picker v-model="annotationDateVal" no-title v-bind="$attrs"
                            @input="isAnnotationDatePicker = false"></v-date-picker>
                        </v-menu>

                        <label class="mb-1">Position</label>
                        <v-autocomplete v-model="dataSetting.annotationPosition" :items="positions" outlined
                          item-text="label" item-value="value" placeholder="Position"
                          aria-label="Position"></v-autocomplete>
                      </v-col>
                    </v-row>
                  </v-expansion-panel-content>
                </v-expansion-panel>
              </v-expansion-panels>
              <!-- End Form Setting Data -->
            </v-col>
          </v-row>
          <!-- End Show Preview Seting Data -->
        </v-col>
      </v-row>
    </template>
  </nde-form>
</template>

<script>
import { mapState } from 'vuex';
import NdeForm from './NdeForm.vue';
import NdeButton from '../Button/NdeButton.vue';
import NdeDatePicker from '../DatePicker/NdeDatePicker.vue';

export default {
  name: 'NdeFormUpdateDocumentPreview',
  components: {
    NdeForm,
    NdeButton,
    NdeDatePicker,
  },
  props: {
    fileSelected: {
      type: [File, Object],
      default: () => ({}),
    },
    files: {
      type: Array,
    },
    uploadTotalStatus: {
      type: String,
      required: true,
    },
    isImport: {
      type: Boolean,
      default: false,
    },
  },

  computed: {
    ...mapState(['hideShowColsData', 'currentDocMimeTypes', 'accesstoken']),

    isFilePDF() {
      let fileType = this.fileSelected.type || '';
      return fileType.includes('pdf');
    },

    urlFilePDF() {
      return URL.createObjectURL(this.fileSelected);
    },

    isAnnotionDate() {
      if (this.dataSetting.annotationType) {
        let filteredType = this.annotations.filter(
          (type) => type.code == this.dataSetting.annotationType,
        )[0];
        if (filteredType && filteredType.label == 'Date Stamp') {
          return true;
        }

        return false;
      }

      return false;
    },

    annotationValidation() {
      if (this.dataSetting.annotationType) {
        let filteredType = this.annotations.filter(
          (type) => type.code == this.dataSetting.annotationType,
        )[0];
        if (filteredType && filteredType.label == 'Batch Code') {
          var mySubString = filteredType.pattern.substring(
            filteredType.pattern.indexOf('{') + 1,
            filteredType.pattern.lastIndexOf('}'),
          );
          console.log(mySubString);
          const length = parseInt(mySubString);
          if (this.dataSetting.annotationText) {
            if (this.dataSetting.annotationText.length == length) {
              if (parseInt(this.dataSetting.annotationText).toString().length == 2) {
                this.annotationError = '';
                return false;
              }
            }
          }

          this.annotationError = 'Trans code must have a length of ' + length + '.';
          return true;
        }
      }
      this.annotationError = '';
      return false;
    },

    isAnnotationShow() {
      let pdf_counts = 0;
      this.files.map(file => {
        if (file.type.includes('pdf')) {
          pdf_counts++;
        }
      });

      if (pdf_counts != this.files.length) {
        this.$emit('onUpdateDataSetting', this.dataSetting);
        return false;
      }

      if (this.annotations.length) return true;

      this.$emit('onUpdateDataSetting', this.dataSetting);
      return false;
    }
  },

  data() {
    return {
      settingPanel: [0],
      isAnnotationDatePicker: false,
      inputAnnotationDate: '',
      annotationDateVal: null,
      dataSetting: {
        bmarkName: '',
        annotationType: null,
        annotationText: null,
        annotationPosition: {
          vertical: null,
          horizontal: null,
        },
      },
      annotations: [],
      positions: [
        {
          label: 'Bottom center',
          value: {
            vertical: 'bottom',
            horizontal: 'center',
          },
        },
        {
          label: 'Top left',
          value: {
            vertical: 'top',
            horizontal: 'left',
          },
        },
        {
          label: 'Top right',
          value: {
            vertical: 'top',
            horizontal: 'right',
          },
        },
        {
          label: 'Top center',
          value: {
            vertical: 'top',
            horizontal: 'center',
          },
        },
        {
          label: 'Bottom left',
          value: {
            vertical: 'bottom',
            horizontal: 'left',
          },
        },
        {
          label: 'Bottom right',
          value: {
            vertical: 'bottom',
            horizontal: 'right',
          },
        },
      ],
      error: '',
      annotationError: '',
    };
  },

  created() {
    this.getAnnotations();
  },

  methods: {
    async getAnnotations() {
      this.annotations = [];

      const resultAnnoTypes = await this.$store.dispatch('callAPI', {
        url: '/getAnnotationTypesOauth',
        method: 'post',
        data: {
          profile_id: this.$store.state.currentProgram.id,
        },
      });

      if (resultAnnoTypes.message == 'success') {
        this.annotations = resultAnnoTypes.data.data.data.types;
        let is_mandatory = false;
        this.annotations.map(ann => {
          if (ann?.is_mandatory == 't') {
            is_mandatory = true;
            this.dataSetting.annotationType = ann?.code;
          }
        });

        this.$emit('onMandatory', is_mandatory);
        if (this.annotations.length) {
          this.annotations.unshift({
            code: 0,
            label: 'None',
          });
        }
      }
    },

    parseDate(date) {
      if (!date) return null

      const [month, day, year] = date.split('/');
      return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`
    }
  },

  watch: {
    dataSetting: {
      handler: function () {
        if (this.dataSetting.annotationType == 0) {
          this.dataSetting.annotationText = '';
        }
        this.$emit('onUpdateDataSetting', this.dataSetting);
      },
      deep: true,
    },

    annotationDateVal: function (val) {
      if (val) {
        if (!val) return null

        const [year, month, day] = val.split('-')
        this.dataSetting.annotationText = `${month}/${day}/${year}`;
      }
    },

    annotationError: function (val) {
      if (val) {
        this.$emit('onChangeAnnotationStatus', true);
      } else {
        this.$emit('onChangeAnnotationStatus', false);
      }
    }
  },
};
</script>
<style scoped lang="scss">
:v-deep .form-preview-not-selected {
  border: 1px dashed #3d769e;
  box-sizing: border-box;
  border-radius: 8px;
  height: 14.375rem;
  margin-top: 16rem;
  justify-content: center !important;
  align-items: center !important;
  display: flex;
  flex-flow: wrap;

  h3 {
    @extend %fontNormalBold;
    font-size: 1rem;
    line-height: 1.5rem;
    color: #3d769e;
  }

  .v-input--checkbox {
    height: 4rem;
    background: #f7f7f7;
    box-shadow: 0px 11px 28px rgba(79, 142, 186, 0.21);
    border-radius: 8px;
    transform: rotate(-2.88deg);
    padding-top: 0.5rem;
    padding-left: 0.5rem;
    width: 100%;

    .v-label {
      border: 1px solid #ebeced;
      box-sizing: border-box;
      border-radius: 8px;
      height: 2.75rem !important;
      padding: 0 1rem;
      background: #ffffff;
      transform: rotate(-2deg);
      margin-right: 1rem;

      .col {
        height: 1.5rem !important;

        .file-name {
          @extend %fontNormalBold;
          font-size: 0.75rem;
          line-height: 1.5rem;
          color: $darkGreyColor;
        }
      }
    }
  }
}

:v-deep .setting-data,
:v-deep .preview-data {
  label {
    text-transform: uppercase;
    @extend %fontNormalBold;
    font-size: 0.75rem;
    line-height: 1.5rem;
    color: #636b6e;
  }
}

:v-deep .preview-data {
  .v-alert__content {
    li {
      @extend %fontNormalBold;
      color: #c32c39;
      font-size: 0.813rem;
      line-height: 1.5rem;
    }
  }
}

:v-deep .setting-data {
  fieldset {
    background-color: #ffffff;
  }

  .v-btn__content {
    color: #ffffff;
  }
}

:v-deep .annotation-date-stamp-input .v-input__slot {
  background: rgb(244, 247, 251);
}
</style>
