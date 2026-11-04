<template>
  <div>
    <vuetable-pagination ref="paginationTop" @vuetable-pagination:change-page="onChangePage"
                         @per-page="onPerPage" :pagination="pagination"></vuetable-pagination>
    <vuetable ref="mytable" v-bind="$attrs" :http-options="httpOptions"
              @vuetable:row-clicked="rowClicked"
              @vuetable:pagination-data="onPaginationData"
              :per-page="pagination.perPage"
              @vuetable:checkbox-toggled="onCheckboxToggled"
              @vuetable:checkbox-toggled-all="onCheckboxToggledAll"
              :append-params="searchParams"
    />
    <vuetable-pagination ref="pagination" @vuetable-pagination:change-page="onChangePage"
                         @per-page="onPerPage" :pagination="pagination"></vuetable-pagination>
  </div>
</template>

<script>
import Vue from 'vue'
import Vuetable from 'vuetable-2';
import Token from '@/utils/token';
import VuetablePagination from "./VuetablePaginationBootstrap4";
import VuetableFieldCheckbox from 'vuetable-2/src/components/VuetableFieldCheckbox'
import VuetableFieldSwitch from './Fields/VuetableFieldSwitch'
import VuetableFieldActions from './Fields/VuetableFieldActions'
import VuetableFieldProductStock from './Fields/VuetableFieldProductStock'
import VuetableFieldProductPrice from './Fields/VuetableFieldProductPrice'
import VuetableFieldProductName from './Fields/VuetableFieldProductName'
import VuetableFieldDeliveryStatus from './Fields/VuetableFieldDeliveryStatus'

Vue.component('vuetable-field-checkbox', VuetableFieldCheckbox)
Vue.component('vuetable-field-switch', VuetableFieldSwitch)
Vue.component('vuetable-field-actions', VuetableFieldActions)
Vue.component('vuetable-field-product-stock', VuetableFieldProductStock)
Vue.component('vuetable-field-product-price', VuetableFieldProductPrice)
Vue.component('vuetable-field-product-name', VuetableFieldProductName)
Vue.component('vuetable-field-delivery-status', VuetableFieldDeliveryStatus)


export default {
  name: 'vtable',
  components: {
    Vuetable,
    VuetablePagination,
  },
  props: {
    appendParams: {
      type: Object,
      default: () => {},
    },
    preSelectedItems: {
      type: Array,
      default: () => []
    },
  },
  data() {
    return {
      httpOptions: {
        baseURL: process.env.SELLER_BASE_API,
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'Authorization': 'Bearer ' + Token.getToken()
        }
      },
      searchParams: this.appendParams,
      pagination: {
        perPage: (this.appendParams.per_page !== undefined) ? this.appendParams.per_page:12,
        pageOptions: [
          {
            text: '12',
            value: 12,
          },
          {
            text: '24',
            value: 24,
          },
          {
            text: '36',
            value: 36,
          },
          {
            text: this.$t('All'),
            value: 0,
          },
        ],
      },
    };
  },
  mounted() {
    if (this.preSelectedItems) {
      this.preSelectedItems.forEach(item => {
        if (item !== null) {
          this.$refs.mytable.selectedTo.push(item)
        }
      });
    }
    this.$events.$off('filter-set')
    this.$events.$on('filter-set', eventData => this.onFilterSet(eventData))
  },
  unmounted() {
    this.$events.$off('filter-set')
  },
  methods: {
    refresh() {
      Vue.nextTick(() => this.$refs.mytable.refresh());
    },
    rowClicked(data) {
      this.$emit('rowClicked', data);
    },
    onPaginationData(paginationData) {
      const params = Object.assign({}, this.searchParams, paginationData);
      this.$emit('data-of-page', params)
      this.$refs.paginationTop.setPaginationData(paginationData);
      this.$refs.pagination.setPaginationData(paginationData);
    },
    onChangePage(page) {
      this.$refs.mytable.changePage(page)
    },
    onPerPage(perPage) {
      this.searchParams.per_page = perPage;
      this.$refs.paginationTop.perPage = perPage;
      this.$refs.pagination.perPage = perPage;
      this.refresh();
    },
    onFilterSet(searchParams) {
      this.searchParams = searchParams;
      this.refresh();
    },
    onCheckboxToggled() {
      this.$emit('toggled-checkbox', this.$refs.mytable.selectedTo)
    },
    onCheckboxToggledAll() {
      this.$emit('toggled-checkbox-all', this.$refs.mytable.selectedTo)
    },
  }
}
</script>
