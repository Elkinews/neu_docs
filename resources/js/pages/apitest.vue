<template>
  <div>
    <v-row>
      <v-col>
        <nde-button color="success" @click="deletionReport">deletionReport</nde-button>
      </v-col>
      <v-col>
        <nde-button color="success" @click="scannerUsageReport">scannerUsageReport</nde-button>
      </v-col>
      <v-col>
        <nde-button color="success" @click="recordMetadataReport">recordMetadataReport</nde-button>
      </v-col>
      <v-col>
        <nde-button color="success" @click="userDocumentHistoryReport"
          >userDocumentHistoryReport</nde-button
        >
      </v-col>
      <v-col>
        <nde-button color="success" @click="loginTrackingReport">loginTrackingReport</nde-button>
      </v-col>
      <v-col>
        <nde-button color="success" @click="getScanModalInfo">getScanModalInfo</nde-button>
      </v-col>

      <v-col>
        <nde-button color="success" @click="getDocHistoryOauth">getDocHistoryOauth</nde-button>
      </v-col>
      <v-col>
        <nde-button color="success" @click="checkDatafeedCompletionOauth">checkDatafeedCompletionOauth</nde-button>
      </v-col>
      <v-col>
        <nde-button color="success" @click="customOnDemandReportOauth">customOnDemandReportOauth</nde-button>
      </v-col>
      <v-col>
        <nde-button color="success" @click="getSearchHistory">Search History</nde-button>
      </v-col>

      <v-col>
        <nde-button color="success" @click="getMfaOauth">Get MFA</nde-button>
      </v-col>

      <v-col>
        <nde-button color="success" @click="forgotPasswordUserOauth">Forgot Password User Oauth</nde-button>
      </v-col>

      <v-col>
        <nde-button color="success" @click="getWatchlist">Watchlist</nde-button>
      </v-col>

      <v-col>
        <nde-button color="success" @click="getModificationReportOauth">Modification Report Oauth</nde-button>
      </v-col>

      <v-col>
        <nde-button color="success" @click="getControls">Get Controls Oauth</nde-button>
      </v-col>

      <v-col>
        <nde-button color="success" @click="getDownloads">Get Downloads</nde-button>
      </v-col>

      <v-col>
        <nde-button color="success" @click="getRetetiomReport">Retetion Report</nde-button>
      </v-col>

      <v-col>
        <nde-button color="success" @click="uploadFileMetadataOauth">Upload File Metadata Oauth</nde-button>
      </v-col>

      <v-col>
        <nde-button color="success" @click="userProfileReportOauth">User Profile Report Oauth</nde-button>
      </v-col>

      <v-col>
        <nde-button color="success" @click="defaultSetting">Default Setting</nde-button>
      </v-col>

      <v-col>
        <nde-button color="success" @click="getEmailIntake">Get EmailIntake</nde-button>
      </v-col>

      <v-col>
        <nde-button color="success" @click="saveEmailIntake">Save EmailIntake</nde-button>
      </v-col>

      <v-col>
        <nde-button color="success" @click="getPiecesByDocIdOauth">GetPiecesByDocIdOauth</nde-button>
      </v-col>

      <v-col>
        <nde-button color="success" @click="getWorkflowProfilesOauth">GetWorkflowProfilesOauth</nde-button>
      </v-col>

    </v-row>
    <v-row>
      <v-col>
        <v-file-input
          label="Select doc"
          v-model="fileForUpload"
        ></v-file-input>
      </v-col>
      <v-col>
        <nde-button color="success" @click="uploadFile">Upload File Partially</nde-button>
      </v-col>
    </v-row>

    <v-row class="mt-6">
      <v-col>
        <v-file-input
          label="Select a File"
          v-model="fileForSaveTemp"
        ></v-file-input>
      </v-col>
      <v-col>
        <nde-button color="success" @click="saveTempFileOauth" :loading="isUploading">Save Temp File</nde-button>
      </v-col>
    </v-row>
    <p class="red--text text--darken-3" v-if="fileForSaveTempError">{{fileForSaveTempError}}</p>

  </div>
