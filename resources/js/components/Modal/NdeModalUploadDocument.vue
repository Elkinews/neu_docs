<template>
  <v-dialog v-model="isModalUploadDocument" max-width="1200" persistent content-class="nde-upload-modal">
    <v-row class="ma-0" v-if="permissionShowModal">
      <v-col cols="12" :md="files.length || isImport ? '6' : '12'" class="ma-0 pa-0" style="background-color: #ffffff">
        <v-card class="pa-2" style="border-radius: 0; box-shadow: none">
          <v-card-title class="font-weight-bold d-flex justify-space-between">
            <h3 v-if="!isMobile">Upload Document</h3>
            <h5 v-else>Upload Document</h5>
            <v-icon v-if="files.length === 0 && importFiles.length === 0" aria-label="Close" size="24px" color="red"
              @click="hideModal">
              mdi-close
            </v-icon>
          </v-card-title>
          <v-card-text>
            <v-row>
              <v-col cols="12" :md="files.length ? '12' : '8'" align="left">
                <!-- Start Import file Menu-->
                <v-menu bottom offset-y content-class="upload-menu">
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn :class="{ 'ma-2': !isMobile, 'mb-2': isMobile }" color="success" v-bind="attrs" v-on="on">
                      <v-icon left>mdi-plus</v-icon>
                      Browse
                    </v-btn>
                  </template>
                  <v-list>
                    <v-list-item v-for="(option, i) in UploadOption.filter(
                      (item) =>
                        !isModalUploadDocumentForReplace ||
                        (isModalUploadDocumentForReplace && item !== 'Folder'),
                    )" :key="i" @click="onAddFiles(option)">
                      <v-list-item-title>{{ option }}</v-list-item-title>
                    </v-list-item>
                  </v-list>
                </v-menu>
                <!-- End of Import file Menu-->
                <input type="file" :multiple="!isModalUploadDocumentForReplace" id="assetsFieldHandle-on-modal"
                  style="display: none" @change="onChange" ref="file" accept=".pdf,.jpg,.jpeg,.png,.pdf,.mp4,.doc,.docx"
                  :allowdirs="isFolderUpload" :directory="isFolderUpload" :webkitdirectory="isFolderUpload" />

                <nde-button color="dark" @click="callMyDoc" :disabled="uploadTotalStatus == 'Uploading'"
                  :class="{ 'w-100 mt-2': isMobile }">
                  <v-icon left>mdi-inbox-arrow-down-outline</v-icon>
                  {{
                      isModalUploadDocumentForReplace
                        ? 'Import File From My Documents'
                        : 'Import Files From My Documents'
                  }}
                </nde-button>
              </v-col>
              <v-col cols="12">
                <nde-form-update-document :files="files" :filesAndData="filesAndData"
                  @onUpdateFileForReplace="onUpdateFileForReplace" @onChangeFiles="onChangeFiles"
                  v-if="uploadTotalStatus !== 'Uploading'">
                </nde-form-update-document>
              </v-col>
              <v-col cols="12">
                <nde-form-update-document-select-preview :files="files" :selected.sync="selected"
                  :filesAndData="filesAndData" :isImport="isImport" :importFiles="importFiles">
                </nde-form-update-document-select-preview>
              </v-col>
            </v-row>
          </v-card-text>
          <v-card-actions>
            <v-row v-show="!!files.length || isImport" class="mt-5">
              <v-col cols="12" class="d-flex justify-space-between" :class="{ 'flex-column': isMobile }">
                <nde-button color="red" class="nde-button-cancel" :class="{ 'order-2 w-100': isMobile }"
                  @click="hideModal" v-if="files.length === 0 || importFiles.length === 0">
                  <v-icon left>mdi-close</v-icon>
                  Cancel
                </nde-button>
                <nde-button style="height: 2.5rem; width: 11.25rem" :class="{ 'order-1 mb-3 w-100': isMobile }"
                  @click="startUploadHandle" :loading="uploadTotalStatus == 'Uploading'" :disabled="isErrorAnnotation">
                  {{ isRetry ? 'Retry Upload' : 'Start Upload' }}</nde-button>
              </v-col>
            </v-row>
          </v-card-actions>
        </v-card>
      </v-col>
      <v-col cols="12" md="6" v-show="!!files.length || isImport" style="background: #f4f7fb; position: relative" class="doc-preview">
        <nde-form-update-document-preview :fileSelected="fileSelected" :uploadTotalStatus="uploadTotalStatus"
          :isImport="isImport" :files="files" @onUpload="handleUpload" @onUpdateDataSetting="onUpdateDataSetting"
          @onChangeAnnotationStatus="onChangeAnnotationStatus" @onMandatory="onMandatory">
          <template v-slot:ndeButtonClose>
            <nde-button fab x-small color="#F9E1E1" @click="hideModal()">
              <v-icon size="18" color="error"> mdi-close </v-icon>
            </nde-button>
          </template>
        </nde-form-update-document-preview>
      </v-col>
    </v-row>
    <nde-alert-warning v-if="!permissionShowModal && !isLoadding" :message="message"></nde-alert-warning>

    <nde-modal-import :showModal="isShowImportModal" @onClose="isShowImportModal = false" @onImport="onImport">
    </nde-modal-import>
  </v-dialog>
