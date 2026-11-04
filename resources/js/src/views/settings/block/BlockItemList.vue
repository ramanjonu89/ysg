<template>
  <div>
    <div class="block-items">
      <b-overlay
          :show="loading"
          rounded="sm"
      >
        <div v-if="(block.id !== null)" class="top mb-1 d-flex align-items-center">
          <h4 class="mb-0 mr-1">Items</h4>
          <b-button
              v-ripple.400="'rgba(255, 255, 255, 0.15)'"
              variant="primary"
              @click="addItem()"
          >
            <feather-icon
                icon="PlusIcon"
                class="mr-50"
            />
            <span>Add Item</span>
          </b-button>
        </div>
        <item
            v-for="(item, index) in items"
            :item="item"
            :index="index"
            :key="item.id"
        >
        </item>
      </b-overlay>
    </div>

    <item-form
    >
    </item-form>

  </div>
</template>

<script>
import { ref, watch, nextTick } from '@vue/composition-api'
import blockService from './blockService';
import blockItemService from './blockItemService';
import {
  BButton,
  BCol,
  BOverlay,
  BRow, VBModal
} from "bootstrap-vue";
import Ripple from "vue-ripple-directive";
import item from "./BlockItem";
import itemForm from "./BlockItemForm";
import itemFormService from "./itemFormService";

export default {
  name: "ItemList",
  components: {
    item,
    itemForm,

    // BSV
    BOverlay,
    BRow,
    BCol,
    BButton,
  },
  directives: {
    'b-modal': VBModal,
    Ripple,
  },
  data () {
    return {
    }
  },
  setup(props, { attrs, slots, emit, expose }) {

    const { block } = blockService();
    const { loading, items, getItems } = blockItemService(block);
    const { addItem } = itemFormService();

    watch(block, (currentValue, oldValue) => {
      if (currentValue.id !== null) {
        getItems();
      }
    });


    return {
      //blockService
      block,
      items,

      loading,
      addItem,
    }
  },
}
</script>

<style lang="scss" scoped>

</style>