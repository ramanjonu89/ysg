<?php
namespace App\Services;

use App\Models\SubSubCategory;
use App\Repositories\SubSubCategoryRepository;
use Illuminate\Support\Str;

class SubSubCategoryService extends AbstractService
{

    protected $repo;

    public function __construct(SubSubCategoryRepository $repo)
    {
        $this->repo = $repo;
        parent::__construct($repo);
    }

    public function formatDataInputs(object $request){
        $inputs['name']                 = @$request->name[0];
        $inputs['meta_title']           = @$request->meta_title[0];
        $inputs['meta_description']     = @$request->meta_description[0];
        $inputs['sub_category_id']      = $request->sub_category_id;

        $slug = '';
        if (isset($request->slug)) {
            $slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->slug));
        } else {
            $slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name[0])) . '-' . Str::random(5);
        }
        $inputs['slug']             = $slug;
        
        return $inputs;
    }

    public function filterData(object $request)
    {
        $subSubCategories = $this->repo->filterData($request);
        return $subSubCategories;
    }

    public function updateStatus(int $id): bool
    {
        return $this->repo->updateStatus($id);
    }

    public function findId($subSubCategoryIds)
    {
        try {
            $subSubCategoryModel = $this->repo->findBy('id', $subSubCategoryIds);
        } catch (\Exception $e) {
            $subSubCategoryModel = null;
        }

        return $subSubCategoryModel;
    }

    public function getSubSubCategoryFilter($validProducts)
    {
        return $this->repo->getSubSubCategoryFilter($validProducts);
    }

    public function getList()
    {
        $params = [
            'status' => SubSubCategory::STATUS_ACTIVE,
            'perPage' => 9999,
        ];

        return $this->repo->getList($params);
    }
}