</template>

<script>
import { mapState } from 'vuex';
import sha1 from 'sha1-file-web';
import NdeButton from '../Button/NdeButton.vue';
import NdeFormUpdateDocument from '../Form/NdeFormUpdateDocument.vue';
import NdeFormUpdateDocumentSelectPreview from '../Form/NdeFormUpdateDocumentSelectPreview.vue';
import NdeFormUpdateDocumentPreview from '../Form/NdeFormUpdateDocumentPreview.vue';
import NdeAlertWarning from '../Alert/NdeAlertWarning.vue';
import NdeModalImport from './NdeModalImport.vue';
import axios from 'axios';
import { CHUNK_SIZE } from '@utils/constants';

export default {
  name: 'NdeModalUploadDocument',
  components: {
    NdeButton,
    NdeFormUpdateDocument,
    NdeFormUpdateDocumentSelectPreview,
    NdeFormUpdateDocumentPreview,
    NdeAlertWarning,
    NdeModalImport,
  },

  computed: {
    ...mapState([
      'isModalUploadDocument',
      'currentUploadBoxType',
      'accesstoken',
      'isModalUploadDocumentForReplace',
      'replace_old_nuid',
      'currentProgram',
      'currentImageId',
      'replaceName',
      'env',
      'currentUploadFolderId',
      //'myDocumentData',
    ]),
    fileSelected() {
      let findFile = {};
      (this.files || []).forEach((file, index) => {
        if (index === this.selected) {
          findFile = file;
        }
      });

      if (this.files && this.selected == -1) {
        findFile = this.files[0];
      }

      return findFile;
    },
  },

  data() {
    return {
      message: 'Your account is not authorized to Update Document',
      files: [], // Store our uploaded files
      selected: -1,
      isLoadding: true,
      permissionShowModal: false,
      tabs: {},
      isShowImportModal: false,
      dataSettings: null,
      filesAndData: [],
      uploadTotalStatus: '',
      isImport: false,
      importFiles: [],
      importCount: 0,
      isRetry: false,
      UploadOption: ['Files', 'Folder'],
      isFolderUpload: false,
      hasNewFile: false,
      isErrorAnnotation: false,
      isMandatory: false
    };
  },

  async created() {
    const scanModalData = await this.$store.dispatch('getScanModalInfo');
    const allowEdit = scanModalData?.AllowEditIndOauth;
    this.isLoadding = false;
    if (allowEdit?.allow_edit_ind === 't') {
      this.permissionShowModal = true;
    }
  },

  methods: {
    async callMyDoc() {
      this.$store.commit('setShowProgressAPI', true);
      await this.$store.dispatch({ type: 'myDocumentLoadingFiles' });
      this.$store.commit('setShowProgressAPI', false);
      this.isShowImportModal = true;
    },

    async calcUploadTotalStatus() {
      const success_count = this.filesAndData.filter(
        (item) => item.uploadData.status === 'Saved' && item.uploadData.progress == 100,
      );
      if (success_count.length == this.filesAndData.length) {
        this.uploadTotalStatus = 'Uploaded All';

        const confirmed = await this.$swal({
          title: 'Success',
          text: 'All files are uploaded successfully!',
          type: 'success',
          showCancelButton: false,
          confirmButtonText: 'Ok',
        });

        if (confirmed.value) {
          this.hasNewFile = true;
          this.hideModal();
        }
      }
      return;
    },

    async calcImportTotalStatus() {
      const checkedItems = this.importFiles.filter((o) => o.checked);
      if (checkedItems.length == this.importCount) {
        this.uploadTotalStatus = 'Uploaded All';

        const confirmed = await this.$swal({
          title: 'Success',
          text: 'All files are imported successfully!',
          type: 'success',
          showCancelButton: false,
          confirmButtonText: 'Ok',
        });

        if (confirmed.value) {
          this.hasNewFile = true;
          this.hideModal();
        }
      }
    },

    hideModal() {
      const data = { hasNewFile: this.hasNewFile };
      this.$store.commit('closeModalUploadDoucment', data);
    },

    async onChange() {
      this.isImport = false;
      this.importFiles = [];
      if (this.isModalUploadDocumentForReplace) {
        this.files = [];
        this.filesAndData = [];

        for (let i = 0; i < this.$refs.file.files.length; i++) {
          let file = this.$refs.file.files[i];
          const replaceExt = this.replaceName.split('.').pop();
          const fileExt = file.name.split('.').pop();
          if (fileExt != replaceExt) {
            await this.$swal({
              title: 'Warnning',
              text: 'Please select another file with same extention.',
              type: 'error',
              showCancelButton: false,
              confirmButtonText: 'Ok',
            });
          } else {
            this.files.push(this.$refs.file.files[i]);
            this.filesAndData.push({
              file: this.$refs.file.files[i],
              uploadData: {
                progress: 0,
                status: '',
              },
              fileData: null,
            });
          }
        }
      } else {
        for (let i = 0; i < this.$refs.file.files.length; i++) {
          let isExist = false;
          let file = this.$refs.file.files[i];
          this.files.map((item) => {
            if (file.name == item.name) {
              isExist = true;
            }
          });
          console.log(isExist);
          if (!isExist) {
            this.files.push(this.$refs.file.files[i]);
            this.filesAndData.push({
              file: this.$refs.file.files[i],
              uploadData: {
                progress: 0,
                status: '',
              },
              fileData: null,
            });
          }
        }

        this.$refs.file.value = '';
      }
    },

    onUpdateFileForReplace(data) {
      this.files = data.files;
      this.filesAndData = data.filesAndData;
      this.isImport = false;
      this.importFiles = [];
    },

    onChangeFiles() {
      this.isImport = false;
      this.importFiles = [];
    },

    onAddFiles(option) {
      if (option === 'Files') {
        this.isFolderUpload = false;
      } else if (option === 'Folder') {
        this.isFolderUpload = true;
      }

      this.$nextTick(() => {
        this.$refs.file.click();
      });
    },

    onImport(data) {
      this.isImport = true;
      this.importFiles = data;
    },

    async startUploadHandle() {
      if (this.isImport) {
        this.handleImportFiles();
      } else {
        await this.handleUpload(null);
      }
    },

    async handleImportFiles() {
      this.uploadTotalStatus = 'Uploading';
      this.importCount = 0;

      for (let item in this.importFiles) {
        var cloneUuid = '';
        var data = new FormData();
        data.append('profileId', this.$store.state.currentProgram.id);
        data.append('transcode', true);
        var config = {
          method: 'post',
          url: this.env.FS_URL + `single/${this.importFiles[item].nuid}/clone`,
          headers: {
            Authorization: 'Bearer ' + this.accesstoken,
          },
          data: data,
        };

        this.$store.dispatch('increaseCallCount');

        await axios(config)
          .then((response) => {
            cloneUuid = response?.data?.nuid;
          })
          .catch((error) => {
            console.log(error);
          });

        this.$store.dispatch('increaseCallCount');

        await axios
          .post('/uploadFileMetadataOauth', {
            profileId: this.currentProgram.id,
            boxType: this.currentUploadBoxType,
            nuid: cloneUuid,
            filePath: this.importFiles[item].name,
            docId: this.currentImageId,
            file_size: this.importFiles[item].file_size,
            page_count: this.importFiles[item].page_count,
          })
          .then((response) => {
            console.log(response);
            this.importFiles[item].checked = true;
            this.importCount++;
            this.calcImportTotalStatus();
          })
          .catch(async (error) => {
            console.log(error);
            this.uploadTotalStatus = 'Uploaded All';

            const confirmed = await this.$swal({
              title: 'Error',
              text: 'Import Failed, please try again',
              type: 'warning',
              showCancelButton: false,
              confirmButtonText: 'Ok',
            });
          });
      }
    },

    onUpdateDataSetting(data) {
      this.dataSettings = data;
    },

    async handleUpload(data) {
      if (this.isMandatory && (!this.dataSettings?.annotationType || !this.dataSettings?.annotationText || !this.dataSettings?.annotationPosition?.horizontal || !this.dataSettings?.annotationPosition?.vertical)) {
        await this.$swal({
          title: 'Annotation is mandatory!',
          text: 'Please Select Annotation type and input all information!',
          type: 'warning',
          showCancelButton: false,
          confirmButtonText: 'Ok',
        });
        return;
      }
      if (this.dataSettings?.annotationType) {
        if (!this.dataSettings?.annotationText || !this.dataSettings?.annotationPosition?.horizontal || !this.dataSettings?.annotationPosition?.vertical) {
          await this.$swal({
            title: 'Error',
            text: 'Please Select Annotation type and input all information',
            type: 'warning',
            showCancelButton: false,
            confirmButtonText: 'Ok',
          });
          return;
        }
      }

      if (!this.files.length) {
        await this.$swal({
          title: 'Error',
          text: 'Please Select Annotation upload!',
          type: 'warning',
          showCancelButton: false,
          confirmButtonText: 'Ok',
        });
        return;
      }

      let fileIndex = 0;
      while (fileIndex < this.files.length) {
        if (this.filesAndData[fileIndex].uploadData.status != 'Saved') {
          this.filesAndData[fileIndex].uploadData.status = 'Uploaded';
          this.filesAndData[fileIndex].uploadData.progress = 0;
          await this.uploadProcess(fileIndex);
        }
        fileIndex++;
      }

      this.uploadTotalStatus = 'Done';

      const success_count = this.filesAndData.filter(
        (item) => item.uploadData.status === 'Saved' && item.uploadData.progress == 100,
      );
      if (success_count.length != this.filesAndData.length) {
        this.isRetry = true;
      } else {
        this.isRetry = false;
      }

      return;
    },

    async uploadProcess(index) {
      this.uploadTotalStatus = 'Uploading';

      this.files[index].status = 'Uploading';
      if (this.files[index].size > CHUNK_SIZE) {
        await this.uploadFile(this.files[index], index);
        return;
      } else {
        var data = new FormData();
        data.append('profile_id', this.$store.state.currentProgram.id);
        data.append('generate_thumbnail', false);
        data.append('transcode', true);
        data.append('file', this.files[index]);
        data.append('sha1check', await sha1(this.files[index]));
        var config = {
          method: 'post',
          url: this.env.FS_URL + 'single',
          headers: {
            Authorization: 'Bearer ' + this.accesstoken,
          },
          data: data,
          onUploadProgress: (progressEvent) => {
            this.filesAndData[index].uploadData.progress = Math.round(
              (progressEvent.loaded * 100) / progressEvent.total,
            );
          },
        };

        try {

          this.$store.dispatch('increaseCallCount');

          const responseUpload = await axios(config);
          console.log(responseUpload);
          if (responseUpload.status == 201) {
            this.filesAndData[index].fileData = responseUpload.data;
            this.filesAndData[index].uploadData.status = 'Uploaded';
            await this.saveProcess(index);
          } else {
            console.log(error);
            this.filesAndData[index].uploadData.status = 'Upload Failed';
            this.$store.dispatch({ type: 'getAccesstokenRefresh' });
          }

          return;
        } catch (error) {
          console.log(error);
          this.filesAndData[index].uploadData.status = 'Upload Failed';
          this.$store.dispatch({ type: 'getAccesstokenRefresh' });
        }
      }
    },

    async lockFile(isUnLocked) {
      this.$store.dispatch('increaseCallCount');
      await axios.post('/lockFileOauth', {
        profile_id: this.$store.state.currentProgram.id,
        nuid: this.replace_old_nuid,
        unlock_file: isUnLocked,
      });
    },

    async saveProcess(index) {
      if (this.isModalUploadDocumentForReplace) {
        // Lock the file on start of upload
        await this.lockFile(false);
        // Start of saving file
        this.filesAndData[index].uploadData.status = 'Saving';
        this.$store.dispatch('increaseCallCount');
        axios
          .post('/updateDocumentPieceOauth', {
            profile_id: this.$store.state.currentProgram.id,
            new_nuid: this.filesAndData[index].fileData?.nuid,
            old_nuid: this.replace_old_nuid,
            new_file_path: this.files[index].name,
            new_file_size: this.filesAndData[index].fileData?.file_size,
            new_page_count: this.filesAndData[index].fileData?.page_count,
          })
          .then(async (response) => {
            console.log(response);
            if (
              this.files[index].size > CHUNK_SIZE &&
              (this.files[index].type.includes('video') || this.files[index].type.includes('mp4'))
            ) {
              var resTranscode = await this.transcoding(index);
            }
            this.filesAndData[index].uploadData.status = 'Saved';
            this.calcUploadTotalStatus();
          })
          .catch((error) => {
            console.log(error);
            this.filesAndData[index].uploadData.status = 'Save Failed';
          });
        // Unlock the file
        await this.lockFile(true);
      } else {
        this.filesAndData[index].uploadData.status = 'Saving';

        const resulUploadFileMetadataOauth = await this.$store.dispatch('callAPI', {
          url: '/uploadFileMetadataOauth',
          method: 'post',
          data: {
            scan: false,
            profileId: this.$store.state.currentProgram.id,
            boxType: this.currentUploadBoxType,
            nuid: this.filesAndData[index].fileData?.nuid,
            filePath: this.files[index].name,
            docId: this.$store.state.currentImageId,
            file_size: this.filesAndData[index].fileData?.file_size,
            page_count: this.filesAndData[index].fileData?.page_count,
            tabname: this.selectedTab,
            bmarkName: this.currentUploadBoxType == 'B' ? this.dataSettings?.bmarkName || '' : '',
            folder_id: this.currentUploadFolderId || '',
            annotation_location:
              this.dataSettings?.annotationPosition?.horizontal &&
                this.dataSettings?.annotationPosition?.vertical
                ? this.dataSettings?.annotationPosition?.horizontal.toUpperCase() +
                ' ' +
                this.dataSettings?.annotationPosition?.vertical.toUpperCase()
                : '',
            annotation_type_code: this.dataSettings?.annotationType || '',
            annotation_input: this.dataSettings?.annotationText || '',
          },
        });
        console.log(resulUploadFileMetadataOauth);
        if (resulUploadFileMetadataOauth.message == 'success') {
          if (
            this.files[index].size > CHUNK_SIZE &&
            (this.files[index].type.includes('video') || this.files[index].type.includes('mp4'))
          ) {
            await this.transcoding(index);
          }

          if (this.files[index].type.includes('pdf')) {
            const org_filename = this.files[index].name;
            const f_name = org_filename.split('.').slice(0, -1).join('.')
            await this.markForAnnotation(this.filesAndData[index].fileData?.nuid, f_name);
          }

          this.filesAndData[index].uploadData.status = 'Saved';
          await this.calcUploadTotalStatus();
          return;
        } else {
          this.filesAndData[index].uploadData.status = 'Save Failed';
          return;
        }
      }
    },

    createChuncks(file, fileType, fileExtension) {
      console.log('type type: ', fileType);
      let startPointer = 0;
      let endPointer = file.size;
      let chunks = [];
      while (startPointer < endPointer) {
        let newStartPointer = startPointer + CHUNK_SIZE;
        chunks.push(file.slice(startPointer, newStartPointer));
        startPointer = newStartPointer;
      }
      const chunkingFile = chunks.map((chunkFile, index) => {
        let file = new File([chunkFile], 'chuck-file-' + index + '.' + fileExtension, {
          type: fileType,
        });
        console.log('chunking file: ', file);
        return file;
      });
      return chunkingFile;
    },

    async uploadFile(file, fileIndex) {
      const fileType = file.type;
      const fileExtension = file.name.split('.').pop();
      const chunkFiles = await this.createChuncks(file, fileType, fileExtension);

      var chunk = 1;
      var index = 0;
      var nuid = '';

      while (chunk <= chunkFiles.length) {
        console.log('ground_uuid: ', ground_uuid);
        console.log('chunk number: ', chunk);
        console.log('chunk: ', chunkFiles[index]);

        this.filesAndData[fileIndex].uploadData.progress = (
          (index / chunkFiles.length) *
          100
        ).toFixed(1);

        if (chunk === 1) {
          var ground_uuid = '';
        }
        if (chunk === chunkFiles.length) {
          chunk = -1;
        }

        var response = await this.uploadingFiles(
          chunk,
          chunkFiles[index],
          ground_uuid,
          fileIndex,
          chunkFiles.length,
        );

        if (response == 'error') {
          await this.$store.dispatch({ type: 'getAccesstokenRefresh' });
          response = await this.uploadingFiles(
            chunk,
            chunkFiles[index],
            ground_uuid,
            fileIndex,
            chunkFiles.length,
          );

          if (response == 'error') {
            break;
          }
        }

        console.log('main response: ', response);
        if (response?.data) {
          ground_uuid = response.data.file_group_uuid;
        }
        if (chunk == -1) {
          nuid = response.data.nuid;
          console.log('finish uploading');
          break;
        }
        chunk++;
        index++;
      }
      if (chunk == -1) {
        this.filesAndData[fileIndex].uploadData.status = 'Uploaded';
        this.filesAndData[fileIndex].uploadData.progress = 100;
        this.filesAndData[fileIndex].fileData = {
          nuid,
          file_size: file.size,
        };
        await this.saveProcess(fileIndex);
      } else {
        this.filesAndData[fileIndex].uploadData.status = 'Upload Failed';
        this.filesAndData[fileIndex].uploadData.progress = 0;
        this.$store.dispatch({ type: 'getAccesstokenRefresh' });
      }

      return;
    },

    async uploadingFiles(chunk, chunkFile, ground_uuid, fileIndex, chunkLength) {
      var data = new FormData();
      data.append('profile_id', this.$store.state.currentProgram.id);
      data.append('generate_thumbnail', false);
      data.append('transcode', true);
      data.append('file_order_number', chunk);
      data.append('file_group_uuid', ground_uuid);

      data.append('file', chunkFile);
      data.append('sha1check', await sha1(chunkFile));
      var config = {
        method: 'post',
        url: this.env.FS_URL + 'partial',
        headers: {
          Authorization: 'Bearer ' + this.accesstoken,
        },
        data: data,
        onUploadProgress: (progressEvent) => {
          this.filesAndData[fileIndex].uploadData.progress = (
            (100 * (chunk - 1 + progressEvent.loaded / progressEvent.total)) /
            chunkLength
          ).toFixed(1);
        },
      };
      try {
        this.$store.dispatch('increaseCallCount');
        return await axios(config);
      } catch (e) {
        return 'error';
      }
    },

    async transcoding(fileIndex) {
      var config = {
        method: 'post',
        url: this.env.FS_URL + `transcode`,
        headers: {
          Authorization: 'Bearer ' + this.accesstoken,
        },
        data: {
          profile_id: this.$store.state.currentProgram.id,
          nuid: this.filesAndData[fileIndex].fileData.nuid,
        },
      };

      this.$store.dispatch('increaseCallCount');

      return await axios(config);
    },

    async markForAnnotation(nuid, f_name) {
      var config = {
        method: 'post',
        url: this.env.FS_URL + `pdf/mark-for-annotation`,
        headers: {
          Authorization: 'Bearer ' + this.accesstoken,
        },
        data: {
          profile_id: this.$store.state.currentProgram.id,
          nuid: nuid,
          filename: f_name
        },
      };

      this.$store.dispatch('increaseCallCount');

      return await axios(config);
    },

    onChangeAnnotationStatus(status) {
      this.isErrorAnnotation = status;
    },

    onMandatory(val) {
      console.log(val);
      this.isMandatory = val;
    }
  },
};
</script>
<style scoped lang="scss">
.nde-upload-modal {
  overflow-y: hidden;
}

