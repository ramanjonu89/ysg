<template>
  <section class="invoice-preview-wrapper" :loading="loading">
    <b-row>
      <b-col cols="12">
        <b-button class="mb-1" variant="info" type="button" @click="download">
          <feather-icon icon="FileTextIcon" class="mr-50" />
          {{ $t('Download Invoice') }}
        </b-button>

        <b-card no-body class="invoice-preview-card">
          <!-- Header -->
          <b-card-body class="invoice-padding pb-0">
            <div
              class="d-flex justify-content-between flex-md-row flex-column invoice-spacing mt-0"
            >
              <!-- Header: Left Content -->
              <div>
                <div class="logo-wrapper">
                  <h3 class="text-primary">
                    {{ order.shippingAddress.name }}
                  </h3>
                </div>
                <p class="card-text mb-25">
                  {{ order.shippingAddress.email }}
                </p>
                <p class="card-text mb-25">
                  {{ order.shippingAddress.address }}
                </p>
                <p class="card-text mb-0">
                  {{ order.shippingAddress.phone }}
                </p>

                <p class="card-text mb-0" v-if="!isSelfCollection">
                  {{ $t('Postal Code') }}: {{ order.shippingAddress.postal_code }}
                </p>

              </div>

              <!-- Header: Right Content -->
              <div class="mt-md-0 mt-2">
                <h4 class="invoice-title">
                  {{ $t('Order') }}
                  <span class="invoice-number">#{{ order.code }}</span>
                  <small
                    v-html="getDeliveryBadgeHtml(order.deliveryStatus, order.shippingType)"
                  ></small>
                </h4>
                <table>
                  <tr>
                    <tr>
                      <td class="pr-1">{{ $t('Order Type') }}:</td>
                      <td>
                        {{ isSelfCollection ? 'Self Collection' : 'Delivery' }}
                      </td>
                    </tr>
                    <td class="pr-1">{{ $t('Order status') }}:</td>
                    <td>
                      <b-form-select
                        id="delivery_status"
                        v-model="order.deliveryStatus"
                        :options="deliveryStatusOptions"
                        @change="updateDeliveryStatus"
                        class="delivery-status"
                      />
                      <div class="d-inline">
                        <b-spinner
                          v-show="status_loading"
                          class="align-middle"
                          label="Loading..."
                          variant="primary"
                        />
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td class="pr-1">{{ $t('Order date') }}:</td>
                    <td>{{ parseDateTime(order.createdAt) }}</td>
                  </tr>
                  <tr>
                    <td class="pr-1">{{ $t('Tracking number') }}:</td>
                    <td>
                      <b>{{ order.trackingNumber }}</b>
                    </td>
                  </tr>
                  <tr>
                    <td class="pr-1">{{ $t('Total amount') }}:</td>
                    <td>{{ Currency.formatPrice(order.subTotal) }}</td>
                  </tr>
                  <tr>
                    <td class="pr-1">{{ $t('Payment method') }}:</td>
                    <td>{{ order.paymentMethod }}</td>
                  </tr>
                </table>
              </div>
            </div>
          </b-card-body>

          <!-- Spacer -->
          <hr class="invoice-spacing" />

          <b-table-lite
            :items="orderItems.items"
            :fields="fields"
            responsive="sm"
          >
            <template #cell(name)="data">
              <span>{{ data.value }}</span>
              <br />
              <span class="self-collection-address" v-if="data.item.shippingType == 'self_collection'">
                Self Collection address: {{ data.item.shopAddress }}
              </span>
            </template>
            <template #cell(image)="data">
              <b-img :src="data.value" rounded :height="75" />
            </template>
            <template #cell(variantDetails)="data">
              <ul class="pl-2">
                <li v-for="(item, index) in data.item.variantDetails" :key="index">
                  {{ item }}
                </li>
              </ul>
            </template>
            <template #cell(discount)="data">
              <span>{{
                getDiscount(data.item.discountAmount, data.item.discountType)
              }}</span>
            </template>
          </b-table-lite>

          <!-- Spacer -->
          <hr class="invoice-spacing" />

          <b-row align-h="end">
            <b-col cols="3">
              <table class="w-100">
                <tr>
                  <td>{{ $t('Sub Total') }}</td>
                  <td class="text-right pr-1">
                    {{ Currency.formatPrice(order.subTotal) }}
                  </td>
                </tr>
                <tr>
                  <td>{{ $t('Seller Voucher') }}</td>
                  <td class="text-right pr-1">
                    - {{ Currency.formatPrice(order.seller_discount) }}
                  </td>
                </tr>
                <tr>
                  <td>{{ $t('YSG Voucher') }}</td>
                  <td class="text-right pr-1">
                    - {{ Currency.formatPrice(order.ysg_discount) }}
                  </td>
                </tr>
                <tr>
                  <td>{{ $t('Shipping cost') }}</td>
                  <td class="text-right pr-1">
                    {{ Currency.formatPrice(order.shippingCost) }}
                  </td>
                </tr>
                <tr>
                  <td>{{ $t('Grand Total') }}</td>
                  <td class="text-right pr-1 font-weight-bold">
                    <h4>
                      {{ Currency.formatPrice(order.grandTotal) }}
                    </h4>
                  </td>
                </tr>
              </table>
            </b-col>
          </b-row>

          <!-- Spacer -->
          <hr class="invoice-spacing" />
        </b-card>
      </b-col>
    </b-row>
  </section>
