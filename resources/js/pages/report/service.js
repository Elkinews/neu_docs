import axios from 'axios';
import { ExportToCsv } from 'export-to-csv';

const MOCK_CONTROL = {
  users: [
    { value: 0, text: 'All' },
    { value: 375, text: 'ThienPL' },
    { value: 2, text: 'KyLe' },
    { value: 3, text: 'LyHuynh' },
    { value: 4, text: 'Roon' },
  ],
  actions: [
    { value: 0, text: 'All' },
    { value: 'CREATE_RECORD', text: 'Created' },
    { value: 'SCAN_UPLOAD', text: 'Scanned' },
    { value: 'E_UPLOAD', text: 'Uploaded' },
    { value: 'PRE_STAGING_UPLOAD', text: 'Prestaged' },
    { value: 'SCANANDINDEX_PART_UPLOAD', text: 'Scan and Indexed' },
    { value: 'BULKSCAN_PART_UPLOAD', text: 'Bulkscanned' },
  ],
};
export const getDeletionData = (data) =>
  axios
    .post('/deletionReportOauth', data)
    .then((res) => res.data)
    .catch((err) => console.log(err));

export const getReportControls = (params) =>
  axios
    .get('/getReportControlsOauth', { params })
    .then((res) => res.data)
    .catch((err) => console.log(err));

export const getUserDocumentHistoryReport = (data) =>
  axios.post('/userDocumentHistoryReportOauth', data);

export const getMetaDataReport = (data) =>
  axios
    .post('/recordMetadataReportOauth', data)
    .then((res) => res.data)
    .catch((err) => console.log(err));

export const getLoginTrackingReport = (data) =>
  axios
    .post('/loginTrackingReportOauth', data)
    .then((res) => res.data)
    .catch((err) => console.log(err));

export const getScannerUsageData = (data) =>
  axios
    .post('/scannerUsageReportOauth', data)
    .then((res) => res.data)
    .catch((err) => console.log(err));

export const getRetentionData = (data) =>
  axios
    .post('/retentionReportOauth', data)
    .then((res) => res.data)
    .catch((err) => console.log(err));

export const getModificationData = (data) =>
  axios
    .post('/getModificationReportOauth', data)
    .then((res) => res.data)
    .catch((err) => console.log(err));

export const getUserProfileData = (data) =>
  axios
    .post('/userProfileReportOauth', data)
    .then((res) => res.data)
    .catch((err) => console.log(err));

export const getEventLogData = (data) =>
  axios
    .post('/eventLogReportOauth', data)
    .then((res) => res.data)
    .catch((err) => console.log(err));

export const getNetworkScannerUsageData = (data) =>
axios
  .post('/networkScannerUsageReportOauth', data)
  .then((res) => res.data)
  .catch((err) => console.log(err));


export const exportAllPages = (data) =>
  axios
      .post('/exportToCSV', data)
      .then((res) => res.data)
      .catch((err) => console.log(err));

export const getExportDataFromColumns = (columns) => {
  let exportdata = '';
  columns.map(column => {
    exportdata += ",&quot;" + column.value + '&quot;'
  });
  return exportdata;
}

export const pageExport = (data, columns, CSVdata, page, pageTitle) => {
  data.forEach((item) => {
    var obj = {};
    Object.keys(item).forEach((itemKey) =>{
      columns.forEach((header) => {
        if(itemKey === header.value){
          if(itemKey === 'recInfo'){
            obj[header.text] = (item[itemKey]).split("<br/>").join(" ")
          } else {
            obj[header.text] = item[itemKey]
          }

        }
      })
    })
    CSVdata.push({ obj})
    obj = {};
  })
  var CsvDataFile = []
  CSVdata.forEach((item) => {
    CsvDataFile.push(Object.assign({}, ...Object.values(item)))
  })
  const options = {
    fieldSeparator: ',',
    quoteStrings: '"',
    decimalSeparator: '.',
    showLabels: true,
    showTitle: true,
    title: pageTitle,
    useTextFile: false,
    useBom: true,
    useKeysAsHeaders: true,
    filename: pageTitle + '_' + page
  };
  const csvExporter = new ExportToCsv(options);

  csvExporter.generateCsv(CsvDataFile);
  CsvDataFile = [];
}

export const pageExportNew = (data, columns, CSVdata, page, pageTitle) => {
  data.forEach((dataItem) => {
    var obj = {};
    columns.map((colItem, index) => {
      obj[colItem.text] = dataItem[index];
    })
    CSVdata.push({ obj})
    obj = {};
  })

  var CsvDataFile = []
  CSVdata.forEach((item) => {
    CsvDataFile.push(Object.assign({}, ...Object.values(item)))
  })
  const options = {
    fieldSeparator: ',',
    quoteStrings: '"',
    decimalSeparator: '.',
    showLabels: true,
    showTitle: true,
    title: pageTitle,
    useTextFile: false,
    useBom: true,
    useKeysAsHeaders: true,
    filename: pageTitle + '_' + page
  };
  const csvExporter = new ExportToCsv(options);

  csvExporter.generateCsv(CsvDataFile);
  CsvDataFile = [];
}
