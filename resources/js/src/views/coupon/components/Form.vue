<template>
  <b-card-code
    :title="isEditing ? $t('Edit coupon ') + coupon.code : $t('Add new coupon')"
  >
    <validation-observer v-slot="{ handleSubmit }" ref="formRef">
      <b-form @submit.prevent="handleSubmit(onSubmit)">
        <b-row>
          <b-col cols="12">
            <b-form-group label-cols-md="4">
              <b-form-checkbox
                v-model="coupon.is_active"
                name="is_active"
                switch
                inline
              >
                {{ $t('Active') }}
              </b-form-checkbox>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label-cols-md="4">
              <b-form-checkbox
                v-model="coupon.only_once"
                name="only_once"
                switch
                inline
              >
                {{ $t('One use per customer') }}
              </b-form-checkbox>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group
              :label="$t('Coupon title')"
              label-for="coupon-title"
              label-cols-md="4"
            >
              <validation-provider
                #default="{ errors }"
                :name="$t('Coupon title')"
                rules="required"
                vid="title"
              >
                <b-form-input
                  id="coupon-title"
                  v-model="coupon.title"
                  :placeholder="$t('Coupon title')"
                />
                <small class="text-danger">{{ errors[0] }}</small>
              </validation-provider>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group
              :label="$t('Coupon code')"
              label-for="coupon-code"
              label-cols-md="4"
            >
              <validation-provider
                #default="{ errors }"
                :name="$t('Coupon code')"
                rules="required|max:15"
                vid="code"
              >
                <b-input-group
                  :prepend="sellerCode"
                  class="input-group-merge"
                >
                  <b-form-input
                    id="coupon-code"
                    v-model="code"
                    @input="code=code.toUpperCase()"
                    :placeholder="$t('Coupon code')"
                    maxlength="15"
                  />
                </b-input-group>
                <b-form-text>
                  {{ $t('Your coupon code: ') }}{{ coupon.code }}
                </b-form-text>
                <small class="text-danger">{{ errors[0] }}</small>
                <p v-if="formError.code">
                  <small class="text-danger">{{ formError.code[0] }}</small>
                </p>
              </validation-provider>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group
              :label="$t('Description')"
              label-for="description"
              label-cols-md="4"
            >
              <b-form-textarea
                id="description"
                v-model="coupon.description"
                :placeholder="$t('Description')"
              />
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group
              :label="$t('Visibility')"
              label-for="visibility"
              label-cols-md="4"
            >
              <b-form-radio
                v-model="coupon.published"
                :value="true"
                class="custom-control-primary d-inline"
              >
                {{ $t('Publish') }}
                <feather-icon
                  icon="InfoIcon"
                  size="20"
                  v-b-tooltip.hover.top="'This voucher will display on Product detail page, Cart page'"
                  class="text-muted cursor-pointer"
                />
              </b-form-radio>
              <b-form-radio
                v-model="coupon.published"
                :value="false"
                class="custom-control-primary d-inline ml-2"
              >
                {{ $t('Private') }}
                <feather-icon
                  icon="InfoIcon"
                  size="20"
                  v-b-tooltip.hover.top="'This voucher will not display anywhere, user can only use this voucher by code'"
                  class="text-muted cursor-pointer"
                />
              </b-form-radio>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group
              :label="$t('No. of Coupon')"
              label-for="no-of-coupon"
              label-cols-md="4"
            >
              <validation-provider
                #default="{ errors }"
                :name="$t('No. of Coupon')"
                rules="min_value:0"
              >
                <b-form-input
                  id="no-of-coupon"
                  type="number"
                  v-model="coupon.details.max_coupon"
                  :placeholder="$t('No. of Coupon')"
                />
                <small class="text-danger">{{ errors[0] }}</small>
              </validation-provider>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group
              :label="$t('Minimum Cart value')"
              label-for="min-value"
              label-cols-md="4"
            >
              <validation-provider
                #default="{ errors }"
                :name="$t('Minimum Cart value')"
                rules="min_value:0"
              >
                <b-form-input
                  id="min-value"
                  type="number"
                  v-model="coupon.details.min_buy"
                  :placeholder="$t('Minimum Cart value')"
                />
                <small class="text-danger">{{ errors[0] }}</small>
              </validation-provider>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group
              :label="$t('Discount')"
              label-for="discount"
              label-cols-md="4"
            >
              <validation-provider
                #default="{ errors }"
                :name="$t('Discount')"
                :rules="{required: true, min_value: 0, discount_percent: [coupon.discount_type]}"
                vid="discount"
              >
                <b-input-group>
                  <b-form-input id="discount" v-model="coupon.discount" :placeholder="$t('Discount')"/>
                  <b-input-group-append>
                    <b-form-select class="rounded-right rounded-left-0" v-model="coupon.discount_type"
                                   :options="discount_type_options"></b-form-select>
                  </b-input-group-append>
                </b-input-group>
                <small class="text-danger">{{ errors[0] }}</small>
              </validation-provider>
            </b-form-group>
          </b-col>
          <b-col cols="12" v-if="coupon.discount_type === 'percent'">
            <b-form-group
              :label="$t('Maximum discount amount')"
              label-for="max-value"
              label-cols-md="4"
            >
              <validation-provider
                #default="{ errors }"
                :name="$t('Maximum discount amount')"
                rules="min_value:0"
              >
                <b-input-group>
                  <b-form-input
                    id="max-value"
                    type="number"
                    v-model="coupon.details.max_discount"
                    :disabled="unlimited"
                    :placeholder="$t('Maximum discount amount')"
                  />
                  <b-input-group-append is-text>
                    <b-form-checkbox
                      name="checkbox-input"
                      class="mr-1"
                      v-model="unlimited"
                    >
                      <span class="ml-1 cursor-pointer">{{ $t('Unlimited') }}</span>
                    </b-form-checkbox>
                  </b-input-group-append>
                </b-input-group>
                <small class="text-danger">{{ errors[0] }}</small>
              </validation-provider>

            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group
              :label="$t('Date')"
              label-for="start-date"
              label-cols-md="4"
            >
              <b-input-group>
                <div class="col p-0">
                  <b-form-datepicker
                    id="start-date" :placeholder="$t('Start date')"
                    v-model="coupon.start_date"
                    class="form-control"
                    :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                    locale="en-SG"
                  />
                </div>
                <b-input-group-append class="col p-0">
                  <b-form-datepicker
                    id="end-date" :placeholder="$t('End date')"
                    v-model="coupon.end_date"
                    class="form-control"
                    :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                    locale="en-SG"
                  />
                </b-input-group-append>
              </b-input-group>
              <validation-provider
                #default="{ errors }"
                :name="$t('Start date')"
                vid="start_date"
              >
                <p class="text-danger small mb-0">{{ errors[0] }}</p>
              </validation-provider>
              <validation-provider
                #default="{ errors }"
                :name="$t('End date')"
                vid="end_date"
              >
                <p class="text-danger small mb-0">{{ errors[0] }}</p>
              </validation-provider>
            </b-form-group>
          </b-col>

          <b-col cols="12">
            <b-form-group
              :label="$t('Apply to')"
              label-for="type"
              label-cols-md="4"
            >
              <b-form-radio
                v-model="coupon.type"
                value="cart_base"
                class="custom-control-primary d-inline"
              >
                {{ $t('All products') }}
              </b-form-radio>
              <b-form-radio
                v-model="coupon.type"
                value="product_base"
                class="custom-control-primary d-inline ml-2"
              >
                {{ $t('Selected products') }}
              </b-form-radio>
            </b-form-group>
          </b-col>

          <b-col cols="12" v-if="coupon.type === 'product_base'">
            <b-form-group
              :label="$t('Choose products')"
              label-for="type"
              label-cols-md="4"
            >
              <div class="text-right">
                <b-button
                  id="toggle-btn"
                  v-ripple.400="'rgba(113, 102, 240, 0.15)'"
                  v-b-modal.modal-product-list
                  variant="primary"
                >
                  {{ $t('+ Add product') }}
                </b-button>
              </div>
            </b-form-group>
          </b-col>

          <b-col cols="12" v-if="coupon.type === 'product_base'">
            <table class="table b-table mt-2 table-hover table-sm">
              <thead>
                <tr>
                  <th width="65%">Product Name</th>
                  <th width="15%">Price</th>
                  <th width="10%">Stock</th>
                  <th width="10%">Action</th>
                </tr>
              </thead>
            </table>

            <b-table-simple small hover class="mt-2 mb-5" v-if="coupon.details.products.length > 0" v-for="(product, key) in groupedItems" :key="key">
              <b-thead head-variant="light">
                <b-tr>
                  <b-th width="65%">
                    <b-img :width="50" thumbnail fluid :src="`${product.product.thumbnail_img}`"
                        :alt="`Image ${product.product.product_name}`"></b-img>
                    {{ product.product.product_name }}</b-th>
                  <b-th width="15%"></b-th>
                  <b-th width="10%"></b-th>
                  <b-th width="10%"></b-th>
                </b-tr>
              </b-thead>

              <b-tbody v-if="product.variants.length > 0">
                <b-tr v-for="item in product.variants" :key="item.id">
                  <b-td>{{ item.variant_name }}</b-td>
                  <b-td>{{ formatPrice(item.price) }}</b-td>
                  <b-td>{{ item.stock }}</b-td>
                  <b-td>
                    <b-button
                      v-ripple.400="'rgba(234, 84, 85, 0.15)'"
                      variant="flat-danger"
                      class="btn-icon rounded-circle"
                      @click="removeProduct(item.id)"
                    >
                      <feather-icon icon="TrashIcon"/>
                    </b-button>
                  </b-td>
                </b-tr>
              </b-tbody>

              <b-tbody v-else>
                <b-tr>
                  <b-td>-</b-td>
                  <b-td>{{ formatPrice(product.product.price) }}</b-td>
                  <b-td>{{ product.product.stock }}</b-td>
                  <b-td>
                    <b-button
                      v-ripple.400="'rgba(234, 84, 85, 0.15)'"
                      variant="flat-danger"
                      class="btn-icon rounded-circle"
                      @click="removeProduct(product.product.id)"
                    >
                      <feather-icon icon="TrashIcon"/>
                    </b-button>
                  </b-td>
                </b-tr>
              </b-tbody>
            </b-table-simple>
          </b-col>

          <!-- submit and reset -->
          <b-col offset-md="4">
            <b-button
              v-ripple.400="'rgba(255, 255, 255, 0.15)'"
              type="submit"
              variant="primary"
              class="mr-1"
            >
              {{ $t('Submit') }}
            </b-button>
            <b-button
              v-ripple.400="'rgba(186, 191, 199, 0.15)'"
              type="reset"
              variant="outline-secondary"
              @click="handleCancel"
            >
              {{ $t('Cancel') }}
            </b-button>
          </b-col>
        </b-row>
      </b-form>

      <ProductListModal @submit-selected-products="handleAddProducts" :preSelectedItems="coupon.details.products"></ProductListModal>
    </validation-observer>
  </b-card-code>
