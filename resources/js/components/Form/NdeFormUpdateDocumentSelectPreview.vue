<template>
  <nde-form>
    <template v-slot:ndeFormBody>
      <v-row v-if="!isImport" class="file-list-container">
        <v-col cols="12" v-for="(file, index) in filesAndData" :key="index">
          <div
            class="nde-upload-file-item"
            @click="selectFile(index)"
            :class="[
              file.uploadData.status == 'Saved' ? 'upload-success' : '',
              file.uploadData.status.includes('Failed') ? 'upload-failed' : '',
            ]"
          >
            <div class="selected-file-info">
              <div class="d-flex justify-space-between align-center">
                <div class="selected-file-info-col text-overflow-space">
                  <v-icon size="32" :color="getFileColor(file.file)">
                    {{ getFileIcon(file.file) }}
                  </v-icon>
                  <span class="file-name mr-5">{{ file.file.name }}</span>
                  <span class="upload-status" v-if="file.uploadData.status">{{
                    file.uploadData.status
                  }}</span>
                </div>
                <div class="selected-file-info-col">
                  <span class="file-progress"> {{ file.uploadData.progress || 0 }}% </span>
                  <nde-button fab @click="removeFile(index)" v-if="file.uploadData.progress === 0">
                    <v-icon size="10" color="#9A9EA1"> mdi-close </v-icon>
                  </nde-button>
                </div>
              </div>
              <div>
                <div cols="12" class="mt-4">
                  <v-progress-linear
                    :value="file.uploadData.progress || 0"
                    height="2"
                    color="teal"
                  ></v-progress-linear>
                </div>
              </div>
            </div>
          </div>
        </v-col>
      </v-row>
      <v-row v-if="isImport">
        <v-col cols="12" v-for="(file, index) in importFiles" :key="index">
          <div class="nde-upload-file-item">
            <div class="selected-file-info">
              <div class="d-flex justify-space-between align-center">
                <div class="selected-file-info-col text-overflow-space">
                  <v-icon size="32" :color="getFileColor(file)">
                    {{ getFileIcon(file) }}
                  </v-icon>
                  <span class="file-name mr-5">{{ file.name }}</span>
                </div>
                <div class="selected-file-info-col">
                  <nde-button fab @click="removeImportFile(index)">
                    <v-icon size="10" color="#9A9EA1"> mdi-close </v-icon>
                  </nde-button>
                </div>
              </div>
            </div>
          </div>
        </v-col>
      </v-row>
    </template>
  </nde-form>
</template>

<script>
import NdeForm from './NdeForm.vue';
import NdeButton from '../Button/NdeButton.vue';

export default {
  name: 'NdeFormUpdateDocumentSelectPreview',
  components: {
    NdeForm,
    NdeButton,
  },
  props: {
    files: {
      type: Array,
      default: () => [],
    },
    selected: {
      type: Number,
      default: -1,
    },
    filesAndData: {
      type: Array,
      default: () => [],
    },
    isImport: {
      type: Boolean,
      default: false,
    },
    importFiles: {
      type: Array,
      default: [],
    },
  },
  data() {
    return {
      removed: false,
    };
  },
  computed: {},
  watch: {},
  methods: {
    removeFile(i) {
      if (this.selected === i) {
        this.$emit('update:selected', -1);
      }
      this.removed = true;
      this.files.splice(i, 1);
      this.filesAndData.splice(i, 1);
    },
    removeImportFile(i) {
      this.removed = true;
      this.importFiles.splice(i, 1);
    },
    selectFile(i) {
      if (!this.removed) {
        this.files.forEach((file, index) => {
          if (index !== i) {
            file.checkbox = false;
          } else {
            file.checkbox = !file.checkbox;
            this.$emit('update:selected', file.checkbox ? i : -1);
          }
        });
      }
      this.removed = false;
    },
    getFileIcon(file) {
      let fileType = '';

      if (this.isImport) {
        fileType = file.name;
      } else {
        fileType = file.type || file.name || '';
      }

      if (fileType.includes('pdf')) {
        return 'mdi-file-pdf';
      }
      if (fileType.includes('mp4')) {
        return 'mdi-file-video';
      }
      if (fileType.includes('doc')) {
        return 'mdi-file-word';
      }
      return 'mdi-file';
    },
    getFileColor(file) {
      let fileType = '';

      if (this.isImport) {
        fileType = file.name;
      } else {
        fileType = file.type || file.name || '';
      }
      if (fileType.includes('pdf')) {
        return 'error';
      }
      if (fileType.includes('mp4')) {
        return '#41BA9D';
      }
      if (fileType.includes('doc')) {
        return '#2979FF';
      }
      return 'primary';
    },
  },
};
</script>
<style scoped lang="scss">
.file-list-container {
  max-height: 280px;
  overflow-y: auto;
}
.upload-success {
  background: #add8e6;
}

.upload-failed {
  background: #ffcccb;
}
.nde-upload-file-item {
  border: 1px solid #ebeced;
  box-sizing: border-box;
  border-radius: 8px;
  min-height: 4rem !important;
  padding: 0.5rem;
  cursor: pointer;

  .text-overflow-space {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    width: 85%;
    &:hover {
      overflow: visible;
      white-space: unset;
      word-break: break-all;
    }
  }

  .selected-file-info {
    width: 100%;

    &-col {
      min-height: 1.5rem !important;
      .v-btn {
        width: 1.125rem !important;
        height: 1.125rem !important;
        margin-left: 0.5rem;
        margin-bottom: 0.5rem;
      }
      .file-name {
        @extend %fontNormalBold;
        font-size: 0.75rem;
        line-height: 1.5rem;
        color: $darkGreyColor;
      }

      .upload-status {
      }

      .file-progress {
        @extend %fontNormal;
        font-size: 0.813rem;
        line-height: 1.5rem;
        color: #cccecf;
      }
    }
  }
}
</style>
