<?php
namespace App\Repositories\Api\Mobile;

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\SubSubCategory;
use App\Repositories\AbstractRepo;
use Illuminate\Support\Facades\DB;

class CategoryRepository extends AbstractRepo
{
    const PER_PAGE = 15;

    protected $subModel;
    protected $subSubModel;

    public function __construct(SubCategory $subModel, SubSubCategory $subSubModel)
    {
        parent::__construct();
        $this->subModel = $subModel;
        $this->subSubModel = $subSubModel;
    }

    public function getModel(): string
    {
        return Category::class;
    }

    /**
     * get category by id
     * @param int $id
     * @return Category|null
     */
    public function get(int $id)
    {
        return $this->model::query()
            ->where('id', $id)
            ->first();
    }

    /**
     * get SubCategory by id
     * @param int $id
     * @return SubCategory|null
     */
    public function getSub(int $id)
    {
        return $this->subModel::query()
            ->where('id', $id)
            ->first();
    }

    /**
     * Get SubCategory by id.
     * @param int $id
     * @return SubSubCategory|null
     */
    public function getSubSub(int $id) {
        return $this->subSubModel::query()
            ->where('id', $id)
            ->first();
    }

    /**
     * Get list of categories with subcategories and subsubcategories that have published products.
     * @param array $params
     * @return mixed
     */
    public function getList(array $params)
    {
        $allCategories = $this->getFullProducts($params);
        $showLevel = $params['show_level'] ?? 1;

        $query = $this->model;
        if ($showLevel > 1) {
            $query->with(['subcategories' => function($q) use ($allCategories, $showLevel) {
                if ($showLevel > 2) {
                    $q->with(['subsubcategories' => function($qs) use ($allCategories) {
                        $qs->whereIn('id', array_column($allCategories, 'subsubcategory_id'));
                    }])->whereIn('id', array_column($allCategories, 'subcategory_id'));
                    $q->where('status', '=', 1);
                }
                $q->where('status', '=', 1);
            }]);
        }

        $query->where('status', '=', 1)
            ->whereIn('id', array_column($allCategories, 'category_id'));

        $perPage = $params['per_page'] ?? self::PER_PAGE;
        if ($perPage != 0) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }

    /**
     * Get all categoryIds, subcategoryIds, and subsubcategoryIds that have published products.
     * @param array $filter
     * @return array
     */
    public function getFullProducts(array $filter = [])
    {
        $query = DB::table('products')
            ->where('published', 1)
            ->select(['category_id', 'subcategory_id', 'subsubcategory_id'])
            ->distinct('subsubcategory_id');

        if (!empty($filter['categoryId'])) {
            $query->where('category_id', $filter['categoryId']);
        }

        if (!empty($filter['userIds'])) {
            $query->where('user_id', $filter['userIds']);
        }

        return $query->get()->toArray();
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
                ->orderBy('name', 'asc')
                ->get();
    }
}
