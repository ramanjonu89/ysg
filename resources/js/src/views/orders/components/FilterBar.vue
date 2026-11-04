<template>
    <div>
        <form class="form-inline mb-2 mt-2" @submit.prevent="doFilter">
            <b-col md="3">
                <b-form-datepicker
                id="start-date" :placeholder="$t('Start date')"
                v-model="searchParams.start_date"
                :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                class="form-control mb-2 mr-sm-2"
                locale="en-SG"
                :reset-button="true"
                />
            </b-col>
            <b-col md="3">
                <b-form-datepicker
                id="end-date" :placeholder="$t('End date')"
                v-model="searchParams.end_date"
                :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                class="form-control mb-2 mr-sm-2"
                locale="en-SG"
                :reset-button="true"
                />
            </b-col>
            <b-col md="4">    
                <div class="d-flex mb-2">
                    <label class="sr-only" for="code">Code</label>
                    <input type="text" v-model="searchParams.code" class="form-control mr-sm-2" id="code" placeholder="Code">
                    <button type="button" class="btn btn-secondary mr-1" @click="doReset">Reset</button>
                    <button type="button" class="btn btn-primary" @click="doFilter">Search</button>
                </div>
            </b-col>
          </form>
    </div>
</template>

<script>
    import {BFormDatepicker, BForm, BRow, BCol} from 'bootstrap-vue';
    
    export default {
        name: 'FilterBar',
        components: {
            BRow,
            BCol,
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
            doReset () {
                console.log('xxxx');
                this.searchParams = {
                    code: '',
                    start_date: '',
                    end_date: '',
                }
                this.$events.fire('filter-set', this.searchParams);
            },
        },

        watch: {
            searchParams: {
                handler(value) {
                    this.$emit('update:searchParams', value);
                },
                deep: true,
            },
        },

    };
</script>
