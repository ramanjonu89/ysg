<template>
  <div>
    <form class="form-inline mb-2 mt-2" @submit.prevent="doFilter">
      <label class="sr-only" for="code">Name</label>
      <input type="text" v-model="params.sku" class="form-control mb-2 mr-sm-2" id="code" placeholder="SKU">
      <input type="text" v-model="params.name" class="form-control mb-2 mr-sm-2" id="code" placeholder="Name">
      <button type="button" class="btn btn-primary mb-2" @click="doFilter">Filter</button>
    </form>
  </div>
</template>

<script>
export default {
  name: 'ProductFilterBar',
  props: {
    searchParams: {
      type: Object,
      default: () => {
        return {
          name: '',
          sku: '',
        };
      },
    },
  },
  data() {
    return {
      params: JSON.parse(JSON.stringify(this.searchParams))
    };
  },
  watch: {
    params: function (val) {
      this.$emit('update:searchParams', val);
    },
  },
  methods: {
    doFilter() {
      this.$events.fire('filter-set', this.params);
    },
  },
};
</script>
