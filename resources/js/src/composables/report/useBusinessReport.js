import ReportResource from "@/api/report"
import {computed, reactive, ref} from "@vue/composition-api";
import currency from "@/utils/currency";

const reportApi = new ReportResource()

//state
const state = reactive({
  'revenue': 0,
  'buyers': 0,
  'items': 0,
  'orders': 0,
  'products': 0,
  'totalSales': 0,
  'successfulOrders': 0,
})
const error = ref('')

export const useBusinessReport = () => {
  //mutations
  const setRevenue = (value) => {
    state.revenue = currency.formatPrice(value)
  }
  const setBuyers = (value) => {
    state.buyers = value
  }
  const setItems = (value) => {
    state.items = value
  }
  const setOrders = (value) => {
    state.orders = value
  }
  const setProducts = (value) => {
    state.products = value
  }
  const setTotalSales = (value) => {
    state.totalSales = value
  }
  const setSuccessfulOrders = (value) => {
    state.successfulOrders = value
  }

  const setBusinessReport = (data) => {
    setRevenue(data.revenue)
    setBuyers(data.buyers)
    setItems(data.items)
    setOrders(data.orders)
    setProducts(data.products)
    setTotalSales(data.totalSales)
    setSuccessfulOrders(data.successfulOrders)
  }

  const setError = (message) => {
    error.value = message
  }

  //actions
  const fetchBusiness = async () => {
    const response = await reportApi.business()

    if (response.success) {
      setBusinessReport(response.data)
    } else {
      setError(response.message)
    }
  }

  //getters
  const getRevenue = computed(() => state.revenue)
  const getBuyers = computed(() => state.buyers)
  const getItems = computed(() => state.items)
  const getOrders = computed(() => state.orders)
  const getProducts = computed(() => state.products)
  const getTotalSales = computed(() => state.totalSales)
  const getSuccessfulOrders = computed(() => state.successfulOrders)

  return {
    fetchBusiness,
    getRevenue,
    getBuyers,
    getItems,
    getOrders,
    getProducts,
    getTotalSales,
    getSuccessfulOrders,
  }
}
