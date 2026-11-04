<template>
  <section class="invoice-add-wrapper">
    <b-row>
      <b-col xl="10">
        <coupon-form/>
      </b-col>
    </b-row>
  </section>
</template>

<script>

import CouponForm from './components/Form'
import {
  BRow, BCol
} from 'bootstrap-vue'

export default {
  name: 'AddCoupon',
  components: {
    BRow,
    BCol,
    CouponForm
  },
}
</script>
