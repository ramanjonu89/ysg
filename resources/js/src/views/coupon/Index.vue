<template>
  <section class="coupons-list">
    <b-row>
      <b-col xl="12">
        <b-card>
          <b-card-header>
            {{ $t('Coupons') }}
          </b-card-header>

          <b-card-body class="table-list">
            <b-tabs variant="tabs" :active-tab="0">
              <b-tab title="All" lazy>
                <filter-bar/>
                <vtable
                    api-url="/coupons"
                    :fields="fields"
                    data-path="data.items"
                    pagination-path="data.pagination"
                    :append-params="searchParams"
                ></vtable>
              </b-tab>
              <b-tab title="Ongoing" lazy>
                <vtable
                    api-url="/coupons?ongoing=1"
                    :fields="fields"
                    data-path="data.items"
                    pagination-path="data.pagination"
                    :append-params="searchParams"
                ></vtable>
              </b-tab>
              <b-tab title="Expired" lazy>
                <vtable
                    api-url="/coupons?ongoing=0"
                    :fields="fields"
                    data-path="data.items"
                    pagination-path="data.pagination"
                    :append-params="searchParams"
                ></vtable>
              </b-tab>
              <b-tab title="Deactivated" lazy>
                <vtable
                    api-url="/coupons?active=0"
                    :fields="fields"
                    data-path="data.items"
                    pagination-path="data.pagination"
                    :append-params="searchParams"
                ></vtable>
              </b-tab>
            </b-tabs>
          </b-card-body>
        </b-card>
      </b-col>
    </b-row>
  </section>
</template>

<script>
import {BTab, BTabs, BRow, BCol, BCard, BCardBody, BCardHeader} from 'bootstrap-vue'
import Vtable from '@/views/components/Vtable';
import {useUtils as useI18nUtils} from '@core/libs/i18n'
import {ref} from "@vue/composition-api";
import FilterBar from "@/views/coupon/components/FilterBar";
import CouponResource from "@/api/coupon";
import ToastificationContent from "@/@core/components/toastification/ToastificationContent";
import {useRouter} from '@core/utils/utils';
import {parseDate} from '@/utils/helper';
import {useToast} from "vue-toastification/composition";

const couponApi = new CouponResource();

export default {
  name: 'Coupon',
  components: {
    BTab,
    BTabs,
    BRow,
    BCol,
    BCard,
    BCardBody,
    BCardHeader,
    Vtable,
    FilterBar,
    ToastificationContent,
  },
  setup() {
    const {t} = useI18nUtils();
    const {router} = useRouter();
    const toast = useToast();

    const switchField = async (id, field, value) => {
      const response = await couponApi.switch(id, field, value);
      if (response.success) {
        toast({
              component: ToastificationContent,
              props: {
                title: 'Update success',
                icon: 'EditIcon',
                variant: 'success',
              },
            })
      }
    }

    const onEdit = (id) => {
      router.push({name: 'coupons.edit', params: {id: id}})
    }

    const getPath = (id) => {
      return { name: 'coupons.edit', params: { id: id }}
    }

    const fields = ref([
      {name: 'title', title: t('Title')},
      {name: 'code', title: t('Code')},
      {name: 'usages', title: t('Usage')},
      {name: 'total', title: t('Total')},
      {
        name: 'type', title: t('Apply to'), formatter(value) {
          return t(value)
        }
      },
      {
        name: 'start_date', title: t('Start date'), formatter(value) {
          return parseDate(value)
        }
      },
      {
        name: 'end_date', title: t('End date'), formatter(value) {
          return parseDate(value)
        }
      },
      {
        name: '__switch',
        title: 'Active',
        titleClass: 'center aligned',
        dataClass: 'center aligned',
        onChange: switchField,
        updateField: 'is_active'
      },
      {
        name: '__switch',
        title: 'Published',
        titleClass: 'center aligned',
        dataClass: 'center aligned',
        onChange: switchField,
        updateField: 'published'
      },
      {
        name: '__actions',
        title: 'Actions',
        titleClass: 'center aligned',
        dataClass: 'center aligned',
        onEdit: onEdit,
        getPath: getPath
      }
    ]);

    return {
      fields,
      }
  },
  data() {
    return {
      searchParams: {
        code: '',
      }
    }
  },
  created() {

  },
}
</script>
