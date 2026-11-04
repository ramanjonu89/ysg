<template>
  <div class="mb-2 mt-2 float-right">
    <b-button variant="info" type="button" @click="handleExport">
      <feather-icon icon="FileTextIcon" class="mr-50" />
      {{ $t('Export') }}
    </b-button>
  </div>
</template>

<script>
import { BButton } from "bootstrap-vue";
import OrderResource from "@/api/order";
export default {
  name: 'ExportAll',
  components: {
    BButton
  },

  props: {
    searchParams: {
      type: Object,
      required: true,
    },
  },

  setup(props) {
    const orderApi = new OrderResource();
    const handleExport = async function () {
      const { success, data, message } = await orderApi.exportExcel(props.searchParams);

      if (success) {
        window.open(data.filePath, '_blank', 'noreferrer');
      } else {

      }
    }

    return {
      handleExport,
    }
  },
};
</script>