import { ref, reactive, computed, watch, nextTick } from '@vue/composition-api';
import SettingBlock from "@/api/setting-block";
import { useToast } from 'vue-toastification/composition';
import ToastificationContent from '@/@core/components/toastification/ToastificationContent';

const stage = reactive(
  {
    items: [],
  }
);

export default function blockItemService(block) {
  // const { block } = blockService();
  // const defItem = {
  //   block_id: block.id,
  //   title: '',
  //   is_enabled: true,
  //   description: '',
  //   image: '',
  //   imageData: null,
  //   options: {
  //     link: ''
  //   },
  // };


  const blockApi = new SettingBlock();
  const toast = useToast();
  const loading = ref(false);

  function clearItems() {
    stage.items = [];
  }

  async function getItems() {
    loading.value = true;
    const { data, success , message } = await blockApi.getItems(block.value.id);
    if (success) {
      stage.items = data.items;
    } else {
      toastError('Error', message)
    }
    loading.value = false;
  }

  async function storeItem(item) {
    loading.value = true;
    const { data, success , message} = await blockApi.storeItem(block.value.id, item);
    if (success) {
      toastSuccess('Success', 'Save Item success')
    } else {
      toastError('Error', message)
    }
    await getItems();
    loading.value = false;
  }

  async function updateItem(id, item) {
    loading.value = true;
    const { data, success , message} = await blockApi.updateItem(block.value.id, id, item);
    if (success) {
      toastSuccess('Success', 'Save Item success')
    } else {
      toastError('Error', message)
    }
    await getItems();
    loading.value = false;
  }

  async function destroyItem(id) {
    loading.value = true;
    const { data, success , message} = await blockApi.deleteItem(block.value.id, id);
    if (success) {
      toastSuccess('Success', 'Save Item success')
    } else {
      toastError('Error', message)
    }
    await getItems();
    loading.value = false;
  }

  async function itemSortUp(index) {
    if (index > 0 && stage.items[index].sort > 1) {
      loading.value = true;

      stage.items[index].sort--;
      stage.items[index-1].sort++;

      const { data, success , message} = await blockApi.updateSortItem(block.value.id, stage.items);
      if (success) {
        await getItems();
      }
      loading.value = false;
    }
  }

  async function itemSortDown(index) {
    if (
      index < stage.items.length - 1
      && stage.items[index].sort < stage.items.length
    ) {
      loading.value = true;

      stage.items[index].sort++;
      stage.items[index+1].sort--;

      const { data, success , message} = await blockApi.updateSortItem(block.value.id, stage.items);
      if (success) {
        await getItems();
        toastSuccess('Success', 'Update sort success')
      } 
      loading.value = false;
    }
  }

  function toastSuccess(title, text) {
    toast.success({
      component: ToastificationContent,
      props: {
        title: title,
        icon: 'CheckIcon',
        text: text,
        variant: 'success',
      },
    })
  }

  function toastError(title, text) {
    toast.error({
      component: ToastificationContent,
      props: {
        title: title,
        icon: 'AlertCircleIcon',
        text: text,
        variant: 'danger',
      },
    })
  }

  return {
    loading: computed(() => loading.value),
    items: computed(() => stage.items),
    getItems,
    clearItems,
    storeItem,
    updateItem,
    destroyItem,
    itemSortUp,
    itemSortDown,
  }
}