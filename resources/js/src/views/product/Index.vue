<template>
  <b-row>
    <b-col xl="12">
      <b-card>
        <b-card-header>
          {{ $t('Products') }}
        </b-card-header>
        <b-card-body class="table-list">
          <b-overlay
            :show="loading"
            rounded="sm"
          >
            <b-tabs variant="tabs" :active-tab="0" @input="getActiveTab">
              <b-tab title="All" lazy>
                <filter-bar/>
                <vtable
                    api-url="/products"
                    :fields="fields"
                    data-path="data.items"
                    :data="list"
                    pagination-path="data.pagination"
                    :append-params="searchParams"
                    @data-of-page="getPageData"
                >
                </vtable>
              </b-tab>
              <b-tab title="Online" lazy>
                <vtable
                    api-url="/products?published=1"
                    :fields="fields"
                    data-path="data.items"
                    :data="list"
                    pagination-path="data.pagination"
                    :append-params="searchParams"
                    @data-of-page="getPageData"
                >
                </vtable>
              </b-tab>
              <b-tab title="Out of Stock" lazy>
                <vtable
                    api-url="/products?out_of_stock=1"
                    :fields="fields"
                    data-path="data.items"
                    :data="list"
                    pagination-path="data.pagination"
                    :append-params="searchParams"
                    @data-of-page="getPageData"
                >
                </vtable>
              </b-tab>
              <b-tab title="Inactive" lazy>
                <vtable
                    api-url="/products?published=0"
                    :fields="fields"
                    :data="list"
                    data-path="data.items"
                    pagination-path="data.pagination"
                    :append-params="searchParams"
                    @data-of-page="getPageData"
                >
                </vtable>
              </b-tab>
              <b-tab v-if="$can('manage', 'products.sort')" title="Sort product" lazy>
                <sort-index />
              </b-tab>
            </b-tabs>
          </b-overlay>
        </b-card-body>
      </b-card>
      <!-- modal -->
      <product-modal />
    </b-col>
  </b-row>
</template>

<script>
import {
  BCol,
  BImg,
  BTab,
  BRow,
  BCard,
  BTabs,
  BOverlay,
  BCardBody,
  BCardHeader,
} from 'bootstrap-vue'
import Currency from '@/utils/currency';
import ProductResource from "@/api/product";
import { parseDateTime } from '@/utils/helper';
import Vtable from '@/views/components/Vtable';
import FilterBar from './components/FilterBar';
import SortIndex from './SortIndex';
import ProductModal from "@/views/product//ProductModal";
import ToastificationContent from "@core/components/toastification/ToastificationContent";

const productApi = new ProductResource();

export default {
  name: 'List',
  components: {
    BCol,
    BImg,
    BTab,
    BRow,
    BTabs,
    BCard,
    Vtable,
    BOverlay,
    FilterBar,
    BCardBody,
    BCardHeader,
    ProductModal,

    SortIndex,
  },
  setup() {

  },
  data() {
    return {
      loading: false,
      list: [],
      fields: [
        {
          name: '__product-name',
          title: this.$t('Product name'),
          _classes: 'font-weight-bold'
        },
        {
          name: '__product-price',
          title: this.$t('Original Price'),
          updatePrice: 'Original Price',
          updateStock: 'Stock',
          updateDiscount: 'Discount',
          getModal: this.getModalUpdateItem
        },
        {
          name: '__switch',
          title: 'Published',
          titleClass: 'center aligned',
          dataClass: 'center aligned',
          onChange: this.switchField,
          updateField: 'published'
        },
        {
          name: '__actions',
          title: 'Actions',
          titleClass: 'center aligned',
          dataClass: 'center aligned',
          onEdit: this.onEdit,
          getPath: this.getPath
        }
      ],
      searchParams: {
        name: '',
      },
      // params get data
      params: null,
      published: null,
      out_of_stock: null,
    }
  },
  created() {

  },
  mounted() {
    this.$events.$on('get-list', data => this.getList(data));
    this.$events.$on('get-list-close-modal', data => this.getListCloseModal(data));
  },
  methods: {
    formatData(list) {
      return list.map(function (item) {
        item.total = Currency.formatPrice(item.total);
        item.createdAt = parseDateTime(item.createdAt);
        return item;
      })
    },
    onEdit(id) {
      this.$router.push({path: `/products/${id}`})
    },

    getPath(id) {
      return { name: 'products.edit', params: { id: id }}
    },

    async switchField(id, field, value) {
      const response = await productApi.switch(id, field, value);
      if (response.success) {
        this.success(this.$t('Update Published Success'));
      }
    },

    getList(rawData) {
      this.loading = true;
      this.getListData(rawData);
      this.loading = false;
    },

    getListCloseModal(rawData) {
      this.getListData(rawData);
    },

    async getListData(rawData) {
      const {data, success} = await productApi.list({
          per_page: rawData.pageData.per_page,
          published: rawData.published,
          out_of_stock: rawData.out_of_stock,
          page: rawData.pageData.current_page,
          brand: rawData.pageData.brand,
          category: rawData.pageData.category,
          name: rawData.pageData.name
      });
      this.list = data.items;
    },

    getModalUpdateItem(data, typeUpdate) {
      const eventData = {
        data: data,
        typeUpdate: typeUpdate
      }
      this.$events.fire('get-modal-item', eventData);
    },

    getPageData(pageData) {
      const eventData = {
        pageData: pageData,
        published: this.published,
        out_of_stock: this.out_of_stock
      }
      this.$events.fire('get-page-data', eventData);
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

    getActiveTab(tab) {
      this.published = null;
      this.out_of_stock = null;
      switch (tab) {
        case 1:
          this.published = 1;
          break;
        case 2:
          this.out_of_stock = 1;
          break;
        case 3:
          this.published = 0;
          break;
        default:
          break;
      }
    },
  }
}
</script>
<style>

</style>


