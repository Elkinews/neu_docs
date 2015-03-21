<template>
  <div class="nde-scan-modal-form">
    <h3>Scan Document</h3>
    <p>File#: 5226</p>
    <v-form v-if="!isCreateNewSubTab">
      <v-row>
        <v-col cols="12">
          <v-select :items="items" label="Select Scaner" solo hide-details></v-select>
        </v-col>
      </v-row>

      <v-row>
        <v-col cols="12">
			<v-text-field
				v-model="selectedPage"
				readonly
				solo
				hide-details
				append-outer-icon="mdi-menu-right"
				prepend-icon="mdi-menu-left"
				type="text"
				class="nde-select-page-pdf"
			>
				<template v-slot:prepend>
					<span class="nde-next-pre-select-pre-btn">
						<v-icon>mdi-menu-left</v-icon>
					</span>
				</template>
				<template v-slot:append-outer>
					<span class="nde-next-pre-select-next-btn">
						<v-icon>mdi-menu-right</v-icon>
					</span>
				</template>
		  	</v-text-field>
        </v-col>
      </v-row>

      <v-row>
        <v-col cols="12" v-if="isPriority">
          <label>Priority</label>
          <v-select
            :items="priorities"
            label="Priority"
            solo
            hide-details
            item-text="name"
            item-value="key"
          ></v-select>
        </v-col>

        <v-col cols="12" v-if="isUploadType">
          <label>Upload Type</label>
          <v-select
            :items="uploadTypes"
            label="Upload Type"
            solo
            hide-details
            item-text="name"
            item-value="name"
            v-model="selectedUploadtype"
          ></v-select>
        </v-col>
      </v-row>

      <v-row>
        <v-col cols="12" v-if="isTab">
          <label>Tab</label>
          <v-select
            :items="tabs"
            label="Tab"
            solo
            hide-details
            item-value="tab_id"
            v-model="selectedTab"
          >
            <template v-slot:selection="{ item }">
              <span>{{ item.name || item.tabname }}</span>
            </template>
            <!-- Template for render data when the select is expanded -->
            <template v-slot:item="{ item, on }">
              <!-- Divider and Header-->
              <template v-if="item.is_default_tab">
                <v-list-item v-on="on">
                  <v-list-item-content>
                    <v-list-item-title v-text="item.tabname"></v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </template>
              <!-- Normal item -->
              <template v-else>
                <v-list-item v-on="on">
                  <v-list-item-content>
                    <v-list-item-title v-text="item.name"></v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </template>
            </template>
          </v-select>
        </v-col>

        <v-col cols="12" v-if="isSubtab">
          <label>SubTab</label>
          <v-select
            :items="subtabs"
            label="SubTab"
            solo
            hide-details
            v-model="selectedSubtab"
            item-text="name"
            item-value="name"
            :loading="isLoadingSubTabs"
          ></v-select>
        </v-col>
      </v-row>

      <v-row v-if="isAnnotationTypeOptions">
        <v-col cols="12" md="12">
          <label>Annotation Type</label>
          <v-select
            :items="annotationTypes"
            label="Annotation Type"
            solo
            hide-details
            item-text="label"
            item-value="value"
            v-model="selectedAnnotationTypes"
          ></v-select>
        </v-col>
      </v-row>

      <div class="d-flex justify-space-between mb-1 mt-2">
        <label>Annotation</label>
        <v-tooltip right color="#547D36" max-width="279" v-model="showTooltipAnnotation">
          <template v-slot:activator="{}">
            <v-icon @click="showTooltipAnnotation = !showTooltipAnnotation">mdi-information</v-icon>
          </template>
          <span
            >Selecting Bates Stamp provides 3 annotation fields: The left field is the prefix for
            the annotation. Prefix text will be included on each page of this uploaded file, before
            the incrementing number. Prefix may be left blank The center field is the incrementing
            number. Non-negative integers are the only allowed values in this field. The first page
            of the uploaded file will have the entered number, and each subsequent page will have a
            number one greater than the previous page. This program is configured to normalize these
            values to 5 digits. The right field is the suffix for the annotation. Suffix text will
            be included on each page of this uploaded file, after the incrementing number. Suffix
            may be left blank.
          </span>
        </v-tooltip>
      </div>

      <v-row>
        <v-col cols="12" md="4">
          <v-text-field label="Pre" solo type="number" hide-details></v-text-field>
        </v-col>

        <v-col cols="12" md="4">
          <v-text-field label="00000" solo type="number" hide-details></v-text-field>
        </v-col>

        <v-col cols="12" md="4">
          <v-text-field label="Suf" solo type="number" hide-details></v-text-field>
        </v-col>
      </v-row>

      <v-row>
        <v-col cols="12" md="12">
          <v-select
            :items="annotationPositions"
            solo
            hide-details
            v-model="selectedAnnotationPosition"
          ></v-select>
        </v-col>
      </v-row>

      <v-row>
        <v-col cols="12" md="12">
          <div class="d-flex justify-space-between mb-1">
            <label>New pages insert</label>
            <v-tooltip right color="#547D36" max-width="279" v-model="showTooltipNewPageInsert">
              <template v-slot:activator="{}">
                <v-icon @click="showTooltipNewPageInsert = !showTooltipNewPageInsert"
                  >mdi-information</v-icon
                >
              </template>
              <span
                >This setting does not affect where the entire batch inserts into in the existing
                record after upload. This controls where images of new physical pages go, in the
                yet-to-be-uploaded batch.</span
              >
            </v-tooltip>
          </div>

          <v-select
            :items="newPageInsertTypes"
            label="New pages insert"
            solo
            hide-details
          ></v-select>
        </v-col>
      </v-row>

      <v-row>
        <v-col class="pt-1 pb-0" cols="12" v-if="isCopyToWorkQueue">
          <v-checkbox v-model="copyToWorkQueue" label="Copy to work queue"></v-checkbox>
        </v-col>

        <v-col class="pt-1 pb-0" cols="12" v-if="isSetHashTags">
          <v-checkbox v-model="addRemoveHashtags" label="Add/Remove Hashtags"></v-checkbox>
        </v-col>
      </v-row>

      <v-row justify="center" align="center">
        <v-col cols="12" md="6">
          <nde-button block @click="done">Close</nde-button>
        </v-col>
      </v-row>
    </v-form>

    <v-form v-if="isCreateNewSubTab">
      <v-row>
        <v-col>
          <nde-button link text color="primary" @click="backToForm()">
            <v-icon left> mdi-menu-left </v-icon>
            Back
          </nde-button>
        </v-col>
      </v-row>

      <v-row>
        <v-col>
          <label>Please enter subtab name</label>
          <v-text-field label="Bates Stamp" solo hide-details></v-text-field>
        </v-col>
      </v-row>

      <v-row>
        <v-col>
          <nde-button block color="primary">Create</nde-button>
        </v-col>

        <v-col>
          <nde-button block @click="backToForm()">Cancel</nde-button>
        </v-col>
      </v-row>
    </v-form>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import axios from 'axios';
