import ReportResource from "@/api/report"
import {computed, reactive, ref} from "@vue/composition-api";
import moment from "moment";

const reportApi = new ReportResource()

//state
const state = reactive({
  ordersChartData: {},
  fromDate: moment().subtract(1, 'M').format('YYYY-MM-DD'),
  toDate: moment().format('YYYY-MM-DD'),
})
const error = ref('')

export const useOrdersChartReport = () => {
  //mutations
  const setOrderChartData = (data) => {
    state.ordersChartData = data
  }

  const setError = (message) => {
    error.value = message
  }

  //actions
  const fetchOrdersChartData = async () => {
    const params = {
      fromDate: state.fromDate,
      toDate: state.toDate,
    };

    const response = await reportApi.ordersChartData(params)

    if (response.success) {
      setOrderChartData(response.data)
    } else {
      setError(response.message)
    }
  }

  //getters
  const getOrdersChartData = computed(() => state.ordersChartData)

  const fromDate = computed({
    get: () => state.fromDate,
    set: (val) => {
      state.fromDate = val;
    }
  });

  const toDate = computed({
    get: () => state.toDate,
    set: (val) => {
      state.toDate = val;
    }
  });

  return {
    fetchOrdersChartData,
    getOrdersChartData,
    fromDate,
    toDate,
  }
}
