<template>
    <div class="nde-drag-and-drop" @dragenter="dragging = true" @dragleave="dragging = false">
        <div class="drag-content text-center" @drag="onChange">
            <!-- <v-icon large color="primary">mdi-cloud-upload-outline</v-icon> -->
            <img src="/images/Upload_File.png" alt="">
            <p class="mb-0">Drag & Drop file s here or</p>
            <a href="javascript:;" @click="handleUploadManual">Browse Files</a>
            <input ref="input" type="file" @change="onChange" accept=".csv">
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            file: '',
            dragging: false
        }
    },

    methods: {
        handleUploadManual() {
            this.$refs.input.click()
        },

        onChange(e) {
            let files = e.target.files || e.dataTransfer.files;
            
            if (!files.length) {
                this.dragging = false;
                return;
            }
            
            this.file = files[0];
            this.$emit('onGetDataFile', this.file)
        },
    }
}
</script>
<style lang="scss" scoped>
.nde-drag-and-drop {
    width: 100%;
    height: 7.5rem;
    border: 1px dashed #3D769E;
    border-radius: 0.5rem;
    background: #F7F7F7;
    position: relative;
    padding: calc((7.5rem - 5.25rem) /2) 0;

    .drag-content {
        width: 100%;
        margin: 0 auto;

        p {
            color: #636B6E
        }

        input {
            position: absolute;
            cursor: pointer;
            top: 0px;
            right: 0;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
        }

    }
}
</style>;