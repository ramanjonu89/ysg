<template>
  <b-row>
    <b-col xl="12">
      <b-card>
        <b-card-body>
          <b-overlay
              :show="loading"
              rounded="sm"
          >

            <div>
              <b-table-simple small responsive>
                <b-thead>
                  <b-tr>
                    <b-th>INDEX</b-th>
                    <b-th>PRODUCT NAME</b-th>
                    <b-th>ACTION</b-th>
                  </b-tr>
                </b-thead>
                <draggable v-model="list" tag="tbody" handle=".handle" @change="onChange">
                  <b-tr v-for="(item, index) in list" :key="item.id">
                    <b-td>{{ index + 1 }}</b-td>
                    <b-td>
                      <b class="text-info">
                      <b-img :width="100" thumbnail fluid :src="'/' + item.thumbnail_img" alt="Image 1"></b-img>
                      {{ item.name }}</b>
                    </b-td>
                    <b-td>
                      <div class="text-nowrap">
                        <b-button
                            v-ripple.400="'rgba(234, 84, 85, 0.15)'"
                            variant="flat-success"
                            :pressed="true"
                            class="handle btn-icon rounded-circle cursor-move"
                            :id="`item-${item.id}-move`"
                        >
                          <feather-icon icon="MoveIcon"/>
                        </b-button>
                      </div>
                    </b-td>
                  </b-tr>
                </draggable>
              </b-table-simple>
            </div>

          </b-overlay>
        </b-card-body>
      </b-card>

    </b-col>
  </b-row>
</template>

<script>
import {
  BTable, BRow, BCol, BCard, BCardBody, BCardHeader, BDropdown, BDropdownItem, BTooltip, BImg, BButton, BOverlay,
  BModal, VBModal, BForm, BFormInput, BFormGroup, BTableSimple, BThead, BTbody, BTr, BTd, BTh, BTfoot,
} from 'bootstrap-vue';
import Ripple from 'vue-ripple-directive';
import vSelect from "vue-select";
import ToastificationContent from "@/@core/components/toastification/ToastificationContent";
import draggable from "vuedraggable";

import ProductResource from "@/api/product";
import ProductListModal from "@/views/components/ProductListModal";

const productApi = new ProductResource();

export default {
  name: 'SortIndex',
  components: {
    BOverlay,
    BRow,
    BCol,
    BCard,
    BCardBody,
    BCardHeader,
    BTable,
    BDropdown,
    BDropdownItem,
    BTooltip,
    BImg,
    BButton,
    ToastificationContent,
    vSelect,
    BModal,
    BForm,
    BFormInput,
    BFormGroup,

    BTableSimple,
    BThead,
    BTbody,
    BTr,
    BTd,
    BTh,
    BTfoot,

    draggable,
    ProductListModal,
  },
  directives: {
    'b-modal': VBModal,
    Ripple,
  },
  data() {
    return {
      loading: true,
      list: [],
      fields: [
        // A virtual column that doesn't exist in items
        'index',
        {key: 'name', label: 'Product Name'},
        {key: 'image', label: 'Product Image'},
        {key: 'actions', label: 'Actions'},
      ],
      selected: [],
      options: [],
    }
  },
  computed: {},
  created() {
    this.getData();
  },
  methods: {
    onChange(evt) {
      console.log(evt);
      const id = evt.moved.element.id;
      const data = {
        newIndex: evt.moved.newIndex,
        oldIndex: evt.moved.oldIndex,
      }
      this.updateSort(id, data);
    },

    async getData() {
      this.loading = true;
      const {data, success} = await productApi.list({order_by: 'frontend_sorting'});
      if (success) {
        this.list = data.items;

        this.selected = this.list.map(function (item) {
          return item.id;
        });

      }
      this.loading = false;
    },
    success(message) {
      this.$toast({
            component: ToastificationContent,
            props: {
              title: this.$t('Success'),
              icon: 'CheckIcon',
              text: message,
              variant: 'success',
            },
          },
          {
            position: 'bottom-center'
          })
    },
    error(title, message) {
      this.$toast({
            component: ToastificationContent,
            props: {
              title: title,
              icon: 'AlertCircleIcon',
              text: message,
              variant: 'danger',
            }
          },
          {
            position: 'bottom-center'
          })
    },
    async updateSort(id, data) {
      this.loading = true;
      let {success, message} = await productApi.updateSort(id, data);
      if (success) {
        this.success(this.$t('Update Sort Success'));
      } else {
        this.error(this.$t('Update Sort Error'), message);
      }
      await this.getData();
      this.loading = false;
    },
  }
}
</script>

<style lang="scss">
@import '@/@core/scss/vue/libs/vue-select.scss';
</style>
<style type="text/css" scoped>
.cursor-pointer {
  cursor: pointer;
}

.cursor-move {
  cursor: move !important;
}
</style>
