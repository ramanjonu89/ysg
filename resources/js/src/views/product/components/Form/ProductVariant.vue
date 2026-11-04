<template>
  <section>
    <b-card>
      <b-card-header class="flex-column align-items-start">
        <b-card-title>Product Variant</b-card-title>
      </b-card-header>
      <b-card-body>
        <b-row
          v-for="(item, index) in product.choice_options"
          :key="index"
        >
          <b-col class="d-flex border rounded p-0 mb-2">
            <b-row class="row flex-grow-1 p-2">
              <b-col>
                <b-form-group
                  :label="$t('Attribute')"
                  class="mb-2"
                >
                  <v-select v-model="item.attribute" :options="allowedAttributes"
                            label="text" />
                </b-form-group>
                <b-form-group
                  :label="$t('Variant options')"
                  class="mb-2"
                >
                  <b-form-tags
                    v-model="item.values"
                    :placeholder="$t('Add options ...')"
                  />
                </b-form-group>
              </b-col>
            </b-row>
            <div class="d-flex flex-column justify-content-between border-left py-50 px-25">
              <feather-icon
                size="16"
                icon="XIcon"
                class="cursor-pointer"
                @click="removeVariant(index)"
              />
            </div>
          </b-col>
        </b-row>
        <b-row>
          <b-button
            v-ripple.400="'rgba(255, 255, 255, 0.15)'"
            variant="primary"
            @click="addMoreVariant"
            v-if="product.choice_options.length < 2"
          >
            <feather-icon
              icon="PlusIcon"
              class="mr-25"
            />
            <span>{{ $t('Add variant') }}</span>
          </b-button>
        </b-row>
        <b-row v-if="product.choice_options.length > 0" class="mt-2">
          <b-table-simple>
            <b-thead>
              <b-tr>
                <b-td>{{ $t('Image') }}</b-td>
                <b-td>{{ $t('Variant') }}</b-td>
                <b-td>{{ $t('SKU') }}</b-td>
                <b-td>{{ $t('Price') }}</b-td>
                <b-td>{{ $t('Discount') }}</b-td>
                <b-td>{{ $t('Quantity') }}</b-td>
              </b-tr>
            </b-thead>

            <b-tbody>
              <b-tr v-for="(item, index) in product.stocks" :key="item.variant">
                <td style="width: 10%;">
                  <upload-image
                    :img="item.image"
                    :editImage="updateStockImage"
                    :currentObject="item"
                  ></upload-image>
                  <b-button
                    v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                    variant="danger"
                    @click="() => {item.image = ''}"
                    v-if="item.image"
                  >Remove Image</b-button>
                </td>
                <td>{{ item.variant }}</td>
                <td>
                  <b-form-input
                    :placeholder="$t('SKU')"
                    v-model="item.sku"
                    :value="item.sku"
                    :state="errors[`stocks.${index}.sku`] ? false : null"
                    :data-error-field="`stocks.${index}.sku`"
                  />
                  <small class="text-danger">{{ errors[`stocks.${index}.sku`] ? errors[`stocks.${index}.sku`][0] : '' }}</small>
                </td>
                <td>
                  <validation-provider
                    #default="validationContext"
                    :vid="`price-${item.variant}`"
                    name="Price"
                    rules="required|integer_or_float|min_value:0"
                  >
                    <b-form-input
                      :id="`price-${item.variant}`"
                      :placeholder="$t('Price')"
                      v-model="item.price"
                      :state="getValidationState(validationContext)"
                    />
                    <small class="text-danger">{{ validationContext.errors[0] }}</small>
                  </validation-provider>
                </td>
                <td>
                  <validation-provider
                      #default="validationContext"
                      :vid="`discount-${item.variant}`"
                      name="Discount"
                      :rules="`integer_or_float|min_value:0|discount_percent:${item.discount_type}|discount_amount:${item.discount_type}, ${item.price}`"
                  >
                      <b-input-group>
                        <div class="d-flex">
                          <div class="w-75">
                            <b-form-input
                              :id="`discount-${item.variant}`"
                              v-model="item.discount"
                              :value="item.discount"
                              :state="getValidationState(validationContext)"
                            />
                          </div>
                          <div class="w-25">
                            <b-form-select class ="" variant="outline-primary" v-model="item.discount_type" :options="taxTypeOptions"></b-form-select>
                          </div>
                        </div>
                      </b-input-group>
                      <small class="text-danger">{{ validationContext.errors[0] }}</small>
                  </validation-provider>
                </td>
                <td>
                  <validation-provider
                    #default="validationContext"
                    :vid="`quantity-${item.variant}`"
                    name="Quantity"
                    rules="integer|min_value:0"
                  >
                    <b-form-input
                      :id="`quantity-${item.variant}`"
                      :placeholder="$t('Quantity')"
                      v-model="item.qty"
                      :state="getValidationState(validationContext)"
                    />
                    <small class="text-danger">{{ validationContext.errors[0] }}</small>
                  </validation-provider>
                </td>
              </b-tr>
            </b-tbody>
          </b-table-simple>
        </b-row>

        <b-row v-if="product.choice_options.length">
          <b-col md="6" class="mt-1">
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
          </b-col>
        </b-row>

        <b-row v-if="product.choice_options.length">
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

          <b-col md="3" v-if="product.choice_options.length&&product.max_qty>0">
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


          <b-col md="3" v-if="product.choice_options.length&&product.max_qty>0">
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
  BButton,
  BRow,
  BCol,
  BCard,
  BCardBody,
  BCardHeader,
  BCardText,
  BCardTitle,
  BFormGroup,
  BFormInput,
  BFormTags,
  BJumbotron,
  BTableSimple,
  BThead,
  BTbody,
  BTr,
  BTd,
  BFormInvalidFeedback,
  BFormSelect,
  BInputGroup,
  BFormDatepicker
} from "bootstrap-vue";
import { ValidationProvider, ValidationObserver, extend } from 'vee-validate';
import VeeValidate from 'vee-validate';
import { required, integer } from '@validations';
import {min_value, max_value, integer_or_float, discount_type_percent, discount_type_amount} from '@/utils/more-validations';

