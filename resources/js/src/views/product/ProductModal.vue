<template>
  <section>
    <b-modal v-model="showModal" :title="modalTitle" @hidden="getData">
      <div class="mb-1">
        <span>{{ dataItem['name'] }}</span>
      </div>
      <!-- batch edit -->
      <div v-if="is_variant_product">
        <b-form class="price-batch-edit">
          <b-col sm="2" class="p-0">
            <span class="batch-editing">Batch Editing</span>
          </b-col>
          <b-col sm="5" class="input-update-for-all-item">
            <!-- batch edit original price -->
            <div v-if="is_price_update" class="input-editing">
              <ValidationObserver>
                <validation-provider #default="validationContext" name="Original Price" rules="integer_or_float|min_value:0"
                  ref="validationProvider">
                  <b-form-input type="number" v-model="input_price_item" id="apply_price_for_all_items"
                    @input="isEnabledButton" name="unit_price"
                    :state="getValidationState(validationContext)"></b-form-input>
                  <small>{{ validationContext.errors[0] }}</small>
                </validation-provider>
              </ValidationObserver>
            </div>
            <!-- end batch edit original price -->

            <!-- batch edit quantity -->
            <div v-if="is_stock_update" class="input-editing">
              <ValidationObserver>
                <validation-provider #default="validationContext" name="Stock" rules="integer|min_value:0"
                  ref="validationProvider">
                  <b-form-input type="number" v-model="input_stock_item" @input="isEnabledButton"
                    id="apply_stock_for_all_items" name="stock"
                    :state="getValidationState(validationContext)"></b-form-input>
                  <small>{{ validationContext.errors[0] }}</small>
                </validation-provider>
              </ValidationObserver>
            </div>
            <!-- end batch edit quantity -->

            <!-- batch edit discount -->
            <div v-if="is_discount_update" class="input-editing">
              <ValidationObserver>
                <validation-provider #default="validationContext" name="Discount"
                  :rules="`required|integer_or_float|min_value:0|discount_percent:${input_discount_type}|discount_amount:${input_discount_type}, ${minPrice}`"
                  ref="validationProvider">
                  <b-input-group>
                    <div class="d-flex">
                      <div class="w-75">
                        <b-form-input name="discount" type="number" v-model="input_discount_item" @input="isEnabledButton"
                          id="apply_discount_for_all_items" :state="getValidationState(validationContext)" trim
                          class="" />
                      </div>
                      <div class="w-25">
                        <b-form-select class="" variant="outline-primary" v-model="input_discount_type"
                          :options="taxTypeOptions"></b-form-select>
                      </div>
                    </div>
                  </b-input-group>
                  <small>{{ validationContext.errors[0] }}</small>
                </validation-provider>
              </ValidationObserver>
            </div>
            <!-- end batch edit discount -->
          </b-col>
          <b-col sm="5" class="btn-update-for-all-item">
            <b-button class="" variant="primary" @click="handleUpdateForAllItem" :disabled="disabled_btn_batch_edit">
              Applies To All Categories
            </b-button>
          </b-col>
        </b-form>
      </div>
      <validation-observer v-slot="{ handleSubmit }" ref="refFormObserver">
        <b-form ref="form_update_product" class="form-update-product" @submit.prevent="handleSubmit(handleUpdateType)">
          <!-- simple product -->
          <span v-if="!is_variant_product" class="flex">
            <div class="table-price-variant">
              <div v-if="!is_discount_update" class="row variant-title">
                <div class="col-sm-6">
                  <span class="item-header"> Item </span>
                </div>
                <div class="col-sm-6">
                  <span v-if="is_price_update" class="header-table"> Original Price </span>
                  <span v-if="is_stock_update" class="header-table"> Stock </span>
                </div>
              </div>
              <div v-else class="row variant-title">
                <div class="col-sm-4">
                  <span class="item-header"> Item </span>
                </div>
                <div class="col-sm-3 text-center">
                  <span class="header-table"> Original Price </span>
                </div>
                <div class="col-sm-5 text-center">
                  <span class="header-table"> Discount </span>
                </div>
              </div>
            </div>
            <div class="table-price-variant">
              <div v-if="!is_discount_update" class="row variant-content">
                <div class="col-sm-6">
                  <span> Sku </span>
                  <p class="item-sku">{{ dataItem['sku'] }}</p>
                </div>
                <div v-if="is_price_update" class="col-sm-6">
                  <ValidationObserver>
                    <validation-provider #default="validationContext" name="Original Price"
                      :rules="`required|integer_or_float|min_value:0|discount_min_value:${discount},${discount_type}`"
                      ref="validationProvider">
                      <b-form-input id="unit_price" v-model="price"
                        :state="getValidationState(validationContext)"></b-form-input>
                      <small>{{ validationContext.errors[0] }}</small>
                    </validation-provider>
                  </ValidationObserver>
                </div>
                <div v-if="is_stock_update" class="col-sm-6">
                  <ValidationObserver>
                    <validation-provider #default="validationContext" name="Stock" rules="required|integer|min_value:0"
                      ref="validationProvider">
                      <b-form-input type="number" id="stock" v-model="stock"
                        :state="getValidationState(validationContext)"></b-form-input>
                      <small>{{ validationContext.errors[0] }}</small>
                    </validation-provider>
                  </ValidationObserver>
                </div>
                <div v-if="is_discount_update" class="col-sm-6">
                  <ValidationObserver>
                    <validation-provider #default="validationContext" name="Discount" rules="required|integer|min_value:0"
                      ref="validationProvider">
                      <b-form-input id="discount" v-model="discount"
                        :state="getValidationState(validationContext)"></b-form-input>
                      <small>{{ validationContext.errors[0] }}</small>
                    </validation-provider>
                  </ValidationObserver>
                </div>
              </div>
              <div v-else class="row variant-content align-items-center">
                <div class="col-sm-4">
                  <span> Sku </span>
                  <p class="item-sku">{{ dataItem['sku'] }}</p>
                </div>
                <div class="col-sm-3 text-center">
                  <span class="item">{{ dataItem['display_price'] }}</span>
                </div>
                <div class="col-sm-5 pl-0">
                  <ValidationObserver>
                    <validation-provider #default="validationContext" name="Discount"
                      :rules="`required|integer_or_float|min_value:0|discount_percent:${discount_type}|discount_amount:${discount_type}, ${price}`"
                      ref="validationProvider">
                      <b-input-group>
                        <div class="d-flex">
                          <div class="w-75">
                            <b-form-input id="discount" v-model="discount"
                              :state="getValidationState(validationContext)" trim class="" />
                          </div>
                          <div class="w-25">
                            <b-form-select class="" variant="outline-primary" v-model="discount_type"
                              :options="taxTypeOptions"></b-form-select>
                          </div>
                        </div>
                      </b-input-group>
                      <small>{{ validationContext.errors[0] }}</small>
                    </validation-provider>
                  </ValidationObserver>
                </div>
              </div>
            </div>
          </span>
          <!-- end simple product -->

          <!-- variant product -->
          <span v-else class="flex">
            <div class="table-price-variant">
              <div v-if="!is_discount_update" class="row variant-title">
                <div class="col-sm-6">
                  <span class="item-header"> Variant </span>
                </div>
                <div class="col-sm-6">
                  <span v-if="is_price_update" class="header-table"> Original Price </span>
                  <span v-else class="header-table"> Stock </span>
                </div>
              </div>
              <div v-else class="row variant-title">
                <div class="col-sm-4">
                  <span class="item-header"> Variant </span>
                </div>
                <div class="col-sm-3 text-center">
                  <span class="header-table"> Original Price </span>
                </div>
                <div class="col-sm-5 text-center">
                  <span class="header-table"> Discount </span>
                </div>
              </div>
            </div>
            <div class="table-price-variant">
              <div v-if="!is_discount_update" class="variant-content">
                <div class="row m-0" v-for="(item, index) in dataItem.stocks" :key="item.id">
                  <div class="col-sm-6" :class="{ 'dart-line': index > 0 }">
                    <span class="item-content"> {{ item.variant }} </span>
                    <p class="item-sku"> Sku: {{ item.sku }}</p>
                  </div>
                  <div class="col-sm-6" :class="{ 'dart-line': index > 0 }">
                    <span v-if="is_price_update" class="content-table">
                      <div class="col-sm-12 pl-0">
                        <ValidationObserver>
                          <validation-provider #default="validationContext" :name="`Original Price ${item.variant}`"
                            :rules="`required|integer_or_float|min_value:0|discount_min_value:${item.discount},${item.discount_type}`"
                            :ref="`validationProvider_${item.id}`">
                            <b-form-input :ref="`price_${item.variant}`" :name="`price_${item.variant}`"
                                          v-model="item.price"
                                          :state="getValidationState(validationContext)">
                            </b-form-input>
                            <small>{{ validationContext.errors[0] }}</small>
                          </validation-provider>
                        </ValidationObserver>
                      </div>
                    </span>
                    <span v-else class="content-table">
                      <div class="col-sm-12 pl-0">
                        <ValidationObserver>
                          <validation-provider #default="validationContext" :name="`Stock ${item.variant}`"
                            rules="required|integer|min_value:0" :ref="`validationProvider_${item.id}`">
                            <b-form-input :ref="`stock_${item.variant}`" type="number" :name="`stock_${item.variant}`"
                                          v-model="item.qty"
                                          :state="getValidationState(validationContext)">
                            </b-form-input>
                            <small>{{ validationContext.errors[0] }}</small>
                          </validation-provider>
                        </ValidationObserver>
                      </div>
                    </span>
                  </div>
                </div>
              </div>
              <div v-else class="variant-content">
                <div class="row m-0" v-for="(item, index) in dataItem.stocks" :key="item.id">
                  <div class="col-sm-4" :class="{ 'dart-line': index > 0 }">
                    <span class="item-content"> {{ item.variant }} </span>
                    <p class="item-sku"> Sku: {{ item.sku }}</p>
                  </div>
                  <div class="col-sm-3 text-center pt-1" :class="{ 'dart-line': index > 0 }">
                    <span class="item">{{ item.display_price }}</span>
                  </div>
                  <div class="col-sm-5 pl-0" :class="{ 'dart-line': index > 0 }">
                    <ValidationObserver>
                      <validation-provider #default="validationContext" :name="`Discount ${item.variant}`"
                          :rules="`required|integer_or_float|min_value:0|discount_percent:${item.discount_type}|discount_amount:${item.discount_type}, ${item.price}`"
                          :ref="`validationProvider_${item.id}`">
                        <b-input-group>
                          <div class="d-flex">
                            <div class="w-75">
                              <b-form-input id="discount" v-model="item.discount"
                                            :state="getValidationState(validationContext)" trim
                                            class="" :ref="`discount_${item.variant}`" />
                            </div>
                            <div class="w-25">
                              <b-form-select :ref="`discount_type_${item.variant}`" class="" variant="outline-primary"
                                v-model="item.discount_type" :options="taxTypeOptions"></b-form-select>
                            </div>
                          </div>
                        </b-input-group>
                        <small>{{ validationContext.errors[0] }}</small>
                      </validation-provider>
                    </ValidationObserver>
                  </div>
                </div>
              </div>
            </div>
          </span>
          <!-- end variant product -->
          <b-button ref="submit_form" class="d-none" type="submit"></b-button>
        </b-form>
      </validation-observer>
      <template #modal-footer>
        <b-button variant="secondary" @click="handleCancel"> Cancel </b-button>
        <b-button variant="primary" @click="handleSubmitForm"> Save </b-button>
      </template>
    </b-modal>
  </section>
