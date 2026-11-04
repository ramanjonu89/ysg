<template>
  <th v-if="isHeader"
      class="vuetable-th-component-actions text-center"
      v-html="title"
  ></th>
  <td v-else
      class="vuetable-td-component-actions text-center"
  >
    <div class="custom-actions">
      <b-button
        v-ripple.400="'rgba(113, 102, 240, 0.15)'"
        variant="flat-primary"
        class="btn-icon rounded-circle"
        :to="getPath()"
        :id="`${rowData.id}-edit`"
    >
      <feather-icon icon="EditIcon"/>
    </b-button>
    </div>
  </td>
</template>

<script>
import VuetableFieldMixin from 'vuetable-2/src/components/VuetableFieldMixin.vue'
import { BButton, VBModal } from 'bootstrap-vue';
import Ripple from 'vue-ripple-directive';
export default {
  name: 'vuetable-field-actions',
  components: {
    BButton
  },
  directives: {
    'b-modal': VBModal,
    Ripple
  },
  mixins: [VuetableFieldMixin],
  methods: {
    editAction() {
      const id = this.rowData.id;

      this.rowField.onEdit(id);
    },

    getPath(){
      const id = this.rowData.id;
      return this.rowField.getPath(id);
    }
  }
}
</script>
