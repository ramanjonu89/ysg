<?php
namespace App\Services;

use App\Repositories\CategoryRepository;
use App\Services\UserService;
use Illuminate\Support\Str;


class CategoryService extends AbstractService
{
    protected $repo;

    public function __construct(CategoryRepository $repo)
    {
        parent::__construct($repo);
    }

    public function getSub($id) {
        return $this->repo->getSub($id);
    }

    public function findSubBy($parentId, $column, $value)
    {
        return $this->repo->findSubBy($parentId, $column, $value);
    }

    public function getSubSub($id) {
        return $this->repo->getSubSub($id);
    }

    public function findSubSubBy($parentId, $column, $value)
    {
        return $this->repo->findSubSubBy($parentId, $column, $value);
    }

    /**
     * get list of Category
     */
    public function getList()
    {
        return $this->repo->getList([
            'status' => 1,
        ]);
    }

    public function getAllList()
    {
        return $this->repo->getList([
            'status' => 1,
            'perPage' => 0,
        ]);
    }

    /**
     * get tree list of Category included sub and subsub
     */
    public function getTree()
    {
        $params = [
            'status' => 1,
            'perPage' => 0,
        ];

        $listSubSub = [];
        foreach ($this->repo->getListSubSub($params) as $subSub) {
            $listSubSub[$subSub->sub_category_id][$subSub->id] = [
                'item' => $subSub
            ];
        }

        $listSub = [];
        foreach ($this->repo->getListSub($params) as $sub) {
            $children = $listSubSub[$sub->id]??[];
            $listSub[$sub->category_id][$sub->id] = [
                'item' => $sub,
                'children' => $children,
            ];
        }

        $list = [];
        foreach ($this->repo->getList($params) as $cate) {
            $children = $listSub[$cate->id]??[];
            $list[$cate->id] = [
                'item' => $cate,
                'children' => $children,
            ];
        }

        return $list;
    }

    public function getMainCategories()
    {
        if (isMicrosite()) {
            $shop = getMicrositeShop();

            $categories = $this->repo->getCategoriesByShop($shop);
        } else {
            $categories = $this->repo->getCategories();
        }

        return $categories;
    }

    public function getSubCategories($categoryId)
    {
        $shop = getMicrositeShop();
        $categories = $this->repo->getSubCategoriesByShop($shop, $categoryId);

        return $categories;
    }

    public function getSubCategoriesFlagShip($categoryId)
    {
        $userService = app()->make(userService::class);
        $userIds = $userService->getFlagShipUserIdsArray();
        $categories = $this->repo->getSubCategoriesFlagShip($categoryId, $userIds);

        return $categories;
    }

    public function getCategoriesByUser($userId = null) {
        return $this->repo->getCategoriesByUser($userId);
    }

    public function formatDataInputs(object $request, $category = null){
        $inputs['name']             = @$request->name[0];
        $inputs['meta_title']             = @$request->meta_title[0];
        $inputs['meta_description']             = @$request->meta_description[0];

        $slug = '';
        if (isset($request->slug)) {
            $slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->slug));
        } else {
            $slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name[0])) . '-' . Str::random(5);
        }
        $inputs['slug']             = $slug;

        if(isset($request->commision_rate)){
            $inputs['commision_rate']  = $request->commision_rate;
        }

        if ($request->hasFile('banner')) {
            $inputs['banner'] = $request->file('banner')->store('uploads/categories/banner');
        }else{
            $inputs['banner'] = isset($category) ? $category->banner : null;
        }
        if ($request->hasFile('icon')) {
            $inputs['icon'] = $request->file('icon')->store('uploads/categories/icon');
        }else{
            $inputs['icon'] = isset($category) ? $category->icon : null;
        }
        $inputs['digital'] = $request->digital;
        $inputs['is_age_restricted'] = $request->is_age_restricted ?? 0;

        return $inputs;
    }

    public function filterData(object $request)
    {
        $categories = $this->repo->filterData($request);
        return $categories;
    }

    public function getSelectOptionsArray()
    {
        return $this->repo->getSelectOptionsArray();
    }

    public function updateFeatured(int $id): bool
    {
        $param = [
            'featured' => true,
        ];
        return $this->repo->updateStatusOrFeatured($param, $id);
    }

    public function updateStatus(int $id): bool
    {
        $param = [
            'status' => true,
        ];
        return $this->repo->updateStatusOrFeatured($param, $id);

    }

    public function getTopCategories()
    {
        return $this->repo->getTopCategories();
    }

    public function findId($categoryIds)
    {
        try {
            $categoryModel = $this->repo->findBy('id', $categoryIds);
        } catch (\Exception $e) {
            $categoryModel = null;
        }

        return $categoryModel;
    }

    public function getCategoryFilter($validProducts)
    {
        return $this->repo->getCategoryFilter($validProducts);
    }

    public function getCategoriesByShop($shop)
    {
        return $this->repo->getCategoriesByShop($shop);
    }
}