</template>

<script>
import { useRouter } from '@core/utils/utils'
import { computed, ref } from '@vue/composition-api'
import { useOrder } from '@/composables/order/useOrder'
import { useToast } from 'vue-toastification/composition'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import Currency from '@/utils/currency'
import {
  parseDateTime,
  getDeliveryBadgeHtml,
  getPaymentBadgeHtml,
  getDiscount,
} from '@/utils/helper'
import {
  BRow,
  BCol,
  BCard,
  BCardBody,
  BTableLite,
  BImg,
  BCardText,
  BButton,
  BAlert,
  BLink,
  BFormSelect,
  BSpinner,
} from 'bootstrap-vue'
import { useUtils as useI18nUtils } from '@core/libs/i18n'
import axios from '@axios'

export default {
  name: 'OrderDetail',
  components: {
    BRow,
    BCol,
    BCard,
    BCardBody,
    BTableLite,
    BImg,
    BCardText,
    BButton,
    BAlert,
    BLink,
    BFormSelect,
    BSpinner,
  },
  methods: {},
  setup() {
    const { route, router } = useRouter()
    const {
      fetchOrder,
      fetchOrderItems,
      order,
      orderItems,
      isSelfCollection,
      error,
      downloadInvoice,
      updateOrder,
    } = useOrder()

    const toast = useToast()
    const loading = ref(true)
    const status_loading = ref(false)
    const { t } = useI18nUtils()
    const fields = [
      { key: 'image', label: 'Image' },
      { key: 'name', label: 'Product' },
      { key: '', label: 'Variation' },
      { key: 'variantDetails', label: t('Variation') },
      {
        key: 'price',
        label: 'Price',
        formatter(value) {
          return Currency.formatPrice(value)
        },
      },
      { key: 'discount', label: t('Discount') },
      {
        key: 'priceAfterDiscount',
        label: t('Price after discount'),
        formatter(value) {
          return Currency.formatPrice(value)
        },
      },
      { key: 'qty', label: 'Quantity' },
      {
        key: 'total',
        label: t('Total'),
        tdClass: 'text-right',
        thClass: 'text-right',
        formatter(value) {
          return Currency.formatPrice(value)
        },
      },
    ]

    const deliveryStatusOptions = computed(() => {
      if (isSelfCollection.value) {
        return [
          {
            value: 'pending',
            text: 'Order placed',
          },
          {
            value: 'on_review',
            text: 'Preparing Order',
          },
          {
            value: 'on_delivery',
            text: 'Ready for Collection',
          },
          {
            value: 'delivered',
            text: 'Collected',
          },
          {
            value: 'cancelled',
            text: 'Cancelled',
          },
        ]
      } else {
        return [
          {
            value: 'pending',
            text: 'Order placed',
          },
          {
            value: 'on_review',
            text: 'Ready to Ship',
          },
          {
            value: 'on_delivery',
            text: 'In Transit',
          },
          {
            value: 'delivered',
            text: 'Delivered',
          },
          {
            value: 'cancelled',
            text: 'Cancelled',
          },
        ]
      }
    });

    const init = async () => {
      const id = route.value.params.id
      const response = await fetchOrder(id)

      if (!response) {
        toast({
          component: ToastificationContent,
          props: {
            title: error.value,
            icon: 'BellIcon',
            variant: 'danger',
          },
        })

        router.push({ name: 'orders' })
      }

      if (!(await fetchOrderItems(id))) {
        toast({
          component: ToastificationContent,
          props: {
            title: error.value,
            icon: 'BellIcon',
            variant: 'danger',
          },
        })

        router.push({ name: 'orders' })
      }
      loading.value = false
    }

    const download = () => {
      const id = route.value.params.id
      downloadInvoice(id)
    }

    const updateDeliveryStatus = async () => {
      status_loading.value = true
      const result = await updateOrder(route.value.params.id, {
        delivery_status: order.value.deliveryStatus,
      })
      status_loading.value = false
    }

    init()

    return {
      order,
      orderItems,
      loading,
      status_loading,
      fields,
      Currency,
      parseDateTime,
      getDeliveryBadgeHtml,
      getPaymentBadgeHtml,
      getDiscount,
      download,
      deliveryStatusOptions,
      updateDeliveryStatus,
      isSelfCollection
    }
  },
}
</script>

<style>
.delivery-status {
  width: 80%;
}

.self-collection-address {
  color: red;
}
</style>
