<template>
  <b-modal
    id="modal-product-list"
    ref="my-modal"
    :title="$t('Select your products')"
    :ok-title="$t('Add products')"
    cancel-variant="outline-secondary"
    size="lg"
    @ok="handleOk"
    @hidden="resetSelectedItems"
  >
    <product-filter-bar :search-params="searchParams" />
    <vtable
      api-url="/products/for-voucher"
      :fields="fields"
      data-path="data.items"
      pagination-path="data.pagination"
      row-class="cursor-pointer"
      :append-params="searchParams"
      :per-page="12"
      @toggled-checkbox="onCheckboxToggled"
      @toggled-checkbox-all="onCheckboxToggled"
      :preSelectedItems="preSelectedItems"
    ></vtable>
  </b-modal>
</template>

<script>
import {BModal, BFormGroup, BFormInput} from 'bootstrap-vue';
import {useToast} from "vue-toastification/composition";
import Currency from '@/utils/currency';
import Vtable from '@/views/components/Vtable';
import {ref, computed} from "@vue/composition-api";
import {useUtils as useI18nUtils} from '@core/libs/i18n'
import ProductFilterBar from './ProductFilterBar'

export default {
  name: 'ProductListModal',
  components: {
    BModal,
    BFormGroup,
    BFormInput,
    Vtable,
    ProductFilterBar,
  },
  props: {
    excluded: {
      type: Array,
      default: () => {
        return [];
      },
    },
    preSelectedItems: {
      type: Array,
      default: () => []
    },
  },
  setup(props, { emit }) {
    const toast = useToast();
    const {t} = useI18nUtils();
    const selectedItems = ref([]);

    // mutations
    const setSelectedItems = items => { selectedItems.value = items }

    const fields = ref([
      {
        name: '__checkbox',
        titleClass: 'center aligned',
        dataClass: 'center aligned'
      },
      {
        name: 'thumbnail_img', title: t('Image'), formatter(value) {
          return "<img alt='' src='/" + value + "' height='75px' />"
        }
      },
      {name: 'sku', title: t('SKU')},
      {name: 'name', title: t('Product name'), _classes: 'font-weight-bold'},
      {
        name: 'price', title: t('Price'), formatter(value) {
          return Currency.formatPrice(value)
        }
      },
    ]);

    const searchParams = computed(() => {
      return {
        name: '',
        sku: '',
        excluded: props.excluded,
      };
    });

    const resetSelectedItems = () => {
      selectedItems.value = []
    }

    const onCheckboxToggled = (items) => {
      setSelectedItems(items)
    }

    const handleOk = () => {
      emit('submit-selected-products', selectedItems.value)
    }

    return {
      fields,
      searchParams,
      handleOk,
      onCheckboxToggled,
      resetSelectedItems
    }
  },
};
</script>
