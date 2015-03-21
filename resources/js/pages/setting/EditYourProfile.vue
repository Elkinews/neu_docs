<template>
  <div role="main">
    <nde-tab-setting />
    <v-app class="mt-5">
      <v-card class="pa-5">
        <v-card-title>
          <h3 role="heading">Edit your profile</h3>
        </v-card-title>
        <v-divider></v-divider>
        <v-card-text>
          <v-row>
            <v-col sm="8" cols="12">
              <v-row>
                <v-col cols="5">
                  <v-subheader>User Name</v-subheader>
                </v-col>
                <v-col cols="7">
                  <v-text-field :value="username" label="User Name" solo disabled  aria-label="Username"></v-text-field>
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="5">
                  <v-subheader>Full Name</v-subheader>
                </v-col>
                <v-col cols="7">
                  <v-text-field :value="userfullname" label="Full Name" solo disabled aria-label="Full Name"></v-text-field>
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="5">
                  <v-subheader>Email</v-subheader>
                </v-col>
                <v-col cols="7">
                  <v-text-field :value="useremail" label="Email" solo disabled aria-label="Email"></v-text-field>
                </v-col>
              </v-row>
            </v-col>
            <v-col sm="4" cols="12">
              <v-row>
                <v-col class="d-flex flex-column align-center">
                  <v-img
                    max-height="300"
                    max-width="300"
                    height="300"
                    width="300"
                    :src="getSrc"
                    class="mb-6"
                  >
                  </v-img>
                  <input
                    ref="input"
                    type="file"
                    name="image"
                    accept="image/*"
                    @change="setImage"
                    class="nde-user-profile-img-input"
                  />
                  <nde-button-primary @click="onClickUploadBTN" title="Upload Photo">
                  </nde-button-primary>
                  <v-dialog v-model="dialog" persistent max-width="500">
                    <v-card>
                      <v-card-title class="text-h5"> Edit Photo </v-card-title>
                      <v-card-text>
                        <vue-cropper ref="cropper" :src="imgSrc" preview=".preview" />
                      </v-card-text>
                      <v-card-actions>
                        <v-progress-linear
                          :value="upload_progress"
                          v-if="isUploading"
                        ></v-progress-linear>
                        <v-spacer></v-spacer>
                        <v-btn
                          color="red darken-1"
                          text
                          @click="dialog = false"
                          :disabled="isUploading"
                        >
                          Cancel
                        </v-btn>
                        <v-btn
                          color="green darken-1"
                          text
                          @click="onHandleUpload"
                          :loading="isUploading"
                        >
                          Upload
                        </v-btn>
                      </v-card-actions>
                    </v-card>
                  </v-dialog>
                </v-col>
              </v-row>
            </v-col>
          </v-row>
        </v-card-text>
        <nde-alert-success v-if="status == 'success'" :message="message" />

        <action-button @onSave="onSave" @onCancel="onCancel" :disabled="true" />
      </v-card>
    </v-app>
  </div>
</template>

<script>
import VueCropper from 'vue-cropperjs';
import 'cropperjs/dist/cropper.css';
import { mapState } from 'vuex';
import sha1 from 'sha1-file-web';

import NdeTabSetting from '../../components/Tabs/NdeTabSetting.vue';
import NdeDashboardLayout from '../../shared/layouts/dashboard/NdeDashboardLayout.vue';
import NdeButtonPrimary from '../../components/Button/NdeButtonPrimary.vue';
import NdeAlertSuccess from '../../components/Alert/NdeAlertSuccess.vue';
import ActionButton from './ActionButton.vue';

