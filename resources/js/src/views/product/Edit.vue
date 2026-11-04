<template>
  <div>
    <b-overlay :show="loading" rounded="sm">
      <product-form />
    </b-overlay>
  </div>
</template>

<script>
import { BOverlay } from 'bootstrap-vue'
import ProductForm from './components/Form/Index'
import { useProduct } from '@/composables/product/useProduct'
import { provide, onUpdated } from '@vue/composition-api'
import { useRouter } from '@core/utils/utils'

export default {
  name: 'Edit',
  components: {
    BOverlay,
    ProductForm,
  },
  setup() {
    const { route, router } = useRouter()
    const { error, loading, product, fetchProduct } = useProduct()

    const id = route.value.params.id
    fetchProduct(id)

    onUpdated(() => {
      if (error.value !== '') {
        router.push({name: 'products'})
      }
    })

    provide('product', product)
    return {
      loading,
    }
  },
}
</script>
