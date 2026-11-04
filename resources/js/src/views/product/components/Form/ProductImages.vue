<template>
  <section>
    <!-- Product Images -->
    <b-card>
      <b-card-header class="flex-column align-items-start">
        <b-card-title>Product Images</b-card-title>
      </b-card-header>
      <b-card-body>

        <b-form-group
          label="Gallery Images"
          class="mb-2"
        >
          <b-row class="mt-1">
            <b-col v-for="(photo, index) in product.photos" :key="`photo`+index" sm="2">
              <upload-image
                :index="index"
                :img="photo"
                :editImage="updateGalleryImage"
                :removeImage="removeGalleryImage"
                :rules="{required:true}"
              ></upload-image>
            </b-col>

            <b-col sm="2">
              <div class="border-success w-100 h-100 rounded d-flex justify-content-center align-items-center"
                   style="min-height: 100px"
              >
                <b-button :id="`add`" v-ripple.400="'rgba(113, 102, 240, 0.15)'"
                          variant="outline-success"
                          class="btn-icon rounded-circle"
                          @click="addGalleryImage"
                >
                  <feather-icon
                    icon="PlusIcon"
                    class="cursor-pointer"
                    size="16"
                  />
                </b-button>
                <b-tooltip
                  title="Add"
                  class="cursor-pointer"
                  :target="`add`"
                />
              </div>
            </b-col>

          </b-row>
        </b-form-group>


        <!-- Thumbnail Image -->
        <b-form-group
          label="Thumbnail Image"
          class="mb-2"
        >
          <b-row class="mt-1">
            <b-col sm="2">
              <upload-image
                :img="product.thumbnail_img"
                :editImage="updateThumb"
                :rules="{required:true}"
              ></upload-image>
            </b-col>
          </b-row>
        </b-form-group>

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
import Ripple from "vue-ripple-directive";
import UploadImage from "@/views/product/components/Form/UploadImage";
import {inject} from "@vue/composition-api";

export default {
  name: "ProductImages",
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

    UploadImage,
  },
  directives: {
    'b-modal': VBModal,
    Ripple,
  },
  setup() {
    const product = inject('product');

    const addGalleryImage = () => {
      if (product.value.photos === null) {
        product.value.photos = [];
      }
      product.value.photos.push('');
    }

    const updateGalleryImage = (data, index) => {
      product.value.photos[index] = data;
    }

    const removeGalleryImage = (index) => {
      product.value.photos.splice(index, 1);
    }

    const updateThumb = (data) => {
      product.value.thumbnail_img = data;
    }

    return {
      product,
      updateThumb,
      updateGalleryImage,
      removeGalleryImage,
      addGalleryImage,
    };
  }
}
</script>

<style scoped>

</style>
