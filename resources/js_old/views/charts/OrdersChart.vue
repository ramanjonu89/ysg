<template>
  <CChartLine
    :datasets="defaultDatasets"
    :options="defaultOptions"
    :labels="labels"
  />
</template>

<script>
import { CChartLine } from '@coreui/vue-chartjs'
import { getStyle, hexToRgba } from '@coreui/utils/src'
import ReportResource from '@/api/report';

const reportApi = new ReportResource();

function random (min, max) {
  return Math.floor(Math.random() * (max - min + 1) + min)
}

export default {
  name: 'OrdersChart',
  components: {
    CChartLine
  },
  data() {
    return {
      chartData: {},
      labels: [],
      values: [],
    };
  },
  methods: {
    async getOrdersChartData() {
      const { data, success = false } = await reportApi.ordersChartData();
      this.labels = Object.keys(data);
      this.values = Object.values(data);
      console.log(this.labels);
      console.log(this.values);
      return data;
    },
  },
  created() {
    this.chartData = this.getOrdersChartData();
  },
  computed: {
    defaultDatasets () {
      const brandInfo = getStyle('info') || '#20a8d8'
      return [
        {
          label: this.$t('Orders'),
          backgroundColor: hexToRgba(brandInfo, 10),
          borderColor: brandInfo,
          pointHoverBackgroundColor: brandInfo,
          borderWidth: 2,
          data: this.values
        },
      ]
    },
    defaultOptions () {
      return {

        maintainAspectRatio: false,
        legend: {
          display: false
        },
        scales: {
          xAxes: [{
            gridLines: {
              drawOnChartArea: false
            }
          }],
          yAxes: [{
            ticks: {
              beginAtZero: true,
              maxTicksLimit: 5,
            },
            gridLines: {
              display: true
            }
          }]
        },
        elements: {
          point: {
            radius: 0,
            hitRadius: 10,
            hoverRadius: 4,
            hoverBorderWidth: 3
          }
        }
      }
    }
  }
}
</script>
