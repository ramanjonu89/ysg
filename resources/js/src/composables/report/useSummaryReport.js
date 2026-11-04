import ReportResource from "@/api/report"
import {computed, reactive, ref} from "@vue/composition-api";

const reportApi = new ReportResource()

//state
const state = reactive({
  'pending': 0,
  'unpaid': 0,
  'cancel': 0,
  'lowStockProducts': 0
})
const error = ref('')

export const useSummaryReport = () => {
  //mutations
  const setPending = (value) => {
    state.pending = value
  }
  const setUnpaid = (value) => {
    state.unpaid = value
  }
  const setCancel = (value) => {
    state.cancel = value
  }
  const setLowStockProducts = (value) => {
    state.lowStockProducts = value
  }

  const setSummaryReport = (data) => {
    setPending(data.pending)
    setUnpaid(data.unpaid)
    setCancel(data.cancel)
    setLowStockProducts(data.lowStockProducts)
  }

  const setError = (message) => {
    error.value = message
  }

  //actions
  const fetchSummary = async () => {
    const response = await reportApi.summary()

    if (response.success) {
      setSummaryReport(response.data)
    } else {
      setError(response.message)
    }
  }

  //getters
  const getPending = computed(() => state.pending)
  const getUnpaid = computed(() => state.unpaid)
  const getCancel = computed(() => state.cancel)
  const getLowStockProducts = computed(() => state.lowStockProducts)

  return {
    fetchSummary,
    getPending,
    getUnpaid,
    getCancel,
    getLowStockProducts,
  }
}
