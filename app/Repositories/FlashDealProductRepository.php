<?php
namespace App\Repositories;

use App\Models\FlashDealProduct;

class FlashDealProductRepository extends AbstractRepo
{
    const PER_PAGE = 15;

    public function getModel(): string
    {
        return FlashDealProduct::class;
    }

    public function getList($params)
    {
        $query = $this->model->query();

        $query->orderBy('created_at', 'desc');

        $perPage = $params['perPage'] ?? self::PER_PAGE;
        if ($perPage != 0) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }

    public function getByProductId($id) {
        return $this->model->where('product_id', $id)->first();
    }

    public function getFlashDealProductList($params) {
        $flashDealId = $params['flashDealId'];
        $query = FlashDealProduct::join('products', 'flash_deal_products.product_id', '=', 'products.id')
                                ->select('flash_deal_products.*', 'products.name as product_name');
                                
        if (!empty($flashDealId)) {
            $query->where('flash_deal_id',$flashDealId);
        }

        return $query->get();
    }
}
