<?php
namespace App\Services;

use App\Models\SubCategory;
use App\Repositories\SubCategoryRepository;
use Illuminate\Support\Str;

class SubCategoryService extends AbstractService
{

    protected $repo;

    public function __construct(SubCategoryRepository $repo)
    {
        $this->repo = $repo;
        parent::__construct($repo);
    }

    public function formatDataInputs(object $request){
        $inputs['name']                 = @$request->name[0];
        $inputs['meta_title']           = @$request->meta_title[0];
        $inputs['meta_description']     = @$request->meta_description[0];
        $inputs['category_id']          = $request->category_id;

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
        $subCategories = $this->repo->filterData($request);
        return $subCategories;
    }

    public function updateStatus(int $id): bool
    {
        return $this->repo->updateStatus($id);
    }

    public function findId($subCategoryIds)
    {
        try {
            $subCategoryModel = $this->repo->findBy('id', $subCategoryIds);
        } catch (\Exception $e) {
            $subCategoryModel = null;
        }

        return $subCategoryModel;
    }

    public function getSubCategoryFilter($validProducts)
    {
        return $this->repo->getSubCategoryFilter($validProducts);
    }

    public function getList()
    {
        $params = [
            'status' => SubCategory::STATUS_ACTIVE,
            'perPage' => 9999,
        ];

        return $this->repo->getList($params);
    }
}