</template>
<script>
import sha1 from 'sha1-file-web';
import { mapState } from 'vuex';
import axios from 'axios';
import NdeButton from '../components/Button/NdeButton.vue';
import NdeDashboardLayoutVue from '../shared/layouts/dashboard/NdeDashboardLayout.vue';

export default {
  layout: NdeDashboardLayoutVue,
  components: {
    NdeButton,
  },
  data() {
    return {
      fileForUpload: null,
      fileForSaveTemp: null,
      fileForSaveTempError: '',
      isUploading: false,
      group_uuid: '',
    }
  },

  computed: {
    ...mapState([
      'accesstoken',
    ]),
  },
  methods: {
    getPiecesByDocIdOauth() {
      axios
        .post('/getPiecesByDocIdOauth', {
          profileId: 1,
          docId: 'TXl6MzZmV3loUHpkZnNVR3dOV2Vwdz09X19CgsSHcpRwKOtvXB9rZHpK',
        })
        .then((response) => {
          console.log(response);
        })
        .catch((error) => {
          console.log(error);
        });
    },

    deletionReport() {
      this.$store.commit('setCustomSearchPostData', {
        type: 'deletionReportOauth',
        data: {
          profile: 1,
          orderBy: 'username asc',
          offset: 0,
          limit: 10,
          from_date: '03/21/2022',
          to_date: '04/19/2022',
          user: 0,
        },
      });

      this.$store.dispatch({ type: 'generateCustomReport' });
    },

    scannerUsageReport() {
      this.$store.commit('setCustomSearchPostData', {
        type: 'scannerUsageReportOauth',
        data: {
          profile: 1,
          orderBy: 'scanner asc',
          offset: 0,
          limit: 10,
          from_date: '01/14/2020',
          to_date: '01/12/2021',
          user: 0,
        },
      });

      this.$store.dispatch({ type: 'generateCustomReport' });
    },

    recordMetadataReport() {
      this.$store.commit('setCustomSearchPostData', {
        type: 'recordMetadataReportOauth',
        data: {
          profile: 1,
          orderBy: 'profile_name asc',
          offset: 0,
          limit: 10,
          from_date: '01/14/2020',
          to_date: '01/12/2021',
        },
      });

      this.$store.dispatch({ type: 'generateCustomReport' });
    },

    userDocumentHistoryReport() {
      this.$store.commit('setCustomSearchPostData', {
        type: 'userDocumentHistoryReportOauth',
        data: {
          profile: 1,
          orderBy: 'username asc',
          offset: 0,
          limit: 10,
          from_date: '01/14/2020',
          to_date: '01/12/2021',
          user: 375,
          activity: 'E_UPLOAD',
        },
      });

      this.$store.dispatch({ type: 'generateCustomReport' });
    },

    loginTrackingReport() {
      this.$store.commit('setCustomSearchPostData', {
        type: 'loginTrackingReportOauth',
        data: {
          profile: 1,
          orderBy: 'username asc',
          offset: 0,
          limit: 10,
          from_date: '01/14/2020',
          to_date: '01/12/2021',
          user: 0,
        },
      });

      this.$store.dispatch({ type: 'generateCustomReport' });
    },

    getScanModalInfo() {
      axios
        .post('/getScanModalInfo', {
          profileId: 1,
          imageId: 'L01QOENaMXhUenRMVThpY1FaZSttZz09X1-cKPR4fX47_WnxWPNezjJu',
        })
        .then((response) => {})
        .catch((error) => {
          console.log(error);
        });
    },

    getDocHistoryOauth() {
      axios
        .post('/getDocHistoryOauth', {
          profile_id: 1,
          order_by: 'activity asc',
          from_date: '2020-12-30',
          to_date: '01/12/2021',
        })
        .then((response) => {})
        .catch((error) => {
          console.log(error);
        });
    },

    checkDatafeedCompletionOauth() {
      axios
        .post('/checkDatafeedCompletionOauth', {
          target_profile: 406,
          source_table: 1,
          fields: {
            number: 64
          }
        })
        .then((response) => {})
        .catch((error) => {
          console.log(error);
        });
    },

    customOnDemandReportOauth() {
      this.$store.commit('setCustomSearchPostData', {
        type: 'customOnDemandReportOauth',
        data: {
          profile: 1,
          orderBy: 'username asc',
          offset: 0,
          limit: 10,
        },
      });

      this.$store.dispatch({ type: 'generateCustomReport' });
    },

    getSearchHistory() {
      axios
        .post('/getSearchHistoryOauth', {
          profileId: 1,
        })
        .then((response) => {})
          .catch((error) => {
            console.log(error);
          })
    },

    getWatchlist() {
      axios
        .post('/getWatchlistOauth', {
          profileId: 1,
          orderBy: 'created_on',
          orderDir: 'asc'
        })
        .then((response) => {})
          .catch((error) => {
            console.log(error);
          })
    },

    getModificationReportOauth() {
      axios
        .post('/getModificationReportOauth', {
          profile: "1",
          orderBy: "username asc",
          offset: 0,
          limit: 10,
          from_date: '04/04/2022',
          to_date: '05/03/2022',
          user: "0"
        })
        .then((response) => {
          console.log(response)
        })
        .catch((error) => {
          console.log(error);
        });
    },

    getMfaOauth() {
      axios
        .post('/getMfaOauth', {
          mfa_token: '89241722115012419691'
        })
        .then((response) => {
          console.log(response)
        })
        .catch((error) => {
          console.log(error);
        });
    },

    forgotPasswordUserOauth() {
      axios
        .post('/forgotPasswordUserOauth', {
          username: 'api'
        })
        .then((response) => {
          console.log(response)
        })
        .catch((error) => {
          console.log(error);
        });
    },

    getControls() {
      axios
        .post('/getControls', {
          profile: 1,
          targetProfileID: 1
        })
        .then((response) => {
          console.log(response)
        })
        .catch((error) => {
          console.log(error);
        });
    },

    getDownloads() {
      axios
        .post('/getDownloads', {
          profile: 1,
          offset: 0,
          limit: 10
        })
        .then((response) => {
          console.log(response)
        })
        .catch((error) => {
          console.log(error);
        });
    },

    uploadFileMetadataOauth() {
      axios
        .post('/uploadFileMetadataOauth', {
          json: {
            profile: 1,
            docId: 'TXl6MzZmV3loUHpkZnNVR3dOV2Vwdz09X19CgsSHcpRwKOtvXB9rZHpK',
            boxType: 'B',
            nuid: '123e4567-e89b-12d3-a456-426614174000',
            filePath: "/image/production/staging/scannedimages/ab.pdf"
          }
        })
        .then((response) => {
          console.log(response)
        })
        .catch((error) => {
          console.log(error);
        });
    },

    getRetetiomReport(){
      axios
        .post('/retentionReportOauth', {
          profile: 1,
          from_date: '2020-01-13',
          to_date: '2021-01-12',
          retention_year: 2020,
          include_frozen: false,
          page: 1,
          page_size: 20,
          // order_by: 'username ASC',
        })
        .then((response) => {
          console.log(response)
        })
        .catch((error) => {
          console.log(error);
        });
    },

    userProfileReportOauth() {
      axios
        .post('/userProfileReportOauth', {
          "profile": 1,
          "orderBy": "username asc",
          "offset": 0,
          "limit": 10,
          "from_date": "08-01-2021",
          "to_date": "05-08-2022",
          "user": 1110,
          "active": 1
        })
        .then((response) => {
          console.log(response)
        })
        .catch((error) => {
          console.log(error);
        });
    },

    defaultSetting(){
      axios
        .post('/saveDefaultSettingsOauth', {
          "defaultSettings": {
              "view_doc":"LOW",
              "create_doc_data_feed":"ON",
              "defaultprofile_id":"8",
              "promote_view":"OFF",
              "download_batestamp":"ON",
              "send_email":"ON",
              "multitask":"OFF",
              "view_doc_auto_zoom":"Normal",
              "default_image_group_id":"1",
              "view_scrolling":"ON"}
        })
        .then((response) => {
          console.log(response)
        })
        .catch((error) => {
          console.log(error);
        });
    },

    createChuncks(file , cSize, fileType, fileExtension){
      console.log("type type: ", fileType)
      let startPointer = 0;
      let endPointer = file.size;
      let chunks = [];
      while(startPointer<endPointer){
        let newStartPointer = startPointer+cSize;
        chunks.push(file.slice(startPointer,newStartPointer));
        //chunks.push(file.slice(startPointer,newStartPointer, 'video/mp4'));
        startPointer = newStartPointer;
      }
      const chunkingFile = chunks.map((chunkFile, index) => {
        let file = new File([chunkFile], "chuck-file-" + index+ "." + fileExtension, {type: fileType})
        console.log("chunking file: ", file)
        return file
      })
      return chunkingFile;
      },

    async uploadFile() {
      const fileType = this.fileForUpload.type;
      const fileExtension = this.fileForUpload.name.split('.').pop();
      const chunkSize = 1024 * 1024 * 50;
      const chunkFiles = await this.createChuncks(this.fileForUpload, chunkSize, fileType, fileExtension)

      var chunk = 1;
      var index = 0;

      while(chunk <= chunkFiles.length) {
        console.log("ground_uuid: ", ground_uuid)
        console.log("chunk number: ", chunk)
        console.log("chunk: ", chunkFiles[index])
        if( chunk === 1){
             var ground_uuid = ''
          }
        if(chunk === chunkFiles.length){
          chunk = -1;
        }

        var response = await this.uploadingFiles(chunk, chunkFiles[index], ground_uuid)
        console.log("main response: ", response)
        if(response?.data){
          ground_uuid = response.data.file_group_uuid
        }
        if(chunk == -1){
          console.log("finish uploading")
          break
        }
        chunk++;
        index++;
      }
    },


    async uploadingFiles(chunk, chunkFile, ground_uuid){
      var data = new FormData();
          data.append('profile_id', 1);
          data.append('generate_thumbnail', false);
          data.append('transcode', true);
          data.append('file_order_number', chunk);
          data.append('file_group_uuid', ground_uuid);

          data.append('file', chunkFile);
          data.append('sha1check', await sha1(chunkFile));
          var config = {
          method: 'post',
          url: 'https://fssandbox.sima.io/api/v1/partial',
          headers: {
            Authorization: 'Bearer ' + this.accesstoken,
          },
          data: data,
        }
        return await axios(config)
    },

    getEmailIntake(){
      axios
        .post('/getEmailIntakeSettingsOauth', {})
        .then((response) => {
          console.log(response)
        })
        .catch((error) => {
          console.log(error);
        });
    },

    saveEmailIntake(){
      axios
        .post('/saveEmailIntakeOauth', {
          "emailSetting": "imap://test_email:password@imap.example.test:100",
          "format": "PDF"
        })
        .then((response) => {
          console.log(response)
        })
        .catch((error) => {
          console.log(error);
        });
    },

    async saveTempFileOauth() {
      if(!this.fileForSaveTemp) {
        alert('Please select a file');
        return;
      }

      console.log(this.fileForSaveTemp)
      try {
        const tokenData = await axios.get('/getAccessToken');

        const token = 'kvH1nd4QksIAtmvshrqZW2tutP5IKLHyKlifClxW';//tokenData.data.data.token;
        console.log(token)
        var data = new FormData();
        data.append('profile_id', '1');
        data.append('file', this.fileForSaveTemp);
        var config = {
          method: 'post',
          url: 'https://qa10.neubus.com/esd3.9.4/api.php?function=SaveTempFileOauth',
          headers: {
            'Authorization': 'Bearer ' + token
          },
          data : data
        };
        this.isUploading = true;

        axios(config)
        .then( (response) => {
          console.log(response);
          this.isUploading = false;
        })
        .catch( (error) => {
          console.log(error);
          this.isUploading = false;
        });

      } catch(e) {
        console.log('error')
        console.log(e.response.data.data)
        this.fileForSaveTempError = e.response.data.data.error;
        this.isUploading = false;
      }



    },

    getWorkflowProfilesOauth() {
      axios
        .get('/getWorkflowProfilesOauth')
        .then((response) => {
          console.log(response)
        })
        .catch((error) => {
          console.log(error);
        });
    }
  }
};
</script>