import NdeButton from '../Button/NdeButton.vue';

export default {
  data() {
    return {
	  selectedPage: "Current Page",
      selectedPriority: null,
      selectedUploadtype: '',
      selectedTab: '',
      selectedSubtab: '',
      selectedAnnotationTypes: null,
      selectedAnnotationPosition: null,
      selectedBoxType: '',
      subtabs: [],
      isLoadingSubTabs: false,

      items: ['Foo', 'Bar', 'Fizz', 'Buzz'],

      newPageInsertTypes: ['Bottom of scan review', 'Above selected page'],
      copyToWorkQueue: false,
      addRemoveHashtags: false,

      isCreateNewSubTab: false,
      showTooltipAnnotation: false,
      showTooltipNewPageInsert: false,
    };
  },

  mounted() {
    document.addEventListener('keydown', (e) => {
      if (e.keyCode == 27) {
        this.showTooltipAnnotation = false;
        this.showTooltipNewPageInsert = false;
      }
    });
  },

  methods: {
    backToForm() {
      this.isCreateNewSubTab = false;
    },
    done() {
      this.$store.commit('closeScanModal');
    },
    loadBookmarks() {
      if (!this.selectedTab || !this.selectedBoxType) {
        return;
      }

      this.subtabs = [];
      this.isLoadingSubTabs = true;
      this.$store.dispatch('increaseCallCount');
      axios
        .post('getBookmarksOauth', {
          profile: this.currentProgram.id,
          docID: this.currentImageId,
          tabName:
            this.tabs.filter((o) => o.tab_id == this.selectedTab)[0].tabname ||
            this.tabs.filter((o) => o.tab_id == this.selectedTab)[0].name,
          boxType: this.selectedBoxType,
        })
        .then((response) => {
          this.isLoadingSubTabs = false;

          if (response.data.message == 'success') {
            this.subtabs = response.data.data.data.bookmarks;
            this.selectedSubtab = this.subtabs[0].name;
          } else {
          }
        })
        .catch((error) => {
          this.isLoadingSubTabs = false;
          console.log(error.response);
        });
    },
  },

  computed: {
    ...mapState([
      'isScanModal',
      'scanModalData',
      'currentImageId',
      'currentProgram',
      'hideShowColsData',
    ]),

    priorities() {
      const values = this.scanModalData.UploadFields.fields.Priority.Values;
      const keys = Object.keys(values);
      const return_value = [];
      keys.map((key) => {
        return_value.push({ key, name: values[key] });
      });

      if (!this.selectedPriority) {
        this.selectedPriority = return_value[0].key;
      }

      return return_value;
    },

    uploadTypes() {
      const values = this.scanModalData.UploadFields.fields['Upload Type']?.Values || [];
      const keys = Object.keys(values);
      const return_value = [];
      keys.map((key) => {
        return_value.push({ key, name: values[key] });
      });
      if (!this.selectedUploadtype && return_value.length) {
        this.selectedUploadtype = return_value[0].name;
      }

      return return_value;
    },

    tabs() {
      const values = this.scanModalData.UploadFields.fields.Tab.Values;
      const return_value = this.selectedUploadtype
        ? [...values[this.selectedUploadtype].tabs, ...values[this.selectedUploadtype].predefined]
        : [];
      if (!this.selectedTab && return_value.length) {
        this.selectedTab = return_value[0]?.tab_id;
      }

      return return_value;
    },
    // subtabs() {
    //   const values =
    //     this.scanModalData.UploadFields.fields.Subtab["Subtab Location"].Values;
    //   if (!this.selectedSubtab) {
    //     this.selectedSubtab = values[0];
    //   }

    //   return values;
    // },
    annotationTypes() {
      let values = [];
      if (
        this.scanModalData.UploadFields.fields?.Batestamp &&
        this.scanModalData.UploadFields.fields?.Batestamp['Annotation Type']
      ) {
        values = this.scanModalData.UploadFields.fields?.Batestamp['Annotation Type']?.Values || [];
      }

      // const batestamp = this.hideShowColsData.hideShowCols.filter((o) => o.name === 'Batestamp');

      // if (batestamp[0].ishidden !== 'f') {
      //   values = values.filter((item) => item.label !== 'Bates Stamp');
      // }

      if (!this.selectedAnnotationTypes && values.length) {
        this.selectedAnnotationTypes = values[0].value;
      }

      return values;
    },

    annotationPositions() {
      let values = [];
      if (
        this.scanModalData.UploadFields.fields?.Batestamp &&
        this.scanModalData.UploadFields.fields?.Batestamp['Annotation Location']
      ) {
        values =
          this.scanModalData.UploadFields.fields?.Batestamp['Annotation Location']?.Values || [];
      }
      // const values = this.scanModalData.UploadFields.fields.Batestamp['Annotation Location'].Values;
      if (!this.selectedAnnotationPosition && values.length) {
        this.selectedAnnotationPosition = values[0];
      }

      return values;
    },

    isAnnotationTypeOptions() {
      const filterValue = this.hideShowColsData.hideShowCols.filter(
        (o) => o.name === 'Batestamp',
      );

      if (filterValue[0].ishidden === 'f') {
        return true;
      }

      return false;
    },

    isCopyToWorkQueue() {
      const filterValue = this.hideShowColsData.hideShowCols.filter(
        (o) => o.name === 'Copy to Work Queue',
      );

      if (filterValue[0].ishidden === 'f') {
        return true;
      }

      return false;
    },

    isPriority() {
      const filterValue = this.hideShowColsData.hideShowCols.filter((o) => o.name === 'Priority');

      if (filterValue[0].ishidden === 'f') {
        return true;
      }

      return false;
    },

    isSetHashTags() {
      const filterValue = this.hideShowColsData.hideShowCols.filter(
        (o) => o.name === 'Set Hashtags',
      );

      if (filterValue[0].ishidden === 'f') {
        return true;
      }

      return false;
    },

    isSubtab() {
      const filterValue = this.hideShowColsData.hideShowCols.filter((o) => o.name === 'Subtab');

      if (filterValue[0].ishidden === 'f') {
        return true;
      }

      return false;
    },

    isTab() {
      const filterValue = this.hideShowColsData.hideShowCols.filter((o) => o.name === 'Tab');

      if (filterValue[0].ishidden === 'f') {
        return true;
      }

      return false;
    },

    isUploadType() {
      const filterValue = this.hideShowColsData.hideShowCols.filter(
        (o) => o.name === 'Upload Type',
      );

      if (filterValue[0].ishidden === 'f') {
        return true;
      }

      return false;
    },
  },

  components: {
    NdeButton,
  },
  watch: {
    selectedUploadtype: function (val) {
      this.selectedBoxType = this.scanModalData.UploadFields.fields.Tab.Values[val].boxType;
      console.log(this.selectedBoxType);
      this.loadBookmarks();
    },
    selectedTab: function (val) {
      this.loadBookmarks();
    },
  },
};
</script>

<style scoped lang="scss">
.nde-scan-modal-form {
  padding: 24px 30px 43px 48px;
  background: white;
}
.nde-select-page-pdf {
	background: $primaryGreyColor;
	border: 1px solid $defaultColor;
	.nde-next-pre-select-pre-btn, .nde-next-pre-select-next-btn{
		width: 2.5rem;
		cursor: pointer;
	}
	.nde-next-pre-select-pre-btn {
		padding-left: 1rem;
	}
	:v-deep .v-input__slot {
		box-shadow: none !important;
		input {
			text-align: center;
			color: $greyColor;
		}
	}
}
</style>
