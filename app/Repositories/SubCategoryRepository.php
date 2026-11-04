<?php
namespace App\Repositories;

use App\Models\SubCategory;

class SubCategoryRepository extends AbstractRepo
{
    
    const PER_PAGE = 15;

    public function getModel(): string
    {
        return SubCategory::class;
    }

    public function filterData($request){
        $sort_search    = null;
        $subCategories     = $this->model->orderBy('created_at', 'desc');
        if ($request->has('search')) {
            $sort_search = $request->search;
            $subCategories = $subCategories->where('name', 'like', '%' . $sort_search . '%');
        }

        $subCategories = $subCategories->paginate(self::PER_PAGE);

        return $subCategories;
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

    public function getSubCategoryFilter($validProducts)
    {
        $allSubCategories = $validProducts
                ->select('subcategory_id')
                ->distinct()
                ->get()
                ->toArray();
        
        $subCategories = SubCategory::where('status', 1)
                ->whereIn('id', array_column($allSubCategories, 'subcategory_id'))
                ->select('id','name')
                ->get();

        return $subCategories;
    }

    public function getList($params)
    {
        $query = $this->model->query();
        if (!empty($params['status'])) {
            $query->where('status', $params['status']);
        }

        $query->orderBy('name', 'asc');

        $perPage = $params['perPage'] ?? self::PER_PAGE;
        if ($perPage != 0) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }

}
