<template>
  <section id="dashboard-ecommerce">
    <b-row class="match-height">
      <b-col
        xl="5"
        md="6"
      >
        <statistics :data="statisticsItems" :title="$t('Orders Statistics')"/>
      </b-col>
      <b-col
        xl="7"
        md="6"
      >
        <statistics :data="statisticsBusinessItems" :title="$t('Statistics')"/>
      </b-col>
    </b-row>

    <b-card>
      <b-card-header>
        <!-- title and subtitle -->
        <div>
          <b-card-title>
            {{ $t('Orders Statistics') }}
          </b-card-title>
        </div>
        <!--/ title and subtitle -->

      </b-card-header>

      <b-card-body>
        <div class="d-flex">
          <div class="mr-1">
            <label for="from-date">{{ $t('From Date') }}:</label>
            <b-form-datepicker
                id="from-date"
                locale="en"
                :max="toDate"
                v-model="fromDate"
                @input="fetchOrdersChartData"
            />
          </div>
          <div>
            <label for="to-date">{{ $t('To Date') }}:</label>
            <b-form-datepicker
                id="to-date"
                locale="en"
                :max="new Date()"
                :min="fromDate"
                v-model="toDate"
                @input="fetchOrdersChartData"
            />
          </div>
        </div>
        <vue-apex-charts
          type="line"
          height="400"
          :options="lineChartSimple.chartOptions"
          :series="lineChartSimple.series"
        />
      </b-card-body>
    </b-card>
  </section>
</template>

<script>
import {computed} from "@vue/composition-api"
import Statistics from "@/views/dashboard/components/Statistics"
import {useSummaryReport} from "@/composables/report/useSummaryReport"
import {useBusinessReport} from "@/composables/report/useBusinessReport"
import {useOrdersChartReport} from "@/composables/report/useOrdersChartReport"
import {
  BRow, BCol, BCard, BCardBody, BCardHeader, BCardTitle, BCardSubTitle, BFormDatepicker, BCardText
} from 'bootstrap-vue'
import VueApexCharts from 'vue-apexcharts'

export default {
  name: 'Dashboard',
  components: {
    Statistics,
    BRow,
    BCol,
    BCard,
    BCardBody,
    BCardHeader,
    BCardTitle,
    BCardSubTitle,
    BFormDatepicker,
    BCardText,
    VueApexCharts,
  },
  setup() {
    const {fetchSummary, getUnpaid, getCancel, getPending} = useSummaryReport()
    const {fetchBusiness, getRevenue, getOrders, getBuyers, getItems, getProducts, getTotalSales, getSuccessfulOrders} = useBusinessReport()
    const {fetchOrdersChartData, getOrdersChartData, fromDate, toDate} = useOrdersChartReport();


    fetchSummary()
    fetchBusiness()
    fetchOrdersChartData()

    const statisticsItems = computed(() => {
      return [
        {
          icon: 'ClockIcon',
          color: 'light-primary',
          title: getPending.value,
          subtitle: 'Pending',
          customClass: 'mb-2 mb-xl-0',
        },
        {
          icon: 'DollarSignIcon',
          color: 'light-info',
          title: getUnpaid.value,
          subtitle: 'Unpaid',
          customClass: 'mb-2 mb-xl-0',
        },
        {
          icon: 'XCircleIcon',
          color: 'light-danger',
          title: getCancel.value,
          subtitle: 'Cancel',
          customClass: 'mb-2 mb-sm-0',
        },
      ]
    })

    const statisticsBusinessItems = computed(() => {
      return [
        {
          icon: 'BoxIcon',
          color: 'light-info',
          title: getProducts.value,
          subtitle: 'Products',
          customClass: 'mb-2 mb-xl-0',
        },
        {
          icon: 'ShoppingCartIcon',
          color: 'light-info',
          title: getTotalSales.value,
          subtitle: 'Total Sales',
          customClass: 'mb-2 mb-xl-0 pr-0',
        },
        {
          icon: 'DollarSignIcon',
          color: 'light-primary',
          title: getRevenue.value,
          subtitle: 'Total Earnings',
          customClass: 'mb-2 mb-xl-0 pl-0',
        },
        {
          icon: 'CheckSquareIcon',
          color: 'light-primary',
          title: getSuccessfulOrders.value,
          subtitle: 'Successful Orders',
          customClass: 'mb-2 mb-xl-0',
        },
        // {
        //   icon: 'DollarSignIcon',
        //   color: 'light-info',
        //   title: getOrders.value,
        //   subtitle: 'Orders',
        //   customClass: 'mb-2 mb-xl-0',
        // },
        // {
        //   icon: 'UsersIcon',
        //   color: 'light-success',
        //   title: getBuyers.value,
        //   subtitle: 'Buyers',
        //   customClass: 'mb-2 mb-sm-0',
        // },
        // {
        //   icon: 'CodesandboxIcon',
        //   color: 'light-danger',
        //   title: getItems.value,
        //   subtitle: 'Items',
        //   customClass: 'mb-2 mb-sm-0',
        // },
      ]
    })

    const lineChartSimple = computed(() => {
      return {
        series: [
          {
            data: Object.values(getOrdersChartData.value),
          },
        ],
        chartOptions: {
          chart: {
            zoom: {
              enabled: false,
            },
            toolbar: {
              show: false,
            },
          },
          markers: {
            strokeWidth: 7,
            strokeOpacity: 1,
          },
          dataLabels: {
            enabled: false,
          },
          stroke: {
            curve: 'straight',
          },
          grid: {
            xaxis: {
              lines: {
                show: true,
              },
            },
          },
          tooltip: {
            custom(data) {
              return `${'<div class="px-1 py-50"><span>'}${
                data.series[data.seriesIndex][data.dataPointIndex]
              }</span></div>`
            },
          },
          xaxis: {
            categories: Object.keys(getOrdersChartData.value)
          },
          yaxis: {
            // opposite: isRtl,
          },
        },
      }
    })

    return {
      statisticsItems,
      statisticsBusinessItems,
      lineChartSimple,
      fromDate,
      toDate,
      fetchOrdersChartData,
    }
  }
}
</script>