</template>

<script>
import {
  BInputGroup, BInputGroupPrepend, BInputGroupAppend, BImg,
  BFormSelect, BFormRadio, BFormText, BFormDatepicker,
  BTableSimple, BTh, BTd, BTr, BThead, BTbody,
  VBModal,
  BRow, BCol, BFormGroup, BFormInput, BFormTextarea, BFormCheckbox, BForm, BButton,
  VBTooltip
} from 'bootstrap-vue'
import BCardCode from '@core/components/b-card-code'
import {ValidationProvider, ValidationObserver} from 'vee-validate';
import {useUtils as useI18nUtils} from '@core/libs/i18n'
import {required} from '@validations';
import {min_value, max} from '@/utils/more-validations';
import Ripple from 'vue-ripple-directive'
import {reactive, ref, watch, computed, watchEffect} from "@vue/composition-api";
import {useCoupon} from "@/composables/coupon/useCoupon";
import {useProduct} from "@/composables/product/useProduct";
import {useRouter} from '@core/utils/utils';
import {useAuth} from "@/composables/auth/useAuth";
import ProductListModal from "@/views/components/ProductListModal";
import {mergeAndRemoveDuplicateTwoArrays} from "@/utils/array"
import ToastificationContent from "@core/components/toastification/ToastificationContent";
import {useToast} from "vue-toastification/composition";
import currency from "@/utils/currency";

