<template>
  <b-nav class="nav-tabs">
    <b-nav-item :active="tab === 'all'" to="/home">All</b-nav-item>
    <b-nav-item :active="tab === 'unpaid'">Unpaid</b-nav-item>
    <b-nav-item :active="tab === 'paid'">Paid</b-nav-item>
  </b-nav>
</template>

<script>
import {BNav, BNavItem} from "bootstrap-vue";

export default {
  name: 'Nav',
  components: {
    BNav,
    BNavItem
  },
  props: {
    tab: {
      type: String,
      default: 'all'
    }
  },
}
</script>
