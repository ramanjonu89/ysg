import OrderResource from "@/api/order";
import {computed, reactive, ref} from "@vue/composition-api";
import Token from "@/utils/token";
import axios from '@axios'
import { useToast } from 'vue-toastification/composition'
import ToastificationContent from '@/@core/components/toastification/ToastificationContent'

export const useOrder = () => {
  const orderApi = new OrderResource()
  const toast = useToast()
  // state
  const state = reactive({
    order: {
      shippingAddress: {},
      deliveryStatus: '',
    },
    orderItems: [],
    isSelfCollection: false,
    error: '',
  })

  // mutations
  const setOrder = (data) => {
    state.order = data
  }
  const setError = (message) => {
    state.error = message
  }
  const setOrderItems = (items) => {
    state.orderItems = items
  }
  const setIsSelfCollection = (isSelfCollection) => {
    state.isSelfCollection = isSelfCollection;
  }

  // actions
  const fetchOrder = async (id) => {
    const response = await orderApi.get(id)
    if (response.success) {
      setOrder(response.data)
      return true
    } else {
      setError(response.message)
      return false
    }
  }

  const fetchOrderItems = async (orderId) => {
    const response = await orderApi.items(orderId)

    if (response.success) {
      setOrderItems(response.data)
      const isSelfCollection = response.data.items[0].shippingType === 'self_collection';

      setIsSelfCollection(isSelfCollection);
      return true
    } else {
      setError(response.message)
      return false
    }
  }

  const updateOrder = async (id, data) => {
    const { errors, success, message } = await orderApi.update(id, data)
    if (success) {
      toastSuccess('Success', 'Update delivery status success')
      return true
    } else {
      setError(message)
      return false
    }
  }

  const toastSuccess = (title, text) => {
    toast.success({
      component: ToastificationContent,
      props: {
        title: title,
        icon: 'CheckIcon',
        text: text,
        variant: 'success',
      },
    })
  }

  const downloadInvoice = async (id) => {
    await axios(
    {
      method: 'get',
      url: '/api/v1/' + orderApi.uri + '/' + id + '/invoice',
      responseType: 'arraybuffer',
      headers: {"Authorization" : 'Bearer ' + Token.getToken()}
    },
    ).then(function(response) {
      let blob = new Blob([response.data], { type: 'application/pdf' })
      let link = document.createElement('a')
      link.href = window.URL.createObjectURL(blob)
      link.download = 'Report.pdf'
      link.click()
    })
  }
  

  // getters
  const order = computed(() => state.order);
  const orderItems = computed(() => state.orderItems);
  const error = computed(() => state.error);
  const isSelfCollection = computed(() => state.isSelfCollection);

  return {
    fetchOrder,
    fetchOrderItems,
    order,
    orderItems,
    isSelfCollection,
    downloadInvoice,
    updateOrder,
    error,
  };
}
