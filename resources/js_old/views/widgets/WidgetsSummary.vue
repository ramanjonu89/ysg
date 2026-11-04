<template>
  <CRow>
    <CCol sm="6" lg="3">
      <CWidgetDropdown color="primary" :header="this.pending" text="Pendding orders">
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
      <CWidgetDropdown color="info" :header="this.unpaid" text="Unpaid orders">
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
        :header="this.cancel"
        text="Cancel Orders"
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
        :header="this.lowStockProducts"
        text="Products - Low stock"
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

const reportApi = new ReportResource();

export default {
  name: 'WidgetsSummary',
  components: { CChartLineSimple, CChartBarSimple },
  data() {
    return {
      unpaid: '0',
      pending: '0',
      cancel: '0',
      lowStockProducts: '0',
    };
  },
  created() {
    this.getSummary();
  },
  methods: {
    async getSummary() {
      const { data, success = false } = await reportApi.summary();
      this.unpaid = data.unpaid.toString();
      this.pending = data.pending.toString();
      this.cancel = data.cancel.toString();
      this.lowStockProducts = data.lowStockProducts.toString();
    },
  }
}
</script>
