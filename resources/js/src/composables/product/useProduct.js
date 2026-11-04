import ToastificationContent from '@/@core/components/toastification/ToastificationContent'
import ProductResource from '@/api/product'
import { computed, reactive, ref } from '@vue/composition-api'
import { watch } from '@vue/composition-api/dist/vue-composition-api'
import { useToast } from 'vue-toastification/composition'

const productApi = new ProductResource()

export const useProduct = () => {
  const toast = useToast()
  // state
  const state = reactive({
    product: {
      tax: 0,
      tax_type: "amount",
      discount_type: "amount",
      added_images: [],
      deleted_images: [],
      photos: [],
      thumbnail_img: "",
      published: true,
      choice_options: [],
      min_qty: 1,
      max_qty: 0,
      max_qty_start: null,
      max_qty_end: null,
      stocks: [],
      category_id: null,
      subcategory_id: null,
      subsubcategory_id: null,
      meta_title: null,
      meta_description: null,
      meta_img: null,
    },
    products: [],
    categoryTree: [],
    brandOptions: [],
    allowedAttributes: [],
    error: "",
  });

  const loading = ref(false)

  // mutations
  const setProduct = (data) => {
    state.product = data
    state.product.added_images = [];
    state.product.deleted_images = [];
  }
  const setError = (message) => {
    state.error = message
  }
  const setProducts = (items) => {
    state.products = items
  }
  const setLoading = (loading) => {
    loading.value = loading
  }

  const setAllowedAttributes = (data) => {
    state.allowedAttributes = data
  }

  // actions
  const fetchProduct = async (id) => {
    loading.value = true
    const response = await productApi.get(id)
    loading.value = false

    if (response.success) {
      setProduct(response.data)
      return true
    } else {
      setError(response.message)
      return false
    }
  }

  const fetchProducts = async () => {
    const response = await productApi.list()

    if (response.success) {
      setProducts(response.data)
    } else {
      setError(response.message)
    }
  }

  const fetchAllowedAttributes = async (id) => {
    const response = await productApi.getAllowedAttributes(id)

    if (response.success) {
      setAllowedAttributes(response.data)
      return true
    } else {
      setError(response.message)
      return false
    }
  }

  const findProductByIds = async (ids) => {
    const response = await productApi.getListForVoucherByIds({ ids: ids })
    if (response.success) {
      return response.data
    } else {
      setError(response.message)
      return false
    }
  }

  function updateMetaImage(data) {
    state.product.meta_img = data
  }

  function addGalleryImage() {
    state.product.photos.push('')
  }

  function updateGalleryImage(data, index) {
    state.product.photos[index] = data
  }

  function removeGalleryImage(index) {
    state.product.photos.splice(index, 1)
  }

  function updateThumb(data) {
    state.product.thumbnail_img = data
  }

  function removeThumb() {
    state.product.thumbnail_img = ''
  }

  function toastSuccess(title, text) {
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

  async function updateProduct(id, product) {
    loading.value = true;
    // await convertProductPhotos(product)
    const result = await productApi.update(id, product);
    if (result.error && result.errors) {
      loading.value = false;
      return result; 
    }

    if (result.success) {
      toastSuccess("Success", "Update product success");
    }

    loading.value = false;
    return result;
  }

  async function saveProduct(product) {
    // await convertProductPhotos(product)
    loading.value = true;
    const result = await productApi.store(product);
    if (result.error && result.errors) {
      loading.value = false;
      return result;
    }

    if (result.success) {
      toastSuccess("Success", "Add product success");
    }

    loading.value = false;
    return true;
  }

  async function convertProductPhotos(product) {
    let index = 0;

    for (const photo of product.photos) {
      if (photo instanceof File) {
        product.photos[index] = await toBase64(photo);
      }
      index++;
    }
    if (product.thumbnail_img instanceof File) {
      product.thumbnail_img = await toBase64(product.thumbnail_img);
    }
  }

  const toBase64 = file => new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = () => resolve(reader.result);
    reader.onerror = reject;
  });

  watch(
    () => state.product.discount,
    (value) => {
      if (value !== null && state.product.discount_type === null) {
        state.product.discount_type = 'amount'
      }
    }
  )

  // getters
  const product = computed(() => state.product)
  const products = computed(() => state.products)
  const allowedAttributes = computed(() => state.allowedAttributes)
  const error = computed(() => state.error)

  return {
    fetchProduct,
    fetchProducts,
    findProductByIds,
    fetchAllowedAttributes,
    updateMetaImage,
    updateThumb,
    updateGalleryImage,
    removeGalleryImage,
    addGalleryImage,
    saveProduct,
    updateProduct,

    product,
    products,
    allowedAttributes,
    loading,
    error,
  }
}
