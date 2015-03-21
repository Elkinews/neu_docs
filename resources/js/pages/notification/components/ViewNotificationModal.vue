<template>
    <nde-modal
      :showModal="isShow"
      title="Notification"
      persistent
      max-width="800"
      class="view-notification-modal"
      @update:closeModal="closeModal"
    >
      <template slot="ndeModalContent">
        <div class="view-notification-modal__content">
            <v-row>
                <v-col cols="12" md="3">
                    <label class="font-weight-bold">Subject</label>
                </v-col>
                <v-col cols="12"  md="9">
                    {{data.subject}}
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12" md="3">
                    <label class="font-weight-bold">Type</label>
                </v-col>
                <v-col cols="12"  md="9">
                    {{data.type}}
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12" md="3">
                    <label class="font-weight-bold">Date Created</label>
                </v-col>
                <v-col cols="12"  md="9">
                    {{ formatDate(data.created_date) }}
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="12">
                    <label class="font-weight-bold">Body</label>
                </v-col>
                <v-col cols="12">
                    <v-card class="pa-5">
                        <span v-html="data.body"></span>
                    </v-card>
                </v-col>
            </v-row>
        </div>
      </template>
  
      <template slot="ndeModalAction">
        <v-row>
          <v-col cols="12" class="d-flex justify-end" align="right">
            <nde-button
              color="primary"
              @click="closeModal"
            >
              Close
            </nde-button>
          </v-col>
        </v-row>
      </template>
    </nde-modal>
  </template>
  
  <script>
  import moment from 'moment';
  import NdeModal from '@components/Modal/NdeModal.vue';
  import NdeButton from '@components/Button/NdeButton.vue';
  export default {
  
    data() {
      return {};
    },
  
    props: {
      isShow: {
        type: Boolean,
        default: () => false,
      },
      data: {
        type: Object,
        default: () => [],
      },
    },
  
    computed: {},
  
    components: {
      NdeModal,
      NdeButton,
    },
    created() {},
    mounted() {},
    watch: {},

    methods: {
      closeModal() {
        this.$emit('closeModal');
      },
      formatDate(date) {
        const format = "YYYY-MM-DD hh:mm A";
        return moment(date).format(format)
      }
    },
  };
  </script>