export default {
  name: 'CouponForm',
  components: {
    BInputGroup,
    BInputGroupPrepend,
    BInputGroupAppend,
    BFormSelect,
    BRow,
    BCol,
    BFormRadio,
    BFormGroup,
    BFormInput,
    BFormTextarea,
    BFormCheckbox,
    BFormDatepicker,
    BForm,
    BFormText,
    BButton,
    BTableSimple,
    BImg,
    BTh,
    BTd,
    BTr,
    BThead,
    BTbody,
    BCardCode,
    ProductListModal,
    ValidationProvider,
    ValidationObserver
  },
  directives: {
    'b-modal': VBModal,
    'b-tooltip': VBTooltip,
    Ripple,
  },
  setup() {
    const toast = useToast();
    const {t} = useI18nUtils();
    const {sellerCode} = useAuth();
    const {findProductByIds} = useProduct();
    const {coupon, createCoupon, fetchCoupon, setDetailProducts, updateCoupon, error, formError} = useCoupon(formRef);

    const discount_type_options = reactive({
      'amount': '$',
      'percent': '%'
    });

    const unlimited = ref(true);
    const selectedProducts = ref([]);
    const groupedItems = ref([]);
    const code = ref('');
    const formRef = ref(null)

    const {route, router} = useRouter();

    const isEditing = route.value.name === 'coupons.edit';

    if (isEditing) {
      fetchCoupon(route.value.params.id).then(() => {
        code.value = coupon.value.code.replace(sellerCode.value, "");
      });
    }

    // mutations
    const setSelectedProducts = data => selectedProducts.value = data
    const setGroupedItems = data => groupedItems.value = data

    const onSubmit = async () => {
      const response = isEditing ? await updateCoupon(coupon.value.id, coupon.value) : await createCoupon(coupon.value)
      if (!response) {
        // toast({
        //   component: ToastificationContent,
        //   props: {
        //     title: error.value,
        //     icon: 'BellIcon',
        //     variant: 'danger',
        //   }
        // })
        return false;
      }
      router.push({name: 'coupons'})
    };

    const handleCancel = () => {
      router.push({name: 'coupons'})
    }

    const handleAddProducts = (selectedItems) => {
      setDetailProducts(selectedItems)
      console.log(coupon.value.details.products)

      // coupon.value.details.products = mergeAndRemoveDuplicateTwoArrays(coupon.value.details.products, selectedItems);
    }

    const removeProduct = (id) => {
      coupon.value.details.products = coupon.value.details.products.filter(function (item) {
        return item !== id
      })
    }

    watch(unlimited, (value) => {
      if (value) {
        coupon.value.details.max_discount = 0;
      }
    })

    watchEffect(() => {
      if (coupon.value.discount_type === 'amount') {
        coupon.value.details.max_discount = 0;
      }
    })

    watch(coupon, (value) => {
      if (value.details.max_discount !== null && parseFloat(value.details.max_discount) !== 0) {
        unlimited.value = false;
      }
    })

    watch(code, (value) => {
      coupon.value.code = sellerCode.value + code.value
    })

    watch(() => coupon.value.details.products, async (value) => {
      if (value.length > 0) {
        const items = await findProductByIds(value)
        setSelectedProducts(items)

        // const groupedItems = []

        // items.forEach(item => {
        //   groupedItems[item.]
        // });

        const groupedItems = items.items.reduce((acc, item) => {
          // Check if the product_id already exists as a key in the accumulator
          if (!acc[item.product_id]) {
            acc[item.product_id] = {
              product: item,
              variants: []
            };
          }

          if (item.id !== item.product_id) {
            acc[item.product_id].variants.push(item);
          }

          return acc;
        }, {});

        setGroupedItems(groupedItems)
      }
    })

    const formatPrice = value => currency.formatPrice(value)

    return {
      coupon,
      groupedItems,
      code,
      isEditing,
      sellerCode,
      formRef,
      unlimited,
      selectedProducts,
      discount_type_options,
      formError,
      required,
      min_value,
      onSubmit,
      handleCancel,
      removeProduct,
      handleAddProducts,
      formatPrice,
    }
  }
}
</script>

<style lang="scss">
@import '@core/scss/vue/libs/vue-flatpicker.scss';
</style>
