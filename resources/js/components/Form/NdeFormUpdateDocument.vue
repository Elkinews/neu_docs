<template>
  <nde-form>
    <template v-slot:ndeFormBody>
      <v-row
        align="center"
        justify="center"
        class="form-upload-document"
        @dragover="dragover"
        @drop="drop"
      >
        <v-col style="cursor: pointer">
          <input
            type="file"
            :multiple="!isModalUploadDocumentForReplace"
            id="assetsFieldHandle"
            style="display: none"
            @change="onChange"
            ref="file"
            accept=".pdf,.jpg,.jpeg,.png,.pdf,.mp4,.doc,.docx"
          />
          <label for="assetsFieldHandle" class="block">
            <div>
              <v-avatar size="40">
                <img :src="'images/Upload_File.png'" alt="Upload file" />
              </v-avatar>
              <span>
                Drag & Drop {{ isModalUploadDocumentForReplace ? 'file' : 'files or folders' }} here
              </span>
            </div>
          </label>
        </v-col>
      </v-row>
    </template>
  </nde-form>
</template>

<script>
import { mapState } from 'vuex';
import NdeForm from './NdeForm.vue';

export default {
  name: 'NdeModalUploadDocument',
  components: {
    NdeForm,
  },

  computed: {
    ...mapState(['isModalUploadDocumentForReplace', 'replaceName']),
  },

  props: {
    files: {
      type: Array,
      default: () => [],
    },
    filesAndData: {
      type: Array,
      default: () => [],
    },
  },

  methods: {
    addFileToFileList(file) {
      this.files.push(file);
      this.filesAndData.push({
        file: file,
        uploadData: {
          progress: 0,
          status: '',
        },
        fileData: null,
      });
    },

    addInputFile(files) {
      if (this.isModalUploadDocumentForReplace) {
        this.replaceFile(files);
      } else {
        for (let i = 0; i < files.length; i++) {
          let isExist = false;
          let file = files[i];
          this.files.map((item) => {
            if (file.name == item.name) {
              isExist = true;
            }
          });

          if (!isExist) {
            this.addFileToFileList(files[i]);
          }
        }
        this.$refs.file.value = '';
        this.$emit('onChangeFiles');
      }
    },

    async replaceFile(files){
      let filesData = [];
      let isSame = true;
      for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const replaceExt = this.replaceName.split('.').pop();
        const fileExt = file.name.split('.').pop();
        if (fileExt != replaceExt) {
          isSame = false;
          await this.$swal({
            title: 'Warnning',
            text: 'Please select another file with same extention.',
            type: 'error',
            showCancelButton: false,
            confirmButtonText: 'Ok',
          });
        } else {
          filesData.push({
            file,
            uploadData: {
              progress: 0,
              status: '',
            },
            fileData: null,
          });
        }
      }
      if (isSame) {
        this.$emit('onUpdateFileForReplace', {
          files: [...files],
          filesAndData: filesData,
        });
      }
    },

    onChange(){
      this.addInputFile(this.$refs.file.files);
    },

    dragover(event) {
      event.preventDefault();
    },

    drop(event) {
      event.preventDefault();
      const items = event.dataTransfer.items;
      let files = [];
      for (let i = 0; i < items.length; i++) {
        const item = items[i].webkitGetAsEntry();
        if (item) {
          if (item.isFile) {
            // Get file
            item.file((file) => {
              files.push(file)
            });
          } else if (item.isDirectory) {
            // Get folder contents  
            const dirReader = item.createReader();
            dirReader.readEntries((entries) => {
              for (let i = 0; i < entries.length; i++) {
                if (entries[i].isFile) {
                  // Get file
                  entries[i].file((file) => {
                    files.push(file)
                  });
                }
              }
            });
          }
        }
      }
      
      setTimeout(() => {
        this.addInputFile(files);
      }, 500);

    }
  },
};
</script>
<style scoped lang="scss">
.form-upload-document {
  background: #f7f7f7;
  min-height: 16rem;
  border: 1px dashed #3d769e;
  box-sizing: border-box;
  border-radius: 8px;
  text-align: center;
  * {
    cursor: pointer;
  }
  span {
    @extend %fontNormal;
    color: #636b6e;
    font-size: 0.813rem;
    line-height: 1.5rem;
    display: block;
    &.underline {
      text-decoration-line: underline;
      color: #3d769e;
      font-weight: 500;
    }
  }
}
</style>
