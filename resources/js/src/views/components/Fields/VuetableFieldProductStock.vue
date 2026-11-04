<template>
  <th v-if="isHeader"
      class="vuetable-th-component-product-stock"
      v-html="title"
  ></th>
  <td v-else
      class="vuetable-td-component-product-stock w-custom-row-stock"
  >
    <span v-if="rowData['stocks'].length == 0">
      <div class="d-flex align-items-center" @mouseenter="is_edit_stock = true" @mouseleave="is_edit_stock = false"> 
        <span class="">{{ rowData['stock'] }}</span>
        <b-button
          v-ripple.400="'rgba(113, 102, 240, 0.15)'"
          variant="flat-primary"
          class="btn-icon rounded-circle"
          @click="getModal(rowData)"
          :id="`${rowData.id}-edit`"
          v-if="is_edit_stock"
        >
        <feather-icon icon="EditIcon"/>
        </b-button>
      </div>
    </span>
    <div v-else>
      <div v-for="( item, index ) in rowData['stocks']" :key="item.id"> 
        <div class="d-flex align-items-center"  @mouseenter="is_edit_stock = true" @mouseleave="is_edit_stock = false">
          <span class="item-variant"> {{ item.qty }} </span>
          <b-button
            v-ripple.400="'rgba(113, 102, 240, 0.15)'"
            variant="flat-primary"
            class="btn-icon rounded-circle"
            @click="getModal(rowData)"
            :id="`${item.id}-edit`"
            v-if="is_edit_stock"
          >
          <feather-icon icon="EditIcon"/>
          </b-button>
        </div>
      </div>
    </div>
    
  </td>
</template>
<script>

import Ripple from 'vue-ripple-directive'
import { BButton} from 'bootstrap-vue'
import VuetableFieldMixin from 'vuetable-2/src/components/VuetableFieldMixin.vue'

export default {
  name: 'vuetable-field-product-stock',
  mixins: [VuetableFieldMixin],
  components: {
    BButton
  },
  directives: {
      Ripple
  },
  data() {
      return {
        is_edit_stock: false,
      }
    },
    methods: {
      getModal(data) {
        const type_update = this.rowField.title;
        this.rowField.getModal(data, type_update);
      }
    }
}
</script>
