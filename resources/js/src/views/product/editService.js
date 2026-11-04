import { ref, reactive, computed, watch, nextTick } from '@vue/composition-api';
import ProductResource from "@/api/product";
import { useToast } from 'vue-toastification/composition'

const stage = ref(
  {
    product: {
      photos: [],
      category_id: 0,
      subcategory_id: 0,
      subsubcategory_id: 0,
    },
    categoryTree: [],
    category1Options: [],
    brandOptions: []
  }
);

export default function editService() {

  const productApi = new ProductResource();
  const toast = useToast();
  const loading = ref(false);

  function clear() {
    stage.value.product = {
      published: true,
      photos: [],
      tax_type: 'amount',
      discount_type: 'amount',
    };
  }

  function addGalleryImage() {
    console.log('addGalleryImage');
    stage.value.product.photos.push('');
  }

  function updateGalleryImage(data, index) {
    console.log('updateGalleryImage');
    stage.value.product.photos[index] = data;
  }

  function removeGalleryImage(index) {
    console.log('removeGalleryImage');
    stage.value.product.photos.splice(index, 1);
  }

  function updateThumb(data) {
    console.log('updateThumb');
    stage.value.product.thumbnail_img = data;
  }

  function removeThumb() {
    console.log('removeThumb');
    stage.value.product.thumbnail_img = '';
  }

  function updateMetaImage(data) {
    console.log('updateMetaImage');
    stage.value.product.meta_img = data;
  }

  async function getSelectOptions() {
    loading.value = true;
    const { data, success , message } = await productApi.getSelectOptions();
    if (success) {
      stage.value.brandOptions = data.brands;
      stage.value.categoryTree = data.categoryTree;
    } else {
      toast.error(message);
    }
    loading.value = false;
  }

  async function get(id) {
    loading.value = true;
    const { data, success , message } = await productApi.get(id);
    if (success) {
      data.product.photos = JSON.parse(data.product.photos);
      stage.value.product = data.product;
    } else {
      toast.error(message);
    }
    loading.value = false;
  }

  async function store() {
    loading.value = true;
    const { errors, success , message } = await productApi.store(stage.value.product);
    if (success) {
      toast.success('Add product success');
    } else {
      toast.error(message);
    }
    loading.value = false;
    return errors;
  }

  async function update() {
    loading.value = true;
    const { errors, success , message } = await productApi.update(stage.value.product.id, stage.value.product);
    if (success) {
      toast.success('Save product success');
    } else {
      toast.error(message);
    }
    loading.value = false;
    return errors;
  }

  async function destroy(id) {

  }

  const category2Options = ref([]);
  const category2Required = ref(false);
  const category3Options = ref([]);
  const category3Required = ref(false);

  const cate2 = computed(() => {
    const cate = stage.value.categoryTree[stage.value.product.category_id];
    return cate === undefined ? [] : cate['children'];
  })

  return {
    loading: computed(() => loading.value),
    product: computed(() => stage.value.product),
    brandOptions: computed(() => stage.value.brandOptions),
    categoryTree: computed(() => stage.value.categoryTree),
    category2Options,
    category2Required,
    category3Options,
    category3Required,
    cate2,
    get,
    getSelectOptions,
    clear,
    store,
    update,
    destroy,
    updateThumb,
    removeThumb,
    addGalleryImage,
    updateGalleryImage,
    removeGalleryImage,
    updateMetaImage,
  }
}
