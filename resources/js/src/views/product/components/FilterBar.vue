<template>
  <div>
    <b-form class="mb-2 mt-2" @submit.prevent="doFilter">
      <b-container>
        <b-row>
          <b-col md="3">
            <label class="sr-only" for="name">Name</label>
            <input type="text" v-model="searchParams.name" class="form-control" id="name" placeholder="Name / SKU">
          </b-col>
          <b-col md="3">
             <b-form-select
                v-model="searchParams.category"
                :options="categoriesBySeller"
                label="Categories"
              >
             </b-form-select>
          </b-col>
          <b-col md="3">
            <b-form-select
                v-model="searchParams.brand"
                :options="brandsBySeller"
              />
          </b-col>
          <b-col md="3">
            <button type="button" class="btn btn-secondary " @click="doReset">Reset</button>
            <button type="button" class="btn btn-primary" @click="doFilter">Filter</button>
          </b-col>
        </b-row>
      </b-container>
    </b-form>
  </div>
</template>

<script>
import {BFormDatepicker, BForm, BRow, BCol, BContainer, BFormSelect} from 'bootstrap-vue';
import { useCategory } from '@/composables/category/useCategory';
import { useBrand } from '@/composables/brand/useBrand';

export default {
  name: 'FilterBar',
  components: {
    BRow,
    BCol,
    BContainer,
    BForm,
    BFormDatepicker,
    BFormSelect,
  },
  data() {
    return {
      searchParams: {
        name: null,
        category: null,
        brand: null,
      }
    };
  },

  setup() {
    const { getCategoriesBySeller, categoriesBySeller } = useCategory()
    const { brandsBySeller, getBrandsBySeller } = useBrand()
    getCategoriesBySeller()
    getBrandsBySeller()

    return {
      categoriesBySeller,
      brandsBySeller
    }
  },

  methods: {
    doFilter() {
      this.$events.fire('filter-set', this.searchParams);
    },
    doReset() {
      this.searchParams = {
        name: null,
        category: null,
        brand: null,
      }
      this.$events.fire('filter-set', this.searchParams);
    },
  },
};
</script>