.loading-content {
  background: white;
}

:v-deep .v-card__text {
  .v-btn__content {
    @extend %fontNormalBold;
    font-size: 0.75rem;
    line-height: 1.5rem;
  }
}

:v-deep .v-card__title {
  .v-icon:hover {
    &::after {
      @extend %afterIconModalClose;
    }
  }

  .v-card__subtitle {
    span {
      @extend %fontNormalBold;
      font-size: 0.875rem;
      line-height: 1.5rem;
      color: #636b6e;
    }
  }
}

.nde-button-cancel {
  color: $whiteColor;
  background-color: $errorColor;
}

.upload-menu {
  margin-top: 12px;
  contain: initial;
  overflow: visible;
}

.upload-menu::before {
  position: absolute;
  content: '';
  top: 0;
  left: 40%;
  transform: translateY(-100%);
  width: 10px;
  height: 13px;
  border-left: 10px solid transparent;
  border-right: 10px solid transparent;
  border-bottom: 13px solid #fff;
}
.doc-preview {
  max-height: 800px;
  overflow-x: hidden;
  overflow-y: auto;
}
@media screen and (max-width: 48rem) {
  .v-btn.v-size--default {
    font-size: 0.75rem !important;
  }
}

@media screen and (max-width: 20rem) {
  .v-btn.v-size--default {
    font-size: 0.625rem !important;
  }
}
</style>
