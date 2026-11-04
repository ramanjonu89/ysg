import { ref, reactive, computed, watch, nextTick } from '@vue/composition-api';
import blockItemService from "./blockItemService";
import blockService from "./blockService";

const stage = reactive(
  {
    item: {},
  }
);

let showItemForm = ref(false);

export default function itemFormService() {
  const { block } = blockService();
  const { loading, items, storeItem, updateItem } = blockItemService(block);

  const defItem = {
    block_id: null,
    title: '',
    is_enabled: true,
    description: '',
    image: '',
    imageData: null,
    options: {
      link: ''
    },
  };

  watch(block, (currentValue, oldValue) => {
    if (currentValue.id !== undefined) {
      defItem.block_id = currentValue.id;
    }
  });

  function clone(object) {
    return JSON.parse(JSON.stringify(object));
  }

  stage.item = clone(defItem);

  function addItem() {
    stage.item = clone(defItem);
    showItemForm.value = true;
  }

  function editItem(index) {
    showItemForm.value = true;
    stage.item = clone(items.value[index]);
  }

  function saveItem() {
    if (stage.item.id !== undefined) {
      updateItem(stage.item.id, stage.item);
    } else {
      storeItem(stage.item);
    }
    closeForm();
  }

  function closeForm() {
    showItemForm.value = false;
  }

  return {
    item: computed(() => stage.item),
    showItemForm: showItemForm,
    loading: loading,
    addItem,
    editItem,
    saveItem,
    closeForm,
  }
}