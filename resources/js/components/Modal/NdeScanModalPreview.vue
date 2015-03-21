<template>
  <div class="nde-scan-modal-preview">
    <div class="nde-image-preview-image">
      <div class="nde-image-preview-left">
        <nde-button icon @click="selectPrevImage()">
          <v-icon>mdi-menu-up</v-icon>
        </nde-button>

        <div
          v-for="(image, index) in images"
          :key="index"
          class="nde-image-preview-left-item"
          v-bind:class="{ selected: index == selectedImageIndex }"
          v-on:click="selectImage(index)"
        >
          <img v-bind:src="image" />
        </div>

        <nde-button icon click="selectNextImage()">
          <v-icon>mdi-menu-down</v-icon>
        </nde-button>
      </div>

      <div class="nde-image-preview-selected">
        <img v-bind:src="images[selectedImageIndex]" v-bind:class="rotateClass" />
      </div>
    </div>

    <div class="nde-image-preview-tools">
      <div class="nde-image-preview-tools-emtpy"></div>
      <div class="nde-image-preview-tools-btns">
        <div style="width: 90%">
          <div class="d-flex justify-space-between">
            <nde-button color="white" @click="rotateLeft()">
              <v-icon left color="primary">mdi-rotate-left</v-icon>
              Rotate Left
            </nde-button>

            <nde-button color="white" @click="rotateRight()">
              Rotate Right
              <v-icon right color="primary">mdi-rotate-right</v-icon>
            </nde-button>
          </div>

          <nde-button block color="error" outlined class="mt-3" @click="deleteSelected()">
            <v-icon>mdi-close-circle-outline</v-icon>
            Delete Selected Image
          </nde-button>

          <nde-button block color="success" outlined class="mt-3" @click="clearScannedImage()">
            <v-icon>mdi-autorenew</v-icon>
            Clear scanned Image
          </nde-button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import NdeButton from '../Button/NdeButton.vue';

export default {
  components: { NdeButton },
  data() {
    return {
      originalImages: [
        'https://4ww1y37tl91gmoej12r01u1c-wpengine.netdna-ssl.com/wp-content/uploads/2019/08/TextDocument.png',
        'https://4ww1y37tl91gmoej12r01u1c-wpengine.netdna-ssl.com/wp-content/uploads/2019/08/Grayscale.jpg',
        'https://4ww1y37tl91gmoej12r01u1c-wpengine.netdna-ssl.com/wp-content/uploads/2019/08/Color.jpg',
      ],
      images: [
        'https://4ww1y37tl91gmoej12r01u1c-wpengine.netdna-ssl.com/wp-content/uploads/2019/08/TextDocument.png',
        'https://4ww1y37tl91gmoej12r01u1c-wpengine.netdna-ssl.com/wp-content/uploads/2019/08/Grayscale.jpg',
        'https://4ww1y37tl91gmoej12r01u1c-wpengine.netdna-ssl.com/wp-content/uploads/2019/08/Color.jpg',
      ],
      selectedImageIndex: 0,

      rotateStatus: 0,
    };
  },

  computed: {
    rotateClass: function () {
      return {
        'rotate-90': this.rotateStatus == 1,
        'rotate-180': this.rotateStatus == 2,
        'rotate-270': this.rotateStatus == 3,
      };
    },
  },
  methods: {
    selectImage(index) {
      this.selectedImageIndex = index;
    },
    selectPrevImage() {
      if (this.selectedImageIndex == 0) return;
      this.selectedImageIndex--;
    },
    selectNextImage() {
      if (this.selectedImageIndex >= this.images.length - 1) return;
      this.selectedImageIndex++;
    },
    deleteSelected() {
      this.images.splice(this.selectedImageIndex, 1);
      if (this.selectedImageIndex >= this.images.length) {
        this.selectedImageIndex = this.images.length - 1;
      }
    },
    clearScannedImage() {
      this.images = [...this.originalImages];
      this.selectedImageIndex = 0;
      this.rotateStatus = 0;
    },
    rotateRight() {
      this.rotateStatus++;
      this.rotateStatus = Math.abs(this.rotateStatus % 4);
    },
    rotateLeft() {
      if (this.rotateStatus == 0) {
        this.rotateStatus = 3;
      } else {
        this.rotateStatus--;
      }

      this.rotateStatus = Math.abs(this.rotateStatus % 4);
    },
  },
};
</script>

<style scoped lang="scss">
.nde-scan-modal-preview {
  padding: 43px 58px 43px 26px;

  .nde-image-preview-image {
    display: flex;

    .nde-image-preview-left {
      display: flex;
      flex-direction: column;
      align-items: center;
      width: 100px;

      &-item {
        width: 90px;
        margin-bottom: 8px;
        cursor: pointer;

        &.selected {
          border: 2px solid $primaryColor;
        }

        &:hover {
          border: 2px solid $primaryColor;
        }

        img {
          width: 100%;
        }
      }
    }

    .nde-image-preview-selected {
      width: 100%;
      display: flex;
      justify-content: center;

      img {
        width: 90%;
      }

      .rotate-90 {
        -ms-transform: rotate(90deg); /* IE 9 */
        transform: rotate(90deg);
        max-height: 500px;
        width: auto;
      }

      .rotate-180 {
        -ms-transform: rotate(180deg); /* IE 9 */
        transform: rotate(180deg);
      }

      .rotate-270 {
        -ms-transform: rotate(270deg); /* IE 9 */
        transform: rotate(270deg);
        max-height: 500px;
        width: auto;
      }
    }
  }

  .nde-image-preview-tools {
    margin-top: 50px;
    display: flex;
    justify-content: space-between;

    &-emtpy {
      width: 90px;
    }

    &-btns {
      display: flex;
      justify-content: center;
      width: 100%;
    }
  }
}
</style>