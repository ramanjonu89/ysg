<template>
  <b-row>
    <b-col xl="12">
      <b-card>
        <b-card-header>
          {{ $t('Orders') }}
        </b-card-header>
        <b-card-body>
          <b-tabs variant="tabs" :active-tab="0">
            <b-tab title="All" lazy>
              <b-row>
                <b-col md="9">
                  <filter-bar :search-params.sync="searchParams"/>
                </b-col>
                <b-col md="3">
                  <export-all :search-params="searchParams"/>  
                </b-col>
              </b-row>
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
            <b-tab title="Unpaid" lazy>
              <vtable
                api-url="/orders?payment_status=unpaid"
                :fields="fields"
                data-path="data.items"
                pagination-path="data.pagination"
                :append-params="searchParams"
                row-class="cursor-pointer"
                @rowClicked="rowClicked"
              ></vtable>
            </b-tab>
            <b-tab title="Paid" lazy>
              <vtable
                api-url="/orders?payment_status=paid"
                :fields="fields"
                data-path="data.items"
                pagination-path="data.pagination"
                :append-params="searchParams"
                row-class="cursor-pointer"
                @rowClicked="rowClicked"
              ></vtable>
            </b-tab>
          </b-tabs>
        </b-card-body>
      </b-card>
    </b-col>
  </b-row>
</template>

<script>
import Currency from '@/utils/currency';
import { parseDateTime, getPaymentBadgeHtml } from '@/utils/helper';
import Vtable from '@/views/components/Vtable';
import FilterBar from './components/FilterBar';
import ExportAll from './components/ExportAll';
import Nav from './components/Nav';
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
    ExportAll,
    'navbar': Nav,
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
        { name: 'customer_name', title: this.$t('Customer') },
        { name: 'grand_total', title: this.$t('Total'), formatter(value) {
          return Currency.formatPrice(value)
        } },
        { name: 'payment_status', title: this.$t('Payment Status'), formatter: getPaymentBadgeHtml },
        { 
          name: '__delivery-status', 
          title: this.$t('Delivery Status'), 
        },
        { name: 'created_at', title: this.$t('Order at'), formatter(value) {
          return parseDateTime(value)
        } },
        {
          name: '__actions',
          title: 'Actions',
          titleClass: 'center aligned',
          dataClass: 'center aligned',
          getPath: this.getPath
        }
      ],
      activePage: 1,
      searchParams: {
        code: '',
        start_date: '',
        end_date: '',
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
    rowClicked ({data, event}) {
      this.$router.push({path: `/orders/${data.id}`})
    },

    getPath(id) {
      return { name: 'order-detail', params: { id: id }}
    },
  }
}
</script>

