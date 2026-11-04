<template>
  <b-row>
    <b-col xl="12">
      <b-card>
        <b-card-header>
          {{ $t('List') }}
        </b-card-header>
        <b-card-body>
          <b-overlay
              :show="loading"
              rounded="sm"
          >

            <b-table-simple small responsive>
              <b-thead>
                <b-tr>
                  <b-th>INDEX</b-th>
                  <b-th>TITLE</b-th>
                  <b-th>TYPE</b-th>
                  <b-th>ACTIVE</b-th>
                  <b-th>ACTIONS</b-th>
                </b-tr>
              </b-thead>

              <draggable v-model="list" tag="tbody" class="cursor-move" ghost-class="ghost-tr" @change="onChange">
                <b-tr v-for="(item, index) in list" :key="item.id">
                  <b-td>{{ index + 1 }}</b-td>
                  <b-td>
                    <b class="text-info">
                      <b-link :to="{ name: 'settings.blocks.edit', params: { id: item.id } }">
                        {{ item.name }}
                      </b-link>
                    </b>
                  </b-td>
                  <b-td>
                    <b class="text-info">{{ item.type }}</b>
                  </b-td>
                  <b-td>
                    <b-form-checkbox
                        v-model="item.is_enabled"
                        name="check-button"
                        switch
                        inline
                        @change="triggerEnabled(item)"
                    >
                      <span class="switch-icon-left">
                        <feather-icon icon="CheckIcon"/>
                      </span>
                      <span class="switch-icon-right">
                        <feather-icon icon="XIcon"/>
                      </span>
                    </b-form-checkbox>
                  </b-td>
                  <b-td>
                    <b-button
                        v-ripple.400="'rgba(113, 102, 240, 0.15)'"
                        variant="flat-primary"
                        class="btn-icon rounded-circle"
                        :to="{ name: 'settings.blocks.edit', params: { id: item.id } }"
                        :id="`block-${item.id}-edit`"
                    >
                      <feather-icon icon="EditIcon"/>
                    </b-button>
                    <b-button
                        v-ripple.400="'rgba(234, 84, 85, 0.15)'"
                        variant="flat-danger"
                        class="btn-icon rounded-circle"
                        @click="destroyItem(item.id)"
                        :id="`block-${item.id}-delete`"
                    >
                      <feather-icon icon="TrashIcon"/>
                    </b-button>
                  </b-td>
                </b-tr>
              </draggable>
            </b-table-simple>

          </b-overlay>
        </b-card-body>
      </b-card>

    </b-col>
  </b-row>
</template>

<script>
import {
  BTable,
  BRow,
  BCol,
  BCard,
  BCardBody,
  BCardHeader,
  BDropdown,
  BTableSimple,
  BDropdownItem,
  BTooltip,
  BImg,
  BButton,
  BOverlay,
  BModal,
  VBModal,
  BForm,
  BFormCheckbox,
  BFormInput,
  BFormGroup,
  BLink,
  BThead,
  BTbody,
  BTr,
  BTd,
  BTh,
  BTfoot,
} from 'bootstrap-vue';
import Ripple from 'vue-ripple-directive';
import vSelect from "vue-select";
import ToastificationContent from "@/@core/components/toastification/ToastificationContent";
import draggable from "vuedraggable";

import SettingFeatureProduct from "../../../api/setting-feature-product";
import SettingBlock from "@/api/setting-block";
import blockService from "./blockService";

const featureProductApi = new SettingFeatureProduct();
const blockApi = new SettingBlock();

export default {
  name: 'BlockList',
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
    BFormCheckbox,
    BFormInput,
    BFormGroup,
    BLink,
    BTableSimple,
    BThead,
    BTbody,
    BTr,
    BTd,
    BTh,
    BTfoot,
    draggable
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
        {key: 'title', label: 'Name'},
        {key: 'type', label: 'Block Type'},
        {key: 'actions', label: 'Actions'},
      ],
      selected: {},
      options: []
    }
  },
  created() {
    this.getData();
  },
  methods: {
    async getData() {
      this.loading = true;
      const {data, success} = await blockApi.list();
      if (success) {
        this.list = data.items;
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
        }
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
      let {success, message} = await blockApi.updateSort(id, data);
      if (success) {
        this.success(this.$t('Update Sort Success'));
      } else {
        this.error(this.$t('Update Sort Error'), message);
      }
      await this.getData();
      this.loading = false;
    },

    async destroyItem(id) {
      this.$bvModal
        .msgBoxConfirm('Remove this block ?', {
          title: 'Confirm',
          size: 'sm',
          okVariant: 'danger',
          okTitle: 'Yes',
          cancelTitle: 'No',
          cancelVariant: 'outline-secondary',
          hideHeaderClose: false,
          centered: true,
        })
        .then((confirm) => {
          if (confirm) {
            this.destroy(id);
          }
        });
    },
    async destroy(id) {
      this.loading = true;
      let {success, message} = await blockApi.destroy(id);
      if (success) {
        this.success(this.$t('Remove Success'));
      } else {
        this.error(this.$t('Remove Error'), message);
      }
      await this.getData();
      this.loading = false;
    },
    async triggerEnabled(item) {
      this.loading = true;
      const {success, message } = await blockApi.switch(item.id, 'is_enabled', item.is_enabled);
      if (success) {
        this.success(this.$t('Update Published Success'));
      } else {
        this.error(this.$t('Update Published Error'), message);
      }
      this.loading = false;
    },
    async onChange(evt) {
      console.log(evt);
      this.loading = true;
      console.log();
      let {success, message} = await blockApi.updateListSort(this.list);
      if (success) {
        this.success(this.$t('Update Sort Success'));
      } else {
        this.error(this.$t('Update Sort Error'), message);
      }
      this.loading = false;
    }

  },
  watch: {
    list: {
      // async handler(val) {
      //   if (val.length > 0) {
      //     this.loading = true;
      //     await blockApi.updateListSort(val);
      //     this.loading = false;
      //   }
      // }
    }
  },
}
</script>

<style lang="scss">
@import '@/@core/scss/vue/libs/vue-select.scss';
</style>
<style type="text/css" scoped>
.ghost-tr {
  opacity: 0.3;
  background: #f9ffd5;
}
</style>
