<?php
namespace App\Services\Api\Mobile;

use App\Http\Resources\Api\Mobile\CategoryCollection;
use App\Http\Resources\Api\Mobile\CategoryListCollection;
use App\Repositories\Api\Mobile\CategoryRepository;
use App\Services\AbstractService;

class CategoryService extends AbstractService
{
    protected $repo;

    public function __construct(CategoryRepository $repo) {
        parent::__construct($repo);
    }

    public function getList($request, array $params)
    {
        $categories = $this->repo->getList($params);

        return $this->buildListResponse($request, $categories);
    }

    public function buildListResponse($request, $paging): array
    {
        $data = CategoryListCollection::collection($paging->items());
        return [
            'items' => $data->toArray($request),
            'pagination' => [
                'page'       => $paging->currentPage(),
                'page_size'  => $paging->perPage(),
                'total'      => $paging->total(),
                'last_page'  => $paging->lastPage(),
                'has_next'   => $paging->hasMorePages(),
            ]
        ];
    }

    public function getByLevel($request, $id, array $params)
    {
        $model = null;

        if ($params['cate_level'] == 1) {
            $model = $this->repo->get($id);
        } elseif ($params['cate_level'] == 2) {
            $model = $this->repo->getSub($id);
        } elseif ($params['cate_level'] == 3) {
            $model = $this->repo->getSubSub($id);
        }

        if ($model) {
            $collection = new CategoryCollection($model);
            return $collection->toArray($request);
        }

        return false;
    }

    public function getCategoriesByShop($shop)
    {
        return $this->repo->getCategoriesByShop($shop);
    }

}