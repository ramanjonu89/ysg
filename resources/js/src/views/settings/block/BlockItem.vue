<template>
  <div>
    <b-card
        class="block-item"
        no-body
    >
      <div class="item-body">
        <!-- Product Image -->
        <div class="item-img">
          <b-img
              :id="`item-${item.id}-image`"
              class="img-fluid"
              :src="item.image"
              :alt="`${item.title}-${item.id}`"
          />
          <b-tooltip
              :title="item.title"
              class="cursor-pointer"
              :target="`item-${item.id}-image`"
          />
        </div>

        <!-- Product Options/Actions -->
        <div class="item-options">
          <div class="text-nowrap">
            <b-row>
              <b-col cols="4">
                <b-button :id="`item-${item.id}-up`" v-ripple.400="'rgba(113, 102, 240, 0.15)'"
                          variant="outline-success" class="btn-icon rounded-circle"
                          @click="itemSortUp(index)"
                          title="Move Up"
                >
                  <feather-icon
                    icon="ChevronUpIcon"
                    class="cursor-pointer"
                    size="16"
                  />
                </b-button>
              </b-col>
              <b-col cols="4">
                <b-button :id="`item-${item.id}-down`" v-ripple.400="'rgba(113, 102, 240, 0.15)'"
                          variant="outline-warning" class="btn-icon rounded-circle"
                          @click="itemSortDown(index)"
                          title="Move Down"
                >
                  <feather-icon
                    icon="ChevronDownIcon"
                    class="cursor-pointer"
                    size="16"
                  />
                </b-button>
              </b-col>
            </b-row>
            <b-row class="mt-1">
              <b-col>
                <b-button
                    v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                    variant="info"
                    @click="editItem(index)"
                >
                  <feather-icon
                      icon="EditIcon"
                      class="mr-50"
                  />
                  <span>Edit</span>
                </b-button>
              </b-col>
            </b-row>
            <b-row class="mt-1">
              <b-col>
                <b-button
                    v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                    variant="danger"
                    @click="deleteItem(item)"
                >
                  <feather-icon
                      icon="TrashIcon"
                      class="mr-50"
                  />
                  <span>Remove</span>
                </b-button>
              </b-col>
            </b-row>
          </div>
        </div>
      </div>
    </b-card>
  </div>
</template>

<script>
import {
  BButton,
  BCard,
  BCardBody,
  BCardHeader,
  BCardText,
  BCardTitle,
  BCol,
  BForm, BFormCheckbox,
  BFormGroup, BFormInput, BFormInvalidFeedback, BFormSelect, BImg,
  BOverlay,
  BRow, BTooltip, VBModal
} from "bootstrap-vue";
import Ripple from "vue-ripple-directive";
import blockService from "./blockService";
import blockItemService from "./blockItemService";
import itemFormService from "./itemFormService";

export default {
  name: "Item",
  components: {
    // BSV
    BOverlay,
    BForm,
    BCard,
    BCardHeader,
    BCardTitle,
    BCardText,
    BCardBody,
    BRow,
    BCol,
    BFormGroup,
    BFormInput,
    BButton,
    BFormInvalidFeedback,
    BFormCheckbox,
    BFormSelect,
    BImg,
    BTooltip,
  },
  directives: {
    'b-modal': VBModal,
    Ripple,
  },
  props: {
    item: {
      type: Object,
      required: true,
    },
    index: {
      type: Number,
      required: true,
    },
  },
  setup(props, context) {
    const { block } = blockService();
    const { editItem } = itemFormService();
    const { destroyItem, itemSortUp, itemSortDown } = blockItemService(block);

    const deleteItem = (item) => {
      context.root.$bvModal
          .msgBoxConfirm('Remove this image?', {
            title: 'Confirm',
            size: 'sm',
            okVariant: 'danger',
            okTitle: 'Yes',
            cancelTitle: 'No',
            cancelVariant: 'outline-secondary',
            hideHeaderClose: false,
            centered: true,

          })
          .then(confirm => {
            if (confirm) {
              destroyItem(item.id);
            }
          })
    }

    return {
      itemSortUp,
      itemSortDown,
      editItem,
      deleteItem,
    }
  },
}
</script>

<style lang="scss" scoped>
.block-items {
  .block-item {
    .item-body {
      display: flex;
      .item-img {
        border-bottom-left-radius: 0.428rem;
        border-top-left-radius: 0.428rem;
        overflow: hidden;
        margin-right: auto;
      }
    }
    .item-options {
      border-left: 1px solid;
      padding: 1.5rem;
    }
  }
}
</style>
