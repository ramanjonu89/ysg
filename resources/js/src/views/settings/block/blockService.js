import { ref, reactive, computed, watch, nextTick } from '@vue/composition-api';
import SettingBlock from "@/api/setting-block";
import { useToast } from 'vue-toastification/composition'
import blockItemService from "./blockItemService";
import ToastificationContent from '@/@core/components/toastification/ToastificationContent';

const stage = reactive(
  {
    block: {
      id: null,
      is_enabled: true,
      type: 'microsite_slider',
    },
    loading: false,
  }
);

export default function blockService() {
  const blockApi = new SettingBlock();

  const toast = useToast();
  const loading = ref(false);

  const defBlock = {
    id: null,
    is_enabled: true,
    type: 'microsite_slider',
    datetime_type: 'date_range'
  };

  function setBlock(data) {
    data.is_enabled = (data.is_enabled == 1);
    stage.block = clone(data);
  }

  function clone(object) {
    return JSON.parse(JSON.stringify(object));
  }

  function addBlock() {
    const { clearItems } = blockItemService(ref(stage.block));
    setBlock(defBlock);
    clearItems();
  }

  async function loadBlock(id) {
    loading.value = true;
    const { data, success , message } = await blockApi.get(id);
    if (success) {
      setBlock(data);
    } else {
      toast.error(message);
    }
    loading.value = false;
  }

  async function saveBlock() {
    loading.value = true;
    if (stage.block.id === null) {
      const { data, success , message} = await blockApi.store(stage.block);
      if (success) {
        setBlock(data);
        toastSuccess('Success', 'Add block success')
      } else {
        toastError('Error', message)
      }
    } else {
      const { data, success , message} = await blockApi.update(stage.block.id, stage.block);
      if (success) {
        toastSuccess('Success', 'Update block success')
      } else {
        toastError('Error', message)
      }
    }
    loading.value = false;
  }

  function toastSuccess(title, text) {
    toast.success(
      {
        component: ToastificationContent,
        props: {
          title: title,
          icon: 'CheckIcon',
          text: text,
          variant: 'success',
        }
      });
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
    loadingBlock: computed(() => loading.value),
    block: computed(() => stage.block),
    addBlock,
    loadBlock,
    saveBlock,
  }
}