import {computed, inject, ref, watch, watchEffect} from "@vue/composition-api";
import UploadImage from "@/views/product/components/Form/UploadImage";
import {useProduct} from "@/composables/product/useProduct";
import formValidation from "@core/comp-functions/forms/form-validation";
import vSelect from 'vue-select'
import Ripple from 'vue-ripple-directive'
import Token from "../../../../utils/token";

extend('greater_than_min_qty', {
  validate(value, args) {
    if (value === undefined || value === null || value === '' || value == 0) {
      return true;
    }
    return parseInt(value) >= parseInt(args.compare);
  },
  params: ['compare'], // Define a parameter for the rule
  message: 'Max Qty must be greater than Min Qty.', // Custom error message
});

extend('date_comparison', {
  validate(value, args) {
    const start_date = args.start_date;
    if (!start_date || start_date == 'undefined') return true; // If "Start Date" is not set, no comparison needed
    return new Date(value) >= new Date(start_date);
  },
  params: ['start_date'],
  message: 'The End Date must be greater than the Start Date.',
});

extend('min_date', {
  validate(value, args) {
    const current_date = args.current_date;
    return new Date(value) >= new Date(current_date);
  },
  params: ['current_date'],
  message: 'Start date must be greater than or equal to current date.',
});

export default {
  name: "ProductVariant",
  components: {
    // BSV
    BRow,
    BCol,
    BButton,
    BCard,
    BCardHeader,
    BCardTitle,
    BCardText,
    BCardBody,
    BFormGroup,
    BFormInput,
    BFormTags,
    BJumbotron,
    BTableSimple,
    BFormInvalidFeedback,
    BThead,
    BTbody,
    BTr,
    BTd,
    // others
    vSelect,
    BFormSelect,
    BInputGroup,
    BFormDatepicker,
    UploadImage,

    // Form Validation
    ValidationProvider,
    ValidationObserver,
  },
  directives: {
    Ripple,
  },
  props: {
    errors: {
      type: Object,
      default: () => ({}),
    },
  },
  setup(props, { emit }) {
    const userData = Token.getUser();
    const {allowedAttributes, fetchAllowedAttributes} = useProduct();
    const product = inject('product')
    const option = ref('');

    const input1 = ref(0);
    const input2 = ref(0);

    const selectedValues = computed(() => {
      if (product.value.choice_options.length === 1) {
        return product.value.choice_options[0].values;
      }
      if (product.value.choice_options.length === 2) {
        const result = [];
        product.value.choice_options[0].values.forEach(item => {
          product.value.choice_options[1].values.forEach(item2 => {
            result.push(item + '___' + item2);
          })
        })

        return result;
      }

      return [];
    })

    if (userData.user_type === 'staff') {
      watchEffect(() => {
        if (product.value.id) {
          fetchAllowedAttributes(product.value.id);
        }
      });
    } else {
      fetchAllowedAttributes();
    }

    watch(
      selectedValues,
      (val) => {
        val.forEach(item => {
          const found = product.value.stocks.find(stock => stock.variant === item);
          if (found === undefined) {
            product.value.stocks.push(
              {
                variant: item,
                price: product.value.unit_price,
                qty: product.value.current_stock,
                discount: 0,
                discount_type: product.value.discount_type,
                image: null
              }
            )
          }
        })

        const removeItems = [];

        product.value.stocks.forEach((stock, index) => {
          if (!val.includes(stock.variant)) {
            removeItems.push(index);
          }
        })

        removeItems.reverse().forEach(item => {
          product.value.stocks.splice(item, 1);
        })
      },
    )

    const addMoreVariant = () => {
      const variant = {
        attribute: {},
        values: []
      }
      product.value.stocks = [];
      product.value.choice_options.push(variant)
    }

    const removeVariant = (index) => {
      product.value.stocks = [];
      product.value.choice_options.splice(index, 1)
    }

    const {
      getValidationState,
    } = formValidation(() => {});

    const taxTypeOptions = [
      { value: 'amount', text: '$' },
      { value: 'percent', text: '%'},
    ];

    const getDiscountRule = (item) => {
      let rule = {
        integer_or_float: true,
        min_value: 0,
      };
      if (item.discount_type === 'amount') {
        rule.max_value = item.price;
      } else if (item.discount_type === 'percent') {
        rule.max_value = 100;
      }
      return rule;
    }

    const getMaxQtyRule = computed(() => {
      return {
        integer: true,
        min_value : product.value.min_qty,
      };
    });

    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');
    const currentDate = `${year}-${month}-${day}`;

    product.value.max_qty_start = currentDate;
    product.value.max_qty_end = currentDate;

    function updateStockImage(base64, index, currentObject) {
      currentObject.image = base64;
    }

    watch(
      () => product.value.stocks.map(s => s.sku),
      (newSkus, oldSkus) => {
        newSkus.forEach((sku, index) => {
          const field = `stocks.${index}.sku`;
          if (sku !== oldSkus[index] && props.errors[field]) {
            emit("clear-error", field);
          }
        });
      }
    );

    return {
      product,
      option,
      addMoreVariant,
      removeVariant,
      allowedAttributes,
      taxTypeOptions,
      getValidationState,

      getDiscountRule,

      getMaxQtyRule,

      currentDate,

      input1,
      input2,

      updateStockImage,
    };
  },
}
</script>

<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>

