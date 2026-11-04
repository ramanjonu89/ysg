import CategoryResource from '@/api/category'
import {computed, reactive, inject} from "@vue/composition-api";

const categoryApi = new CategoryResource()

export const useCategory = () => {
  // State
  const state = reactive({
    categoryTree: [],
    categoriesBySeller: [],
    error: '',
    loadingTree: false
  });

  // Mutation
  const setCategoriesBySeller = (categories) => {
    state.categoriesBySeller = categories;
  }

  const setCategoryTree = (tree) => {
    state.categoryTree = tree
  }

  const setLoadingTree = (val) => {
    state.loadingTree = val
  }

  const setError = (message) => {
    state.error = message
  }

  // actions
  const getCategoriesBySeller = async () => {
    const response = await categoryApi.getCategoriesBySeller();
    if (response.success) {
      let categoriesOptions = [{value: null, text: 'Categories'}, ...response.data]
      setCategoriesBySeller(categoriesOptions);
      return true
    } else {
      setError(response.message)
      return false
    }
  }

  const getCategoryTree = async () => {
    const response = await categoryApi.tree();
    if (response.success) {
      setCategoryTree(response.data);
      setLoadingTree(true)
      return true;
    } else {
      setError(response.message);
      return false;
    }
  }

  // getters
  const categoriesBySeller = computed(() => state.categoriesBySeller);
  const categoryTree = computed(() => state.categoryTree);
  const subCategories = computed(() => {
    const cat = state.categoryTree[inject('product').value.category_id]
    return cat === undefined ? {} : cat['child'];
  })
  const subsubCategories = computed(() => {
    if (inject('product').value.category_id === undefined || state.categoryTree.length === 0) {
      return {}
    }

    const catId = inject('product').value.category_id;
    if (catId == undefined) {
      return {};
    }
    const subcatId = inject('product').value.subcategory_id;
    const cat =
      subcatId == null
        ? undefined
        : state.categoryTree[catId]["child"][subcatId];
    return cat === undefined ? {} : cat['child'];
  })

  const loadingTree = computed(() => state.loadingTree);
  const error = computed(() => state.error);

  return {
    getCategoriesBySeller,
    getCategoryTree,

    categoriesBySeller,
    categoryTree,
    subCategories,
    subsubCategories,
    loadingTree,
    error
  }
}
