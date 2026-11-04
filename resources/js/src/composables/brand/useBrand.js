import BrandResource from '@/api/brand'
import {computed, reactive} from '@vue/composition-api'

const brandApi = new BrandResource()

export const useBrand = () => {
  //State
  const state = reactive({
    brandsBySeller: [],
    brandOptions: [],
    error: ''
  });

  // Mutation
  const setBrandsBySeller = (brands) => {
    state.brandsBySeller = brands;
  }

  const setBrandOptions = (brands) => {
    state.brandOptions = brands;
  }

  const setError = message => {
    state.error = message
  }

  // Actions
  const getBrandsBySeller = async () => {
    const response = await brandApi.getBrandsBySeller();
    if (response.success) {
      let brandsOption = [{value: null, text: 'Brands'}, ...response.data]
      setBrandsBySeller(brandsOption);
      return true
    } else {
      setError(response.message)
      return false
    }
  }

  const getBrandOptions = async () => {
    const response = await brandApi.getBrandOptions();
    if (response.success) {
      setBrandOptions(response.data);
      return true
    } else {
      setError(response.message)
      return false
    }
  }

  // Getters
  const brandsBySeller = computed(() => state.brandsBySeller)
  const brandOptions = computed(() => state.brandOptions)
  const error = computed(() => state.error)

  return {
    getBrandsBySeller,
    getBrandOptions,

    brandsBySeller,
    brandOptions,
    error
  }
}
