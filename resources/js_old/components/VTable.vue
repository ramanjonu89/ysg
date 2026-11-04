<template>
  <div>
    <vuetable ref="vuetable" v-bind="$attrs" :http-options="httpOptions" 
      @vuetable:row-clicked="rowClicked" 
      @vuetable:pagination-data="onPaginationData"
      :append-params="searchParams"
    />
    <vuetable-pagination ref="pagination" @vuetable-pagination:change-page="onChangePage"></vuetable-pagination>
  </div>
</template>

<script>
import Vue from 'vue'
import Vuetable from 'vuetable-2';
import service from '@/utils/requests';
import TokenService from '@/services/Token';
import VuetablePagination from "@/components/VuetablePaginationBootstrap4";

export default {
  name: 'vtable',
  components: {
    Vuetable,
    VuetablePagination
  },
  data() {
    return {
      httpOptions: {
        baseURL: service.defaults.baseURL,
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'Authorization': 'Bearer ' + TokenService.getToken()
        }
      },
      searchParams: {},
    };
  },
  mounted() {
    this.$events.$on('filter-set', eventData => this.onFilterSet(eventData))
  },
  methods: {
    rowClicked(data, event) {
      this.$emit('rowClicked', data);
    },
    onPaginationData (paginationData) {
      this.$refs.pagination.setPaginationData(paginationData)
    },
    onChangePage (page) {
      this.$refs.vuetable.changePage(page)
    },
    onFilterSet (searchParams) {
      console.log(searchParams);
      this.searchParams = searchParams;
      Vue.nextTick( () => this.$refs.vuetable.refresh());
    },
  }
}
</script>
