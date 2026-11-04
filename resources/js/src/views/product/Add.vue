<template>
  <product-form />
</template>

<script>
import ProductForm from './components/Form/Index'
import {useProduct} from "@/composables/product/useProduct";
import {provide} from "@vue/composition-api";

export default {
  name: "Add",
  components: {
    ProductForm,
  },
  setup() {
    const { product } = useProduct();
    provide('product', product);
  }
}
</script>
