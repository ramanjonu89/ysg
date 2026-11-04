<template>
    <th v-if="isHeader" class="vuetable-th-component-delivery-status" v-html="title"></th>
    <td v-else class="vuetable-td-component-delivery-status">
        <span v-html="getDeliveryBadgeHtml(rowData.delivery_status, rowData.shipping_type)"></span>
    </td>
</template>
<script>
import {
    BCol,
    BImg,
    BRow,
} from 'bootstrap-vue'
import VuetableFieldMixin from 'vuetable-2/src/components/VuetableFieldMixin.vue'
import { getDeliveryBadgeHtml } from '@/utils/helper'
export default {
    name: 'vuetable-field-product-name',
    mixins: [VuetableFieldMixin],
    components: {
        BCol,
        BRow
    },
    methods: {
      getDeliveryBadgeHtml,
    },
  }
</script>
