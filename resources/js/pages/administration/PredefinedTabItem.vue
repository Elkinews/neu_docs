<template>
  <v-row>
    <v-col md="3">
      <label for="pre-name" class="mb-3">Name</label>
      <v-text-field
        label="Name"
        placeholder="Name"
        dense
        solo
        hide-details
        v-model="name"
        aria-label="Name"

      ></v-text-field>
    </v-col>
    <v-col md="3">
      <label for="pre-upload-type" class="mb-3">Upload Type</label>
      <v-select
        :items="boxTypeInfo"
        label="Upload Type"
        dense
        solo
        hide-details
        item-text="description"
        item-value="box_type"
        v-model="box_type"
        aria-label="Upload Type"

      ></v-select>
    </v-col>
    <v-col md="3">
      <label for="pre-image-group" class="mb-3">Image Group</label>
      <v-select
        :items="imageGroupInfo"
        label="Image Group"
        dense
        solo
        hide-details
        item-text="name"
        item-value="id"
        v-model="image_group"
        aria-label="Image Group"

      ></v-select>
    </v-col>
    <v-col md="1" class="d-flex align-end">
      <nde-button outlined color="error" class="mr-6" @click="remove" :loading="isdeleting">Remove</nde-button>
    </v-col>
  </v-row>
</template>

<script>
import NdeButton from '../../components/Button/NdeButton.vue';
export default {
  components: {
    NdeButton,
  },
  data() {
    return {
      name: '',
      image_group: null,
      box_type: '',
      isdeleting: false
    };
  },

  props: {
    predefinedTab: {
      type: Object,
      required: true,
    },
    boxTypeInfo: {
      type: Array,
      required: true,
    },
    imageGroupInfo: {
      type: Array,
      required: true,
    },
    profileId: {
      type: String,
      required: true
    }
  },

  created() {
    this.name = this.predefinedTab.name;
    this.image_group = this.predefinedTab.image_group_id;
    this.box_type = this.predefinedTab.box_type;
  },

  watch: {
    predefinedTab(val) {
      this.name = this.predefinedTab.name;
      this.image_group = this.predefinedTab.image_group_id;
      this.box_type = this.predefinedTab.box_type;
    },

    name(val) {
      this.emitUpdate();
    },

    image_group(val) {
      this.emitUpdate();
    },

    box_type(val) {
      this.emitUpdate();
    },
  },

  methods: {
    async remove() {
      const confirmed = await this.$swal({
        title: 'Delete Confirmation',
        text: 'Are you sure you want to delete this tab?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Delete',
        confirmButtonColor: '#c32c39',
        cancelButtonText: 'Cancel',
        cancelButtonColor: '#9a9ea1',
        reverseButtons: true
      });

      if (!confirmed.value) {
        return;
      }
      if(this.predefinedTab.isnew) {
        this.$emit('onRemove', this.predefinedTab.id);
      } else {
        this.isdeleting = true;

        const deleteResult = await this.$store.dispatch('callAPI', {
          url: '/deletePredefinedTabOauth',
          method: 'post',
          data: {
            profile_id: this.profileId,
            name: this.name,
            box_type: this.box_type,
            image_group_id: this.image_group
          },
        });
        this.isdeleting = false;
        if (deleteResult.message == 'success') {
          this.$emit('onRemove', this.predefinedTab.id);
        } else {
          await this.$swal({
            title: 'Delete Error',
            text: 'Delete this tab error! Please try again!',
            type: 'warning',
            showCancelButton: false,
            confirmButtonText: 'Close',
          });
        }


      }
    },

    emitUpdate() {
      this.$emit('onUpdate', {
        id: this.predefinedTab.id,
        name: this.name,
        image_group: this.image_group,
        box_type: this.box_type,
        isnew: this.predefinedTab.isnew || false
      });
    },
  },
};
</script>

<style>
</style>
