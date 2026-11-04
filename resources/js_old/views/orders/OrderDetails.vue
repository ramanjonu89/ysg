<template>
  <table class="table">
    <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Product</th>
      <th scope="col">Variation</th>
      <th scope="col">Quantity</th>
      <th scope="col">Delivery Type</th>
      <th scope="col">Price</th>
    </tr>
    </thead>
    <tbody>
    <tr>
      <th scope="row">1</th>
      <td>[CORPORATE] BOEKENHOUTSKLOOF THE CHOCOLATE BLOCK 2019</td>
      <td></td>
      <td>1</td>
      <td></td>
      <td>$48.24</td>
    </tr>
    </tbody>
  </table>
</template>

<script>
import Currency from '@/utils/currency';
import moment from 'moment';
import { parseDateTime } from '@/utils/helper';


export default {
  name: 'OrderDetails',
  data () {
    return {

    }
  },
  created() {
  },
  watch: {

  },
  methods: {

  }
}
</script>
