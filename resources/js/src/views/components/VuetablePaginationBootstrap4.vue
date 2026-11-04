<template>
  <nav class="d-flex justify-content-between">
    <ul class="pagination">
      <li :class="['page-item', {'disabled': isOnFirstPage}]">
        <a class="page-link" @click.prevent="loadPage('prev')">
          <span>&laquo;</span>
        </a>
      </li>

      <template v-if="notEnoughPages">
        <li v-for="n in totalPage" :key="n" :class="['page-item', {'active': isCurrentPage(n)}]">
          <a class="page-link" @click.prevent="loadPage(n)" v-html="n"></a>
        </li>
      </template>
      <template v-else>
        <li v-for="n in windowSize" :key="n" :class="['page-item', {'active': isCurrentPage(windowStart+n-1)}]">
          <a class="page-link" @click.prevent="loadPage(windowStart+n-1)" v-html="windowStart+n-1"></a>
        </li>
      </template>

      <li :class="['page-item', {'disabled': isOnLastPage}]">
        <a class="page-link" href="" @click.prevent="loadPage('next')">
          <span>&raquo;</span>
        </a>
      </li>
    </ul>
    <b-form-group
        label="Per Page"
        label-cols="6"
        label-align="left"
        label-size="sm"
        label-for="sortBySelect"
        class="text-nowrap mb-md-0 mr-1"
    >
      <b-form-select
          id="perPageSelect"
          v-model="perPage"
          size="sm"
          inline
          :options="pagination.pageOptions"
      />
    </b-form-group>
  </nav>
</template>

<script>
import VuetablePaginationMixin from "vuetable-2/src/components/VuetablePaginationMixin";
import {
  BFormGroup,
  BFormSelect,
} from "bootstrap-vue";

export default {
  mixins: [VuetablePaginationMixin],
  components: {
    // BSV
    BFormGroup,
    BFormSelect,
  },
  props: {
    pagination: {
      type: Object,
      default: () => {},
    },
  },
  data() {
    return {
      perPage: this.pagination.perPage,
    };
  },
  watch: {
    perPage(val) {
      this.$emit('per-page', val)
    },
  },
  methods: {
  }
};
</script>
