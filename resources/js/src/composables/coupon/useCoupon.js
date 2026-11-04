import CouponResource from "@/api/coupon";
import {computed, reactive} from "@vue/composition-api";
import {mergeAndRemoveDuplicateTwoArrays} from "@/utils/array"
import { useToast } from 'vue-toastification/composition'
import ToastificationContent from "@/@core/components/toastification/ToastificationContent";

const couponApi = new CouponResource();

export const useCoupon = formRef => {
  const toast = useToast();
  // state
  const state = reactive({
    error: '',
    formError: {},
    coupon: {
      title: '',
      code: '',
      description: '',
      type: 'cart_base',
      discount: '',
      discount_type: 'amount',
      details: {
        'min_buy': 0,
        'max_discount': 0,
        'max_coupon': 1,
        'products': [],
      },
      start_date: '',
      end_date: '',
      published: true,
      is_active: true,
      only_once: false,
    },
  })

  // mutations
  const setError = (message) => {
    state.error = message
  }
  const setFormError = (data) => {
    formRef.value.setErrors(data)
  }
  const setCoupon = (data) => {
    state.coupon = data
  }

  const setDetailProducts = (data) => {
    state.coupon.details.products = mergeAndRemoveDuplicateTwoArrays(state.coupon.details.products, data)
  }

  // actions
  const createCoupon = async (data) => {
    const { response, success, message } = await couponApi.store(data)
    if (success) {
      toastSuccess('Success', 'Add coupon success')
      return true
    } else {
      toastError('Error', message)
      // setError(response.message)
      // setFormError({})
      // if (response.errors) {
      //   setFormError(response.errors)
      // }

      return false
    }
  }

  function toastSuccess(title, text) {
    toast.success(
      {
        component: ToastificationContent,
        props: {
          title: title,
          icon: 'CheckIcon',
          text: text,
          variant: 'success',
        }
      });
  }

  function toastError(title, text) {
    toast.error({
      component: ToastificationContent,
      props: {
        title: title,
        icon: 'AlertCircleIcon',
        text: text,
        variant: 'danger',
      },
    })
  }

  const updateCoupon = async (id, data) => {
    const { errors, success , message } = await couponApi.update(id, data)
    if (success) {
      toastSuccess('Success', 'Update coupon success')
      return true
    } else {
      toastError('Error',message)
      return false
    }
  }

  const fetchCoupon = async id => {
    const response = await couponApi.get(id)
    if (response.success) {
      setCoupon(response.data)
    } else {
      setError(response.message)
    }
  }

  // getters
  const error = computed(() => state.error)
  const formError = computed(() => state.formError)
  const coupon = computed(() => state.coupon)
  const shortCode = computed(() => state.coupon.code.substring(5))

  return {
    error,
    formError,
    coupon,
    shortCode,

    // actions
    createCoupon,
    updateCoupon,
    fetchCoupon,
    setDetailProducts
  }
}
