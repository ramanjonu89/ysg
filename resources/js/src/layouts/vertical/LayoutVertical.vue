<template>
  <layout-vertical>

    <router-view />

    <template #navbar="{ toggleVerticalMenuActive }">
      <navbar :toggle-vertical-menu-active="toggleVerticalMenuActive" />
    </template>

    <div
      slot="breadcrumb"
      class="p-1"
    >
      <breadcrumb />
    </div>

    <!-- <app-customizer
      v-if="showCustomizer"
      slot="customizer"
    /> -->
  </layout-vertical>
</template>

<script>
import LayoutVertical from '@core/layouts/layout-vertical/LayoutVertical.vue'
// import AppCustomizer from '@core/layouts/components/app-customizer/AppCustomizer.vue'
// import { $themeConfig } from '@themeConfig'
import Navbar from '../components/Navbar.vue'
import Breadcrumb from '../components/Breadcrumb.vue'

export default {
  components: {
    // AppCustomizer,
    LayoutVertical,
    Navbar,
    Breadcrumb,
  },
  data() {
    return {
      // showCustomizer: $themeConfig.layout.customizer,
    }
  },
}
</script>
