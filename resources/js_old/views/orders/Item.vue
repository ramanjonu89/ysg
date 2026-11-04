<template>
  <div>
    <CRow>
      <CCol sm="12">
        <CCard bodyWrapper>
          Order id: 20210531-01163819
        </CCard>
      </CCol>
    </CRow>

    <CRow>
      <CCol sm="12">
        <CCard>
          <CCardHeader>
            Order Summary
          </CCardHeader>
          <CCardBody>
            <CRow>

              <CCol sm="6">
                <CRow>
                  <CCol sm="4">
                    Order Code:
                  </CCol>
                  <CCol sm="8">
                    {{ orderData.code }}
                  </CCol>
                </CRow>
              </CCol>

              <CCol sm="6">
                <CRow>
                  <CCol sm="4">
                    Order date:
                  </CCol>
                  <CCol sm="8">
                    {{ orderData.creationTime }}
                  </CCol>
                </CRow>
              </CCol>

              <CCol sm="6">
                <CRow>
                  <CCol sm="4">
                    Customer:
                  </CCol>
                  <CCol sm="8">
                    {{ orderData.customerName }}
                  </CCol>
                </CRow>
              </CCol>

              <CCol sm="6">
                <CRow>
                  <CCol sm="4">
                    Order status:
                  </CCol>
                  <CCol sm="8">
                    {{ orderData.deliveryStatus }}
                  </CCol>
                </CRow>
              </CCol>

              <CCol sm="6">
                <CRow>
                  <CCol sm="4">
                    Email:
                  </CCol>
                  <CCol sm="8">
                    {{ orderData.customerEmail }}
                  </CCol>
                </CRow>
              </CCol>

              <CCol sm="6">
                <CRow>
                  <CCol sm="4">
                    Total order amount:
                  </CCol>
                  <CCol sm="8">
                    {{ orderData.total }}
                  </CCol>
                </CRow>
              </CCol>

              <CCol sm="6">
                <CRow>
                  <CCol sm="4">
                    Shipping address:
                  </CCol>
                  <CCol sm="8">
                    {{ orderData.fullShippingAddress }}
                  </CCol>
                </CRow>
              </CCol>

              <CCol sm="6">
                <CRow>
                  <CCol sm="4">
                    Contact:
                  </CCol>
                  <CCol sm="8">
                    {{ orderData.contactPhone }}
                  </CCol>
                </CRow>
              </CCol>

              <CCol sm="6">
                <CRow>
                  <CCol sm="4">
                    Payment method:
                  </CCol>
                  <CCol sm="8">
                    {{ orderData.paymentMethod }}
                  </CCol>
                </CRow>
              </CCol>

            </CRow>
          </CCardBody>
        </CCard>
      </CCol>
    </CRow>

    <CRow>
      <CCol sm="8">
        <CCard>
          <CCardHeader>
            Order Details
          </CCardHeader>
          <CCardBody>
            <OrderDetails></OrderDetails>
          </CCardBody>
        </CCard>
      </CCol>
      <CCol sm="4">
        <CCard>
          <CCardHeader>
            Order Amount
          </CCardHeader>
          <CCardBody>

            <CRow>
              <CCol sm="6">
                Subtotal:
              </CCol>
              <CCol sm="6">
                {{ orderData.subTotal }}
              </CCol>
            </CRow>

            <CRow>
              <CCol sm="6">
                Shipping
              </CCol>
              <CCol sm="6">
                {{ orderData.shippingTotal }}
              </CCol>
            </CRow>

            <CRow>
              <CCol sm="6">
                Tax
              </CCol>
              <CCol sm="6">
                {{ orderData.taxTotal }}
              </CCol>
            </CRow>

            <CRow>
              <CCol sm="6">
                Total
              </CCol>
              <CCol sm="6">
                {{ orderData.total }}
              </CCol>
            </CRow>

          </CCardBody>
        </CCard>
      </CCol>
    </CRow>

  </div>
</template>

<script>
import Resource from '@/api/order';
import OrderDetails from "@/views/orders/OrderDetails";

const orderApi = new Resource();

import usersData from './UsersData'
import Currency from "@/utils/currency";
import {parseDateTime} from "@/utils/helper";
export default {
  name: 'Item',
  beforeRouteEnter(to, from, next) {
    next(vm => {
      vm.usersOpened = from.fullPath.includes('orders')
    })
  },
  components: {
    OrderDetails,
  },
  data () {
    return {
      usersOpened: null,
      orderData: {}
    }
  },
  computed: {
    fields () {
      return [
        { key: 'key', label: this.username, _style: 'width:150px'},
        { key: 'value', label: '', _style: 'width:150px;' }
      ]
    },
    userData () {
      const id = this.$route.params.id
      const user = usersData.find((user, index) => index + 1 == id)
      const userDetails = user ? Object.entries(user) : [['id', 'Not found']]
      return userDetails.map(([key, value]) => { return { key, value } })
    },
    visibleData () {
      return this.userData.filter(param => param.key !== 'username')
    },
    username () {
      return this.userData.filter(param => param.key === 'username')[0].value
    }
  },
  created() {
    this.getItem();
  },
  methods: {
    async getItem() {
      const id = this.$route.params.id
      this.listLoading = true;
      const { data, success = false } = await orderApi.get(id);
      this.orderData = this.formatData(data);
      this.listLoading = false;
    },
    formatData(data) {
      const subTotal = data.total - data.shippingTotal - data.taxTotal;

      data.total = Currency.formatPrice(data.total);
      data.subTotal = Currency.formatPrice(subTotal);
      data.shippingTotal = Currency.formatPrice(data.shippingTotal);
      data.taxTotal = Currency.formatPrice(data.taxTotal);
      data.creationTime = parseDateTime(data.creationTime);
      data.fullShippingAddress = data.shippingAddress.address + ', ' + data.shippingAddress.city + ', ' + data.shippingAddress.postal_code + ', ' + data.shippingAddress.country ;
      data.contactPhone = data.shippingAddress.phone ;
      return data;
    },
    goBack() {
      this.usersOpened ? this.$router.go(-1) : this.$router.push({path: '/orders/list'})
    }
  }
}
</script>
