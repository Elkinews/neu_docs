import axios from 'axios';
import { v4 as uuidv4 } from 'uuid';

import {
  CUSTOM_SEARCHURLS,
  DASHBOARD_TAB_URL
} from '@utils/constants';
import {
  isPositiveInteger
} from '../utils/functions';
import {
  cloneDeep
} from "lodash";

const scrollToTop = (querySelectorValue = '', delayTimeout = 100) => {
  setTimeout(() => {
    const element = querySelectorValue ? document.querySelector(querySelectorValue) : document.body;
	element.scroll({
      top: 0,
      left: 0,
      behavior: 'smooth',
    });
	try {
		element.scrollIntoView({ behavior: 'smooth', block: 'start' });
	} catch (error) {}
  }, delayTimeout);
};

export default {
  state: {
    loadingMyDocumentFiles: false,
    myDocumentData: {},
    accesstoken: '',
    refreshtoken: '',
    currentControls: [],
    isLoadingCurrentControls: false,
    currentProgram: {},
    programs: null,
    selectedProgramTree: [],
    searchResult: {},
    searchError: '',
    isSearchLoaded: true,
    totalResults: 0,
    Neusearch: {
      profile: '',
      Searchitems: null,
      includeName: '',
      excludeName: '',
      excludeValue: '',
      recordFromDate: '',
      recordToDate: '',
      page: 1,
      pageSize: 10,
      strict: 'true',
      saveSearch: 'true',
      extraParams: '',
      order_by: '',
      order: 'asc',
    },
    oldNeusearch: {},
    NeuCustomesearch: {},
    currentCustomSearchURL: '',
    customSearchResult: {},
    isCustomSearchLoaded: true,

    isScanModal: false,
    isLoadingScanModalInfo: false,
    scanModalInfo: null,
    currentImageId: '',
    currentUploadBoxType: '',
    isViewDocument: false,
    currentViewDocumentData: null,

    docHistoryPostData: {
      profile_id: '',
      order_by: 'activity',
      order: 'asc',
      page: 1,
      page_size: 10,
      from_date: '',
      to_date: '',
    },
    isDocHistoryLoading: false,
    docHistoryResult: null,

    docWatchlistPostData: {
      profileId: '',
      orderBy: 'created_on',
      orderDir: 'desc',
    },
    isWatchlistLoading: false,
    docWatchlistResult: null,

    deleteWatchlistPost: {
      profileId: '',
      groupHash: '',
    },

    isSearchHistory: true,
    NeuSearchHistorySearch: {},
    NeuSearchHistoryURL: '',
    NeuSearchHistoryResult: {},
    isModalUploadDocument: false,
    isModalUploadDocumentForReplace: false,
    isModalUploadDocumentHasNewFile: false,
    replace_old_nuid: '',
    isModalRecordNotFound: false,
    isModalTransferRecord: false,
    isModalEditIndexingFields: false,
    isModalDoubleEntry: false,
    isModalTransferRecordIndexingFields: false,
    isModalTransferTabs: false,
    isModalProvince: false,
    isModalSortRecordOrder: false,
    isModalDataFeed: false,
    columnsDataFeed: [],
    itemsDataFeed: [],
    allowSortRecordOrder: false,
    dataProvinceDetail: {},
    isShowMessageRecordNotFound: false,
    mergeFromDocId: '',
    mergeFromId: '',
    bulkOptions: [],
    isCallAPILoaded: false,
    hideShowColsData: [],
    VisibleColumnData: [],
    dataDoubleEntry: null,
    createDocumentData: null,
    visibleProgramBtn: true,
    isShowProgressAPI: false,
    targetProgramId: '',
    dataTransferRecordIndexingFields: [],
    targetDocId: 0,
    dataDoc: [],
    permissionsControl: {},
    currentDocMimeTypes: null,
    apiCallCount: 0,
    replaceName: '',
    env: null,
    user_id: '',
    profile_nuid: '',
    profile_avatar_link: '',
    currentUploadFolderId: '',
    unreadNotification: 0,
    currentCopyFileOrg: null,
    modules: [],
  },

  getters: {},

  actions: {
    async callAPI (store, data) {
      try {
        store.state.apiCallCount = store.state.apiCallCount + 1;
        const result = await axios({
          method: data.method || 'post',
          url: data.url || '',
          data: data.data || {},
          params: data.params || {},
        });
        return result?.data;
      } catch (error) {
        return error?.response?.data || {
          status: 'fail',
          message: 'The system is crashing, please try again later'
        }
      }
    },

    increaseCallCount: ({state}) => {
      state.apiCallCount = state.apiCallCount + 1;
    },

    scrollToBottom: ({}, querySelectorValue = '', delayTimeout = 100) => {
      setTimeout(() => {
        const element = querySelectorValue ?
          document.querySelector(querySelectorValue) :
          document.body;
        element.scrollTo(0, element.scrollHeight);
      }, delayTimeout);
    },

    getProfileAvatarInfo: async ({
      state
    }) => {
      state.apiCallCount = state.apiCallCount + 1;
      const profileData = await axios.post('/getProfileAvatarInfo');
      let id = uuidv4();
      state.user_id = profileData.data.data.user_id;
      state.profile_nuid = profileData.data.data.nuid;
      state.profile_avatar_link = profileData.data.data.link + '?' + id;
    },

    downloadAvatarForce: async ({
      state
    }) => {
      state.apiCallCount = state.apiCallCount + 1;
      const profileData = await axios.post('/downloadAvatarForce');
      let id = uuidv4();
      state.user_id = profileData.data.data.user_id;
      state.profile_nuid = profileData.data.data.nuid;
      state.profile_avatar_link = '';
      state.profile_avatar_link = profileData.data.data.link + '?' + id;
    },

    getAccesstoken: async ({
      state
    }) => {
      state.apiCallCount = state.apiCallCount + 1;
      const tokenData = await axios.get('/getAccessToken');
      state.accesstoken = tokenData.data.data.token;
      state.refreshtoken = tokenData.data.data.refreshtoken;
    },

    getENV: async({state}) => {
      state.apiCallCount = state.apiCallCount + 1;
      const envData = await axios.get('/getENV');
      state.env = envData.data.data.env;
    },


    getAccesstokenRefresh: async ({
      state
    }) => {
      state.apiCallCount = state.apiCallCount + 1;
      const tokenData = await axios.get('/getAccessTokenRefresh');
      state.accesstoken = tokenData.data.data.token;
      state.refreshtoken = tokenData.data.data.refreshtoken;
    },

    getDocMimeTypeOauth: async ({
      state
    }, payload) => {
      state.apiCallCount = state.apiCallCount + 1;
      const types = await axios.post('/getDocMimeTypeOauth', payload);
      console.log(types);
      if (types.data.message == 'success') {
        state.currentDocMimeTypes = types.data.data.docTypes;
      }
    },

    searchImages: ({
      commit,
      state,
      dispatch
    }) => {
      state.apiCallCount = state.apiCallCount + 1;
      if (state.Neusearch.excludeName && !state.Neusearch.excludeValue) {
        state.searchError = 'Please input Exclude Value!';
        scrollToTop('.nde-dashboard-container');
        return;
      }

      // check validation
      let validation = true
      state.Neusearch.Searchitems.item.map(item => {
        if (validation) {
          if (item.type == 'INTRANGE') {
            const tmpArray = item.value.split("|");
            tmpArray.map(integerItem => {
              if (validation) {
                if (!isPositiveInteger(integerItem)) {
                  validation = false;
                  state.searchError = 'An internal error occurred, please check your input and try again.';
                }
              }

            })
          }
        }
      })

      let fromD = Date.parse(state.Neusearch.recordFromDate);
      let toD = Date.parse(state.Neusearch.recordToDate);
      let currentD = Date.now();

      if (state.Neusearch.recordFromDate && !fromD) {
        validation = false;
        state.searchError = 'From Date formate is wrong!';
      }

      if (state.Neusearch.recordToDate && !toD) {
        validation = false;
        state.searchError = 'To Date formate is wrong!';
      }

      if (fromD > toD) {
        validation = false;
        state.searchError = 'From Date can not be bigger than To Date';
      }

      if (fromD > currentD || toD > currentD) {
        validation = false;
        state.searchError = 'Date can not be future date.';
      }

      if (!validation) {
        scrollToTop('.nde-dashboard-container');
        return;
      }

      commit('setSearchLoadStatus', false);
      commit('setCallAPILoaded', false);
      commit('setTotalResults', 0);
      state.searchError = '';
      state.isShowMessageRecordNotFound = false;
      state.oldNeusearch = cloneDeep(state.Neusearch);
      axios
        .post('getSearchImages', state.Neusearch)
        .then((response) => {
          commit('setSearchLoadStatus', true);
          if (response.data.message == 'success') {
            commit('setSearchResult', response.data.data.data);
            commit('setTotalResults', response.data?.data?.meta?.num_images?.value);
            dispatch('scrollToBottom', '.nde-dashboard-container');
            let payload = {
              targetProfile: parseFloat(state.Neusearch.profile),
            };
            dispatch('getHideShowColumnData', payload);

            if (Array.isArray(response?.data?.data?.data) && !response?.data?.data?.data.length) {
              state.searchError = 'No records found!';
              state.isShowMessageRecordNotFound = true;
              commit('setModalRecordNotFound', true);
            }
          } else {
            console.log(response);
            state.searchError = response.data.data.data.message;
            scrollToTop('.nde-dashboard-container');
          }
          console.log(response);
        })
        .catch((error) => {
          commit('setSearchLoadStatus', true);
          console.log(error.response);
          state.searchError = error.response.data.data.data.message;
          scrollToTop('.nde-dashboard-container');
        })
        .finally(() => {
          commit('setCallAPILoaded', true);
        });
    },

    generateCustomReport: ({
      commit,
      state
    }) => {
      state.apiCallCount = state.apiCallCount + 1;
      commit('setCustomSearchLoadStatus', false);
      axios
        .post(state.currentCustomSearchURL, state.NeuCustomesearch)
        .then((response) => {
          console.log(response);
          commit('setCustomSearchLoadStatus', true);
          if (response.data.message == 'success') {
            commit('setCustomSearchResult', response.data.data.data);
            commit('setTotalResults', response.data.data.meta.num_images.value);
          } else {
            console.log(response);
          }
          console.log(response);
        })
        .catch((error) => {
          commit('setCustomSearchLoadStatus', true);
          console.log(error);
        });
    },

    getScanModalInfo: async ({
      commit,
      state
    }) => {
      state.apiCallCount = state.apiCallCount + 1;
      commit('setSearchLoadStatus', false);
      const dataScanModalInfo = await axios
        .post('getScanModalInfo', {
          profile_id: state.currentProgram.id,
          image_id: state.currentImageId,
        })
        .then((response) => {
          console.log(response);
          commit('setSearchLoadStatus', true);
          return response.data.data;
        })
        .catch((error) => {
          commit('setSearchLoadStatus', true);
          console.log(error);
          return {};
        });
      return dataScanModalInfo;
    },

    generateSearchHistory: ({
      commit,
      state
    }) => {
      state.apiCallCount = state.apiCallCount + 1;
      commit('setSearchHistory', false);
      axios
        .post(state.NeuSearchHistoryURL, state.NeuSearchHistorySearch)
        .then((response) => {
          commit('setSearchHistory', true);
          if (response.data.message == 'success') {
            commit('setSearchHistoryResult', response.data.data.data);
          } else {
            console.log(response);
          }
          console.log(response);
        })
        .catch((error) => {
          commit('setSearchHistory', true);
          console.log(error);
        });
    },

    getProvinces: ({
      commit, state
    }, payload) => {
      state.apiCallCount = state.apiCallCount + 1;
      return new Promise((resolve, reject) => {
        axios
          .post('/getProvinces', payload)
          .then(({
            data
          }) => {
            if (data) {
              resolve(data && data.data)
            }
          }).catch(error => {
            reject(error)
          })
      })

    },

    getHideShowColumnData: ({
      state,
      commit
    }, payload) => {
      state.apiCallCount = state.apiCallCount + 1;
      return new Promise((resolve, reject) => {
        axios
          .post('/getHideShowColumn', {
            profile_id: payload.targetProfile
          })
          .then((response) => {
            commit('setHideShowColsData', response && response.data && response.data.data)
            resolve(response && response.data && response.data.data)
          })
          .catch((error) => {
            reject(error)
          });
      })
    },

    saveHideShowColumnData: ({
      state
    }, payload) => {
      state.apiCallCount = state.apiCallCount + 1;
      state.isSearchLoaded = false
      axios
        .post('/saveHideShowColumn', payload)
        .then((response) => {
          state.isSearchLoaded = true
          if (response && response.data && response.data.data && response.data.data.message === 'Succeed') {
            alert('Hide Show Column settings were successfully saved.')
          }
        })
        .catch((error) => {
          state.isSearchLoaded = true
          console.error(error)
        });
    },

    handleVisibleProgramBtn: ({
      commit
    }, value) => {
      commit('setVisbleProgramBtn', value)
    },

    myDocumentLoadingFiles: async ({
      commit,
      state
    }) => {
      state.apiCallCount = state.apiCallCount + 1;
      if(!state.isLoadedMyDoc) {
        commit('setMyDocumentLoadStatus', true);
        await axios
          .post('/getDownloadOauth', {
            profile_id: 1,
            page_size: 100,
            page: 1,
          })
          .then((response) => {
            //console.log("mydocument loading is finished", response?.data?.data?.data)
            const documentFiles = response?.data?.data?.data;
            commit('setMyDocumentData', documentFiles)
            commit('setMyDocumentLoadStatus', false);
            state.isLoadedMyDoc = true;
          })
          .catch((error) => {
            console.log(error);
            commit('setMyDocumentLoadStatus', false);
            state.isLoadedMyDoc = false;
          });
      }

    },

    getPermissionControlsOauth: ({
      state
    }) => {
      state.apiCallCount = state.apiCallCount + 1;
      axios
        .get('/getPermissionControlsOauth')
        .then((response) => {
          if (response && response.data && response.data.message === "success") {
            state.permissionsControl = response?.data?.data?.data?.permissions
          }
        })
        .catch((error) => {
          console.error(error)
        });
    },

    getUnreadNotificationOauth: async({state}) => {
      state.apiCallCount = state.apiCallCount + 1;
      const response = await axios.get('/getUnreadNotificationOauth');
      state.unreadNotification = response.data.data.data.unread;
    },

    getModulesOauth: async({state}) => {
      state.apiCallCount = state.apiCallCount + 1;
      const response = await axios.get('/getModulesOauth');
      state.modules = response?.data?.data?.data?.data?.modules;
    },
  },

  mutations: {
    setCurrentProgram(state, program) {
      // reset search
      state.searchResult = {};
      state.isSearchLoaded = true;
      state.Neusearch = {
        profile: '',
        Searchitems: null,
        includeName: '',
        excludeName: '',
        excludeValue: '',
        recordFromDate: '',
        recordToDate: '',
        page: 1,
        pageSize: 10,
        strict: 'true',
        saveSearch: 'true',
        extraParams: '',
        orderBy: '',
        order: 'asc',
      };
      state.currentProgram = program;
      state.Neusearch.profile = program.id;
      state.docHistoryPostData.profile_id = program.id;
      state.docWatchlistPostData.profileId = program.id;
      state.deleteWatchlistPost.profileId = program.id;

      state.currentControls = [];
      state.isLoadingCurrentControls = true;
      state.apiCallCount = state.apiCallCount + 1;
      axios
        .post('/getControls', {
          profile_id: program.id,
          is_bulk_indexing: true
        })
        .then((response) => {
          console.log(response)
          state.isLoadingCurrentContros = false;
          state.currentControls = response.data.data.controls;
        })
        .catch((error) => {
          console.log(error);
          state.isLoadingCurrentControls = false;
        });
    },
    setOrderBy(state, column) {
      state.Neusearch.order_by = column;
    },
    setOrderDesc(state, isDesc) {
      state.Neusearch.order = isDesc ? 'asc' : 'desc';
    },
    setCurrentControls(state, controls) {
      state.currentControls = controls;
    },

    setPage(state, page) {
      state.Neusearch.page = page;
    },
    setPageSize(state, pageSize) {
      state.Neusearch.pageSize = pageSize;
      state.Neusearch.page = 1;
    },
    setTotalResults(state, results) {
      state.totalResults = results;
    },
    setPrograms(state, programs) {
      state.programs = programs;
    },
    setSelectedProgramTree(state, tree) {
      state.selectedProgramTree = tree;
    },
    setSearchResult(state, data) {
      state.isSearchLoaded = true;
      state.searchResult = data;
    },
    setSearchLoadStatus(state, val) {
      state.isSearchLoaded = val;
    },
    setNeusearch: (state, data) => {
      state.Neusearch[data.key] = data.data;
    },

    setCustomSearchLoadStatus(state, val) {
      state.isCustomSearchLoaded = val;
    },

    setCustomSearchPostData: (state, data) => {
      state.NeuCustomesearch = data.data;
      state.currentCustomSearchURL = CUSTOM_SEARCHURLS[data.type];
    },
    setCustomSearchResult: (state, data) => {
      state.customSearchResult = data;
    },

    setVisibleColumns: (state, data) => {
      state.VisibleColumnData = data;
    },

    openScanModal(state, imageId) {
      if (state.isLoadingScanModalInfo) return;

      state.isLoadingScanModalInfo = false;
      state.currentImageId = imageId;
      state.isScanModal = true;
    },

    closeScanModal: (state) => {
      state.isScanModal = false;
      state.scanModalData = null;
      //state.currentImageId = '';
    },

    setDocHistoryPostData: (state, data) => {
      state.docHistoryPostData[data.key] = data.data;
    },

    getDocHistoryOauth: (state) => {
      state.isDocHistoryLoading = true;
      state.docHistoryResult = null;
      state.apiCallCount = state.apiCallCount + 1;
      axios
        .post('/getDocHistoryOauth', state.docHistoryPostData)
        .then((response) => {
          state.isDocHistoryLoading = false;
          if (response.data.message == 'success') {
            state.docHistoryResult = response.data.data.data;
          }
        })
        .catch((error) => {
          state.isDocHistoryLoading = false;
          console.log(error);
        });
    },

    getWatchlistOauth: async (state) => {
      state.isWatchlistLoading = true;
      state.docWatchlistResult = null;
      state.apiCallCount = state.apiCallCount + 1;
      const res = await axios
        .post('/getWatchlistOauth', state.docWatchlistPostData)
        .then((response) => {
          state.isWatchlistLoading = false;
          if (response.data.message == 'success') {
            state.docWatchlistResult = response.data.data.data;
          }
        })
        .catch((error) => {
          state.isWatchlistLoading = false;
          console.log(error);
        });
      return res;
    },

    deleteUserWatchlistOauth: (state, data) => {
      state.deleteWatchlistPost.groupHash = data;
      state.apiCallCount = state.apiCallCount + 1;
      axios
        .post('/deleteUserWatchlistOauth', state.deleteWatchlistPost)
        .then((response) => {
          //console.log("response delete data:")
          console.log(response);
        })
        .catch((error) => {
          console.log(error);
        });
    },

    setDataDoubleEntry: (state, data) => {
      state.dataDoubleEntry = data;
    },

    openViewDocument: (state, data) => {
      state.isViewDocument = true;
      state.currentViewDocumentData = data;
    },

    setCurrentDocData: (state, data) => {
      state.currentViewDocumentData = data;
    },

    closeViewDocument: (state) => {
      state.isViewDocument = false;
      state.currentViewDocumentData = null;
    },

    setSearchHistoryData: (state, data) => {
      state.NeuSearchHistorySearch = data.data;
      state.NeuSearchHistoryURL = DASHBOARD_TAB_URL[data.type];
    },

    setSearchHistory(state, value) {
      state.isSearchHistory = value;
    },

    setSearchHistoryResult: (state, data) => {
      state.NeuSearchHistoryResult = data;
    },

    openModalUploadDoucment(state, data) {
      state.currentImageId = data.imageId;
      state.currentUploadBoxType = data.box_type;
      state.currentUploadFolderId = data?.folder_id || '';
      state.isModalUploadDocument = true;
    },

    openModalUploadDoucmentForReplace(state, data) {
      state.currentImageId = data.imageId;
      state.currentUploadBoxType = data.box_type;
      state.isModalUploadDocument = true;
      state.isModalUploadDocumentForReplace = true;
      state.replace_old_nuid = data.old_nuid;
      state.replaceName = data.name;
      state.isModalUploadDocumentHasNewFile = false;
    },

    closeModalUploadDoucment: (state, data) => {
      state.isModalUploadDocument = false;
      // state.currentImageId = '';
      // state.currentUploadBoxType = '';
      state.isModalUploadDocumentForReplace = false;
      state.replace_old_nuid = '';
      state.isModalUploadDocumentHasNewFile = data.hasNewFile;
    },

    openModalTransferRecord(state, imageId) {
      state.currentImageId = imageId;
      state.isModalTransferRecord = true;
    },

    closeModalTransferRecord: (state) => {
      state.isModalTransferRecord = false;
    },

    openModalEditIndexingFields(state, value) {
      state.currentImageId = value.docId;
      state.dataDoc = cloneDeep(value.dataDoc);
      state.targetDocId = '';
      state.isModalEditIndexingFields = true;
    },

    closeModalEditIndexingFields: (state) => {
      state.isModalEditIndexingFields = false;
      state.currentImageId = '';
      state.dataDoc = {};
    },

    openModalDoubleEntry(state, imageId) {
      state.currentImageId = imageId;
      state.isModalDoubleEntry = true;
    },

    closeModalDoubleEntry: (state) => {
      state.isModalDoubleEntry = false;
    },

    openModalTransferRecordIndexingFields(state) {
      state.isModalTransferRecordIndexingFields = true;
    },

    closeModalTransferRecordIndexingFields: (state) => {
      state.isModalTransferRecordIndexingFields = false;
    },

    openModalTransferTabs(state, value) {
      state.isModalTransferTabs = true;
      state.dataTransferRecordIndexingFields = value.formData;
      state.targetDocId = value.targetDocId;
    },

    closeModalTransferTabs: (state) => {
      state.isModalTransferTabs = false;
      state.currentImageId = '';
      state.dataTransferRecordIndexingFields = [];
      state.targetDocId = 0;
    },

    openModalProvince(state, dataProvinceDetail) {
      state.dataProvinceDetail = dataProvinceDetail;
      state.isModalProvince = true;
    },

    closeModalProvince: (state) => {
      state.dataProvinceDetail = {};
      state.isModalProvince = false;
    },

    setDataFeed: (state, dataFeed) => {
      state.columnsDataFeed = dataFeed.columnsDataFeed;
      state.itemsDataFeed = dataFeed.itemsDataFeed;
    },

    openModalDataFeed: (state, dataFeed) => {
      state.isModalDataFeed = true;
      state.columnsDataFeed = dataFeed.columnsDataFeed;
      state.itemsDataFeed = dataFeed.itemsDataFeed;
    },

    closeModalDataFeed: (state) => {
      state.isModalDataFeed = false;
      state.columnsDataFeed = [];
      state.itemsDataFeed = [];
    },

    setTargetProgramId(state, value) {
      state.targetProgramId = value;
    },

    openModalSortRecordOrder(state) {
      state.isModalSortRecordOrder = true;
    },

    closeModalSortRecordOrder: (state) => {
      state.isModalSortRecordOrder = false;
    },

    setAllowSortRecordOrder(state, val) {
		  state.allowSortRecordOrder = val;
	  },

    setCallAPILoaded(state, value) {
      state.isCallAPILoaded = value;
    },
    setBulkOptions(state, value) {
      const arrayBulkOptions = [];
      let bulkOptions = value;
      try {
        bulkOptions = JSON.parse(value);
      } catch (error) {
        bulkOptions = value;
      }
      if (bulkOptions?.actions['Bulk Options']) {
        arrayBulkOptions.push(...Object.values(bulkOptions?.actions['Bulk Options'] || {}));
      }
      state.bulkOptions = arrayBulkOptions;
    },
    setModalRecordNotFound(state, value) {
      state.isModalRecordNotFound = value;
    },

    setHideShowColsData(state, data) {
      state.hideShowColsData = data
    },
    setVisbleProgramBtn(state, value) {
      state.visibleProgramBtn = value
    },
    setShowProgressAPI(state, value) {
      state.isShowProgressAPI = value
    },
    setMergeFromDocId(state, docId) {
      state.mergeFromDocId = docId;
    },
    setMergeFromId(state, id) {
      state.mergeFromId = id;
    },
    setMyDocumentData(state, totalFiles) {
      state.myDocumentData = totalFiles
    },
    setMyDocumentLoadStatus(state, status) {
      state.loadingMyDocumentFiles = status
    },

    scrollToTop(state, querySelectorValue) {
      scrollToTop(querySelectorValue);
    },

    setCopyFileOrgNuid(state, data) {
      state.currentCopyFileOrg = data;
    }
  },
};
