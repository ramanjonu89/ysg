<?php
namespace App\Repositories;

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\SubSubCategory;
use Illuminate\Support\Facades\DB;

class CategoryRepository extends AbstractRepo
{

    const PER_PAGE = 15;

    public function getModel(): string
    {
        return Category::class;
    }

    public function getSub($id) {
        $item = SubCategory::findOrFail($id);
        return $item;
    }

    public function findSubBy($parentId, $column, $value)
    {
        return SubCategory::where($column, $value)->where('category_id', $parentId)->firstOrFail();
    }

    public function getSubSub($id) {
        $item = SubSubCategory::findOrFail($id);
        return $item;
    }

    public function findSubSubBy($parentId, $column, $value)
    {
        return SubSubCategory::where($column, $value)->where('sub_category_id', $parentId)->firstOrFail();
    }

    public function getList($params)
    {
        $status = $params['status'] ?? null;
        $featured = $params['featured'] ?? null;

        $query = $this->model->query();

        if (!is_null($status)) {
            $query->where('status', '=', $status);
        }

        if (!is_null($featured)) {
            $query->where('featured', '=', $featured);
        }

        $perPage = $params['perPage'] ?? self::PER_PAGE;
        if ($perPage != 0) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }

    public function getListSub($params)
    {
        $status = $params['status'] ?? null;
        $query = SubCategory::query();
        if (!is_null($status)) {
            $query->where('status', '=', $status);
        }
        $perPage = $params['perPage'] ?? self::PER_PAGE;
        if ($perPage != 0) {
            return $query->paginate($perPage);
        }
        return $query->get();
    }

    public function getListSubSub($params)
    {
        $status = $params['status'] ?? null;
        $query = SubSubCategory::query();
        if (!is_null($status)) {
            $query->where('status', '=', $status);
        }
        $perPage = $params['perPage'] ?? self::PER_PAGE;
        if ($perPage != 0) {
            return $query->paginate($perPage);
        }
        return $query->get();
    }

    public function getCategories()
    {
        $filter['onlyMicrosite'] = false;
        $allCategories = $this->getFullProducts($filter);
        return $this->model->with(['subcategories' => function($q) use ($allCategories) {
                $q->with(['subsubcategories' => function($qs) use ($allCategories) {
                    $qs->whereIn('id', array_column($allCategories, 'subsubcategory_id'));
                }])->whereIn('id', array_column($allCategories, 'subcategory_id'));
            }])
            ->where('status', '=', 1)
            ->whereIn('id', array_column($allCategories, 'category_id'))
            ->get();
    }

    public function getSubCategories()
    {
        return $this->model->where('status', '=', 1)->whereHas('products')->get();
    }

    public function getCategoriesByShop($shop)
    {
        $filter['userIds'] = [$shop->user->id];
        $allCategories = $this->getFullProducts($filter);

        return $this->model->with(['subcategories' => function($q) use ($allCategories) {
                    $q->with(['subsubcategories' => function($qs) use ($allCategories) {
                        $qs->whereIn('id', array_column($allCategories, 'subsubcategory_id'));
                    }])->whereIn('id', array_column($allCategories, 'subcategory_id'));
                }])
                ->where('status', '=', 1)
                ->whereIn('id', array_column($allCategories, 'category_id'))
                ->get();
    }

    public function getSubCategoriesByShop($shop, $categoryId)
    {
        $filter['userIds'] = [$shop->user->id];
        $filter['categoryId'] = [$categoryId];

        $cats = $this->getFullProducts($filter);

        return $this->generateSubCategories($cats);
    }

    public function getSubCategoriesFlagShip($categoryId, $userIds)
    {
        $filter['userIds']       = $userIds;
        $filter['categoryId']    = [$categoryId];
        $filter['onlyMicrosite'] = 0;
        $allCategories = $this->getFullProducts($filter);

        return $this->generateSubCategories($allCategories);
    }

    public function generateSubCategories($cats)
    {
        $subCats = [];
        $subsubCats = [];
        foreach ($cats as $key => $cat) {
            if ($cat->subcategory_id != null) {
                $subCats[] = $cat->subcategory_id;
            }
            if ($cat->subsubcategory_id != null) {
                $subsubCats[$cat->subcategory_id][] = $cat->subsubcategory_id;
            }
        }

        if (!empty($subCats)) {
            $subCats = array_unique($subCats);
        }

        $result = [];
        $result['subCats'] = [];
        $result['subsubCats'] = [];

        if (!empty($subCats)) {
            $result['subCats'] = SubCategory::whereIn('id', $subCats)->where('status', 1)->get();
            foreach ($subCats as $key => $value) {
                $result['subsubCats'][$value] = [];
                if (!empty($subsubCats[$value])) {
                    $result['subsubCats'][$value] = SubSubCategory::whereIn('id', $subsubCats[$value])->where('status', 1)->get();
                }
            }
        }

        return $result;
    }

    public function getFullProducts(array $filter = [])
    {
        $query = DB::table('products')
                ->where('published', 1)
                // ->where('current_stock', '>',  0)
                ->select(['category_id', 'subcategory_id', 'subsubcategory_id'])
                ->distinct('subsubcategory_id');

        if (!empty($filter['userIds'])) {
            $query->whereIn('user_id', $filter['userIds']);
        }

        if (isset($filter['onlyMicrosite'])) {
            $query->where('only_microsite', $filter['onlyMicrosite']);
        }

        if (!empty($filter['categoryId'])) {
            $query->where('category_id', $filter['categoryId']);
        }

        return $query->get()->toArray();
    }

    public function getCategoriesByUser($userId = null) {
        $query = DB::table('products')
            ->select('categories.id', 'categories.name')
            ->join('categories', 'products.category_id', '=', 'categories.id' )
            ->distinct();
        if ($userId) {
            $query->where('products.user_id', $userId);
        }
        return $query->get();
    }

    public function filterData($request){
        $sort_search    = null;
        $categories     = $this->model->orderBy('created_at', 'desc');
        if ($request->has('search')) {
            $sort_search = $request->search;
            $categories = $categories->where('name', 'like', '%' . $sort_search . '%');
        }

        $categories = $categories->paginate(self::PER_PAGE);

        return $categories;
    }

    public function getSelectOptionsArray()
    {
        return DB::table('categories')->select(['id', 'name'])->get();
    }

    public function updateStatusOrFeatured($param, $id): bool
    {
        $model = $this->model::find($id);
        if ($model) {
            if (isset($param['featured'])) {
                $model->featured = !$model->featured;
                if ($model->save()) {
                    return true;
                }
            }
            if (isset($param['status'])) {
                $model->status = !$model->status;
                if ($model->save()) {
                    return true;
                }
            }
        }
        return false;
    }

    public function getTopCategories()
    {
        return Category::where('top', 1)
                       ->where('status', 1)
                       ->get();
    }

    public function getCategoryFilter($validProducts)
    {
        $allCategories = $validProducts
                    ->whereNotNull('products.category_id')
                    ->select('category_id')
                    ->distinct()
                    ->get()
                    ->toArray();

        $categories = Category::where('status', 1)
                    ->whereIn('id', array_column($allCategories, 'category_id'))
                    ->select('id','name', 'is_age_restricted')
                    ->get();

        return $categories;
    }

}
