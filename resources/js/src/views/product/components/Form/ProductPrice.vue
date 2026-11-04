<template>
  <section>
    <b-card>
      <b-card-header class="flex-column align-items-start">
        <b-card-title>Product price + stock</b-card-title>
      </b-card-header>
      <b-card-body>

        <validation-provider
            #default="validationContext"
            name="Unit price"
            rules="required|integer_or_float|min_value:1"
        >
          <b-form-group
              label="Unit price"
              label-for="unit-price"
              class="mb-2"
          >
            <b-form-input
                id="unit-price"
                v-model="product.unit_price"
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
            name="Discount"
            :rules="discountRule"
        >
          <b-form-group
              label="Discount"
              label-for="discount"
              class="mb-2"
          >
            <b-input-group>
              <b-form-input
                  id="discount"
                  v-model="product.discount"
                  :state="getValidationState(validationContext)"
                  trim
                  class="w-75"
              />
              <b-form-select variant="outline-primary" v-model="product.discount_type" :options="taxTypeOptions"></b-form-select>
              <b-form-invalid-feedback>
                {{ validationContext.errors[0] }}
              </b-form-invalid-feedback>
            </b-input-group>
          </b-form-group>
        </validation-provider>

        <validation-provider
            #default="validationContext"
            name="Quantity"
            rules="integer|min_value:0"
        >
          <b-form-group
              label="Quantity"
              label-for="quantity"
              class="mb-2"
          >
            <b-form-input
                id="quantity"
                v-model="product.current_stock"
                :state="getValidationState(validationContext)"
                trim
            />
            <b-form-invalid-feedback>
              {{ validationContext.errors[0] }}
            </b-form-invalid-feedback>
          </b-form-group>
        </validation-provider>

        <validation-provider
          name="SKU"
        >
          <b-form-group :label="$t('SKU')" label-for="sku" class="mb-2">
            <b-form-input
              id="sku"
              v-model="product.unit"
              :state="errors.unit ? false : null"
              trim
              data-error-field="unit"
            />
            <b-form-invalid-feedback>
              {{ errors.unit ? errors.unit[0] : '' }}
            </b-form-invalid-feedback>
          </b-form-group>
        </validation-provider>

        <validation-provider
          #default="validationContext"
          name="Minimum Purchase Qty"
          rules="required|integer|min_value:1"
        >
          <b-form-group
            label="Minimum Purchase Qty"
            label-for="min-qty"
            class="mb-2"
          >
            <b-form-input
              id="min-qty"
              v-model="product.min_qty"
              :state="getValidationState(validationContext)"
              trim
            />
            <b-form-invalid-feedback>
              {{ validationContext.errors[0] }}
            </b-form-invalid-feedback>
          </b-form-group>
        </validation-provider>

        <b-row >
          <b-col md="6">
            <validation-provider
              #default="validationContext"
              name="Max Qty"
              :rules="`integer|min_value:0|greater_than_min_qty:${product.min_qty}`"
            >
              <b-form-group label="Max Qty" label-for="max-qty" class="">
                <b-form-input
                  id="max-qty"
                  v-model="product.max_qty"
                  :state="getValidationState(validationContext)"
                  trim
                />
                <b-form-invalid-feedback>
                  {{ validationContext.errors[0] }}
                </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>

          </b-col>

          <b-col md="3" v-if="product.max_qty>0">
            <validation-provider
              #default="validationContext"
              name="Max Qty Start Date"
              :rules="`required|min_date:${currentDate}`"
            >
              <b-form-group
                label="Start Date"
                label-for="max-qty-start-date"
              >
                <b-form-datepicker
                  id="max-qty-start-date" :placeholder="$t('Start date')"
                  v-model="product.max_qty_start"
                  :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                  class="form-control"
                  locale="en-SG"
                  :reset-button="true"
                  :min="currentDate"
                  :state="getValidationState(validationContext)"
                />

                <b-form-invalid-feedback>
                  {{ validationContext.errors[0] }}
                </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>
          </b-col>


          <b-col md="3" v-if="product.max_qty>0">
            <validation-provider
              #default="validationContext"
              name="Max Qty End Date"
              :rules="`required|date_comparison:${product.max_qty_start}`"
            >
              <b-form-group
                label="End Date"
                label-for="max-qty-end-date"
              >
                <b-form-datepicker
                  id="max-qty-end-date" :placeholder="$t('End date')"
                  v-model="product.max_qty_end"
                  :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                  class="form-control"
                  locale="en-SG"
                  :reset-button="true"
                  :min="product.max_qty_start"
                  :state="getValidationState(validationContext)"
                />
                <b-form-invalid-feedback>
                  {{ validationContext.errors[0] }}
                </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>
          </b-col>
        </b-row>

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
  BCardTitle, BCol, BFormDatepicker,
  BFormGroup,
  BFormInput,
  BFormInvalidFeedback, BFormSelect, BInputGroup, BInputGroupAppend, BInputGroupPrepend, BRow
} from "bootstrap-vue";
import { required } from '@validations';
import {min_value, max_value, double, integer_or_float} from '@/utils/more-validations';
import { ValidationProvider, ValidationObserver } from 'vee-validate';
import formValidation from "@core/comp-functions/forms/form-validation";
import {computed, inject, reactive, watch} from "@vue/composition-api";

export default {
  name: "ProductPrice",
  components: {
    BFormDatepicker, BRow, BCol,
    // BSV
    BCard,
    BCardHeader,
    BCardTitle,
    BCardText,
    BCardBody,
    BFormGroup,
    BFormInput,
    BFormInvalidFeedback,
    BInputGroup,
    BInputGroupPrepend,
    BInputGroupAppend,
    BFormSelect,

    // Form Validation
    ValidationProvider,
    ValidationObserver,
  },
  props: {
    errors: {
      type: Object,
      default: () => ({}),
    },
  },
  setup (props,  { emit }) {
    const editorOption = {
      theme: 'snow',
    };
    const errors = {}
    const product = inject('product');

    const {
      getValidationState,
    } = formValidation(() => {});

    const taxTypeOptions = [
      { value: 'amount', text: '$' },
      { value: 'percent', text: '%'},
    ];

    const discountRule = computed(() => {
      let rule = {
        integer_or_float: true,
        min_value: 0,
      };
      if (product.value.discount_type === 'amount') {
        rule.max_value = product.value.unit_price;
      } else if (product.value.discount_type === 'percent') {
        rule.max_value = 100;
      }
      return rule;
    });

    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');
    const currentDate = `${year}-${month}-${day}`;

    watch(
      () => product.value.unit,
      (newVal) => {
        if (props.errors.unit) {
          emit("clear-error", "unit");
        }
      }
    );

    return {
      discountRule,

      getValidationState,
      editorOption,
      product,
      taxTypeOptions,

      currentDate,
      // errros
    };
  },
}
</script>
