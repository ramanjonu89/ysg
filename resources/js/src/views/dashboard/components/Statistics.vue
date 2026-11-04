<template>
  <b-card
    v-if="data"
    no-body
    class="card-statistics"
  >
    <b-card-header>
      <b-card-title>{{ title }}</b-card-title>
    </b-card-header>
    <b-card-body class="statistics-body">
      <b-row>
        <b-col
          v-for="item in data"
          :key="item.icon"
          :xl="12/data.length"
          sm="6"
          :class="item.customClass"
        >
          <b-media no-body>
            <b-media-aside

              class="mr-1"
            >
              <b-avatar
                size="48"
                :variant="item.color"
              >
                <div v-if="item.subtitle === 'Pendding'">
                  <span class="iconify" data-icon="bi:hourglass" style="width: 2rem; height: auto"></span>
                </div>
                <div v-else-if="item.subtitle === 'Unpaid'">
                  <span class="iconify" data-icon="octicon:credit-card-24" style="width: 2rem; height: auto"></span>
                </div>
                <div v-else-if="item.subtitle === 'Total Sales' || item.subtitle === 'Orders'">
                  <span class="iconify" data-icon="akar-icons:cart" style="width: 2rem; height: auto"></span>
                </div>
                <div v-else-if="item.subtitle === 'Items'">
                  <span class="iconify" data-icon="fe:list-order" style="width: 2rem; height: auto"></span>
                </div>
                <div v-else>
                  <feather-icon
                      size="24"
                      :icon="item.icon"
                  />
                </div>
              </b-avatar>
            </b-media-aside>
            <b-media-body class="my-auto">
              <h4 class="font-weight-bolder mb-0">
                {{ item.title }}
              </h4>
              <b-card-text class="font-small-3 mb-0">
                {{ item.subtitle }}
              </b-card-text>
            </b-media-body>
          </b-media>
        </b-col>
      </b-row>
    </b-card-body>
  </b-card>
</template>

<script>
import {
  BCard, BCardHeader, BCardTitle, BCardText, BCardBody, BRow, BCol, BMedia, BMediaAside, BAvatar, BMediaBody,
} from 'bootstrap-vue'

export default {
  components: {
    BRow,
    BCol,
    BCard,
    BCardHeader,
    BCardTitle,
    BCardText,
    BCardBody,
    BMedia,
    BAvatar,
    BMediaAside,
    BMediaBody,
  },
  props: {
    data: {
      type: Array,
      default: () => [],
    },
    title: {
      type: String,
      default: () => 'Report'
    }
  },
}
</script>
