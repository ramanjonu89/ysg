<template>
  <section class="invoice-add-wrapper">
    <b-row>
      <b-col cols="12">
        <validation-observer ref="refFormObserver">
          <b-form
            class="list-view"
            @submit.prevent="onSubmit"
          >
            <b-overlay
              :show="loading"
              rounded="sm"
              class="custom-spinner-position"
            >
              <!-- Product Images -->
              <product-images-multiple-upload />

              <!-- Product Information -->
              <product-information :refFormObserver="refFormObserver"/>

              <!-- Product Description -->
              <product-description />

              <!-- Product Variant -->
              <product-variant 
                :errors="errors" 
                @clear-error="handleClearError" 
              />

              <!-- Product price + stock -->
              <product-price
                v-if="product.choice_options.length === 0"
                :errors="errors"
                @clear-error="handleClearError" 
              />
              <!-- SEO Meta Tags -->
              <s-e-o-meta-tags />
            </b-overlay>

              <b-button
                variant="primary"
                type="submit"
              >
                <feather-icon
                  icon="CheckCircleIcon"
                  class="mr-50"
                />
                Save
              </b-button>

              <b-button v-ripple.400="'rgba(186, 191, 199, 0.15)'" variant="secondary"
                        @click="handleCancel">
                <feather-icon
                  icon="XCircleIcon"
                  class="mr-50"
                />
                Cancel
              </b-button>
            </b-form>
          </validation-observer>
      </b-col>

      <b-col cols="3"></b-col>
    </b-row>
  </section>
</template>

<script>
import {
  BRow,
  BCol,
  BForm,
  BOverlay,
  BButton,
  BTooltip,
  VBModal,
} from "bootstrap-vue";
import { required, integer } from "@validations";
import { ValidationProvider, ValidationObserver } from "vee-validate";
import formValidation from "@core/comp-functions/forms/form-validation";

import ProductImagesMultipleUpload from "@/views/product/components/Form/ProductImagesMultipleUpload";
import SEOMetaTags from "@/views/product/components/Form/SEOMetaTags";
import ProductDescription from "@/views/product/components/Form/ProductDescription";
import ProductPrice from "@/views/product/components/Form/ProductPrice";
import ProductInformation from "@/views/product/components/Form/ProductInformation";
import ProductVariant from "@/views/product/components/Form/ProductVariant";
import Ripple from "vue-ripple-directive";
import { useRouter } from "@core/utils/utils";
import { useProduct } from "@/composables/product/useProduct";
import { inject, ref } from "@vue/composition-api";

export default {
  name: "ProductForm",
  components: {
    ProductImagesMultipleUpload,
    ProductDescription,
    SEOMetaTags,
    ProductPrice,
    ProductInformation,
    ProductVariant,

    // BSV
    BOverlay,
    BForm,
    BButton,
    BTooltip,
    BRow,
    BCol,

    // Form Validation
    ValidationProvider,
    ValidationObserver,
  },
  directives: {
    'b-modal': VBModal,
    Ripple,
  },
  setup() {
    const {router} = useRouter();
    const {loading, saveProduct, updateProduct} = useProduct();
    const product = inject('product')
    const errors = ref({});

    const {
        refFormObserver,
      } = formValidation(() => {
    });

    const editorOption = {
      theme: 'snow',
    };

    const handleSave = async () => {
      const response =
        product.value.id === undefined
          ? await saveProduct(product.value)
          : await updateProduct(product.value.id, product.value);

      if (response.error && response.errors) {
        errors.value = response.errors;
        scrollToFirstError();
        return false;
      }

      router.push({ name: "products" });
    };

    const onSubmit = async () => {
      loading.value = true
      const isValid = await refFormObserver.value.validate();
      if (isValid) {
        await handleSave();
      } else {
        loading.value = false
        scrollToFirstError();
      }
    };

    const handleCancel = () => {
      router.push({ name: "products" });
    };

    const scrollToFirstError = () => {
      if (refFormObserver.value) {
        const invalidEls = refFormObserver.value.$el.querySelectorAll(
          ".is-invalid, [aria-invalid='true']"
        );

        if (invalidEls.length > 0) {
          invalidEls[0].scrollIntoView({ behavior: "smooth", block: "center" });
          invalidEls[0].focus();
          return;
        }
      }

      if (errors.value && Object.keys(errors.value).length > 0) {
        const firstErrorField = Object.keys(errors.value)[0];
        const selector = `[data-error-field="${firstErrorField}"]`;
        const el = document.querySelector(selector);
        if (el) {
          el.scrollIntoView({ behavior: "smooth", block: "center" });
          el.focus();
        }
      }
    };

    return {
      product,
      refFormObserver,

      // Form Validators
      required,
      integer,

      loading,
      handleSave,
      onSubmit,
      handleCancel,
      editorOption,
      errors,
    };
  },

  methods: {
    handleClearError(field) {
      console.log(field);
      if (this.errors[field]) {
        delete this.errors[field];
      }
    },
  },
}
</script>

<style lang="scss">
@import '~@core/scss/vue/libs/quill.scss';

.custom-spinner-position .b-overlay .position-absolute:not(.bg-light) {
  top: 90% !important;
}
</style>
