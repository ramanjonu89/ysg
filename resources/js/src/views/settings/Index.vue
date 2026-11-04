<template>
  <b-row>
    <b-col xl="12">
      <b-card>
        <b-card-header>
          {{ $t('Orders') }}
        </b-card-header>
        <b-card-body>
          <b-tabs variant="tabs" :active-tab="0">
            <b-tab title="All">
              <filter-bar />
              <vtable
                api-url="/orders"
                :fields="fields"
                data-path="data.items"
                pagination-path="data.pagination"
                :append-params="searchParams"
                row-class="cursor-pointer"
                @rowClicked="rowClicked"
              ></vtable>
            </b-tab>
            <b-tab title="Unpaid">
              <!-- <vtable
                api-url="/api/v1/orders?payment_status=unpaid"
                :fields="fields"
                data-path="data.items"
                pagination-path="data.pagination"
                row-class="cursor-pointer"
                @rowClicked="rowClicked"
              ></vtable> -->
            </b-tab>
            <b-tab title="Paid">
              <!-- <vtable
                api-url="/api/v1/orders?payment_status=paid"
                :fields="fields"
                data-path="data.items"
                pagination-path="data.pagination"
                row-class="cursor-pointer"
                @rowClicked="rowClicked"
              ></vtable> -->
            </b-tab>
          </b-tabs>
        </b-card-body>
      </b-card>
    </b-col>
  </b-row>
</template>

<script>
import Currency from '@/utils/currency';
import { parseDateTime, getDeliveryBadgeHtml, getPaymentBadgeHtml } from '@/utils/helper';
import Vtable from '@/views/components/Vtable';
import FilterBar from './components/FilterBar';
import { BTab, BTabs, BRow, BCol, BCard, BCardBody, BCardHeader } from 'bootstrap-vue'


export default {
  name: 'List',
  components: {
    BTab,
    BTabs,
    BRow,
    BCol,
    BCard,
    BCardBody,
    BCardHeader,
    Vtable,
    FilterBar,
  },
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
