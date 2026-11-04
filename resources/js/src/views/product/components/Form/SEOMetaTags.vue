<template>
  <section>
    <!-- SEO Meta Tags -->
    <b-card>
      <b-card-header class="flex-column align-items-start">
        <b-card-title>SEO Meta Tags</b-card-title>
      </b-card-header>
      <b-card-body>

        <b-form-group
            label="Meta Title"
            label-for="meta-title"
            class="mb-2"
        >
          <b-form-input
              id="meta-title"
              v-model="product.meta_title"
              trim
          />
        </b-form-group>

        <b-form-group
            label="Meta Description"
            label-for="meta-description"
            class="mb-2"
        >
          <b-form-textarea
              id="meta-description"
              placeholder="Description"
              v-model="product.meta_description"
              rows="3"
          />
        </b-form-group>

        <!-- Meta Image -->
        <b-form-group
            label="Meta Image"
            label-for="thumbnail-img"
            class="mb-2"
        >
          <b-row class="mt-1">
            <b-col sm="2">
              <upload-image
                  :img="product.meta_img"
                  :editImage="updateMetaImage"
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
  BCard,
  BCardBody,
  BCardHeader,
  BCardText,
  BCardTitle, BCol,
  BFormGroup,
  BFormInput,
  BFormTextarea, BRow,
  VBModal
} from "bootstrap-vue";
import Ripple from "vue-ripple-directive";

import UploadImage from "@/views/product/components/Form/UploadImage";
import {inject} from "@vue/composition-api";
import {useProduct} from "@/composables/product/useProduct";

export default {
  name: "SEOMetaTags",
  components: {
    UploadImage,

    // BSV
    BRow,
    BCol,
    BCard,
    BCardHeader,
    BCardTitle,
    BCardText,
    BCardBody,
    BFormGroup,
    BFormInput,
    BFormTextarea,
  },
  directives: {
    'b-modal': VBModal,
    Ripple,
  },
  setup () {
    const updateMetaImage = (data) => {
      product.value.meta_img = data;
    }

    const product = inject('product')

    return {
      product,
      updateMetaImage
    };
  }
}
</script>

<style scoped>

</style>
