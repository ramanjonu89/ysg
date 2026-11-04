<template>
  <th v-if="isHeader"
      class="vuetable-th-component-switch text-center"
      v-html="title"
  ></th>
  <td v-else
      class="vuetable-td-component-switch text-center"
  >
    <b-form-checkbox
      v-model="rowData[rowField.updateField]"
      name="check-button"
      switch
      inline
      v-on:change="handleChange"
    >
      <span class="switch-icon-left">
          <feather-icon icon="CheckIcon"/>
        </span>
      <span class="switch-icon-right">
          <feather-icon icon="XIcon"/>
        </span>
    </b-form-checkbox>
  </td>
</template>
<script>
import VuetableFieldMixin from 'vuetable-2/src/components/VuetableFieldMixin.vue'
import {BFormCheckbox} from 'bootstrap-vue'

export default {
  name: 'vuetable-field-switch',

  mixins: [VuetableFieldMixin],

  components: {
    BFormCheckbox,
  },

  methods: {
    handleChange() {
      const id = this.rowData.id;
      const field = this.rowField.updateField;
      const value = this.rowData[field]

      this.rowField.onChange(id, field, value)
    }
  },
}
</script>
