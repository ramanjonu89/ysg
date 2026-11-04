<template>
  <div>
    <b-form class="mb-2 mt-2" @submit.prevent="doFilter">
      <b-container>
        <b-row>
          <b-col md="3">
            <label class="sr-only" for="code">Code</label>
            <input type="text" v-model="searchParams.code" class="form-control" id="code" placeholder="Code">
          </b-col>
          <b-col md="3">
            <b-form-datepicker
              id="start-date" :placeholder="$t('Start date')"
              v-model="searchParams.start_date"
              :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
              class="form-control"
              locale="en-SG"
              :reset-button="true"
            />
          </b-col>
          <b-col md="3">
            <b-form-datepicker
              id="end-date" :placeholder="$t('End date')"
              v-model="searchParams.end_date"
              :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
              class="form-control"
              locale="en-SG"
              :reset-button="true"
            />
          </b-col>
          <b-col md="3" class="text-right">
            <button type="button" class="btn btn-secondary " @click="doReset">Reset</button>
            <button type="button" class="btn btn-primary" @click="doFilter">Filter</button>
          </b-col>
        </b-row>
      </b-container>
    </b-form>
  </div>
</template>

<script>
import {BFormDatepicker, BForm, BRow, BCol, BContainer} from 'bootstrap-vue';

export default {
  name: 'FilterBar',
  components: {
    BRow,
    BCol,
    BContainer,
    BForm,
    BFormDatepicker,
  },
  data() {
    return {
      searchParams: {
        code: '',
        start_date: '',
        end_date: '',
      },
    };
  },
  methods: {
    doFilter() {
      this.$events.fire('filter-set', this.searchParams);
    },
    doReset() {
      this.searchParams = {
        code: '',
        start_date: '',
        end_date: '',
      }
      this.$events.fire('filter-set', this.searchParams);
    },
  },
};
</script>