export default {
  layout: NdeDashboardLayout,
  components: {
    NdeTabSetting,
    NdeButtonPrimary,
    NdeAlertSuccess,
    VueCropper,
    ActionButton,
  },
  data() {
    return {
      status: '',
      message: '',
      nuid: '',
      dialog: false,
      imgSrc: '',
      cropImg: '',
      upload_progress: 0,
      isUploading: false,
      file: null,
      username: '',
      userfullname: '',
      useremail: ''
    };
  },

  computed: {
    ...mapState([
      'accesstoken',
      'currentProgram',
      'currentImageId',
      'env',
      'user_id',
      'profile_nuid',
      'profile_avatar_link',
    ]),
    getSrc() {
      if (this.profile_avatar_link) {
        return this.profile_avatar_link;
      } else {
        return '/images/default-avatar.png';
      }
    },
  },

  mounted() {
    this.username = this.getDataCookieRole().username;
    this.userfullname = this.getDataCookieRole().full_name;
    this.useremail = this.getDataCookieRole().email;
  },

  methods: {
    onSave() {},
    onCancel() {},
    onClickUploadBTN() {
      this.$refs.input.value = null;
      this.$refs.input.click();
    },

    async setImage(e) {
      this.file = e.target.files[0];
      if (this.file.type.indexOf('image/') === -1) {
        await this.$swal({
          text: 'Please select an image file!',
          type: 'warning',
          showCancelButton: false,
          confirmButtonText: 'OK',
        });
        return;
      }
      if (typeof FileReader === 'function') {
        const reader = new FileReader();
        reader.onload = (event) => {
          this.dialog = true;
          this.imgSrc = event.target.result;
          // rebuild cropperjs with the updated source
          this.$refs.cropper.replace(event.target.result);
        };
        reader.readAsDataURL(this.file);
      } else {
        await this.$swal({
          text: 'Sorry, FileReader API not supported! Please try to use another web browser!',
          type: 'warning',
          showCancelButton: false,
          confirmButtonText: 'OK',
        });
      }
    },

    dataURLtoFile(dataurl, filename) {
      var arr = dataurl.split(','),
        mime = arr[0].match(/:(.*?);/)[1],
        bstr = atob(arr[1]),
        n = bstr.length,
        u8arr = new Uint8Array(n);

      while (n--) {
        u8arr[n] = bstr.charCodeAt(n);
      }

      return new File([u8arr], filename, { type: mime });
    },

    async onHandleUpload() {
      this.cropImg = this.$refs.cropper.getCroppedCanvas().toDataURL();
      let uploadFile = this.dataURLtoFile(this.cropImg, this.file.name);
      var data = new FormData();
      data.append('profile_id', this.currentProgram.id || 1);
      data.append('generate_thumbnail', true);
      data.append('transcode', true);
      data.append('file', uploadFile);
      data.append('sha1check', await sha1(uploadFile));
      var config = {
        method: 'post',
        url: this.env.FS_URL + 'single',
        headers: {
          Authorization: 'Bearer ' + this.accesstoken,
        },
        data: data,
        onUploadProgress: (progressEvent) => {
          this.upload_progress = Math.round((progressEvent.loaded * 100) / progressEvent.total);
        },
      };

      try {
        this.isUploading = true;
        this.$store.dispatch('increaseCallCount');
        const responseUpload = await axios(config);

        if (responseUpload.status == 201) {
          this.upload_progress = 100;
          this.nuid = responseUpload.data.nuid;
          await this.uploadAvatar();
        } else {
          this.isUploading = false;
          console.log(error);
          await this.$swal({
            text: 'Upload error. Please try again!',
            type: 'warning',
            showCancelButton: false,
            confirmButtonText: 'OK',
          });
        }
      } catch (error) {
        this.isUploading = false;
        console.log(error);
        await this.$swal({
          text: 'Upload error. Please try again!',
          type: 'warning',
          showCancelButton: false,
          confirmButtonText: 'OK',
        });
      }
    },

    async uploadAvatar() {
      const resultAvatar = await this.$store.dispatch('callAPI', {
        url: '/uploadAvatarOauth',
        method: 'post',
        data: {
          user_id: this.user_id,
          nuid: this.nuid,
        },
      });

      if (resultAvatar.message == 'success') {
        this.isUploading = false;

        var config = {
          method: 'delete',
          url: this.env.FS_URL + `single/${this.profile_nuid}`,
          headers: {
            Authorization: 'Bearer ' + this.accesstoken,
          },
        };
        this.$store.dispatch('increaseCallCount');

        await axios(config)
          .then((response) => {
            console.log(response);
          })
          .catch((error) => {
            console.log(error);
          });

        this.$store.dispatch({ type: 'downloadAvatarForce' });

        await this.$swal({
          text: 'Uploaded successfully!',
          type: 'success',
          showCancelButton: false,
          confirmButtonText: 'OK',
        });

        this.dialog = false;
      } else {
        await this.$swal({
          text: 'Upload error. Please try again!',
          type: 'warning',
          showCancelButton: false,
          confirmButtonText: 'OK',
        });
      }
    },
  },

  watch: {
    userLoginFullName: function(val) {
      this.userfullname = val;
    },
    userLoginEmail: function(val) {
      this.useremail = val;
    },
    userLoginUserName: function(val) {
      this.username = val;
    }
  }
};
</script>

<style>
.nde-user-profile-img-input {
  display: none;
}
</style>
