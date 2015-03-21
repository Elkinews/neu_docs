<template>
  <div class="nde-filter-report-form">
    <nde-form class="nde-form">
      <template v-slot:ndeFormBody>
        <v-row>
          <v-col cols="12" md="4" v-if="enableSelectProgram">
            <label class="mb-1">Select a Program:</label>
            <nde-select
              :items="programs"
              :value="defaultData.program"
              @onSelect="handleSelectProgram"
            />
          </v-col>
          <v-col cols="12" md="4" v-if="enableSelectAction">
            <label class="mb-1">Select a Actions:</label>
            <nde-select
              :items="actions"
              :value="defaultData.action"
              @onSelect="handleSelectAction"
            />
          </v-col>
          <v-col cols="12" md="4" v-if="enableSelectUser">
            <label class="mb-1">Select a User:</label>
            <nde-select :items="users" :value="defaultData.user" @onSelect="handleSelectUser" />
          </v-col>
          <v-col cols="12" md="4" v-if="enableDateBetween">
            <label class="mb-1">{{ typeReport }} between:</label>
            <nde-date-picker range :value="defaultData.range" @onInput="handleChangeDateRange" />
          </v-col>
          <v-col cols="12" md="4" v-if="enableSelectRetentionYear">
            <label class="mb-1">Select Retention Year:</label>
            <nde-select :items="generateArrayOfYears" :value="defaultData.retentionYear" @onSelect="handleSelectRetentionYear" />
          </v-col>
          <v-col cols="12" md="4" v-if="enableIncludeFrozen">
            <v-checkbox
              :value="defaultData.includeFrozen"
              label="Include Frozen Record"
              hide-details
              @onSelect="handleSelectIncludeFrozen"
            ></v-checkbox>
          </v-col>
          <v-col cols="12" align="right">
            <nde-button-primary title="Generate Report" class="mr-2" @click="onGenerateReport" />
            <nde-button-primary title="Queue Report" @click="onQueueReport" />
          </v-col>
        </v-row>
      </template>
    </nde-form>
  </div>
</template>

<script>
import NdeForm from '@components/Form/NdeForm.vue';
import NdeButtonPrimary from '@components/Button/NdeButtonPrimary.vue';
import NdeButtonWarning from '@components/Button/NdeButtonWarning.vue';
import NdeSelect from '@components/Inputs/NdeSelect';
import NdeDatePicker from '@components/DatePicker/NdeDatePicker';
export default {
  name: 'NdeFilterReportForm',
  props: {
    enableSelectProgram: {
      type: Boolean,
      default: () => true,
    },
    enableSelectAction: {
      type: Boolean,
      default: () => true,
    },
    enableSelectUser: {
      type: Boolean,
      default: () => true,
    },
    enableDateBetween: {
      type: Boolean,
      default: () => true,
    },
    enableSelectRetentionYear: {
      type: Boolean,
      default: () => false,
    },
    enableIncludeFrozen: {
      type: Boolean,
      default: () => false,
    },
    typeReport: {
      type: String,
      default: 'Modified',
    },
    programs: {
      type: Array,
      default: () => [],
    },
    users: {
      type: Array,
      default: () => [],
    },
    actions: {
      type: Array,
      default: () => [],
    },
    defaultData: {
      type: Object,
      default: () => ({ program: '', action: '', user: '', range: { from: '', to: '' } }),
    },
  },
  data() {
    return {
      program: this.defaultData.program,
      action: this.defaultData.action,
      user: this.defaultData.user,
      dateRange: this.defaultData.range,
	    retentionYear: this.defaultData.retentionYear,
	    includeFrozen: this.defaultData.includeFrozen,
    };
  },
  computed: {
    dataFilter() {
      let data = {};
      data = this.enableSelectProgram ? (data = { ...data, program: this.program }) : data;
      data = this.enableSelectUser ? (data = { ...data, user: this.user }) : data;
      data = this.enableSelectAction ? (data = { ...data, action: this.action }) : data;
      data = this.enableDateBetween ? (data = { ...data, dateRange: this.dateRange }) : data;
	    data = this.enableSelectRetentionYear ? (data = { ...data, retentionYear: this.retentionYear }) : data;
	    data = this.enableIncludeFrozen ? (data = { ...data, includeFrozen: this.includeFrozen }) : data;

      return data;
    },
    generateArrayOfYears() {
      const max = new Date().getFullYear();
      const min = max - 10;
      const years = []

      for (let i = max; i >= min; i--) {
        years.push(i)
      }
      return years
    }
  },
  methods: {
    handleSelectProgram(value) {
      this.program = value;
    },
    handleSelectAction(value) {
      this.action = value;
    },
    handleSelectUser(value) {
      this.user = value;
    },
    handleChangeDateRange(range) {
      this.dateRange = range;
    },
    handleSelectRetentionYear(retentionYear) {
      this.retentionYear = retentionYear;
    },
    handleSelectIncludeFrozen(includeFrozen) {
      this.includeFrozen = includeFrozen;
    },
    onGenerateReport() {
      this.$emit('onGenerateReport', this.dataFilter);
    },
    onQueueReport() {
      this.$emit('onQueueReport', this.dataFilter);
    },
  },
  components: {
    NdeDatePicker,
    NdeSelect,
    NdeForm,
    NdeButtonPrimary,
    NdeButtonWarning,
  },
};
</script>
<style scoped lang="scss">
.nde-filter-report-form {
  .nde-form {
    background-color: $whiteColor;
  }
}
</style>
