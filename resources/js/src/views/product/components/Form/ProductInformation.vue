<template>
  <section>
    <!-- Product Information -->
    <b-card>
      <b-card-header class="flex-column align-items-start">
        <b-card-title>Product Information</b-card-title>
      </b-card-header>
      <b-card-body>
        <b-form-group label="Published" label-for="published" class="mb-2">
          <b-form-checkbox id="published" v-model="product.published" switch />
        </b-form-group>

        <validation-provider
          #default="validationContext"
          name="Product Name"
          rules="required|max:200"
        >
          <b-form-group label="Product Name" label-for="name" class="mb-2">
            <b-form-input
              id="name"
              v-model="product.name"
              :state="getValidationState(validationContext)"
              trim
            />
            <b-form-invalid-feedback>
              {{ validationContext.errors[0] }}
            </b-form-invalid-feedback>
          </b-form-group>
        </validation-provider>

        <validation-provider
          #default="validationContext"
          name="Category"
          rules="required"
        >
          <b-form-group label="Category" label-for="category_id">
            <b-form-select
              id="category_id"
              v-model="product.category_id"
              :options="categoryTree"
              class="mt-1"
              :state="getValidationState(validationContext)"
            />
            <b-form-invalid-feedback>
              {{ validationContext.errors[0] }}
            </b-form-invalid-feedback>
          </b-form-group>
        </validation-provider>

        <div v-if="!isEmptyObj(subCategories)">
          <b-form-group label="Sub Category" label-for="subcategory_id">
            <b-form-select
              id="subcategory_id"
              v-model="product.subcategory_id"
              :options="subCategories"
              class="mt-1"
            />
          </b-form-group>
        </div>

        <div v-if="!isEmptyObj(subsubCategories)">
          <validation-provider
            #default="validationContext"
            name="Sub Sub Category"
          >
            <b-form-group
              label="Sub Sub Category"
              label-for="subsubcategory_id"
            >
              <b-form-select
                id="subsubcategory_id"
                v-model="product.subsubcategory_id"
                :options="subsubCategories"
                class="mt-1"
                :state="getValidationState(validationContext)"
              />
              <b-form-invalid-feedback>
                {{ validationContext.errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>
        </div>

        <validation-provider
          #default="validationContext"
          name="Brand"
          rules="required"
        >
          <b-form-group
            label="Brand"
            label-for="brand"
          >
            <b-form-select
              id="brand"
              v-model="product.brand_id"
              :options="brandOptions"
              class="mt-1"
              :state="getValidationState(validationContext)"
            />
            <b-form-invalid-feedback>
              {{ validationContext.errors[0] }}
            </b-form-invalid-feedback>
          </b-form-group>
        </validation-provider>
      </b-card-body>
    </b-card>
  </section>
</template>

<script>
import { ValidationObserver, ValidationProvider } from 'vee-validate'
import {
  BCard,
  BCardBody,
  BCardHeader,
  BCardText,
  BCardTitle,
  BCol,
  BFormCheckbox,
  BFormGroup,
  BFormInput,
  BFormInvalidFeedback,
  BFormSelect,
  BRow,
} from 'bootstrap-vue'
import formValidation from '@core/comp-functions/forms/form-validation'
import { inject, watch } from '@vue/composition-api'
import { useCategory } from '@/composables/category/useCategory'
import { useBrand } from '@/composables/brand/useBrand'
import { isEmptyObj } from '@/utils/helper'

export default {
  name: 'ProductInformation',
  components: {
    // BSV
    BCard,
    BCardHeader,
    BCardTitle,
    BCardText,
    BCardBody,
    BRow,
    BCol,
    BFormGroup,
    BFormInput,
    BFormCheckbox,
    BFormSelect,
    BFormInvalidFeedback,

    // Form Validation
    ValidationProvider,
    ValidationObserver,
  },
  props: {
    refFormObserver: Object,
  },

  setup() {
    const { getBrandOptions, brandOptions } = useBrand()
    const {
      getCategoryTree,
      subCategories,
      subsubCategories,
      categoryTree,
      loadingTree,
      hasSubCategories,
      hasSubsubCategories,
    } = useCategory()

    getCategoryTree()
    getBrandOptions()

    const product = inject('product')

    const { getValidationState } = formValidation(() => {})

    return {
      product,
      brandOptions,
      categoryTree,
      subCategories,
      subsubCategories,
      getValidationState,
      isEmptyObj,
    }
  },
}
</script>
