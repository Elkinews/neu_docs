<template>
  <div class="d-md-flex justify-space-around align-center options-record">
    <v-row class="mt-1 mb-1">
      <v-col cols="12" md="4">
        <p class="mt-2 ml-2">Scanned/Uploaded When:</p>
      </v-col>
      <v-col cols="12" md="4">
        <v-menu
          class="ma-2 d-flex align-center"
          v-model="recordCreationDateFromVal"
          :close-on-content-click="true"
          transition="scale-transition"
          max-width="290px"
          min-width="auto"
          attach
          left
          nudge-bottom="40"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-text-field
              v-model="displayStartDate"
              label="From"
              outlined
              persistent-hint
              append-icon="mdi-calendar-text"
              readonly
              v-bind="attrs"
              v-on="on"
              solo
              dense
              hide-details
            ></v-text-field>
          </template>
          <v-date-picker v-model="startDate" no-title @input="recordCreationDateFromVal = false" :max="endDate"></v-date-picker>
        </v-menu>
      </v-col>
      <v-col cols="12" md="4">
        <v-menu
          class="nde-date ma-2"
          v-model="recordCreationDateToVal"
          :close-on-content-click="true"
          transition="scale-transition"
          max-width="290px"
          min-width="auto"
          attach
          left
          nudge-bottom="40"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-text-field
              v-model="displayEndDate"
              outlined
              label="To"
              persistent-hint
              append-icon="mdi-calendar-text"
              readonly
              v-bind="attrs"
              v-on="on"
              solo
              dense
              hide-details
            ></v-text-field>
          </template>
          <v-date-picker v-model="endDate" no-title @input="recordCreationDateToVal = false" :min="startDate"></v-date-picker>
        </v-menu>
      </v-col>
    </v-row>
  </div>
</template>
<script>
import * as date from 'date.js';
import * as moment from 'moment';
import NdeDatePicker from '../DatePicker/NdeDatePicker.vue'

export default {
  name: "NdeTableScanUploadHistoryDate",
  props: {

  },
  components: {
    NdeDatePicker
  },
  data() {
	  const start = date('7 days ago');
    const end = date();
    return {
      recordCreationDateFromVal: false,
      recordCreationDateToVal: false,
      startDate: moment(start).format('YYYY-MM-DD'),
      endDate:  moment(end).format('YYYY-MM-DD'),
      displayStartDate: moment(start).format('MM-DD-YYYY'),
      displayEndDate: moment(end).format('MM-DD-YYYY'),
    };
  },
  methods: {},
  mounted() {
    this.$store.commit("setDocHistoryPostData", {key: 'from_date', data: this.startDate});
    this.$store.commit("setDocHistoryPostData", {key: 'to_date', data: this.endDate});
    this.$store.commit("getDocHistoryOauth");
  },
  watch: {
    startDate: function(val) {
      this.startDate = val
      this.$store.commit("setDocHistoryPostData", {key: 'from_date', data: val});
      this.displayStartDate = moment(val).format('MM-DD-YYYY')

      if(this.endDate) {
		    this.$store.commit("setDocHistoryPostData", {key: 'page', data: 1});
        this.$store.commit("getDocHistoryOauth");
      }
    },
    endDate: function(val) {
      this.endDate = val
      this.$store.commit("setDocHistoryPostData", {key: 'to_date', data: val});
      this.displayEndDate = moment(val).format('MM-DD-YYYY')

      if(this.startDate) {
        this.$store.commit("setDocHistoryPostData", {key: 'page', data: 1});
        this.$store.commit("getDocHistoryOauth");
      }
    }
  }
};
</script>
<style lang="scss" scoped>
.options-record {
  margin-bottom: -1.5rem;
}

</style>