</template>

<script>
import {
  BCol,
  BForm,
  BModal,
  BButton,
  BFormInput,
  BInputGroup,
  BFormSelect
} from 'bootstrap-vue';
import { required, integer } from '@validations';
import ProductResource from "@/api/product";
import { ValidationProvider, ValidationObserver, extend } from 'vee-validate';
import formValidation from "@core/comp-functions/forms/form-validation";
import { min_value, max_value, double, integer_or_float, discount_type_percent, discount_type_amount } from '@/utils/more-validations';
import ToastificationContent from "@core/components/toastification/ToastificationContent";

const productApi = new ProductResource();

extend('discount_min_value', {
  validate: (value, {discount, discount_type}) => {
    if (discount === undefined || discount === null || discount === '' || discount == 0) {
      return value >= 0;
    }
    if (discount_type === 'amount' && value < parseFloat(discount)) {
      return false;
    }
    return true;
  },
  params: ['discount', 'discount_type'],
  message: (fieldName, params) => {
    const { discount: discount, discount_type: discount_type } = params;
    if (discount_type === 'amount') {
      return `The ${fieldName} field must be ${discount} or more`;
    } else {
      return `The ${fieldName} field must be 0 or more`;
    }
  },
})

export default {
  name: 'Modal',
  components: {
    BCol,
    BForm,
    BModal,
    BButton,
    BFormInput,
    BInputGroup,
    BFormSelect,
    ValidationObserver,
    ValidationProvider
  },
  setup() {
    const {
      getValidationState,
    } = formValidation(() => { })

    const taxTypeOptions = [
      { value: 'amount', text: '$' },
      { value: 'percent', text: '%' },
    ];

    return {
      getValidationState,
      taxTypeOptions
    };
  },
  data() {
    return {
      loading: false,
      data: [],
      dataItem: [],

      price: '',
      value: '',
      field: '',
      input_price_item: '',
      is_price_update: false,
      update_price_product: 'Original Price',

      //params get data
      params: null,
      published: null,
      out_of_stock: null,

      //modal
      modalTitle: 'Title',
      showModal: false,

      //update quantity
      stock: '',
      input_stock_item: '',
      is_variant_product: false,
      is_stock_update: false,
      update_stock_product: 'Stock',

      // update discount
      discount: '',
      discount_type: '',
      input_discount_item: '',
      input_discount_type: 'amount',
      is_discount_update: false,
      update_discount_product: "Discount",

      // validation
      disabled_btn_batch_edit: true,
      validationProviders: {},

      handleUpdateType: ''
    }
  },
  created() {

  },
  mounted() {
    this.$events.$on('get-modal-item', eventData => this.getModalItem(eventData));
    this.$events.$on('get-page-data', eventData => this.getPageData(eventData));
  },
  methods: {
    getModalItem(eventData) {
      this.data = [];
      this.minPrice = Infinity;
      const { data: data, typeUpdate: typeUpdate } = eventData;
      this.showModal = true;
      this.modalTitle = `Update ${typeUpdate}`

      this.dataItem = data;
      this.price = data.price;
      this.stock = data.stock;
      this.discount = data.discount;
      this.discount_type = data.discount_type;

      this.is_price_update = false;
      this.is_stock_update = false;
      this.is_discount_update = false;

      this.input_price_item = '';
      this.input_stock_item = '';
      this.input_discount_item = '';
      this.disabled_btn_batch_edit = true;

      if (typeUpdate === this.update_price_product) {
        this.is_price_update = true;
        this.handleUpdateType = this.handleUpdatePrice;
      }

      if (typeUpdate === this.update_discount_product) {
        this.is_discount_update = true;
        this.handleUpdateType = this.handleUpdateDiscount;
      }

      if (typeUpdate === this.update_stock_product) {
        this.is_stock_update = true;
        this.handleUpdateType = this.handleUpdateQuantity;
      }

      if (data.stocks.length) {
        this.is_variant_product = true;
        this.dataItem.stocks.forEach((item) => {
          if (item.price < this.minPrice) {
            this.minPrice = item.price;
          }
        })
      } else {
        this.is_variant_product = false;
      }
    },

    handleCancel() {
      this.showModal = false;
    },

    handleSubmitForm() {
      this.$refs.submit_form.click();
    },

    isEnabledButton() {
      if (this.input_price_item.length || this.input_stock_item.length || this.input_discount_item.length) {
        this.disabled_btn_batch_edit = false;
      } else {
        this.disabled_btn_batch_edit = true;
      }
    },

    getData() {
      this.$events.fire('get-list', this.params);
    },

    async handleUpdatePrice() {
      this.showModal = false;
      const id = this.dataItem.id;

      if (this.dataItem.stocks.length) {
        this.dataItem.stocks.forEach((item) => {
          const variant = item.variant;
          const value = this.$refs[`price_${item.variant}`][0].value;
          this.data.push({
            ['price']: value,
            ['variant']: variant
          })
        })
      } else {
        const value = this.price;
        this.data.push({
          ['price']: value,
        })
      }
      const { success, message } = await productApi.updateProductPrice(id, this.data);
      if (success) {
        this.success(this.$t('Update Price Success'));
      } else {
        this.error(this.$t('Update Price Error'), message);
      }
      this.getData();
    },

    async handleUpdateQuantity() {
      this.showModal = false;
      const id = this.dataItem.id;

      if (this.dataItem.stocks.length) {
        this.dataItem.stocks.forEach((item) => {
          const variant = item.variant;
          const value = this.$refs[`stock_${item.variant}`][0].value;
          this.data.push({
            ['quantity']: value,
            ['variant']: variant
          })
        })
      } else {
        const value = this.stock;
        this.data.push({
          ['quantity']: value
        })
      }
      const { success, message } = await productApi.updateProductStock(id, this.data);
      if (success) {
        this.success(this.$t('Update Stock Success'));
      } else {
        this.error(this.$t('Update Stock Error'), message);
      }
      this.getData();
    },

    async handleUpdateDiscount() {
      this.showModal = false;
      const id = this.dataItem.id;

      if (this.dataItem.stocks.length) {
        this.dataItem.stocks.forEach((item) => {
          const variant = item.variant;
          const value = this.$refs[`discount_${item.variant}`][0].value;
          const discount_type = this.$refs[`discount_type_${item.variant}`][0].value;
          this.data.push({
            ['variant']: variant,
            ['discount']: value,
            ['discount_type']: discount_type
          })
        })
      } else {
        const value = this.discount;
        const discount_type = this.discount_type
        this.data.push({
          ['discount']: value,
          ['discount_type']: discount_type
        })
      }

      const { success, message } = await productApi.updateProductDiscount(id, this.data);
      if (success) {
        this.success(this.$t('Update Discount Success'));
      } else {
        this.error(this.$t('Update Discount Error'), message);
      }
      this.getData();
    },

    handleUpdateForAllItem() {
      const checkError = this.$refs.validationProvider.errors;
      if (checkError.length) {
        return this.error(this.$t('Updated For All Error Entries'), 'Invalid value');
      }

      this.dataItem.stocks.forEach((item) => {
        if (this.is_price_update) {
          item.price = this.input_price_item;
        }

        if (this.is_stock_update) {
          item.qty = this.input_stock_item;
        }

        if (this.is_discount_update) {
          item.discount = this.input_discount_item;
          item.discount_type = this.input_discount_type;
        }
      });
    },

    getPageData(eventData) {
      this.params = eventData;
    },

    success(message) {
      this.$toast({
        component: ToastificationContent,
        props: {
          title: this.$t('Success'),
          icon: 'CheckIcon',
          text: message,
          variant: 'success',
        },
      },
        {
          position: 'bottom-center'
        })
    },

    error(title, message) {
      this.$toast({
        component: ToastificationContent,
        props: {
          title: title,
          icon: 'AlertCircleIcon',
          text: message,
          variant: 'danger',
        }
      },
        {
          position: 'bottom-center'
        })
    },
  }
}
</script>

