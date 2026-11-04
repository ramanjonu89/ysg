<template>
    <div>
        <form class="form-inline mb-2 mt-2" @submit.prevent="doFilter">
            <label class="sr-only" for="code">Code</label>
            <input type="text" v-model="searchParams.code" class="form-control mb-2 mr-sm-2" id="code" placeholder="Code">
            <button type="button" class="btn btn-primary mb-2" @click="doFilter">Filter</button>
          </form>
    </div>
</template>

<script>
    export default {
        name: 'FilterBar',
        data() {
            return {
                searchParams: {
                    code: '',
                },
            };
        },
        methods: {
            doFilter () {
                this.$events.fire('filter-set', this.searchParams);
            },
        },
    };
</script>
