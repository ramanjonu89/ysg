<template>
  <CRow>
    <CCol sm="6" lg="3">
      <CWidgetDropdown color="primary" :header="revenue" text="Revenue">
        <template #footer>
          <CChartLineSimple
            pointed
            class="mt-3 mx-3"
            style="height:70px"
            :data-points="[65, 59, 84, 84, 51, 55, 40]"
            point-hover-background-color="primary"
            label="Orders"
            labels="months"
          />
        </template>
      </CWidgetDropdown>
    </CCol>
    <CCol sm="6" lg="3">
      <CWidgetDropdown color="info" :header="buyers" text="Buyers">
        <template #footer>
          <CChartLineSimple
            pointed
            class="mt-3 mx-3"
            style="height:70px"
            :data-points="[1, 18, 9, 17, 34, 22, 11]"
            point-hover-background-color="info"
            :options="{ elements: { line: { tension: 0.00001 }}}"
            label="Orders"
            labels="months"
          />
        </template>
      </CWidgetDropdown>
    </CCol>
    <CCol sm="6" lg="3">
      <CWidgetDropdown
        color="warning"
        :header="items"
        text="Total No. of Items sold"
      >
        <template #footer>
          <CChartLineSimple
            class="mt-3"
            style="height:70px"
            background-color="rgba(255,255,255,.2)"
            :data-points="[78, 81, 80, 45, 34, 12, 40]"
            :options="{ elements: { line: { borderWidth: 2.5 }}}"
            point-hover-background-color="warning"
            label="Orders"
            labels="months"
          />
        </template>
      </CWidgetDropdown>
    </CCol>
    <CCol sm="6" lg="3">
      <CWidgetDropdown
        color="danger"
        :header="orders"
        text="Total No. of orders"
      >
        <template #footer>
          <CChartBarSimple
            class="mt-3 mx-3"
            style="height:70px"
            background-color="rgb(250, 152, 152)"
            label="Members"
            labels="months"
          />
        </template>
      </CWidgetDropdown>
    </CCol>
  </CRow>
</template>

<script>
import { CChartLineSimple, CChartBarSimple } from '../charts/index.js'
import ReportResource from '@/api/report';
import Currency from '@/utils/currency';

const reportApi = new ReportResource();

export default {
  name: 'WidgetsBusiness',
  components: { CChartLineSimple, CChartBarSimple },
  data() {
    return {
      revenue: '0',
      buyers: '0',
      items: '0',
      orders: '0',
    };
  },
  created() {
    this.getBusiness();
  },
  methods: {
    async getBusiness() {
      const { data, success = false } = await reportApi.business();
      this.revenue = Currency.formatPrice(data.revenue).toString();
      this.buyers = data.buyers.toString();
      this.items = data.items.toString();
      this.orders = data.orders.toString();
    },
  }
}
</script>
