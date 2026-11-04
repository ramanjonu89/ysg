<template>
  <CRow>
    <CCol col="12" xl="12">
      <CCard>
        <CCardHeader>
          {{ $t('Orders') }}
        </CCardHeader>
        <CCardBody>
          <CTabs variant="tabs" :active-tab="0">
            <CTab title="All">
              <filter-bar />
              <vtable 
                api-url="/api/v1/orders"
                :fields="fields"
                data-path="data.items"
                pagination-path="data.pagination"
                :append-params="searchParams"
                row-class="cursor-pointer"
                @rowClicked="rowClicked"
              ></vtable>
            </CTab>
            <CTab title="Unpaid">
              <!-- <vtable 
                api-url="/api/v1/orders?payment_status=unpaid"
                :fields="fields"
                data-path="data.items"
                pagination-path="data.pagination"
                row-class="cursor-pointer"
                @rowClicked="rowClicked"
              ></vtable> -->
            </CTab>
            <CTab title="Paid">
              <!-- <vtable 
                api-url="/api/v1/orders?payment_status=paid"
                :fields="fields"
                data-path="data.items"
                pagination-path="data.pagination"
                row-class="cursor-pointer"
                @rowClicked="rowClicked"
              ></vtable> -->
            </CTab>
          </CTabs>
        </CCardBody>
      </CCard>
    </CCol>
  </CRow>
</template>

<script>
import Resource from '@/api/order';
import Currency from '@/utils/currency';
import moment from 'moment';
import { parseDateTime, getDeliveryBadgeHtml, getPaymentBadgeHtml } from '@/utils/helper';
import Vtable from '@/components/VTable';
import FilterBar from './components/FilterBar';
// import Vuetable from 'vuetable-2';

const orderApi = new Resource();

export default {
  name: 'List',
  data () {
    return {
      listQuery: {
        page: 1,
        perPage: 20,
      },
      items: [],
      fields: [
        { name: 'code', title: this.$t('Order Code'), _classes: 'font-weight-bold' },
        { name: 'customerName', title: this.$t('Customer') },
        { name: 'total', title: this.$t('Total'), formatter(value) {
          return Currency.formatPrice(value)
        } },
        { name: 'paymentStatus', title: this.$t('Payment Status'), formatter: getPaymentBadgeHtml },
        { name: 'deliveryStatus', title: this.$t('Delivery Status'), formatter: getDeliveryBadgeHtml },
        { name: 'createdAt', title: this.$t('Order at'), formatter(value) {
          return parseDateTime(value)
        } },
      ],
      activePage: 1,
      searchParams: {
        code: '',
      }
    }
  },
  components: {
    Vtable,
    FilterBar,
  },
  created() {
    
  },
  watch: {
    $route: {
      immediate: true,
      handler (route) {
        if (route.query && route.query.page) {
          this.activePage = Number(route.query.page)
        }
      }
    }
  },
  methods: {
    formatData(list) {
      return list.map(function(item) {
        item.total = Currency.formatPrice(item.total);
        item.createdAt = parseDateTime(item.createdAt);
        return item;
      })
    },
    rowClicked (data, event) {
      this.$router.push({path: `/orders/${data.id}`})
    },
  }
}
</script>

<style type="text/css" scoped>
  .cursor-pointer {
    cursor: pointer;
  }
</style>
