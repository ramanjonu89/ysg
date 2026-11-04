<template>
  <section>
    <b-card>
      <b-card-header class="flex-column align-items-start">
        <b-card-title>Product Images</b-card-title>
      </b-card-header>
      <b-card-body>
        <div class="main">
          <div
            :style="isDragging && 'border-color: green;'"
            class="dropzone-container"
            @dragover="dragover"
            @dragleave="dragleave"
            @drop="drop"
          >
            <input
              type="file"
              multiple
              name="file"
              id="fileInput"
              class="hidden-input"
              @change="onChange"
              ref="file"
              accept=".jpg,.jpeg,.png"
            />

            <label for="fileInput" class="file-label">
              <div v-if="isDragging">Release to drop files here.</div>
              <div v-else>Drop files here or <u>click here</u> to upload.</div>
            </label>
          </div>
        </div>
        <div class="row" v-if="product.photos.length">
          <draggable v-model="product.photos" @change="onDragEnd" class="row">
            <div v-for="file in product.photos" :key="fileKey(file)" class="col-2 col-xs-4 mt-4 draggable-item">
              <div class="preview-img">
                <img class="preview-img" :src="generateURL(file)" />
                <div class="img-frame" :style="isThumbnail(file) ? 'display: block' : ''">
                  <div class="row">
                    <div class="col-6 col-xs-4">
                      <span v-if="isThumbnail(file)">Thumb</span>
                    </div>

                    <div class="col-6 col-xs-8 text-right">
                      <b-button
                        variant="flat-info"
                        class="btn-icon btn-tools"
                        @click="setThumbnail(product.photos.indexOf(file), file)"
                        title="Set as thumbnail"
                      >
                        <feather-icon
                          icon="StarIcon"
                          size="12"
                        />
                      </b-button>
                      <b-button
                        variant="flat-info"
                        class="btn-icon btn-tools"
                        title="Delete image"
                        @click="remove(product.photos.indexOf(file), file)"
                      >
                        <feather-icon
                          icon="DeleteIcon"
                          size="12"
                        />
                      </b-button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </draggable>
        </div>
      </b-card-body>
    </b-card>
  </section>

</template>

<script>
import {
  BButton,
  BCard,
  BCardBody,
  BCardHeader,
  BCardText,
  BCardTitle,
  BCol,
  BFormGroup, BImg,
  BRow, BTooltip,
  VBModal
} from "bootstrap-vue";
import draggable from 'vuedraggable';
import { inject, ref } from "@vue/composition-api";
export default {
  name: "ProductImagesMultipleUpload",
  components: {
    BCard,
    BCardHeader,
    BCardTitle,
    BCardText,
    BCardBody,
    BFormGroup,
    BRow,
    BCol,
    BButton,
    BTooltip,
    BImg,
    draggable,
  },
  setup() {
    const product = inject('product');
    const isDragging = ref(false);
    const files = ref([]);
    const file = ref(null);
    const thumbnail = ref('')

    //methods
    const onChange = () => {
      product.value.photos.push(...file.value.files);
    }

    const dragover = e => {
      e.preventDefault();
      isDragging.value = true;
    }

    const dragleave = () => {
      isDragging.value = false;
    }

    const drop = e => {
      e.preventDefault();
      file.value.files = e.dataTransfer.files;
      onChange();
      isDragging.value = false;
    }

    const remove = (i, file) => {
      if (isThumbnail(file)) {
        product.value.thumbnail_img_index = 0
        product.value.thumbnail_img = ''
      }
      if (typeof file === 'string') {
        // if file is string => mean the file is uploaded => need remove
        product.value.deleted_images.push(file)
      }
      product.value.photos.splice(i, 1);
    }

    const generateURL = file => {
      let fileSrc = '';
      if (typeof file === 'string' && file.indexOf('uploads') !== -1 && file.indexOf('products') !== -1) {
        fileSrc = file.startsWith('/') ? file : '/' + file;
      } else {
        fileSrc = `/uploads/products/${product.value.id}/thumbnail/` + file;
      }
      if (file instanceof File) {
        fileSrc = URL.createObjectURL(file);
        setTimeout(() => {
            URL.revokeObjectURL(fileSrc);
        }, 1000);
      }

      return fileSrc;
    }

    const isThumbnail = file => {
      if (typeof file === 'string') {
        return file == product.value.thumbnail_img
      }
      return file.name == product.value.thumbnail_img
    }

    const setThumbnail = (index, file) => {
      const fileName = typeof file === 'string' ? file : file.name;
      product.value.thumbnail_img = product.value.thumbnail_img === fileName ? '' : fileName;
    }

    const fileKey = (file) => {
      return file instanceof File ? file.name : file;
    };

    const onDragEnd = (event) => {
      // Update the product's photos array if necessary
      // product.value.photos = convertToFileNameArray(files.value);
      // product.value.added_images = getFileOnly()
    };

    return {
      product,
      isDragging,
      files,
      file,

      //methods
      onChange,
      dragleave,
      dragover,
      drop,
      remove,
      generateURL,
      isThumbnail,
      setThumbnail,
      fileKey,
      onDragEnd,
    }
  }
};
</script>

<style scoped>
.main {
    display: flex;
    flex-grow: 1;
    align-items: center;
    justify-content: center;
    text-align: center;
}

.dropzone-container {
    padding: 4rem;
    background: #f7fafc;
    border: 2px dashed;
    border-color: #9e9e9e;
    width: 100%;
}

.hidden-input {
    opacity: 0;
    overflow: hidden;
    position: absolute;
    width: 1px;
    height: 1px;
}

.file-label {
    font-size: 14px;
    display: block;
    cursor: pointer;
}

.preview-container {
    display: flex;
    margin-top: 2rem;
    flex-wrap: wrap;
}

.preview-card {
  display: flex;
  padding: 5px;
  margin-left: 5px;
  flex: 1 1 140px;
}

.preview-img {
  border-radius: 3px;
  width: 100%;
  position: relative;
}
.img-frame {
  background-color: rgba(0,0,0,.3);
  position: absolute;
  bottom: 0;
  height: 34px;
  line-height: 34px;
  backdrop-filter: saturate(180%) blur(20px);
  width: 100%;
  color: #fff;
  font-size: 12px;
  font-weight: 700;
  display: none;
}

.img-frame span {
  padding-left: 5px;
}

.preview-img:hover .img-frame {
  display: block;
}

.preview-img:hover .btn-tools {
  display: inline-block;
} 
.btn-tools {
  display: none;
}
.btn-tools:hover {
  color: #fff;
}

.draggable-item {
  cursor: grab;
}

.draggable-item:active {
  cursor: grabbing;
}
</style>