<style lang="scss">
  .modal {
    padding: 0 !important;
  }

  .modal-dialog {
    max-width: 620px;
    margin-top: 15.625rem;
  }

  .price-batch-edit {
    padding: 1rem 0.5rem;
    display: flex;
    margin-bottom: 1rem;
    border-radius: 0.25rem;
    background: #f6f6f6;
    align-items: center;
    justify-content: space-around;
  }

  .product-non-variant {
    margin: 0;
    display: flex;
    width: 100%;
    align-items: center;
    justify-content: space-around;
    padding: 1rem 0.5rem;
    background: #f6f6f6;
    border-radius: 0.25rem;
  }

  .table-price-variant {
    display: flex;
    width: 100%;
    position: relative;
  }

  .variant-title {
    margin: 0;
    width: 100%;
    padding: 0.5rem 0;
    border: 1px solid #e5e5e5;
    border-radius: 4px;
    margin-bottom: 0;
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 0;
    background-color: #f6f6f6;
    border-bottom-color: #eee;
  }

  .variant-content {
    margin: 0px;
    padding-top: 0.5rem;
    border: 1px solid #e5e5e5;
    border-radius: 4px;
    margin-top: 0;
    border-top: 0;
    border-top-right-radius: 0;
    border-top-left-radius: 0;
    position: relative;
    -webkit-box-flex: 1;
    -ms-flex: 1;
    flex: 1;
    background-color: #fff;
    -webkit-transform: scale(1);
    transform: scale(1);
  }

  .dart-line {
    padding-top: 0.5rem;
    border-top: 1px solid;
    border-color: #e5e5e5;
  }

  .item-sku {
    font-size: 13px;
    opacity: 0.7;
    margin-bottom: 0.5rem;
  }

  .batch-editing {
    font-size: 13px;
  }

  .input-update-for-all-item {
    padding-right: 0rem;
    padding-left: 0.5rem;
  }

  .btn-update-for-all-item {
    padding-right: 0rem;
    padding-left: 0.5rem;
  }
</style>