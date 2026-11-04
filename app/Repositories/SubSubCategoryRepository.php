<?php
namespace App\Repositories;

use App\Models\SubSubCategory;

class SubSubCategoryRepository extends AbstractRepo
{
    
    const PER_PAGE = 15;

    public function getModel(): string
    {
        return SubSubCategory::class;
    }

    public function filterData($request){
        $sort_search    = null;
        $subSubCategories     = $this->model->orderBy('created_at', 'desc');
        if ($request->has('search')) {
            $sort_search = $request->search;
            $subSubCategories = $subSubCategories->where('name', 'like', '%' . $sort_search . '%');
        }

        $subSubCategories = $subSubCategories->paginate(self::PER_PAGE);

        return $subSubCategories;
    }

    public function updateStatus($id): bool
    {
        $model = $this->model::find($id);
        if ($model) {
            $model->status = !$model->status;
            if ($model->save()) {
                return true;
            }
        }
        return false;
    }

    public function getSubSubCategoryFilter($validProducts)
    {
        $allSubSubCategories = $validProducts
                ->select('subsubcategory_id')
                ->distinct()
                ->get()
                ->toArray();

        $subSubCategories = SubSubCategory::where('status', 1)
            ->whereIn('id', array_column($allSubSubCategories, 'subsubcategory_id'))
            ->select('id','name')
            ->get();

        return $subSubCategories;
    }

    public function getList($params)
    {
        $query = $this->model->query();
        if (!empty($params['status'])) {
            $query->where('status', $params['status']);
        }

        $perPage = $params['perPage'] ?? self::PER_PAGE;
        if ($perPage != 0) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }
}
